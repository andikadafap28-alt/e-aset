@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Hero / Greeting Banner -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
        <!-- Subtle mesh gradient background effect -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-teal-400 via-emerald-100 to-transparent"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Selamat Datang, {{ auth()->user()?->name ?? 'Admin' }}</h2>
                <p class="text-slate-500 mt-2 font-medium">Sistem Informasi Manajemen Aset & Logistik (RAKSA) siap digunakan.</p>
            </div>
            <div class="hidden md:flex gap-3">
                <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-semibold shadow-sm hover:bg-slate-50 transition-all">Laporan Cepat</button>
                <button class="px-5 py-2.5 bg-teal-600 text-white rounded-xl font-semibold shadow-sm shadow-teal-500/30 hover:bg-teal-700 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">add</span> Input Data Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Top Metrics Grid (Bento Box) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:border-teal-300 transition-all">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-teal-600">account_balance_wallet</span>
            </div>
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Nilai Perolehan</p>
            <h3 class="text-3xl font-extrabold text-slate-800">Rp {{ number_format($assetStats['total_purchase'] ?? 0, 0, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-emerald-600 bg-emerald-50 w-fit px-2 py-1 rounded-md">
                <span class="material-symbols-outlined text-[14px]">trending_up</span> Total Keseluruhan
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:border-red-300 transition-all">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-red-600">trending_down</span>
            </div>
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Akumulasi Penyusutan</p>
            <h3 class="text-3xl font-extrabold text-slate-800">Rp {{ number_format($assetStats['total_depreciation'] ?? 0, 0, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-red-600 bg-red-50 w-fit px-2 py-1 rounded-md">
                <span class="material-symbols-outlined text-[14px]">info</span> Estimasi Penurunan Nilai
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:border-teal-300 transition-all">
            <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(circle_at_bottom_right,_var(--tw-gradient-stops))] from-teal-500 to-transparent"></div>
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-teal-600">savings</span>
            </div>
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Nilai Buku Saat Ini</p>
            <h3 class="text-3xl font-extrabold text-teal-700">Rp {{ number_format($assetStats['total_book_value'] ?? 0, 0, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-slate-600 bg-slate-100 w-fit px-2 py-1 rounded-md">
                <span class="material-symbols-outlined text-[14px]">calculate</span> Nilai Aktif Riil
            </div>
        </div>
    </div>

    <!-- Asset Health Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aset Aktif</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($assetStats['aktif'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-slate-200 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi Baik</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($assetStats['baik'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rusak</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($assetStats['rusak'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">build</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Kalibrasi</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($assetStats['perlu_kalibrasi'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Area Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Tren Transaksi Logistik (6 Bulan)</h3>
                <div class="text-xs font-medium bg-slate-100 text-slate-600 px-3 py-1 rounded-full">Semua Kategori</div>
            </div>
            <div id="mainChart" class="h-72 w-full"></div>
        </div>

        <!-- Pie Charts -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4">Distribusi Kondisi Aset</h3>
                <div id="kondisiChart" class="flex justify-center"></div>
            </div>
            <div class="border-t border-slate-100 mt-4 pt-4">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Kategori Aset Terbanyak</h3>
                <div id="kategoriChart" class="flex justify-center"></div>
            </div>
        </div>
    </div>

    <!-- Bottom Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Panel 1: Recent Transactions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-500">history</span>
                    <h3 class="text-lg font-bold text-slate-800">Aktivitas Terbaru</h3>
                </div>
                <a href="#" class="text-sm font-semibold text-teal-600 hover:text-teal-700">Lihat Semua</a>
            </div>
            <div class="p-0 flex-1">
                @if(count($recentTransactions ?? []) > 0)
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentTransactions as $trx)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                @if($trx->jenis_transaksi == 'masuk')
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[20px]">arrow_downward</span>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $trx->item->nama_barang ?? 'Barang Dihapus' }}</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }} • {{ ucfirst($trx->jenis_transaksi) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($trx->jenis_transaksi == 'masuk')
                                    <p class="text-sm font-bold text-emerald-600">+{{ number_format($trx->jumlah, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-sm font-bold text-red-600">-{{ number_format($trx->jumlah, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inbox</span>
                        <p class="text-sm">Belum ada aktivitas transaksi</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel 2: System Alerts -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">notifications_active</span>
                    <h3 class="text-lg font-bold text-slate-800">Peringatan Sistem</h3>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto max-h-[400px]">
                <ul class="divide-y divide-slate-100">
                    
                    <!-- Low Stock Alerts -->
                    @if(isset($lowStockItems) && count($lowStockItems) > 0)
                        @foreach($lowStockItems as $item)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-4">
                            <div class="w-8 h-8 mt-1 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[16px]">warning</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Stok Menipis: {{ $item->nama_barang }}</p>
                                <p class="text-xs text-slate-600 mt-1">Sisa stok saat ini hanya <strong class="text-amber-600">{{ $item->stok_sekarang }}</strong> unit.</p>
                            </div>
                        </li>
                        @endforeach
                    @endif

                    <!-- Calibration Alerts -->
                    @if(isset($calibrationReminders) && count($calibrationReminders) > 0)
                        @foreach($calibrationReminders as $asset)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-4">
                            <div class="w-8 h-8 mt-1 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[16px]">build</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Jadwal Kalibrasi: {{ $asset->asset_name }}</p>
                                <p class="text-xs text-slate-600 mt-1">Jatuh tempo pada <strong class="text-blue-600">{{ \Carbon\Carbon::parse($asset->next_calibration)->translatedFormat('d M Y') }}</strong>.</p>
                            </div>
                        </li>
                        @endforeach
                    @endif

                    @if((!isset($lowStockItems) || count($lowStockItems) == 0) && (!isset($calibrationReminders) || count($calibrationReminders) == 0))
                        <div class="p-8 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">check_circle</span>
                            <p class="text-sm">Semua sistem dalam kondisi aman.</p>
                        </div>
                    @endif

                </ul>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- MAIN TREND CHART ---
    // Prepare Data for Area Chart
    const rawData = {!! json_encode($allChartData ?? []) !!};
    const labels = {!! json_encode($chartLabels ?? []) !!};
    
    // Aggregate total masuk & keluar for all categories
    let totalMasuk = [0,0,0,0,0,0];
    let totalKeluar = [0,0,0,0,0,0];

    Object.keys(rawData).forEach(cat => {
        if(rawData[cat].masuk) {
            rawData[cat].masuk.forEach((val, i) => totalMasuk[i] += val);
        }
        if(rawData[cat].keluar) {
            rawData[cat].keluar.forEach((val, i) => totalKeluar[i] += val);
        }
    });

    var optionsMain = {
        series: [{
            name: 'Pemasukan (Rp)',
            data: totalMasuk
        }, {
            name: 'Pengeluaran (Rp)',
            data: totalKeluar
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: ['#0d9488', '#e11d48'], // Teal & Rose
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
        },
        xaxis: {
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#64748b' } }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + (value / 1000000).toFixed(1) + "M";
                },
                style: { colors: '#64748b' }
            }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right' }
    };
    var chartMain = new ApexCharts(document.querySelector("#mainChart"), optionsMain);
    chartMain.render();

    // --- KONDISI PIE CHART ---
    const kondisiDataRaw = {!! json_encode($chartKondisi ?? []) !!};
    const kondisiLabels = Object.keys(kondisiDataRaw);
    const kondisiSeries = Object.values(kondisiDataRaw);

    var optionsKondisi = {
        series: kondisiSeries.length > 0 ? kondisiSeries : [1],
        labels: kondisiLabels.length > 0 ? kondisiLabels : ['Data Kosong'],
        chart: { type: 'donut', height: 220, fontFamily: 'Plus Jakarta Sans, sans-serif' },
        colors: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6'],
        plotOptions: {
            pie: { donut: { size: '70%' } }
        },
        dataLabels: { enabled: false },
        legend: { position: 'right', fontSize: '12px' },
        stroke: { show: false }
    };
    var chartKondisi = new ApexCharts(document.querySelector("#kondisiChart"), optionsKondisi);
    chartKondisi.render();

    // --- KATEGORI PIE CHART ---
    const kategoriDataRaw = {!! json_encode($chartKategori ?? []) !!};
    const kategoriLabels = Object.keys(kategoriDataRaw);
    const kategoriSeries = Object.values(kategoriDataRaw);

    var optionsKategori = {
        series: kategoriSeries.length > 0 ? kategoriSeries : [1],
        labels: kategoriLabels.length > 0 ? kategoriLabels : ['Data Kosong'],
        chart: { type: 'pie', height: 220, fontFamily: 'Plus Jakarta Sans, sans-serif' },
        colors: ['#0ea5e9', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#64748b'],
        dataLabels: { enabled: false },
        legend: { position: 'right', fontSize: '12px' },
        stroke: { width: 1, colors: ['#fff'] }
    };
    var chartKategori = new ApexCharts(document.querySelector("#kategoriChart"), optionsKategori);
    chartKategori.render();

});
</script>
@endsection
