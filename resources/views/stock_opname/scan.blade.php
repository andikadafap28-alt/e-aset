@extends('layouts.app')

@section('header_title', 'Scanner Stock Opname')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Pemindaian Aset</h2>
            <p class="text-slate-500 text-sm mt-1">
                Lokasi Audit: <span class="font-bold text-indigo-600">{{ $stockOpname->location ?? 'Semua Ruangan' }}</span>
            </p>
        </div>
        <div>
            <form action="{{ route('stock-opname.finish', $stockOpname->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengakhiri sesi Stock Opname ini? Semua aset yang tidak dipindai akan ditandai sebagai Missing.');">
                @csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Akhiri Sesi
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Area Scanner -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div id="reader" width="100%" class="rounded-2xl overflow-hidden"></div>
                <div class="mt-4 flex justify-between items-center text-sm text-slate-500">
                    <span>Gunakan kamera belakang</span>
                    <span id="scan-status" class="text-emerald-500 font-bold hidden flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Pemindai Aktif
                    </span>
                </div>
            </div>

            <!-- Manual Input Fallback -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 mb-3">Kamera Bermasalah? Input Manual</h3>
                <form id="manualForm" class="flex gap-3">
                    <input type="text" id="manualInput" placeholder="Masukkan Kode Aset" class="flex-1 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-700">Submit</button>
                </form>
            </div>
        </div>

        <!-- Log Aktivitas Scan (Realtime Feedback) -->
        <div class="space-y-6">
            <div class="bg-indigo-600 text-white rounded-2xl p-6 shadow-md text-center">
                <h3 class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-2">Total Terpindai</h3>
                <div class="text-5xl font-black" id="counterDisplay">{{ $scannedCount }}</div>
                <p class="text-indigo-200 text-xs mt-2">Aset dalam sesi ini</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 h-[400px] flex flex-col">
                <h3 class="text-sm font-bold text-slate-800 mb-3 border-b border-slate-100 pb-2">Log Pemindaian</h3>
                <div id="scanLogs" class="flex-1 overflow-y-auto space-y-3">
                    <!-- Logs will be injected here via JS -->
                    <p class="text-xs text-slate-400 text-center italic mt-10">Belum ada aktivitas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library Scanner -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';
        const recordUrl = '{{ route("stock-opname.record", $stockOpname->id) }}';
        const scanLogsContainer = document.getElementById('scanLogs');
        const counterDisplay = document.getElementById('counterDisplay');
        const manualForm = document.getElementById('manualForm');
        const manualInput = document.getElementById('manualInput');

        let isProcessing = false;

        // Audio Beep untuk feedback
        const beepSound = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU'+Array(100).join('1234567890')); // Mock base64 beep

        function playBeep() {
            try { beepSound.play(); } catch(e) {}
        }

        function prependLog(message, isSuccess = true, assetName = null, status = null) {
            // Hilangkan tulisan "belum ada aktivitas"
            if(scanLogsContainer.innerHTML.includes('Belum ada aktivitas')) {
                scanLogsContainer.innerHTML = '';
            }

            let bgClass = isSuccess ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-rose-50 border-rose-100 text-rose-800';
            let icon = isSuccess ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            
            let statusBadge = '';
            if(status === 'Misplaced') {
                statusBadge = '<span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-800">Salah Tempat</span>';
            }

            let logHtml = `
                <div class="p-3 border rounded-xl text-xs flex items-start gap-2 ${bgClass} animate-pulse">
                    <div class="mt-0.5">${icon}</div>
                    <div>
                        <p class="font-bold">${message} ${statusBadge}</p>
                        ${assetName ? `<p class="text-[10px] opacity-80 mt-0.5">${assetName}</p>` : ''}
                    </div>
                </div>
            `;
            
            scanLogsContainer.insertAdjacentHTML('afterbegin', logHtml);
            
            // Remove pulse after 1s
            setTimeout(() => {
                scanLogsContainer.firstElementChild.classList.remove('animate-pulse');
            }, 1000);
        }

        function processScan(qrCodeMessage) {
            if (isProcessing) return;
            isProcessing = true;

            // Di e-Aset, QR code yang di-generate adalah URL, misalnya: http://localhost:8000/item/02.06.02.01.03
            // Kita perlu mengekstrak kode aset dari URL tersebut
            let assetCode = qrCodeMessage;
            if (assetCode.includes('/item/')) {
                assetCode = assetCode.split('/item/')[1];
            }

            playBeep();

            fetch(recordUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ asset_code: assetCode })
            })
            .then(response => response.json().then(data => ({status: response.status, body: data})))
            .then(result => {
                if (result.status === 200 || result.status === 201) {
                    prependLog(`Scan Berhasil: ${result.body.data.asset_code}`, true, result.body.data.name, result.body.data.status);
                    counterDisplay.innerText = result.body.data.scanned_count;
                } else {
                    prependLog(result.body.message || 'Gagal merekam aset', false);
                }
                
                // Jeda agar tidak double scan terlalu cepat
                setTimeout(() => { isProcessing = false; }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                prependLog('Terjadi kesalahan jaringan', false);
                setTimeout(() => { isProcessing = false; }, 1500);
            });
        }

        // Setup html5-qrcode
        function onScanSuccess(decodedText, decodedResult) {
            processScan(decodedText);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true
            }, 
            false
        );
        
        html5QrcodeScanner.render(onScanSuccess);

        // Manual Submit
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let val = manualInput.value.trim();
            if(val) {
                processScan(val);
                manualInput.value = '';
            }
        });
    });
</script>
@endsection
