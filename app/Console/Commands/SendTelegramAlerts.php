<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTelegramAlerts extends Command
{
    protected $signature = 'telegram:alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily alerts to authorized Telegram chats for low stock and expiring calibrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN not set.');
            return;
        }

        $allowedChatsRaw = \App\Models\Setting::where('key', 'authorized_telegram_chats')->value('value');
        $allowedChats = $allowedChatsRaw ? explode(',', str_replace(' ', '', $allowedChatsRaw)) : [];

        if (empty($allowedChats)) {
            $this->info('No authorized telegram chats found. Skipping alerts.');
            return;
        }

        // 1. Check Low Stock Items
        $lowStockItems = \App\Models\Item::where('stok_sekarang', '<=', 5)
            ->where('stok_sekarang', '>', 0)
            ->get();

        $stockMessage = "";
        if ($lowStockItems->count() > 0) {
            $stockMessage .= "⚠️ *PERINGATAN STOK MENIPIS* ⚠️\n\n";
            $stockMessage .= "Berikut adalah daftar barang logistik yang stoknya menipis dan perlu segera dipesan:\n";
            foreach ($lowStockItems as $item) {
                $stockMessage .= "- {$item->nama_barang} (Sisa: *{$item->stok_sekarang}* {$item->satuan})\n";
            }
            $stockMessage .= "\nSilakan cek sistem E-Aset untuk detailnya.";
        }

        // 2. Check Calibration Expiry (30 days from now)
        $calibrationItems = \App\Models\Asset::where('status_aktif', true)
            ->whereNotNull('next_calibration')
            ->where('next_calibration', '<=', now()->addDays(30))
            ->where('next_calibration', '>=', now())
            ->get();

        $calibMessage = "";
        if ($calibrationItems->count() > 0) {
            $calibMessage .= "🔧 *PERINGATAN KALIBRASI ALAT* 🔧\n\n";
            $calibMessage .= "Berikut adalah alat kesehatan yang masa kalibrasinya akan segera habis (kurang dari 30 hari):\n";
            foreach ($calibrationItems as $asset) {
                $date = \Carbon\Carbon::parse($asset->next_calibration)->format('d-M-Y');
                $calibMessage .= "- {$asset->name} ({$asset->asset_code})\n  Expired: *{$date}*\n";
            }
            $calibMessage .= "\nMohon segera agendakan kalibrasi.";
        }

        if (empty($stockMessage) && empty($calibMessage)) {
            $this->info('No alerts to send today.');
            return;
        }

        // Combine messages or send separately. We will send separately for clarity.
        foreach ($allowedChats as $chatId) {
            if (!empty($stockMessage)) {
                $this->sendMessage($token, $chatId, $stockMessage);
            }
            
            if (!empty($calibMessage)) {
                $this->sendMessage($token, $chatId, $calibMessage);
            }
        }
        
        $this->info('Alerts sent successfully.');
    }

    private function sendMessage($token, $chatId, $text)
    {
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->post($url, [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
