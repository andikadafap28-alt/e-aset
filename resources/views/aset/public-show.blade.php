<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $asset->asset_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-indigo-100 min-h-screen">
    
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-xl relative pb-24">
        <!-- Header Image / Top Bar -->
        <div class="bg-indigo-600 h-32 flex flex-col items-center justify-center text-white px-6">
            <h1 class="text-xl font-bold tracking-tight text-center">Informasi Aset</h1>
            <p class="text-sm text-indigo-100 opacity-90 mt-1">Puskesmas Mantup</p>
        </div>

        <div class="px-6 -mt-10">
            <!-- Main Info Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $asset->name }}</h2>
                        <span class="inline-block px-2.5 py-1 mt-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ is_object($asset->category) ? $asset->category->nama_kategori : ($asset->getAttribute('category') ?: '-') }}
                        </span>
                    </div>
                    @if($asset->condition === 'Baik')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">Baik</span>
                    @elseif($asset->condition === 'Rusak Ringan')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">Rusak Ringan</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700">Rusak Berat</span>
                    @endif
                </div>

                <div class="space-y-4 mt-6">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Kode Barang</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $asset->asset_code }}</p>
                    </div>
                    <div x-data="{ expanded: false }">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Lokasi</p>
                        <p class="text-sm text-slate-700">{{ $asset->location }}</p>
                        
                        @if($asset->latitude && $asset->longitude)
                        <button @click="expanded = !expanded" class="text-xs font-medium text-indigo-600 mt-2 flex items-center gap-1">
                            <span x-show="!expanded">Lihat Peta</span>
                            <span x-show="expanded">Sembunyikan Peta</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="expanded" class="mt-3 h-48 rounded-lg border border-slate-200 overflow-hidden" style="display: none;">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Secret / Authenticated Details -->
            @if($isAuthenticated)
            <div class="mt-6">
                <div class="flex items-center gap-2 mb-3 px-1">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Rincian Internal</h3>
                </div>
                
                <div class="bg-indigo-50/50 rounded-2xl shadow-sm border border-indigo-100 p-5 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-1">Tahun Pengadaan</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $asset->year_purchased }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-1">Harga Perolehan</p>
                        <p class="text-sm font-semibold text-slate-800">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-1">Penanggung Jawab</p>
                        <p class="text-sm text-slate-700">{{ $asset->penanggung_jawab ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-1">Tanggal Kalibrasi</p>
                        <p class="text-sm text-slate-700">{{ $asset->last_calibration ? \Carbon\Carbon::parse($asset->last_calibration)->format('d M Y') : '-' }}</p>
                    </div>
                    @if($asset->document_link)
                    <div>
                        <a href="{{ $asset->document_link }}" target="_blank" class="text-sm font-medium text-indigo-600 flex items-center gap-2 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Dokumen Pengadaan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Password Modal Trigger -->
        @if(!$isAuthenticated)
        <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-4 bg-white/80 backdrop-blur-md border-t border-slate-200" x-data="{ open: {{ $errors->has('password') ? 'true' : 'false' }} }">
            <button @click="open = true" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-3 rounded-xl shadow-lg transition-colors flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Lihat Detail Spesifik
            </button>

            <!-- Modal -->
            <div x-show="open" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center bg-slate-900/40 backdrop-blur-sm" style="display: none;" x-transition.opacity>
                <div @click.away="open = false" class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-2xl shadow-2xl p-6 transform transition-all" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95 opacity-0" x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:scale-100 opacity-100">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-slate-800">Akses Detail Internal</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('public.verify', $asset->asset_code) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Masukkan Kata Sandi</label>
                            <input type="password" name="password" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="••••••••" required>
                            @error('password')
                                <p class="text-rose-500 text-xs mt-2 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-xl shadow-md transition-colors">
                            Buka Gembok
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-4 bg-emerald-50 border-t border-emerald-100 flex items-center justify-center gap-2 text-emerald-700 text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Akses Detail Terbuka
        </div>
        @endif
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        @if($asset->latitude && $asset->longitude)
        document.addEventListener('alpine:init', () => {
            // Kita inisiasi peta saat user membuka dropdown "Lihat Peta" agar Leaflet merender dengan benar
            let mapInitialized = false;
            
            // Menggunakan MutationObserver untuk mengecek saat elemen map muncul
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    const target = mutation.target;
                    if(target.style.display !== 'none' && !mapInitialized) {
                        const lat = {{ $asset->latitude }};
                        const lng = {{ $asset->longitude }};
                        
                        const map = L.map('map').setView([lat, lng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);
                        
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup('<b>{{ $asset->name }}</b><br>{{ $asset->location }}');
                            
                        mapInitialized = true;
                    }
                });
            });
            
            const mapContainer = document.querySelector('[x-show="expanded"]');
            if(mapContainer) {
                observer.observe(mapContainer, { attributes: true, attributeFilter: ['style'] });
            }
        });
        @endif
        
        // Tampilkan modal jika ada error sandi
        @if($errors->has('password'))
            document.addEventListener('alpine:initialized', () => {
                // Alpine sudah diinisialisasi, kita trigger buka modal manual
                // Membutuhkan cara untuk mengubah 'open' di dalam scope x-data.
                // Lebih mudah menggunakan auto-open jika ada error melalui blade
            });
        @endif
    </script>
</body>
</html>
