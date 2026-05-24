<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BotConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function setupWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            return response()->json(['error' => 'TELEGRAM_BOT_TOKEN not set in .env'], 500);
        }

        $url = url('/webhook/telegram');
        $response = Http::withOptions(['verify' => false])
            ->get("https://api.telegram.org/bot{$token}/setWebhook", [
                'url' => $url
            ]);

        return response()->json($response->json());
    }

    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            Log::channel('telegram')->info('Incoming Telegram Data: ' . json_encode($data));

            if (isset($data['message']['text'])) {
                $chatId = $data['message']['chat']['id'];
                $textMessage = $data['message']['text'];

                // Authorization Check
                $allowedChatsRaw = \App\Models\Setting::where('key', 'authorized_telegram_chats')->value('value');
                $allowedChats = $allowedChatsRaw ? explode(',', str_replace(' ', '', $allowedChatsRaw)) : [];

                if (!empty($allowedChats) && !in_array((string)$chatId, $allowedChats)) {
                    $this->sendTelegramMessage($chatId, "⛔ Akses Ditolak.\nID Telegram Anda adalah: `{$chatId}`.\n\nSilakan daftarkan ID ini di menu Pengaturan Sistem E-Aset untuk menggunakan asisten ini.");
                    return response('OK', 200);
                }

                if (empty($allowedChats)) {
                    $this->sendTelegramMessage($chatId, "⚠️ Sistem Keamanan Aktif.\nBelum ada pengguna yang diizinkan.\nID Telegram Anda adalah: `{$chatId}`.\n\nSilakan daftarkan ID ini di menu Pengaturan Sistem E-Aset untuk memberikan akses.");
                    return response('OK', 200);
                }

                BotConversation::create([
                    'phone_number' => $chatId,
                    'sender' => 'user',
                    'message' => $textMessage,
                    'platform' => 'telegram'
                ]);

                $this->processAiResponse($chatId, $textMessage);
            }
        } catch (\Throwable $th) {
            Log::channel('telegram')->error('FATAL ERROR in Handle: ' . $th->getMessage());
        }

        return response('OK', 200);
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

    protected function processAiResponse($chatId, $message)
    {
        try {
            $normalizedMessage = $this->normalizeText($message);
            Log::channel('telegram')->info("Pesan ternormalisasi: " . $normalizedMessage);

            // 1. AMBIL KONTEKS DATA DARI BERBAGAI TABEL
            $assets = \App\Models\Asset::select('asset_code', 'name', 'location', 'condition')
                        ->orderBy('created_at', 'desc')
                        ->limit(20)->get();

            $stopwords = [
                'jumlah', 'stok', 'berapa', 'obat', 'barang', 'ini', 'itu', 'di', 'pada', 'dari', 
                'yang', 'dan', 'atau', 'untuk', 'ada', 'tidak', 'tolong', 'carikan', 'tampilkan', 
                'apakah', 'sisa', 'klo', 'kalo', 'kalau', 'jika', 'saya', 'punya', 'total', 'totalnya', 
                'hitung', 'dihitung', 'dengan', 'rupiah', 'harga', 'harganya'
            ];
            
            $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($normalizedMessage)));
            $keywords = array_diff($words, $stopwords);
            $keywords = array_filter($keywords, fn($w) => strlen($w) > 3); 

            $inventoryQuery = \App\Models\Item::query();
            if (!empty($keywords)) {
                $inventoryQuery->where(function($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($kw) . '%']);
                    }
                });
            } else {
                $inventoryQuery->orderBy('created_at', 'desc')->limit(10);
            }
            $inventories = $inventoryQuery->get();

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

            $namaBulanIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
            $namaBulanLalu = \Carbon\Carbon::now()->subMonth()->locale('id')->translatedFormat('F Y');

            $formatRp = function($nominal) {
                return 'Rp ' . number_format((float)$nominal, 0, ',', '.');
            };

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
            
            $systemPrompt = "Kamu adalah RAKSA AI. Berikut adalah identitasmu, panduan percakapan, dan referensi data real-time dari database Puskesmas Mantup:\n\n{$dataContext}\n\nTugasmu: Jawab pertanyaan user sesuai dengan Identitas dan Panduan Percakapan di atas. Jika user bertanya tentang data obat/aset/pengadaan, gunakan Data Referensi. Gunakan gaya Telegram yang ramah. Jangan menebak data yang tidak ada.";

            // 3. Tembak Gemini API
            $apiKey = env('GEMINI_API_KEY');
            $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . $apiKey;

            Log::channel('telegram')->info("Raksa: Mengirim prompt RAG Multi-Tabel + Training Data ke Gemini...");

            $geminiResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
                ->timeout(20)
                ->post($geminiUrl, [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $normalizedMessage]]]],
                    'generationConfig' => ['maxOutputTokens' => 400]
                ]);

            if (!$geminiResponse->successful()) {
                $errorBody = $geminiResponse->json('error.message') ?? $geminiResponse->body();
                $statusCode = $geminiResponse->status();
                Log::channel('telegram')->error("Gemini API Error: " . $errorBody);
                $botReply = "⚠️ Sistem AI sedang mengalami gangguan dari Google API (Status: {$statusCode}).\n\nDetail Error:\n`{$errorBody}`";
            } else {
                $botReply = $geminiResponse->json('candidates.0.content.parts.0.text');
                
                // Cek Safety Ratings (Jika Gemini memblokir pesan karena dianggap berbahaya/medis sensitif)
                if (!$botReply) {
                    $finishReason = $geminiResponse->json('candidates.0.finishReason');
                    if ($finishReason === 'SAFETY') {
                        $botReply = "⚠️ Pesan diblokir oleh Google AI Safety Filter karena mengandung kata-kata yang dianggap sensitif (medis/bahaya).";
                    } else {
                        $botReply = "Maaf, format balasan AI tidak sesuai atau kosong. Reason: " . ($finishReason ?? 'Unknown');
                    }
                }
            }

            // 4. Simpan & Kirim
            \App\Models\BotConversation::create([
                'phone_number' => $chatId,
                'sender' => 'bot',
                'message' => $botReply,
                'platform' => 'telegram'
            ]);

            $this->sendTelegramMessage($chatId, $botReply);

        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            Log::channel('telegram')->error('FATAL Error pada processAiResponse: ' . $errorMsg . ' di baris ' . $e->getLine());
            $this->sendTelegramMessage($chatId, "⚠️ Terjadi kesalahan internal sistem: \n`{$errorMsg}`");
        }
    }

    private function sendTelegramMessage($chatId, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) return;

        $teleUrl = "https://api.telegram.org/bot{$token}/sendMessage";

        $teleResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
            ->timeout(20)
            ->post($teleUrl, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);

        if (!$teleResponse->successful()) {
            Log::channel('telegram')->error("Telegram API Error: " . $teleResponse->body());
        }
    }
}
