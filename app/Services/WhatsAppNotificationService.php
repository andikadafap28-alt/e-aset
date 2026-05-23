<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->token = env('META_WA_TOKEN');
        $this->phoneId = env('META_PHONE_NUMBER_ID');
    }

    /**
     * Mengirim notifikasi teks standar ke nomor tujuan.
     * Nomor tujuan harus dimulai dengan kode negara (contoh: 62812345678)
     */
    public function sendTextMessage($to, $message)
    {
        if (empty($this->token) || empty($this->phoneId)) {
            Log::channel('whatsapp')->warning("WhatsAppNotificationService: Token atau Phone ID kosong. Notifikasi ke {$to} dibatalkan.");
            return false;
        }

        try {
            $url = "https://graph.facebook.com/v19.0/{$this->phoneId}/messages";

            $response = Http::withOptions(['verify' => false])
                ->withToken($this->token)
                ->timeout(20)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $message]
                ]);

            if ($response->successful()) {
                Log::channel('whatsapp')->info("Proactive Notification terkirim ke {$to}");
                return true;
            } else {
                Log::channel('whatsapp')->error("Meta WA Error (Proactive Notification): " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('FATAL Error pada WhatsAppNotificationService: ' . $e->getMessage());
            return false;
        }
    }
}
