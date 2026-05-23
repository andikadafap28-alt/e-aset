<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetLoan;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckLoanDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-loan-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek peminjaman aset yang jatuh tempo besok atau hari ini dan mengirim notifikasi WA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan peminjaman aset...');

        // Cari peminjaman yang statusnya dipinjam dan expected_return_date besok atau kurang dari besok
        $tomorrow = Carbon::tomorrow()->toDateString();
        $today = Carbon::today()->toDateString();

        $loans = AssetLoan::with('asset')
            ->where('status', 'Dipinjam')
            ->whereDate('expected_return_date', '<=', $tomorrow)
            ->get();

        if ($loans->isEmpty()) {
            $this->info('Tidak ada peminjaman yang mendekati jatuh tempo.');
            return;
        }

        $message = "🚨 *PENGINGAT PENGEMBALIAN ASET* 🚨\n\n";
        $message .= "Berikut adalah daftar aset yang jadwal pengembaliannya sudah dekat atau terlewat:\n\n";

        foreach ($loans as $loan) {
            $assetName = $loan->asset ? $loan->asset->name : 'Unknown Asset';
            $tglBalik = Carbon::parse($loan->expected_return_date)->translatedFormat('d F Y');
            $message .= "• Aset: *{$assetName}*\n";
            $message .= "  Peminjam: {$loan->borrower_name}\n";
            $message .= "  Jatuh Tempo: {$tglBalik}\n\n";
        }

        $message .= "_Harap segera menindaklanjuti. Pesan ini dikirim otomatis oleh Sistem RAKSA._";

        $adminPhone = env('WA_ADMIN_PHONE', '');

        if (empty($adminPhone)) {
            $this->warn('Nomor WA_ADMIN_PHONE belum diatur di .env');
            Log::channel('whatsapp')->warning('CheckLoanDueCommand: WA_ADMIN_PHONE belum diatur.');
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
