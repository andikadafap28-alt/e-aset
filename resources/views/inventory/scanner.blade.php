@extends('layouts.app')

@section('head')
    <!-- Mencegah zoom pada mobile agar scanner stabil -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
@endsection

@section('header_title', 'QR Code Scanner')

@section('content')
<!-- Sembunyikan margin/padding berlebih pada mobile -->
<div class="max-w-md mx-auto -mt-4 sm:mt-6">
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full mb-4">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">Pindai Label Aset</h2>
        <p class="text-slate-500 mb-6 text-xs sm:text-sm">Arahkan kamera ke QR Code yang tertera pada stiker aset.</p>
        
        <!-- Frame scanner diperbesar untuk mobile -->
        <div id="reader" class="overflow-hidden rounded-xl border-2 border-emerald-400 mx-auto w-full" style="min-height: 300px;"></div>
        
        <div class="mt-6 text-xs sm:text-sm text-slate-500 bg-slate-50 p-3 rounded-lg border border-slate-100">
            <span class="font-semibold text-slate-700">Tips:</span> Gunakan kamera belakang untuk hasil terbaik. Izinkan akses kamera saat diminta browser.
        </div>
    </div>
</div>

<style>
    /* Styling tambahan untuk menyesuaikan tombol scanner library bawaan agar lebih modern */
    #reader button {
        background-color: #10b981 !important;
        color: white !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin: 5px !important;
    }
    #reader select {
        padding: 8px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        margin-bottom: 10px !important;
        max-width: 100% !important;
    }
    #reader__dashboard_section_csr span {
        color: #ef4444 !important;
        font-weight: bold;
    }
</style>
@endsection

@section('scripts')
<!-- Library Scanner -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Callback ketika QR Code terbaca
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear();
            window.location.href = decodedText;
        }

        function onScanFailure(error) {
            // Abaikan frame failure
        }

        // Konfigurasi UI Scanner, dioptimasi untuk mobile
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { 
                fps: 15, 
                qrbox: { width: 280, height: 280 },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true,
                videoConstraints: {
                    facingMode: "environment" // Paksa kamera belakang di mobile
                }
            },
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
@endsection
