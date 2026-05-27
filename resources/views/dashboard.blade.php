@extends('layouts.app')

@section('header_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Hero Section (Welcome) -->
    <div class="relative overflow-hidden bg-gradient-to-br from-teal-500 to-emerald-700 rounded-3xl p-8 md:p-10 text-white shadow-xl shadow-teal-500/20 flex flex-col justify-center">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-teal-300 opacity-20 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4"></div>
        
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">Selamat Datang di RAKSA</h1>
            <p class="text-teal-50 text-base md:text-lg font-medium leading-relaxed">
                Respons Akurat Kelola Seluruh Aset. Sistem Informasi Inventaris dan Logistik Terpadu untuk menunjang pelayanan optimal di Puskesmas Mantup.
            </p>
        </div>
    </div>

    <!-- Stats Bento Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined icon-fill">inventory_2</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Total Aset Aktif</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($assetStats['aktif'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined icon-fill">verified</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Kondisi Baik</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($assetStats['baik'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined icon-fill">warning</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Rusak / Perlu Servis</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($assetStats['rusak'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined icon-fill">thermostat</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Perlu Kalibrasi</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($assetStats['perlu_kalibrasi'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Area Chart: Aktivitas Transaksi (Span 2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Dinamika Transaksi Logistik</h3>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full">6 Bulan Terakhir</span>
            </div>
            <div class="flex-1 min-h-[300px]">
                <div id="mainActivityChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Pie Chart: Distribusi Kondisi Aset -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-slate-800">Kondisi Aset</h3>
            </div>
            <p class="text-xs text-slate-500 mb-4">Persentase kondisi seluruh aset terdaftar.</p>
            <div class="flex-1 flex items-center justify-center">
                <div id="conditionPieChart" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- Alert & Tables Bento Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Peringatan Sistem -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-rose-500 icon-fill">campaign</span>
                <h3 class="text-lg font-bold text-slate-800">Peringatan Sistem</h3>
            </div>
            
            <div class="space-y-4">
                @if($lowStockItems->isEmpty() && $calibrationReminders->isEmpty() && $serviceReminders->isEmpty() && $expiryReminders->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-slate-400 text-3xl">check_circle</span>
                        </div>
                        <p class="text-slate-500 font-medium">Sistem dalam keadaan optimal. Tidak ada peringatan.</p>
                    </div>
                @endif

                @foreach($lowStockItems as $item)
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-amber-50/50 border border-amber-100 hover:bg-amber-50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <span class="material-symbols-outlined">inventory</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $item->nama_barang }}</p>
                        <p class="text-xs text-slate-500 mt-1">Stok Menipis: <span class="font-bold text-amber-600">{{ $item->stok_sekarang }} {{ $item->satuan }}</span> ({{ $kategoriList[$item->kategori_besar]['label'] ?? $item->kategori_besar }})</p>
                    </div>
                </div>
                @endforeach
                
                @foreach($calibrationReminders as $asset)
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-rose-50/50 border border-rose-100 hover:bg-rose-50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <span class="material-symbols-outlined">thermostat</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $asset->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">Jadwal Kalibrasi: <span class="font-bold text-rose-600">{{ \Carbon\Carbon::parse($asset->next_calibration)->format('d M Y') }}</span></p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-teal-500 icon-fill">history</span>
                <h3 class="text-lg font-bold text-slate-800">Aktivitas Transaksi Terbaru</h3>
            </div>
            
            <div class="space-y-4">
                @forelse($recentTransactions as $trx)
                <div class="flex items-center justify-between p-4 rounded-2xl border border-slate-50 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-4">
                        @if($trx->jenis_transaksi == 'masuk')
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                                <span class="material-symbols-outlined">arrow_downward</span>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                                <span class="material-symbols-outlined">arrow_upward</span>
                            </div>
                        @endif
                        
                        <div>
                            <p class="text-sm font-bold text-slate-800 line-clamp-1">{{ $trx->item->nama_barang }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }} • {{ $trx->jenis_transaksi == 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold {{ $trx->jenis_transaksi == 'masuk' ? 'text-teal-600' : 'text-rose-600' }}">
                            {{ $trx->jenis_transaksi == 'masuk' ? '+' : '-' }}{{ $trx->jumlah }} {{ $trx->item->satuan }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-500 text-sm">
                    Belum ada transaksi terbaru.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- DATA PREPARATION ---
        const chartLabels = {!! json_encode($chartLabels) !!};
        const allChartData = {!! json_encode($allChartData) !!};
        
        // Summing up all categories for the aggregated chart
        let totalMasuk = [0, 0, 0, 0, 0, 0];
        let totalKeluar = [0, 0, 0, 0, 0, 0];

        Object.values(allChartData).forEach(data => {
            data.masuk.forEach((val, i) => totalMasuk[i] += val);
            data.keluar.forEach((val, i) => totalKeluar[i] += val);
        });

        // 1. Main Area Chart (Aktivitas Transaksi)
        var optionsMain = {
            series: [{
                name: 'Barang Masuk (Rp)',
                data: totalMasuk
            }, {
                name: 'Barang Keluar (Rp)',
                data: totalKeluar
            }],
            chart: {
                type: 'area',
                height: 320,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false },
                background: 'transparent'
            },
            colors: ['#0d9488', '#e11d48'], // Teal-600 and Rose-600
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: chartLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        if(value >= 1000000) return (value / 1000000).toFixed(1) + 'Jt';
                        if(value >= 1000) return (value / 1000).toFixed(0) + 'Rb';
                        return value;
                    },
                    style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                markers: { radius: 12 },
                itemMargin: { horizontal: 10, vertical: 0 }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                    }
                }
            }
        };

        var chartMain = new ApexCharts(document.querySelector("#mainActivityChart"), optionsMain);
        chartMain.render();

        // 2. Pie Chart (Kondisi Aset)
        const conditionData = {!! json_encode($chartKondisi) !!};
        const pieLabels = Object.keys(conditionData);
        const pieSeries = Object.values(conditionData).map(val => parseInt(val));

        // Define colors based on label (Baik = emerald, Rusak Ringan = amber, Rusak Berat = rose, etc)
        const pieColors = pieLabels.map(label => {
            if(label === 'Baik') return '#10b981'; // emerald-500
            if(label === 'Rusak Ringan') return '#f59e0b'; // amber-500
            if(label === 'Rusak Berat') return '#f43f5e'; // rose-500
            return '#6366f1'; // indigo-500 fallback
        });

        var optionsPie = {
            series: pieSeries,
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            labels: pieLabels,
            colors: pieColors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { fontSize: '14px', fontWeight: 600, color: '#64748b' },
                            value: { fontSize: '24px', fontWeight: 800, color: '#1e293b' },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#64748b',
                                fontSize: '14px',
                                fontWeight: 600
                            }
                        }
                    },
                    expandOnClick: false
                }
            },
            dataLabels: { enabled: false },
            stroke: { width: 4, colors: ['#ffffff'] },
            legend: {
                position: 'bottom',
                markers: { radius: 12 },
                itemMargin: { horizontal: 10, vertical: 5 }
            },
            tooltip: {
                theme: 'light',
                y: { formatter: function (val) { return val + " Aset" } }
            }
        };

        var chartPie = new ApexCharts(document.querySelector("#conditionPieChart"), optionsPie);
        chartPie.render();
    });
</script>
@endsection
