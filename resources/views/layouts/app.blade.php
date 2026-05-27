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
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

    <div class="flex h-screen overflow-hidden">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 transition-all duration-300 z-20">
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                        <span class="material-symbols-outlined text-white">health_and_safety</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-wide">RAKSA</h1>
                        <p class="text-[10px] font-medium text-teal-400 uppercase tracking-widest">Puskesmas Mantup</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8" x-data="{ persediaanOpen: false, asetOpen: true }">
                
                <!-- Section 1: Dashboard -->
                <div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-teal-500 text-white shadow-md shadow-teal-500/20 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'icon-fill' : '' }}">dashboard</span>
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
                        <a href="{{ url('/inventaris') }}" class="block py-2 text-sm text-slate-400 hover:text-white transition-colors">Semua Kategori</a>
                        <a href="{{ url('/inventaris?kategori=atk') }}" class="block py-2 text-sm text-slate-400 hover:text-white transition-colors">ATK & Kertas</a>
                        <a href="{{ url('/inventaris?kategori=obat') }}" class="block py-2 text-sm text-slate-400 hover:text-white transition-colors">Obat & Vaksin</a>
                    </div>
                </div>

                <!-- Section 3: Manajemen Aset -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Manajemen Aset</p>
                    <div class="space-y-1">
                        <a href="{{ route('aset.data.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.data.items') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">dataset</span>
                            <span class="text-sm">Data Aset</span>
                        </a>
                        <a href="{{ route('aset.pengadaan.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pengadaan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                            <span class="text-sm">Pengadaan</span>
                        </a>
                        <a href="{{ route('aset.pemeliharaan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pemeliharaan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">build</span>
                            <span class="text-sm">Pemeliharaan</span>
                        </a>
                        <!-- If monitoring doesn't exist, we safely omit it or use correct route -->
                        <a href="{{ route('aset.pelabelan.items') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('aset.pelabelan.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                            <span class="text-sm">Label QR Code</span>
                        </a>
                    </div>
                </div>

                <!-- Section 4: Laporan -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pelaporan</p>
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('laporan.*') ? 'bg-teal-500 text-white shadow-md shadow-teal-500/20 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('laporan.*') ? 'icon-fill' : '' }}">analytics</span>
                        <span>Pusat Laporan</span>
                    </a>
                </div>

                <!-- Section 5: Asisten AI -->
                <div>
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Integrasi</p>
                    <a href="{{ route('asisten.wa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('asisten.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">smart_toy</span>
                        <span class="text-sm">Asisten AI</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
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
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->role ?? 'Admin Utama' }}</p>
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
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
            
            <!-- Top Header -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-10 sticky top-0">
                <div class="flex items-center gap-4">
                    <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-full flex items-center gap-2 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide">Sistem Aktif & Real-time</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-6">
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

    @yield('scripts')
</body>
</html>
