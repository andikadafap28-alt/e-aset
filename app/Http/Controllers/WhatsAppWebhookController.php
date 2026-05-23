<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BotConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === env('WHATSAPP_VERIFY_TOKEN')) {
            Log::channel('whatsapp')->info('Webhook Verified Successfully.');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }
        
        Log::channel('whatsapp')->warning('Webhook Verification Failed.', $request->all());
        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            // Gunakan json_encode agar tidak terjadi error normalization
            Log::channel('whatsapp')->info('Incoming WA Data: ' . json_encode($data));

            if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
                $messageData = $data['entry'][0]['changes'][0]['value']['messages'][0];
                $phone = $messageData['from'];
                
                if ($messageData['type'] === 'text') {
                    $textMessage = $messageData['text']['body'];

                    \App\Models\BotConversation::create([
                        'phone_number' => $phone,
                        'sender' => 'user',
                        'message' => $textMessage,
                    ]);

                    $this->processAiResponse($phone, $textMessage);
                }
            }
        } catch (\Throwable $th) {
            // Tangkap SEMUA error di level terluar agar tidak pernah 500
            Log::channel('whatsapp')->error('FATAL ERROR in Handle: ' . $th->getMessage());
        }

        // WAJIB SELALU RETURN 200 AGAR META BERHENTI SPAM RETRY
        return response('EVENT_RECEIVED', 200);
    }

    private function normalizeText($text)
    {
        $dict = cache()->remember('normalize_dict', 86400, function () {
            $path = storage_path('app/normalize.txt');
            $dictionary = [];
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $parts = preg_split('/\s+/', trim($line), 2);
                    if (count($parts) == 2) {
                        $dictionary[strtolower(trim($parts[0]))] = strtolower(trim($parts[1]));
                    }
                }
            }
            return $dictionary;
        });

        return preg_replace_callback('/\b([a-zA-Z0-9]+)\b/i', function($matches) use ($dict) {
            $word = strtolower($matches[1]);
            return isset($dict[$word]) ? $dict[$word] : $matches[1];
        }, $text);
    }

    private function getChatbotTrainingData()
    {
        return cache()->remember('chatbot_training_data', 86400, function () {
            $files = ['agent.json', 'dialog.json', 'motivasi.json', 'user.json', 'None.json'];
            $trainingText = "--- PANDUAN PERCAKAPAN UMUM (INTENT) ---\nJika pertanyaan user cocok dengan kata kunci di bawah, gunakan jawaban yang disediakan:\n";
            $identity = "--- IDENTITAS UTAMA ---\n";

            foreach ($files as $file) {
                $path = storage_path('app/' . $file);
                if (file_exists($path)) {
                    $data = json_decode(file_get_contents($path), true);
                    if (is_array($data)) {
                        foreach ($data as $item) {
                            if (isset($item['intent'])) {
                                $utterances = implode(' / ', $item['utterances'] ?? []);
                                $answers = implode(' / ', $item['answers'] ?? []);
                                if (!empty($utterances) && !empty($answers)) {
                                    $trainingText .= "• User: [{$utterances}] -> AI: [{$answers}]\n";
                                } else if ($item['intent'] === 'None' && !empty($answers)) {
                                    $trainingText .= "• Jika sama sekali tidak paham maksud user -> AI: [{$answers}]\n";
                                }
                            } else if (isset($item['name']) && isset($item['identity'])) {
                                $lokasi = $item['location'] ?? '';
                                $sikap = $item['tone'] ?? '';
                                $identity .= "Nama: {$item['name']}\nPeran: {$item['identity']}\nLokasi: {$lokasi}\nSikap: {$sikap}\n";
                                if (isset($item['capabilities']) && is_array($item['capabilities'])) {
                                    $identity .= "Kemampuan: " . implode(', ', $item['capabilities']) . "\n";
                                }
                                $identity .= "\n";
                            }
                        }
                    }
                }
            }
            return $identity . $trainingText . "\n";
        });
    }

    protected function processAiResponse($phone, $message)
    {
        try {
            // Normalisasi pesan user (mengubah bahasa alay/singkatan menjadi baku)
            $normalizedMessage = $this->normalizeText($message);
            Log::channel('whatsapp')->info("Pesan ternormalisasi: " . $normalizedMessage);

            // 1. AMBIL KONTEKS DATA DARI BERBAGAI TABEL
            
            // A. Data Aset Tetap
            $assets = \App\Models\Asset::select('asset_code', 'name', 'location', 'condition')
                        ->orderBy('created_at', 'desc')
                        ->limit(20)->get();

            // B. Filter Data Persediaan (Obat/BHP) berdasarkan kata kunci di pesan user
            $stopwords = [
                'jumlah', 'stok', 'berapa', 'obat', 'barang', 'ini', 'itu', 'di', 'pada', 'dari', 
                'yang', 'dan', 'atau', 'untuk', 'ada', 'tidak', 'tolong', 'carikan', 'tampilkan', 
                'apakah', 'sisa', 'klo', 'kalo', 'kalau', 'jika', 'saya', 'punya', 'total', 'totalnya', 
                'hitung', 'dihitung', 'dengan', 'rupiah', 'harga', 'harganya'
            ];
            
            // Gunakan pesan yang sudah ternormalisasi agar alay/singkatan bisa ke-filter dengan baik
            $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($normalizedMessage)));
            $keywords = array_diff($words, $stopwords);
            // Ambil kata > 3 huruf agar singkatan/typo tidak salah nyangkut ke nama barang
            $keywords = array_filter($keywords, fn($w) => strlen($w) > 3); 

            $inventoryQuery = \App\Models\Item::query();
            if (!empty($keywords)) {
                $inventoryQuery->where(function($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($kw) . '%']);
                    }
                });
            } else {
                // Jika tidak ada kata kunci spesifik, batasi sangat sedikit agar tidak membebani memori
                $inventoryQuery->orderBy('created_at', 'desc')->limit(10);
            }
            $inventories = $inventoryQuery->get();

            // C. Agregat Pengadaan (Masuk) Bulan Ini & Bulan Lalu (Termasuk Harga)
            $pengadaanBulanIni = \App\Models\InventoryTransaction::where('jenis_transaksi', 'masuk')
                                    ->whereMonth('tanggal_transaksi', date('m'))
                                    ->whereYear('tanggal_transaksi', date('Y'))
                                    ->selectRaw('SUM(jumlah) as total_jumlah, SUM(jumlah * harga_satuan) as total_harga')
                                    ->first();
            
            $pengadaanBulanLalu = \App\Models\InventoryTransaction::where('jenis_transaksi', 'masuk')
                                    ->whereMonth('tanggal_transaksi', date('m', strtotime('-1 month')))
                                    ->whereYear('tanggal_transaksi', date('Y', strtotime('-1 month')))
                                    ->selectRaw('SUM(jumlah) as total_jumlah, SUM(jumlah * harga_satuan) as total_harga')
                                    ->first();

            // D. Agregat Pengeluaran (Keluar) Bulan Ini & Bulan Lalu (Termasuk Harga)
            $pengeluaranBulanIni = \App\Models\InventoryTransaction::where('jenis_transaksi', 'keluar')
                                    ->whereMonth('tanggal_transaksi', date('m'))
                                    ->whereYear('tanggal_transaksi', date('Y'))
                                    ->selectRaw('SUM(jumlah) as total_jumlah, SUM(jumlah * harga_satuan) as total_harga')
                                    ->first();
            
            $pengeluaranBulanLalu = \App\Models\InventoryTransaction::where('jenis_transaksi', 'keluar')
                                    ->whereMonth('tanggal_transaksi', date('m', strtotime('-1 month')))
                                    ->whereYear('tanggal_transaksi', date('Y', strtotime('-1 month')))
                                    ->selectRaw('SUM(jumlah) as total_jumlah, SUM(jumlah * harga_satuan) as total_harga')
                                    ->first();

            // Dapatkan nama bulan secara eksplisit agar AI paham
            $namaBulanIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
            $namaBulanLalu = \Carbon\Carbon::now()->subMonth()->locale('id')->translatedFormat('F Y');

            // Helper untuk format rupiah
            $formatRp = function($nominal) {
                return 'Rp ' . number_format((float)$nominal, 0, ',', '.');
            };

            // Ambil data training dari file JSON
            $trainingData = $this->getChatbotTrainingData();

            // 2. BANGUN TEKS KONTEKS UNTUK AI
            $dataContext = "{$trainingData}";

            $dataContext .= "--- DATA ASET TETAP (ALAT KESEHATAN) ---\n";
            if ($assets->isEmpty()) $dataContext .= "Kosong.\n";
            foreach($assets as $asset) {
                $dataContext .= "• {$asset->name} (Kode: {$asset->asset_code}, Kondisi: {$asset->condition})\n";
            }

            $dataContext .= "\n--- DATA PERSEDIAAN (HASIL FILTER PENCARIAN) ---\n";
            if ($inventories->isEmpty()) {
                $dataContext .= "Item yang ditanyakan tidak ditemukan dalam daftar persediaan.\n";
            } else {
                foreach($inventories as $inv) {
                    $namaItem = $inv->nama_barang ?? 'Item';
                    $stokItem = $inv->stok_sekarang ?? 0;
                    $hargaSatuan = $formatRp($inv->harga_satuan ?? 0);
                    $dataContext .= "• {$namaItem} (Stok: {$stokItem}, Harga Satuan: {$hargaSatuan})\n";
                }
            }

            $dataContext .= "\n--- REKAP PENGADAAN (BARANG MASUK) ---";
            $dataContext .= "\nTotal pengadaan {$namaBulanIni} (Bulan Ini): " . ($pengadaanBulanIni->total_jumlah ?? 0) . " unit (Senilai " . $formatRp($pengadaanBulanIni->total_harga ?? 0) . ").";
            $dataContext .= "\nTotal pengadaan {$namaBulanLalu} (Bulan Lalu): " . ($pengadaanBulanLalu->total_jumlah ?? 0) . " unit (Senilai " . $formatRp($pengadaanBulanLalu->total_harga ?? 0) . ").\n";

            $dataContext .= "\n--- REKAP PENGELUARAN (BARANG KELUAR) ---";
            $dataContext .= "\nTotal pengeluaran {$namaBulanIni} (Bulan Ini): " . ($pengeluaranBulanIni->total_jumlah ?? 0) . " unit (Senilai " . $formatRp($pengeluaranBulanIni->total_harga ?? 0) . ").";
            $dataContext .= "\nTotal pengeluaran {$namaBulanLalu} (Bulan Lalu): " . ($pengeluaranBulanLalu->total_jumlah ?? 0) . " unit (Senilai " . $formatRp($pengeluaranBulanLalu->total_harga ?? 0) . ").\n";
            
            $systemPrompt = "Kamu adalah RAKSA AI. Berikut adalah identitasmu, panduan percakapan, dan referensi data real-time dari database Puskesmas Mantup:\n\n{$dataContext}\n\nTugasmu: Jawab pertanyaan user sesuai dengan Identitas dan Panduan Percakapan di atas. Jika user bertanya tentang data obat/aset/pengadaan, gunakan Data Referensi. Gunakan gaya WhatsApp (ramah, *tebal*, _miring_). Jangan menebak data yang tidak ada.";

            // 3. Tembak Gemini API
            $apiKey = env('GEMINI_API_KEY');
            $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . $apiKey;

            Log::channel('whatsapp')->info("Raksa: Mengirim prompt RAG Multi-Tabel + Training Data ke Gemini...");

            $geminiResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                ->timeout(20)
                ->post($geminiUrl, [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $normalizedMessage]]]],
                    'generationConfig' => ['maxOutputTokens' => 400]
                ]);

            $botReply = $geminiResponse->json('candidates.0.content.parts.0.text') ?? 'Maaf, format balasan AI tidak sesuai.';

            if (!$geminiResponse->successful()) {
                Log::channel('whatsapp')->error("Gemini API Error: " . $geminiResponse->body());
                $botReply = "Maaf, memori AI RAKSA sedang offline.";
            }

            // 4. Simpan & Kirim
            \App\Models\BotConversation::create([
                'phone_number' => $phone,
                'sender' => 'bot',
                'message' => $botReply,
            ]);

            $waToken = env('META_WA_TOKEN');
            $waPhoneId = env('META_PHONE_NUMBER_ID');
            $waUrl = "https://graph.facebook.com/v19.0/{$waPhoneId}/messages";

            $waResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                ->withToken($waToken)
                ->timeout(20)
                ->post($waUrl, [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => ['body' => $botReply]
                ]);

            if (!$waResponse->successful()) {
                Log::channel('whatsapp')->error("Meta WA Error: " . $waResponse->body());
            }

        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('FATAL Error pada processAiResponse: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }
    }
}
