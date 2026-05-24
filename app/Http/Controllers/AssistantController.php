<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistantController extends Controller
{
    public function waChats()
    {
        $chats = \App\Models\BotConversation::where('platform', 'whatsapp')->orderBy('created_at', 'asc')->get();
        
        $waPhoneId = env('META_PHONE_NUMBER_ID');
        $waToken = env('META_WA_TOKEN');
        $webhookStatus = 'disconnected';
        
        if ($waPhoneId && $waToken) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->withToken($waToken)
                    ->timeout(5)
                    ->get("https://graph.facebook.com/v19.0/{$waPhoneId}");
                    
                if ($response->successful()) {
                    $webhookStatus = 'connected';
                }
            } catch (\Exception $e) {
                Log::error('Meta API Check Error: ' . $e->getMessage());
            }
        }

        $platform = 'WhatsApp';
        return view('asisten.index', compact('chats', 'webhookStatus', 'platform'));
    }

    public function teleChats()
    {
        $chats = \App\Models\BotConversation::where('platform', 'telegram')->orderBy('created_at', 'asc')->get();
        
        $teleToken = env('TELEGRAM_BOT_TOKEN');
        $webhookStatus = 'disconnected';
        
        if ($teleToken) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(5)
                    ->get("https://api.telegram.org/bot{$teleToken}/getMe");
                    
                if ($response->successful()) {
                    $webhookStatus = 'connected';
                }
            } catch (\Exception $e) {
                Log::error('Telegram API Check Error: ' . $e->getMessage());
            }
        }

        $platform = 'Telegram';
        return view('asisten.index', compact('chats', 'webhookStatus', 'platform'));
    }
}
