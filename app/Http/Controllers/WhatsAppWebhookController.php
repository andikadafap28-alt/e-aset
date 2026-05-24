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
            Log::channel('whatsapp')->info('Incoming WA Data: ' . json_encode($data));

            if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
                $messageData = $data['entry'][0]['changes'][0]['value']['messages'][0];
                $phone = $messageData['from'];
                
                if ($messageData['type'] === 'text') {
                    $textMessage = $messageData['text']['body'];

                    BotConversation::create([
                        'phone_number' => $phone,
                        'sender' => 'user',
                        'message' => $textMessage,
                        'platform' => 'whatsapp'
                    ]);

                    // Call ChatbotService
                    $botService = new \App\Services\ChatbotService();
                    $botReply = $botService->processMessage($phone, $textMessage, 'whatsapp');

                    BotConversation::create([
                        'phone_number' => $phone,
                        'sender' => 'bot',
                        'message' => $botReply,
                        'platform' => 'whatsapp'
                    ]);

                    $this->sendWhatsAppMessage($phone, $botReply);
                }
            }
        } catch (\Throwable $th) {
            Log::channel('whatsapp')->error('FATAL ERROR in Handle: ' . $th->getMessage());
        }

        return response('EVENT_RECEIVED', 200);
    }

    private function sendWhatsAppMessage($phone, $text)
    {
        $waToken = env('META_WA_TOKEN');
        $waPhoneId = env('META_PHONE_NUMBER_ID');
        if (!$waToken || !$waPhoneId) return;

        $waUrl = "https://graph.facebook.com/v19.0/{$waPhoneId}/messages";

        $waResponse = Http::withOptions(['verify' => false])
            ->withToken($waToken)
            ->timeout(20)
            ->post($waUrl, [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => ['body' => $text]
            ]);

        if (!$waResponse->successful()) {
            Log::channel('whatsapp')->error("Meta WA Error: " . $waResponse->body());
        }
    }
}
