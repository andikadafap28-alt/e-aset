<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Item;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Log;

#[Signature('app:check-low-stock')]
#[Description('Mengecek stok persediaan yang menipis dan mengirim notifikasi WhatsApp')]
class CheckLowStockCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan stok...');

        $threshold = 5;
        $lowStockItems = Item::where('stok_sekarang', '<=', $threshold)->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('Semua stok aman.');
            return;
        }

        $message = "🚨 *PERINGATAN STOK MENIPIS* 🚨\n\n";
        $message .= "Berikut adalah daftar barang persediaan yang stoknya hampir habis (≤ $threshold):\n\n";

        foreach ($lowStockItems as $item) {
            $message .= "• {$item->nama_barang} (Sisa: {$item->stok_sekarang} {$item->satuan})\n";
        }

        $message .= "\n_Harap segera melakukan pengadaan. Pesan ini dikirim otomatis oleh Sistem RAKSA._";

        $adminPhone = env('WA_ADMIN_PHONE', '');

        if (empty($adminPhone)) {
            $this->warn('Nomor WA_ADMIN_PHONE belum diatur di .env');
            Log::channel('whatsapp')->warning('CheckLowStockCommand: WA_ADMIN_PHONE belum diatur.');
            return;
        }

        $waService = new WhatsAppNotificationService();
        $isSent = $waService->sendTextMessage($adminPhone, $message);

        if ($isSent) {
            $this->info("Notifikasi berhasil dikirim ke $adminPhone");
        } else {
            $this->error("Gagal mengirim notifikasi (cek log channel whatsapp).");
        }
    }
}
