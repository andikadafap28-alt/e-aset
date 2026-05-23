<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetMaintenance;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckMaintenanceDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-maintenance-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek jadwal pemeliharaan aset dalam 3 hari ke depan dan mengirim notifikasi WA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan jadwal pemeliharaan aset...');

        // Cari pemeliharaan yang dijadwalkan dalam 3 hari ke depan (atau kurang)
        // Dan belum selesai
        $targetDate = Carbon::today()->addDays(3)->toDateString();

        $maintenances = AssetMaintenance::with('asset')
            ->whereIn('status', ['Scheduled', 'Pending'])
            ->whereDate('tanggal_jadwal', '<=', $targetDate)
            ->get();

        if ($maintenances->isEmpty()) {
            $this->info('Tidak ada jadwal pemeliharaan mendesak.');
            return;
        }

        $message = "🛠️ *PENGINGAT PEMELIHARAAN ASET* 🛠️\n\n";
        $message .= "Berikut adalah jadwal pemeliharaan aset yang perlu dilakukan segera (H-3):\n\n";

        foreach ($maintenances as $maint) {
            $assetName = $maint->asset ? $maint->asset->name : 'Unknown Asset';
            $tglJadwal = Carbon::parse($maint->tanggal_jadwal)->translatedFormat('d F Y');
            $message .= "• Aset: *{$assetName}*\n";
            $message .= "  Jenis: {$maint->jenis_pemeliharaan}\n";
            $message .= "  Jadwal: {$tglJadwal}\n\n";
        }

        $message .= "_Siapkan keperluan teknisi. Pesan ini dikirim otomatis oleh Sistem RAKSA._";

        $adminPhone = env('WA_ADMIN_PHONE', '');

        if (empty($adminPhone)) {
            $this->warn('Nomor WA_ADMIN_PHONE belum diatur di .env');
            Log::channel('whatsapp')->warning('CheckMaintenanceDueCommand: WA_ADMIN_PHONE belum diatur.');
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
