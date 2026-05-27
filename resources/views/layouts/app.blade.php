<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAKSA - Puskesmas Mantup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .icon-fill { font-variation-settings: 'FILL' 1; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @yield('head')
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden flex min-h-screen">

    <div class="flex w-full h-screen overflow-hidden">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 transition-all duration-300 z-50">
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <span class="material-symbols-outlined text-white">health_and_safety</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-wide">RAKSA</h1>
                        <p class="text-[10px] font-medium text-blue-400 uppercase tracking-widest">Puskesmas Mantup</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8" x-data="{ persediaanOpen: {{ in_array(request()->segment(1), ['atk', 'kertas_cover', 'bahan_cetak', 'benda_pos', 'bahan_komputer', 'obat', 'bahan_lainnya', 'natura_pakan_lainnya', 'vaksin', 'obat_apbd', 'obat_apbn']) ? 'true' : 'false' }}, asetOpen: {{ request()->is('aset/*') || request()->is('aset') ? 'true' : 'false' }} }">
                
                <!-- Section 1: Dashboard -->
                <div>
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined {{ request()->is('dashboard') ? 'icon-fill' : '' }}">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Section 2: Manajemen Persediaan -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Manajemen Persediaan</p>
                    <button @click="persediaanOpen = !persediaanOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">inventory_2</span>
                            <span>Logistik</span>
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="persediaanOpen ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="persediaanOpen" x-collapse class="pl-11 pr-4 mt-1 space-y-1">
                        <a href="/atk/items" class="{{ request()->segment(1) == 'atk' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">ATK</a>
                        <a href="/kertas_cover/items" class="{{ request()->segment(1) == 'kertas_cover' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Kertas & Cover</a>
                        <a href="/bahan_cetak/items" class="{{ request()->segment(1) == 'bahan_cetak' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Bahan Cetak</a>
                        <a href="/benda_pos/items" class="{{ request()->segment(1) == 'benda_pos' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Benda Pos</a>
                        <a href="/bahan_komputer/items" class="{{ request()->segment(1) == 'bahan_komputer' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Bahan Komputer</a>
                        <a href="/obat/items" class="{{ request()->segment(1) == 'obat' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Obat</a>
                        <a href="/bahan_lainnya/items" class="{{ request()->segment(1) == 'bahan_lainnya' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Bahan Lainnya</a>
                        <a href="/natura_pakan_lainnya/items" class="{{ request()->segment(1) == 'natura_pakan_lainnya' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Natura & Pakan</a>
                        <a href="/vaksin/items" class="{{ request()->segment(1) == 'vaksin' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Vaksin</a>
                        <a href="/obat_apbd/items" class="{{ request()->segment(1) == 'obat_apbd' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Obat APBD</a>
                        <a href="/obat_apbn/items" class="{{ request()->segment(1) == 'obat_apbn' ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-400 hover:text-white transition-colors' }} block py-2 text-sm">Obat APBN</a>
                    </div>
                </div>

                <!-- Section 3: Manajemen Aset -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Manajemen Aset</p>
                    <button @click="asetOpen = !asetOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">category</span>
                            <span>Data Aset</span>
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="asetOpen ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="asetOpen" x-collapse class="space-y-1 mt-1">
                        <a href="{{ route('aset.data.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.data.items') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">dataset</span>
                            <span class="text-sm">Data Aset</span>
                        </a>
                        <a href="{{ route('aset.categories.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.categories.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">folder</span>
                            <span class="text-sm">Kategori Aset</span>
                        </a>
                        <a href="{{ route('aset.pengadaan.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pengadaan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                            <span class="text-sm">Pengadaan</span>
                        </a>
                        <a href="{{ route('aset.bantuan_sarpras.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.bantuan_sarpras.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">handshake</span>
                            <span class="text-sm">Bantuan Sarpras</span>
                        </a>
                        <a href="{{ route('aset.pemeliharaan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pemeliharaan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">build</span>
                            <span class="text-sm">Pemeliharaan</span>
                        </a>
                        <a href="{{ route('aset.monitoring.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.monitoring.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">monitor_heart</span>
                            <span class="text-sm">Monitoring</span>
                        </a>
                        <a href="{{ route('aset.pelabelan.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pelabelan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                            <span class="text-sm">Label QR Code</span>
                        </a>
                        <a href="{{ route('aset.mutasi.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.mutasi.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">swap_horiz</span>
                            <span class="text-sm">Mutasi</span>
                        </a>
                    </div>
                </div>

                <!-- Section 4: Laporan -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pelaporan</p>
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('laporan.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('laporan.*') ? 'icon-fill' : '' }}">analytics</span>
                        <span>Pusat Laporan</span>
                    </a>
                </div>

                <!-- Section 5: Asisten AI -->
                <div x-data="{ aiOpen: {{ request()->routeIs('asisten.*') ? 'true' : 'false' }} }">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Integrasi</p>
                    <button @click="aiOpen = !aiOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">smart_toy</span>
                            <span>Asisten AI</span>
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="aiOpen ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="aiOpen" x-collapse class="space-y-1 mt-1">
                        <a href="{{ route('asisten.wa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('asisten.wa') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-[20px] font-bold text-emerald-400">W</span>
                            <span class="text-sm">Asisten WhatsApp</span>
                        </a>
                        <a href="{{ route('asisten.tele') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('asisten.tele') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="text-[20px] font-bold text-sky-400">T</span>
                            <span class="text-sm">Asisten Telegram</span>
                        </a>
                    </div>
                    <a href="{{ route('settings.index') }}" class="mt-2 flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">settings</span>
                        <span class="text-sm">Pengaturan</span>
                    </a>
                </div>
            </nav>

            <!-- User Area -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3 px-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Admin') }}&background=14b8a6&color=fff" class="w-10 h-10 rounded-full border-2 border-slate-700">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()?->name ?? 'Administrator' }}</p>
                        <p class="text-xs text-slate-400 truncate">Puskesmas Mantup</p>
                    </div>
                    <form method="POST" action="{{ url('/logout') }}" class="inline">
                        @csrf
                        <button class="text-slate-400 hover:text-red-400 transition-colors p-1" title="Logout">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative">
            
            <!-- Top Header -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-10 sticky top-0 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-slate-800">@yield('header_title', 'RAKSA')</h2>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-full items-center gap-2 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide">Sistem Aktif</span>
                    </div>
                    
                    <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                    
                    <div class="flex items-center gap-3 pl-6 border-l border-slate-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-slate-700">{{ auth()->user()?->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-slate-500 font-medium">Healthcare Admin</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Floating Print Cart -->
    <div x-data="printCart()" x-init="init()" class="fixed bottom-6 right-6 z-50">
        <!-- Floating Button -->
        <button @click="open = !open" class="relative bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg transition-transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span x-show="count > 0" x-text="count" class="absolute -top-2 -right-2 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-full border-2 border-white shadow-sm" style="display: none;"></span>
        </button>

        <!-- Cart Panel -->
        <div x-show="open" @click.away="open = false" x-transition.opacity class="absolute bottom-full right-0 mb-4 w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-96" style="display: none;">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Antrean Cetak
                </h3>
                <span x-text="count + ' Aset'" class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded"></span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-2 min-h-[100px]">
                <template x-if="count === 0">
                    <div class="text-center py-6 text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm">Keranjang kosong.</p>
                    </div>
                </template>
                <template x-for="asset in assets" :key="asset.id">
                    <div class="flex justify-between items-start p-2 hover:bg-slate-50 rounded-lg group transition-colors">
                        <div class="min-w-0 pr-2">
                            <p class="text-sm font-semibold text-slate-700 truncate" x-text="asset.asset_code"></p>
                            <p class="text-xs text-slate-500 truncate" x-text="asset.name"></p>
                        </div>
                        <button @click="remove(asset.id)" class="text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="p-3 bg-slate-50 border-t border-slate-100 space-y-2">
                <a :href="count > 0 ? '{{ route('aset.print-queue.print') }}' : '#'" :target="count > 0 ? '_blank' : ''" :class="count > 0 ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-slate-300 text-slate-500 cursor-not-allowed'" class="w-full block text-center py-2 rounded-lg text-sm font-medium transition-colors" @click="open = false">
                    Cetak Semua Label
                </a>
                <button x-show="count > 0" @click="clear()" class="w-full py-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                    Kosongkan Antrean
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('printCart', () => ({
                open: false,
                count: 0,
                assets: [],
                
                init() {
                    this.fetchData();
                    window.addEventListener('focus', () => this.fetchData());
                },
                
                fetchData() {
                    fetch('{{ route('aset.print-queue.data') }}')
                        .then(res => res.json())
                        .then(data => {
                            this.count = data.count;
                            this.assets = data.assets;
                        })
                        .catch(err => console.error('Error fetching print queue:', err));
                },

                remove(id) {
                    fetch(`{{ url('aset/print-queue/remove') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) this.fetchData();
                    });
                },

                clear() {
                    if(!confirm('Yakin ingin mengosongkan antrean cetak?')) return;
                    fetch('{{ route('aset.print-queue.clear') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.fetchData();
                            this.open = false;
                        }
                    });
                }
            }))
        })
    </script>

    @yield('scripts')
</body>
</html>

