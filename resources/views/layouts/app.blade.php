<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('header_title', 'RAKSA - Healthcare Asset Management')</title>
    
    <!-- Tailwind CSS (Stitch Configuration) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Alpine JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f7f9fb;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .mesh-gradient {
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(56, 189, 248, 0.05) 0px, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(224, 227, 229, 0.5);
            box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.04);
        }
        .bento-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bento-card:hover {
            transform: translateY(-4px);
            box-shadow: 0px 20px 40px rgba(15, 23, 42, 0.08);
        }
    </style>
    
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class", 
        theme: {
            extend: {
                colors: {
                    "surface-variant": "#e0e3e5", "error-container": "#ffdad6", "tertiary-fixed": "#dde3eb", "on-tertiary-fixed-variant": "#41474e", "secondary-fixed-dim": "#bec6e0", "on-surface": "#191c1e", "surface-container-low": "#f2f4f6", "on-error-container": "#93000a", "surface-container": "#eceef0", "primary-fixed": "#6ffbbe", "primary-container": "#10b981", "on-tertiary-container": "#333a40", "surface-dim": "#d8dadc", "on-tertiary": "#ffffff", "tertiary-container": "#9da3ab", error: "#ba1a1a", "on-secondary": "#ffffff", "on-primary": "#ffffff", "on-primary-fixed-variant": "#005236", "on-error": "#ffffff", "primary-fixed-dim": "#4edea3", "surface-container-lowest": "#ffffff", "inverse-primary": "#4edea3", "on-secondary-container": "#5c647a", primary: "#006c49", outline: "#6c7a71", "surface-tint": "#006c49", "on-primary-fixed": "#002113", "outline-variant": "#bbcabf", "on-tertiary-fixed": "#161c22", surface: "#f7f9fb", tertiary: "#595f66", "on-secondary-fixed-variant": "#3f465c", "on-surface-variant": "#3c4a42", "inverse-on-surface": "#eff1f3", "secondary-fixed": "#dae2fd", "surface-container-high": "#e6e8ea", "inverse-surface": "#2d3133", "on-primary-container": "#00422b", "surface-container-highest": "#e0e3e5", "surface-bright": "#f7f9fb", "on-background": "#191c1e", "secondary-container": "#dae2fd", background: "#f7f9fb", "tertiary-fixed-dim": "#c1c7cf", secondary: "#565e74", "on-secondary-fixed": "#131b2e"
                }, 
                borderRadius: {DEFAULT: "1rem", lg: "2rem", xl: "3rem", full: "9999px"}, 
                spacing: {unit: "4px", sm: "1rem", gutter: "1.5rem", lg: "2.5rem", "margin-desktop": "2.5rem", md: "1.5rem", xs: "0.5rem", "margin-mobile": "1rem", xl: "4rem"}, 
                fontFamily: {
                    "label-sm": ["Plus Jakarta Sans"], "headline-lg-mobile": ["Plus Jakarta Sans"], "headline-md": ["Plus Jakarta Sans"], "headline-lg": ["Plus Jakarta Sans"], "display-lg": ["Plus Jakarta Sans"], "body-sm": ["Plus Jakarta Sans"], "label-md": ["Plus Jakarta Sans"], "body-lg": ["Plus Jakarta Sans"], "headline-sm": ["Plus Jakarta Sans"], "body-md": ["Plus Jakarta Sans"], headline: ["Plus Jakarta Sans"], display: ["Plus Jakarta Sans"], body: ["Plus Jakarta Sans"], label: ["Plus Jakarta Sans"]
                }, 
                fontSize: {
                    "label-sm": ["12px", {lineHeight: "1", fontWeight: "600"}], "headline-lg-mobile": ["24px", {lineHeight: "1.3", fontWeight: "700"}], "headline-md": ["24px", {lineHeight: "1.4", fontWeight: "600"}], "headline-lg": ["32px", {lineHeight: "1.25", letterSpacing: "-0.01em", fontWeight: "700"}], "display-lg": ["48px", {lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700"}], "body-sm": ["14px", {lineHeight: "1.5", fontWeight: "400"}], "label-md": ["14px", {lineHeight: "1", letterSpacing: "0.02em", fontWeight: "600"}], "body-lg": ["18px", {lineHeight: "1.6", fontWeight: "400"}], "headline-sm": ["20px", {lineHeight: "1.4", fontWeight: "600"}], "body-md": ["16px", {lineHeight: "1.6", fontWeight: "400"}]
                }
            }
        }
    };
    </script>
    
    @yield('head')
</head>

<body class="bg-background text-on-surface">

<!-- Sidebar Integration -->
<aside class="fixed left-0 top-0 h-screen w-[280px] bg-inverse-surface dark:bg-surface-container-lowest flex flex-col py-lg shadow-xl border-r border-outline-variant/20 z-50 overflow-y-auto overflow-x-hidden">
    <div class="px-gutter mb-xl flex items-center gap-sm">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">medical_services</span>
        </div>
        <div>
            <h1 class="font-headline-sm text-headline-sm font-black text-primary-fixed tracking-tight">RAKSA</h1>
            <p class="font-label-sm text-label-sm text-surface-variant/70">Puskesmas Mantup</p>
        </div>
    </div>

    <nav class="flex-1 px-sm">
        <ul class="space-y-xs">
            <!-- Dashboard Active -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-md px-md py-sm {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary-container' : 'text-surface-variant hover:text-surface hover:bg-on-surface-variant/10' }} rounded-lg transition-all active:scale-95 group">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('dashboard') ? '1' : '0' }};">dashboard</span>
                    <span class="font-label-md text-label-md group-hover:translate-x-1 duration-200">Dashboard</span>
                </a>
            </li>

            <!-- Manajemen Persediaan (Logistik) -->
            <li x-data="{ persediaanOpen: {{ in_array(request()->segment(1), ['atk', 'kertas_cover', 'bahan_cetak', 'benda_pos', 'bahan_komputer', 'obat', 'bahan_lainnya', 'natura_pakan_lainnya', 'vaksin', 'obat_apbd', 'obat_apbn']) ? 'true' : 'false' }} }">
                <button @click="persediaanOpen = !persediaanOpen" class="w-full flex items-center justify-between gap-md px-md py-sm text-surface-variant hover:text-surface hover:bg-on-surface-variant/10 rounded-lg transition-all group">
                    <div class="flex items-center gap-md">
                        <span class="material-symbols-outlined">inventory_2</span>
                        <span class="font-label-md text-label-md group-hover:translate-x-1 duration-200">Manajemen Persediaan</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="{'rotate-180': persediaanOpen}">expand_more</span>
                </button>
                <ul x-show="persediaanOpen" class="space-y-1 mt-1 ml-4 pl-4 border-l border-outline-variant/20">
                    @php
                    $persediaanMenus = [
                        'atk' => 'ATK',
                        'kertas_cover' => 'Kertas & Cover',
                        'bahan_cetak' => 'Bahan Cetak',
                        'benda_pos' => 'Benda Pos',
                        'bahan_komputer' => 'Bahan Komputer',
                        'obat' => 'Obat',
                        'bahan_lainnya' => 'Bahan Lainnya',
                        'natura_pakan_lainnya' => 'Natura & Pakan',
                        'vaksin' => 'Vaksin',
                        'obat_apbd' => 'Obat APBD',
                        'obat_apbn' => 'Obat APBN',
                    ];
                    @endphp
                    @foreach($persediaanMenus as $slug => $label)
                    <li>
                        <a href="/{{ $slug }}/items" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->segment(1) == $slug ? 'bg-primary-container/20 text-primary-fixed font-bold' : 'text-surface-variant/80 hover:text-surface hover:bg-on-surface-variant/10 font-label-md' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->segment(1) == $slug ? 'bg-primary-fixed' : 'bg-surface-variant/50' }}"></span>
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </li>

            <!-- Manajemen Aset -->
            <li x-data="{ asetOpen: {{ request()->routeIs('aset.*') || request()->routeIs('asset-maintenance.*') || request()->routeIs('asset-disposals.*') || request()->routeIs('print-labels.index') || request()->routeIs('public.asset.show') ? 'true' : 'false' }} }">
                <button @click="asetOpen = !asetOpen" class="w-full flex items-center justify-between gap-md px-md py-sm text-surface-variant hover:text-surface hover:bg-on-surface-variant/10 rounded-lg transition-all group">
                    <div class="flex items-center gap-md">
                        <span class="material-symbols-outlined">precision_manufacturing</span>
                        <span class="font-label-md text-label-md group-hover:translate-x-1 duration-200">Manajemen Aset</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="{'rotate-180': asetOpen}">expand_more</span>
                </button>
                <ul x-show="asetOpen" class="space-y-1 mt-1 ml-4 pl-4 border-l border-outline-variant/20">
                    <li>
                        <a href="{{ route('aset.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('aset.index') ? 'bg-primary-container/20 text-primary-fixed font-bold' : 'text-surface-variant/80 hover:text-surface hover:bg-on-surface-variant/10 font-label-md' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('aset.index') ? 'bg-primary-fixed' : 'bg-surface-variant/50' }}"></span>
                            Data Aset & Pengadaan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('asset-maintenance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('asset-maintenance.*') ? 'bg-primary-container/20 text-primary-fixed font-bold' : 'text-surface-variant/80 hover:text-surface hover:bg-on-surface-variant/10 font-label-md' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('asset-maintenance.*') ? 'bg-primary-fixed' : 'bg-surface-variant/50' }}"></span>
                            Pemeliharaan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('asset-disposals.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('asset-disposals.*') ? 'bg-primary-container/20 text-primary-fixed font-bold' : 'text-surface-variant/80 hover:text-surface hover:bg-on-surface-variant/10 font-label-md' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('asset-disposals.*') ? 'bg-primary-fixed' : 'bg-surface-variant/50' }}"></span>
                            Penghapusan Aset
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('print-labels.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('print-labels.index') ? 'bg-primary-container/20 text-primary-fixed font-bold' : 'text-surface-variant/80 hover:text-surface hover:bg-on-surface-variant/10 font-label-md' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('print-labels.index') ? 'bg-primary-fixed' : 'bg-surface-variant/50' }}"></span>
                            Label QR Code
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Pusat Laporan -->
            <li>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-md px-md py-sm {{ request()->routeIs('reports.*') ? 'bg-primary-container text-on-primary-container' : 'text-surface-variant hover:text-surface hover:bg-on-surface-variant/10' }} rounded-lg transition-all group">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('reports.*') ? '1' : '0' }};">analytics</span>
                    <span class="font-label-md text-label-md group-hover:translate-x-1 duration-200">Pusat Laporan</span>
                </a>
            </li>

            <!-- Pengaturan -->
            <li>
                <a href="#" class="flex items-center gap-md px-md py-sm text-surface-variant hover:text-surface hover:bg-on-surface-variant/10 rounded-lg transition-all group">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="font-label-md text-label-md group-hover:translate-x-1 duration-200">Pengaturan</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Main Content Area -->
<main class="ml-[280px] min-h-screen pb-10">
    <!-- TopAppBar Integration -->
    <header class="sticky top-0 z-40 flex justify-between items-center px-gutter h-16 bg-surface/70 backdrop-blur-md border-b border-outline-variant/50 shadow-sm">
        <div class="flex items-center gap-md w-1/3">
            <h2 class="font-headline-sm text-on-surface whitespace-nowrap hidden md:block">@yield('header_title')</h2>
        </div>

        <div class="flex items-center gap-lg">
            <div class="flex items-center gap-sm">
                <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors relative active:scale-95">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-surface"></span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-full transition-colors active:scale-95" title="Logout">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            </div>
            
            <div class="flex items-center gap-md pl-md border-l border-outline-variant/30">
                <div class="text-right hidden sm:block">
                    <p class="font-label-md text-label-md text-on-surface">{{ auth()->user()->name ?? 'Admin Utama' }}</p>
                    <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider">Super Administrator</p>
                </div>
                <img alt="Admin Profile Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-primary-container" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=006c49&color=fff"/>
            </div>
        </div>
    </header>

    <!-- Slot Content -->
    <div class="pt-6 px-gutter max-w-[1440px] mx-auto">
        @yield('content')
    </div>
</main>

@yield('scripts')

</body>
</html>
