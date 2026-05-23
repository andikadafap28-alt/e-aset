<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistantController extends Controller
{
    public function index()
    {
        // Mengambil riwayat percakapan, diurutkan dari yang terlama ke terbaru agar enak dibaca seperti UI Chat
        $chats = \App\Models\BotConversation::orderBy('created_at', 'asc')->get();
        
        // Cek status koneksi Meta API secara dinamis
        $waPhoneId = env('META_PHONE_NUMBER_ID');
        $waToken = env('META_WA_TOKEN');
        
        $webhookStatus = 'disconnected';
        
        if ($waPhoneId && $waToken) {
            try {
                // Ping endpoint Meta Graph API
                $response = Http::withOptions(['verify' => false])
                    ->withToken($waToken)
                    ->timeout(5) // timeout singkat agar halaman tidak terlalu lama dimuat jika error
                    ->get("https://graph.facebook.com/v19.0/{$waPhoneId}");
                    
                if ($response->successful()) {
                    $webhookStatus = 'connected';
                }
            } catch (\Exception $e) {
                Log::error('Meta API Check Error: ' . $e->getMessage());
            }
        }

        return view('asisten.index', compact('chats', 'webhookStatus'));
    }
}
