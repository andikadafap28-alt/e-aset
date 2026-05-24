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

                // Call ChatbotService
                $botService = new \App\Services\ChatbotService();
                $botReply = $botService->processMessage($chatId, $textMessage, 'telegram');

                BotConversation::create([
                    'phone_number' => $chatId,
                    'sender' => 'bot',
                    'message' => $botReply,
                    'platform' => 'telegram'
                ]);

                $this->sendTelegramMessage($chatId, $botReply);
            }
        } catch (\Throwable $th) {
            Log::channel('telegram')->error('FATAL ERROR in Handle: ' . $th->getMessage());
        }

        return response('OK', 200);
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
