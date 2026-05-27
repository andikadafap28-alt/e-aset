@extends('layouts.app')

@section('header_title', 'Asisten AI ' . $platform)

@section('content')
<!-- Container Utama (Glassmorphism Dark-Blue) -->
<div class="h-[80vh] flex flex-col bg-slate-900/40 backdrop-blur-lg rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
    
    <!-- Header Chat -->
    <div class="px-6 py-4 bg-slate-800/60 border-b border-slate-700/50 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-teal-500/20 rounded-full flex items-center justify-center border border-teal-500/30">
                <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">RAKSA AI Assistant</h2>
                <p class="text-xs text-indigo-300">Pusat Layanan Informasi Aset</p>
            </div>
        </div>
        
        <!-- Status Webhook Dinamis -->
        @if(isset($webhookStatus) && $webhookStatus === 'connected')
            <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-semibold tracking-wide">Online (Webhook Active)</span>
            </div>
        @else
            <div class="flex items-center gap-2 px-3 py-1.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-full shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                <span class="text-xs font-semibold tracking-wide">Offline (Terputus)</span>
            </div>
        @endif
    </div>

    @if($platform === 'Telegram')
    <div class="px-6 py-3 bg-slate-800/80 border-b border-slate-700/50 flex items-center justify-between">
        <p class="text-xs text-slate-400">Pastikan Token Bot API sudah ditambahkan di .env. Lalu klik tombol di samping untuk mengaktifkan webhook.</p>
        <button onclick="fetch('/webhook/telegram/setup').then(r=>r.json()).then(d=>alert(JSON.stringify(d)))" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
            Setup Webhook Telegram
        </button>
    </div>
    @endif

    <!-- Area Chat -->
    <div class="flex-1 overflow-y-auto p-6 space-y-5" id="chat-container">
        <!-- Welcoming Message (Static) -->
        <div class="flex flex-col mb-6">
            <div class="bg-slate-800/60 text-slate-400 text-xs py-1 px-4 rounded-full mx-auto font-medium border border-slate-700/50">Percakapan Dimulai</div>
        </div>

        @forelse($chats as $chat)
            @if($chat->sender === 'user')
                <!-- Bubble User (Kanan) -->
                <div class="flex flex-col items-end w-full">
                    <div class="flex items-end gap-2 max-w-[80%]">
                        <div class="bg-emerald-600/80 backdrop-blur-sm text-white px-4 py-3 rounded-2xl rounded-br-sm shadow-md border border-emerald-500/30">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $chat->message }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 mt-1.5 text-[10px] text-slate-400">
                        <span>{{ \Carbon\Carbon::parse($chat->created_at)->format('H:i') }}</span>
                        @if($chat->phone_number)
                            <span class="mx-1">•</span>
                            <span>{{ $chat->phone_number }}</span>
                        @endif
                    </div>
                </div>
            @else
                <!-- Bubble Bot (Kiri) -->
                <div class="flex flex-col items-start w-full">
                    <div class="flex items-end gap-2 max-w-[85%]">
                        <div class="w-8 h-8 rounded-full bg-teal-500/20 flex items-center justify-center shrink-0 mb-1 border border-teal-500/30">
                            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="bg-gray-800/80 backdrop-blur-sm text-slate-200 px-4 py-3 rounded-2xl rounded-bl-sm border border-slate-700/50 shadow-md">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $chat->message }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 mt-1.5 text-[10px] text-slate-500 ml-10">
                        <span>{{ \Carbon\Carbon::parse($chat->created_at)->format('H:i') }}</span>
                    </div>
                </div>
            @endif
        @empty
            <!-- Kondisi Kosong -->
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                <svg class="w-16 h-16 mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <p class="text-sm font-medium">Belum ada riwayat percakapan.</p>
                <p class="text-xs mt-1 text-slate-500">Kirim pesan {{ $platform }} ke bot untuk memulai.</p>
            </div>
        @endforelse
    </div>

    <!-- Footer / Fake Input -->
    <div class="px-6 py-4 bg-slate-800/80 border-t border-slate-700/50 shrink-0">
        <div class="flex items-center gap-3 bg-slate-900/50 border border-slate-700/50 rounded-full px-4 py-2.5">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <input type="text" readonly disabled class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-slate-400 cursor-not-allowed placeholder-slate-500" placeholder="Gunakan aplikasi {{ $platform }} di HP Anda untuk berinteraksi dengan AI...">
            <div class="w-8 h-8 rounded-full bg-slate-700/50 flex items-center justify-center cursor-not-allowed">
                <svg class="w-4 h-4 text-slate-500 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-Scroll ke bawah saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        var chatContainer = document.getElementById('chat-container');
        if(chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection

