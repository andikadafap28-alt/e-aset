<?php

namespace App\Services;

use App\Models\BotConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    public function processMessage($phoneOrChatId, $message, $platform)
    {
        $modeKey = 'bot_mode_' . $phoneOrChatId;
        $mode = Cache::get($modeKey);

        $normalizedMessage = $this->normalizeText($message);
        $cleanMessage = strtolower(trim($message));

        // Command handler
        if ($cleanMessage === '1') {
            Cache::put($modeKey, 'persediaan', 86400); // 24 hours
            return "✅ *Mode Persediaan* diaktifkan.\nSilakan tanyakan seputar kuantitas/jumlah stok dan harga barang/obat.";
        } elseif ($cleanMessage === '2') {
            Cache::put($modeKey, 'aset', 86400);
            return "✅ *Mode Aset* diaktifkan.\nSilakan tanyakan seputar daftar, kondisi, nomor kode, atau lokasi penempatan alat kesehatan.";
        } elseif ($cleanMessage === '3') {
            Cache::put($modeKey, 'pengadaan', 86400);
            return "✅ *Mode Pengadaan* diaktifkan.\nSilakan cari riwayat transaksi masuk/keluar atau minta dokumen pengadaan (Surat Pesanan, dll) dari Google Drive.";
        } elseif (in_array($cleanMessage, ['menu', 'batal', 'kembali', 'exit', 'quit'])) {
            Cache::forget($modeKey);
            return "Sesi direset.\nSilakan pilih kategori yang ingin Anda akses:\n1️⃣ Persediaan\n2️⃣ Aset\n3️⃣ Pengadaan\n\nKetik angka 1, 2, atau 3.";
        }

        if (!$mode) {
            return "Halo! Saya RAKSA AI.\n\nSilakan pilih kategori yang ingin ditanyakan terlebih dahulu:\n1️⃣ Persediaan\n2️⃣ Aset\n3️⃣ Pengadaan\n\nKetik angka 1, 2, atau 3.";
        }

        return $this->callGeminiApi($phoneOrChatId, $normalizedMessage, $mode, $platform);
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
            $identity = "--- IDENTITAS UTAMA ---\nNama: RAKSA AI\nPeran: Asisten Puskesmas Mantup\n\n";
            return $identity;
        });
    }

    private function callGeminiApi($phoneOrChatId, $message, $mode, $platform)
    {
        $formatRp = function($nominal) {
            return 'Rp ' . number_format((float)$nominal, 0, ',', '.');
        };

        $dataContext = "--- DATA REFERENSI SAAT INI ---\n";
        $systemInstructions = "";

        if ($mode === 'persediaan') {
            $stopwords = ['jumlah', 'stok', 'berapa', 'obat', 'barang', 'ini', 'itu', 'di', 'pada', 'dari', 'yang', 'dan', 'atau', 'untuk', 'ada', 'tidak', 'tolong', 'carikan', 'tampilkan', 'apakah', 'sisa', 'klo', 'kalo', 'kalau', 'jika', 'saya', 'punya', 'total', 'totalnya', 'hitung', 'dihitung', 'dengan', 'rupiah', 'harga', 'harganya'];
            $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($message)));
            $keywords = array_filter(array_diff($words, $stopwords), fn($w) => strlen($w) > 3); 

            $inventoryQuery = \App\Models\Item::query();
            if (!empty($keywords)) {
                $inventoryQuery->where(function($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($kw) . '%']);
                    }
                });
            } else {
                $inventoryQuery->orderBy('created_at', 'desc')->limit(15);
            }
            $inventories = $inventoryQuery->get();

            if ($inventories->isEmpty()) {
                $dataContext .= "Item yang ditanyakan tidak ditemukan dalam daftar persediaan.\n";
            } else {
                foreach($inventories as $inv) {
                    $stok = $inv->stok_sekarang ?? 0;
                    $harga = $formatRp($inv->harga_satuan ?? 0);
                    $dataContext .= "• {$inv->nama_barang} (Stok Kuantitas: {$stok}, Harga Satuan: {$harga})\n";
                }
            }

            $systemInstructions = "Kamu sedang berada di Mode Persediaan. Jawab berdasarkan data persediaan di atas.\nATURAN KETAT KATA KUNCI:\n1. Jika user menanyakan 'jumlah' atau 'berapa banyak' atau 'stok', berikan angka KUANTITAS STOK.\n2. Jika user menanyakan 'total biaya', 'harga', 'total harga', berikan nominal HARGA RUPIAH.";
        
        } elseif ($mode === 'aset') {
            $assets = \App\Models\Asset::select('asset_code', 'name', 'location', 'condition')
                        ->orderBy('created_at', 'desc')
                        ->limit(30)->get();

            if ($assets->isEmpty()) $dataContext .= "Data aset kosong.\n";
            foreach($assets as $asset) {
                $dataContext .= "• {$asset->name} (Kode: {$asset->asset_code}, Ruang: {$asset->location}, Kondisi: {$asset->condition})\n";
            }

            $systemInstructions = "Kamu sedang berada di Mode Aset. Jawab berdasarkan data aset di atas. Berikan detail nomor kode barang dan ruangan penempatannya.";

        } elseif ($mode === 'pengadaan') {
            $namaBulanIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
            
            // Rekap Transaksi
            $pengadaan = \App\Models\InventoryTransaction::where('jenis_transaksi', 'masuk')
                            ->whereMonth('tanggal_transaksi', date('m'))->whereYear('tanggal_transaksi', date('Y'))
                            ->selectRaw('SUM(jumlah) as qty, SUM(jumlah * harga_satuan) as rp')->first();
            
            $dataContext .= "Total pengadaan {$namaBulanIni}: " . ($pengadaan->qty ?? 0) . " unit (Senilai " . $formatRp($pengadaan->rp ?? 0) . ").\n\n";

            // Daftar File GDrive
            $files = \App\Models\ProcurementFile::with('item')->get();
            $dataContext .= "--- DOKUMEN PENGADAAN (GOOGLE DRIVE) ---\n";
            if ($files->isEmpty()) {
                $dataContext .= "Belum ada dokumen pengadaan.\n";
            } else {
                foreach($files as $f) {
                    $namaItem = $f->item ? $f->item->nama_barang : 'Barang Umum';
                    $dataContext .= "ID-Doc: {$f->id} | Kategori: {$f->kategori} | Barang: {$namaItem} | Penyedia: {$f->nama_penyedia} | Dokumen: {$f->jenis_dokumen} | Tautan Drive: {$f->path_gdrive}\n";
                }
            }

            $systemInstructions = "Kamu sedang berada di Mode Pengadaan.\nATURAN KETAT:\n1. Jika user meminta link/download file (misal Surat Pesanan Tensimeter), cari di Dokumen Pengadaan.\n2. JIKA ADA NAMA PENGADAAN YANG SAMA/KEMBAR, DILARANG langsung mengirim link. Kamu WAJIB menampilkan daftar pilihannya (misal: 'Ada 2 dokumen: 1. dari PT A, 2. dari PT B. Mau yang mana?').\n3. Jika hanya ada satu dokumen ATAU user sudah menyebut spesifik (nomor/penyedia), berikan tautan Drive-nya dengan format: [Nama File](Link Drive).";
        }

        // Ambil riwayat chat
        $history = BotConversation::where('phone_number', $phoneOrChatId)
                    ->where('platform', $platform)
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get()
                    ->reverse();

        $historyText = "=== RIWAYAT CHAT TERAKHIR (Sebagai Konteks) ===\n";
        foreach ($history as $chat) {
            $role = $chat->sender === 'bot' ? 'AI' : 'User';
            $historyText .= "{$role}: {$chat->message}\n";
        }
        $historyText .= "=====================\n";

        $identity = $this->getChatbotTrainingData();
        
        $systemPrompt = "{$identity}\n{$systemInstructions}\n\n{$dataContext}";
        $promptContent = "{$historyText}\nTugasmu: Respons pesan terakhir dari User. Berikan jawaban yang sopan dan ramah.";

        $apiKey = env('GEMINI_API_KEY');
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . $apiKey;

        Log::channel($platform)->info("Mengirim RAG Mode: {$mode} ke Gemini...");

        $geminiResponse = Http::withOptions(['verify' => false])
            ->timeout(20)
            ->post($geminiUrl, [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => [['parts' => [['text' => $promptContent]]]],
                'generationConfig' => ['maxOutputTokens' => 500]
            ]);

        if (!$geminiResponse->successful()) {
            $errorBody = $geminiResponse->json('error.message') ?? $geminiResponse->body();
            $statusCode = $geminiResponse->status();
            Log::channel($platform)->error("Gemini API Error: " . $errorBody);
            return "⚠️ Sistem AI sedang mengalami gangguan dari Google API (Status: {$statusCode}).\n\nDetail Error:\n`{$errorBody}`";
        }

        $botReply = $geminiResponse->json('candidates.0.content.parts.0.text');
        
        if (!$botReply) {
            $finishReason = $geminiResponse->json('candidates.0.finishReason');
            if ($finishReason === 'SAFETY') {
                return "⚠️ Pesan diblokir oleh Google AI Safety Filter karena mengandung kata-kata yang dianggap sensitif (medis/bahaya).";
            }
            return "Maaf, format balasan AI tidak sesuai atau kosong. Reason: " . ($finishReason ?? 'Unknown');
        }

        return trim($botReply);
    }
}
