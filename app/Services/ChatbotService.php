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
        if ($cleanMessage === '/start') {
            return "Selamat datang di Layanan Asisten E-Aset Puskesmas Mantup!\n\nSilakan pilih kategori yang ingin ditanyakan:\n1️⃣ Persediaan\n2️⃣ Aset\n3️⃣ Pengadaan\n4️⃣ Manajemen DPP\n\nAtau ketik /laporan untuk mendapatkan ringkasan aset dan persediaan saat ini.";
        } elseif ($cleanMessage === '/laporan') {
            return $this->generateLaporan();
        }

        if ($cleanMessage === '1') {
            Cache::put($modeKey, 'persediaan', 86400); // 24 hours
            return "✅ *Mode Persediaan* diaktifkan.\nSilakan tanyakan seputar kuantitas/jumlah stok dan harga barang/obat.";
        } elseif ($cleanMessage === '2') {
            Cache::put($modeKey, 'aset', 86400);
            return "✅ *Mode Aset* diaktifkan.\nSilakan tanyakan seputar daftar, kondisi, nomor kode, atau lokasi penempatan alat kesehatan.";
        } elseif ($cleanMessage === '3') {
            Cache::put($modeKey, 'pengadaan', 86400);
            return "✅ *Mode Pengadaan* diaktifkan.\nSilakan cari riwayat transaksi masuk/keluar atau minta dokumen pengadaan (Surat Pesanan, dll) dari Google Drive.";
        } elseif ($cleanMessage === '4') {
            Cache::put($modeKey, 'dpp', 86400);
            return "✅ *Mode Manajemen DPP* diaktifkan.\n\nUntuk membuat DPP baru, silakan copy template di bawah ini, isi datanya, lalu kirimkan kembali ke sini:\n\n*Format Buat DPP:*\nNomor Surat: \nKode RUP (Obat/BMHP/Jasa/dll): \nTanggal Pesanan (DD/MM/YYYY): \nRencana Tiba (DD/MM/YYYY): \n\nAtau Anda dapat bertanya seputar data DPP yang sudah ada.";
        } elseif (in_array($cleanMessage, ['menu', 'batal', 'kembali', 'exit', 'quit'])) {
            Cache::forget($modeKey);
            return "Sesi direset.\nSilakan pilih kategori yang ingin Anda akses:\n1️⃣ Persediaan\n2️⃣ Aset\n3️⃣ Pengadaan\n4️⃣ Manajemen DPP\n\nKetik angka 1, 2, 3, atau 4.";
        }

        if (!$mode) {
            return "Halo! Saya RAKSA AI.\n\nSilakan pilih kategori yang ingin ditanyakan terlebih dahulu:\n1️⃣ Persediaan\n2️⃣ Aset\n3️⃣ Pengadaan\n4️⃣ Manajemen DPP\n\nKetik angka 1, 2, 3, atau 4.";
        }

        // Intercept DPP Creation if in DPP mode
        if ($mode === 'dpp' && (str_contains($cleanMessage, 'nomor surat:') || str_contains($cleanMessage, 'nomor surat :') || preg_match('/nomor\s+surat/i', $cleanMessage))) {
            return $this->handleDppCreation($message);
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
        } elseif ($mode === 'dpp') {
            $dpps = \App\Models\Dpp::orderBy('created_at', 'desc')->limit(10)->get();
            $dataContext .= "--- DAFTAR DPP (10 TERAKHIR) ---\n";
            if ($dpps->isEmpty()) {
                $dataContext .= "Belum ada data DPP.\n";
            } else {
                foreach($dpps as $dpp) {
                    $dataContext .= "No. Surat: {$dpp->nomor_surat} | RUP: {$dpp->kode_rup} | Tgl Pesan: {$dpp->tanggal_dpp} | Tgl Tiba: {$dpp->tanggal_selesai}\n";
                }
            }
            }
            $systemInstructions = "Kamu sedang berada di Mode Manajemen DPP. Jawab berdasarkan data DPP di atas. Jika user bertanya cara membuat DPP atau meminta template, berikan template berikut persis seperti ini agar mudah disalin:\n\nFormat Pembuatan DPP:\nNomor Surat: ...\nKode RUP: [Isi dengan angka 1 atau 2]\nTanggal Pesanan: DD/MM/YYYY\nTanggal Tiba: DD/MM/YYYY\n\nPilihan Kode RUP:\n1 = Belanja Obat-obatan (JKN)\n2 = Belanja Bahan-bahan lainnya (JKN)\n\n*(Silakan salin template di atas, isi datanya, dan kirimkan ke saya)*";
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

    private function generateLaporan()
    {
        $totalAset = \App\Models\Asset::where('status_aktif', 'true')->count();
        $totalRusak = \App\Models\Asset::where('status_aktif', 'true')->whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])->count();
        $totalPersediaan = \App\Models\Item::sum('stok_sekarang');
        
        $nilaiAset = \App\Models\Asset::sum('harga_perolehan');
        $formatRp = 'Rp ' . number_format((float)$nilaiAset, 0, ',', '.');
        
        $laporan = "📊 *LAPORAN RINGKAS E-ASET* 📊\n\n";
        $laporan .= "🔹 *Data Aset Tetap:*\n";
        $laporan .= "- Total Aset Aktif: *{$totalAset}* unit\n";
        $laporan .= "- Total Aset Rusak: *{$totalRusak}* unit\n";
        $laporan .= "- Nilai Perolehan Aset: *{$formatRp}*\n\n";
        
        $laporan .= "🔹 *Data Persediaan Logistik:*\n";
        $laporan .= "- Total Kuantitas Barang: *{$totalPersediaan}* unit/satuan\n\n";
        
        $laporan .= "Data ini di-generate secara otomatis oleh sistem.\nSilakan akses dashboard E-Aset untuk rincian lebih lengkap.";
        
        return $laporan;
    }

    private function handleDppCreation($message)
    {
        try {
            $lines = explode("\n", $message);
            $data = [];
            
            foreach ($lines as $line) {
                if (preg_match('/nomor\s*surat\s*:\s*(.+)/i', $line, $matches)) {
                    $val = trim($matches[1]);
                    // Auto format nomor surat if it's just a number
                    if (!str_contains(strtoupper($val), 'PPBJ')) {
                        $year = date('Y');
                        $val = "000.3.1/{$val}/PPBJ/413.102.5.18/{$year}";
                    }
                    $data['nomor_surat'] = $val;
                } elseif (preg_match('/kode\s*rup\s*:\s*(.+)/i', $line, $matches) || preg_match('/kode\s*rup\s*(.+)/i', $line, $matches)) {
                    $inputRup = strtolower(trim($matches[1]));
                    // Extract just the number if they type "1", "1.", etc.
                    $inputRupNumber = preg_replace('/[^0-9]/', '', $inputRup);
                    
                    if (str_contains($inputRup, 'obat') || $inputRupNumber === '1' || str_contains($inputRup, '67261766')) {
                        $data['kode_rup'] = '67261766';
                        $data['nama_paket'] = 'Belanja Barang dan Jasa (Belanja Bahan Obat-obatan(JKN))';
                        $data['spesifikasi_teknis'] = 'Belanja Obat-obatan';
                        $data['jumlah'] = '1 Paket';
                        $data['harga_satuan'] = 144000000.00;
                        $data['pagu_anggaran'] = 144000000.00;
                    } elseif (str_contains($inputRup, 'bmhp') || str_contains($inputRup, 'bahan') || $inputRupNumber === '2' || str_contains($inputRup, '67261750')) {
                        $data['kode_rup'] = '67261750';
                        $data['nama_paket'] = 'Belanja Barang dan Jasa (Belanja Bahan-bahan lainnya (JKN))';
                        $data['spesifikasi_teknis'] = 'Belanja Bahan-Bahan Lainnya';
                        $data['jumlah'] = '1 Paket';
                        $data['harga_satuan'] = 100000000.00;
                        $data['pagu_anggaran'] = 100000000.00;
                    } else {
                        $data['kode_rup'] = trim($matches[1]); // Fallback
                    }
                } elseif (preg_match('/tanggal\s*pesanan\s*:\s*(.+)/i', $line, $matches)) {
                    $rawDate = trim($matches[1]);
                    $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', $rawDate)));
                    $data['tanggal_dpp'] = $parsedDate;
                    $data['tanggal_mulai'] = $parsedDate;
                } elseif (preg_match('/(?:tanggal|rencana)\s*tiba\s*:\s*(.+)/i', $line, $matches)) {
                    $rawDate = trim($matches[1]);
                    $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', $rawDate)));
                    $data['tanggal_selesai'] = $parsedDate;
                }
            }

            if (empty($data['nomor_surat']) || empty($data['kode_rup']) || empty($data['tanggal_dpp']) || empty($data['tanggal_selesai'])) {
                $missing = [];
                if (empty($data['nomor_surat'])) $missing[] = 'Nomor Surat';
                if (empty($data['kode_rup'])) $missing[] = 'Kode RUP';
                if (empty($data['tanggal_dpp'])) $missing[] = 'Tanggal Pesanan';
                if (empty($data['tanggal_selesai'])) $missing[] = 'Tanggal Tiba';
                
                return "⚠️ *Gagal membuat DPP: Data tidak lengkap*\n\nSistem tidak dapat mendeteksi informasi berikut: *" . implode(', ', $missing) . "*\n\nPastikan format teks Anda persis seperti ini (perhatikan tanda titik dua):\n\nNomor Surat: 333\nKode RUP: Obat\nTanggal Pesanan: 26/09/2026\nTanggal Tiba: 10/10/2026\n\n*Error Code/Diagnosa Parsing*: MISSING_FIELDS_[" . implode('_', $missing) . "]";
            }

            $dpp = \App\Models\Dpp::create($data);

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('aset.dpp.pdf', compact('dpp'));
            $pdf->setPaper('A4', 'portrait');
            $pdfContent = $pdf->output();
            
            $filename = 'DPP_' . str_replace(['/', '\\'], '_', $dpp->nomor_surat) . '.pdf';

            // Upload to Google Drive (Optional/Best Effort)
            try {
                $year = date('Y');
                $folderPath = 'PENGADAAN_' . $year . '/DPP';
                $fullPath = $folderPath . '/' . $filename;
                \Illuminate\Support\Facades\Storage::disk('google')->put($fullPath, $pdfContent);
                $driveMsg = "✅ Tersimpan di Google Drive ({$folderPath})";
            } catch (\Exception $ex) {
                $driveMsg = "⚠️ Gagal menyimpan ke Google Drive (Pastikan konfigurasi drive benar).";
                \Illuminate\Support\Facades\Log::error('Drive Upload Error: ' . $ex->getMessage());
            }

            $tglPesanFmt = date('d/m/Y', strtotime($data['tanggal_dpp']));
            $tglTibaFmt = date('d/m/Y', strtotime($data['tanggal_selesai']));
            $nominalFmt = 'Rp ' . number_format($data['pagu_anggaran'] ?? 0, 0, ',', '.');

            $caption = "🎉 *DPP BERHASIL DIBUAT!*\n"
                     . "━━━━━━━━━━━━━━━━━━━━━\n"
                     . "📋 *Detail Pengadaan:*\n"
                     . "• *No. Surat:* `{$data['nomor_surat']}`\n"
                     . "• *Kode RUP:* `{$data['kode_rup']}`\n"
                     . "• *Paket:* {$data['nama_paket']}\n"
                     . "• *Pagu Anggaran:* {$nominalFmt}\n"
                     . "• *Tgl Pesanan:* {$tglPesanFmt}\n"
                     . "• *Rencana Tiba:* {$tglTibaFmt}\n"
                     . "━━━━━━━━━━━━━━━━━━━━━\n"
                     . "💾 *Status Penyimpanan:*\n"
                     . "• Database: ✅ Tersimpan (ID: #{$dpp->id})\n"
                     . "• Google Drive: {$driveMsg}\n"
                     . "━━━━━━━━━━━━━━━━━━━━━\n"
                     . "📄 *File PDF DPP siap diunduh di atas.*";

            return [
                'type' => 'document',
                'document' => $pdfContent,
                'filename' => $filename,
                'text' => $caption
            ];

        } catch (\Exception $e) {
            return "❌ *Terjadi Kesalahan!*\nGagal menyimpan DPP ke database.\nPastikan format tanggal benar (Misal: 26/09/2026). Error: " . $e->getMessage();
        }
    }
}
