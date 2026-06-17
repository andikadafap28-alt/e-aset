@extends('layouts.app')

@section('header_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Hero Section (Welcome) -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 md:p-10 text-white shadow-xl shadow-blue-600/20 flex flex-col justify-center">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-300 opacity-20 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4"></div>
        
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-2">Selamat Datang di RAKSA</h1>
            <p class="text-blue-50 text-base md:text-lg font-medium leading-relaxed">
                Respons Akurat Kelola Seluruh Aset. Sistem Informasi Inventaris dan Logistik Terpadu untuk menunjang pelayanan optimal di Puskesmas Mantup.
            </p>
        </div>
    </div>

    <!-- Stats Bento Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <!-- Card 1: Valuasi Aset Bersih -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300 relative group cursor-help" title="Rp {{ number_format($assetStats['total_book_value'], 0, ',', '.') }}">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined icon-fill">account_balance_wallet</span>
            </div>
            <div class="w-full">
                <p class="text-sm font-semibold text-slate-500">Valuasi Aset Bersih</p>
                <h3 class="text-xl md:text-2xl font-bold text-slate-800 mt-1">{{ $assetStats['formatted_book_value'] }}</h3>
                <p class="text-xs text-slate-400 mt-2">Nilai buku saat ini (Book Value)</p>
            </div>
        </div>

        <!-- Card 2: Total Akumulasi Penyusutan -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300 relative group cursor-help" title="Rp {{ number_format($assetStats['total_depreciation'], 0, ',', '.') }}">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined icon-fill">trending_down</span>
            </div>
            <div class="w-full">
                <p class="text-sm font-semibold text-slate-500">Total Depresiasi</p>
                <h3 class="text-xl md:text-2xl font-bold text-slate-800 mt-1">{{ $assetStats['formatted_depreciation'] }}</h3>
                <p class="text-xs text-slate-400 mt-2">Penyusutan nilai aset keseluruhan</p>
            </div>
        </div>

        <!-- Card 3: Pengadaan Logistik -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300 relative group cursor-help" title="Rp {{ number_format($masukBulanIni, 0, ',', '.') }}">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600">
                <span class="material-symbols-outlined icon-fill">add_shopping_cart</span>
            </div>
            <div class="w-full">
                <p class="text-sm font-semibold text-slate-500">Pengadaan (Bulan Ini)</p>
                <div class="flex items-center gap-2 mt-1">
                    <h3 class="text-xl md:text-2xl font-bold text-slate-800">{{ $formattedMasukBulanIni }}</h3>
                    @if($masukBulanLalu > 0)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $masukGrowth > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                            {{ $masukGrowth > 0 ? '▲' : '▼' }} {{ number_format(abs($masukGrowth), 1) }}%
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">Vs Bulan Lalu: Rp {{ number_format($masukBulanLalu, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Card 4: Distribusi Logistik -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col items-start gap-4 hover:-translate-y-1 transition-transform duration-300 relative group cursor-help" title="Rp {{ number_format($keluarBulanIni, 0, ',', '.') }}">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined icon-fill">local_shipping</span>
            </div>
            <div class="w-full">
                <p class="text-sm font-semibold text-slate-500">Distribusi (Bulan Ini)</p>
                <div class="flex items-center gap-2 mt-1">
                    <h3 class="text-xl md:text-2xl font-bold text-slate-800">{{ $formattedKeluarBulanIni }}</h3>
                    @if($keluarBulanLalu > 0)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $keluarGrowth > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                            {{ $keluarGrowth > 0 ? '▲' : '▼' }} {{ number_format(abs($keluarGrowth), 1) }}%
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">Vs Bulan Lalu: Rp {{ number_format($keluarBulanLalu, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Area Chart: Aktivitas Transaksi (Span 2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-800">Dinamika Transaksi Logistik</h3>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full">6 Bulan Terakhir</span>
            </div>
            <div class="flex-1 min-h-[300px] relative w-full h-full">
                <canvas id="mainActivityChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Distribusi Kondisi Aset -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-bold text-slate-800">Kondisi Aset</h3>
            </div>
            <p class="text-xs text-slate-500 mb-4">Persentase kondisi seluruh aset terdaftar.</p>
            <div class="flex-1 relative flex items-center justify-center w-full min-h-[300px]">
                <canvas id="conditionPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Kategori Persediaan Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <div class="flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-blue-500 icon-fill">category</span>
            <h3 class="text-base font-bold text-slate-800">Filter Kategori Persediaan</h3>
            @if(isset($filterKat) && $filterKat != '')
                <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Menampilkan: {{ $kategoriList[$filterKat]['label'] ?? $filterKat }}</span>
            @endif
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Semua Kategori Button -->
            <a href="{{ url('/dashboard') }}" class="flex flex-col p-4 rounded-2xl bg-slate-50 border {{ empty($filterKat) ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'border-slate-200' }} hover:bg-slate-100 transition-colors group">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 mb-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-sm">grid_view</span>
                </div>
                <h4 class="text-sm font-bold text-slate-800 line-clamp-1">Semua Kategori</h4>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Tampilkan Semua</span>
                </div>
            </a>
            
            @foreach($kategoriList as $key => $cat)
            <a href="{{ url('/dashboard?kategori=' . $key) }}" class="flex flex-col p-4 rounded-2xl bg-{{ $cat['icon'] }}-50/50 border {{ (isset($filterKat) && $filterKat == $key) ? 'border-'.$cat['icon'].'-500 ring-2 ring-'.$cat['icon'].'-500/20 shadow-md' : 'border-'.$cat['icon'].'-100' }} hover:bg-{{ $cat['icon'] }}-50 transition-colors group">
                <div class="w-8 h-8 rounded-full bg-{{ $cat['icon'] }}-100 flex items-center justify-center text-{{ $cat['icon'] }}-600 mb-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                </div>
                <h4 class="text-sm font-bold text-slate-800 line-clamp-1" title="{{ $cat['label'] }}">{{ $cat['label'] }}</h4>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Stok: <strong class="text-{{ $cat['icon'] }}-600">{{ number_format($cat['total'], 0, ',', '.') }}</strong></span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Alert & Tables Bento Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Peringatan Sistem -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-rose-500 icon-fill">campaign</span>
                <h3 class="text-base font-bold text-slate-800">Peringatan Sistem</h3>
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
                <span class="material-symbols-outlined text-blue-500 icon-fill">history</span>
                <h3 class="text-base font-bold text-slate-800">Aktivitas Transaksi Terbaru</h3>
            </div>
            
            <div class="space-y-4">
                @forelse($recentTransactions as $trx)
                <div class="flex items-center justify-between p-4 rounded-2xl border border-slate-50 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-4">
                        @if($trx->jenis_transaksi == 'masuk')
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
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
                        <p class="text-sm font-bold {{ $trx->jenis_transaksi == 'masuk' ? 'text-blue-600' : 'text-rose-600' }}">
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
        const filterKat = '{!! $filterKat ?? '' !!}';
        
        // Summing up for the aggregated chart
        let totalMasuk = [0, 0, 0, 0, 0, 0];
        let totalKeluar = [0, 0, 0, 0, 0, 0];

        if (filterKat && allChartData[filterKat]) {
            let data = allChartData[filterKat];
            data.masuk.forEach((val, i) => totalMasuk[i] += val);
            data.keluar.forEach((val, i) => totalKeluar[i] += val);
        } else {
            Object.values(allChartData).forEach(data => {
                data.masuk.forEach((val, i) => totalMasuk[i] += val);
                data.keluar.forEach((val, i) => totalKeluar[i] += val);
            });
        }

        // 1. Main Area Chart (Aktivitas Transaksi)
        const ctxMain = document.getElementById('mainActivityChart').getContext('2d');
        
        // Create vibrant gradients for Area Chart
        let gradientMasuk = ctxMain.createLinearGradient(0, 0, 0, 400);
        gradientMasuk.addColorStop(0, 'rgba(37, 99, 235, 0.4)'); // Blue-600
        gradientMasuk.addColorStop(1, 'rgba(37, 99, 235, 0.05)');
        
        let gradientKeluar = ctxMain.createLinearGradient(0, 0, 0, 400);
        gradientKeluar.addColorStop(0, 'rgba(225, 29, 72, 0.4)'); // Rose-600
        gradientKeluar.addColorStop(1, 'rgba(225, 29, 72, 0.05)');

        new Chart(ctxMain, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Barang Masuk (Rp)',
                        data: totalMasuk,
                        borderColor: '#2563eb', // blue-600
                        backgroundColor: gradientMasuk,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Barang Keluar (Rp)',
                        data: totalKeluar,
                        borderColor: '#e11d48', // rose-600
                        backgroundColor: gradientKeluar,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#e11d48',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1e293b',
                        bodyColor: '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13, weight: 'bold' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        beginAtZero: true,
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                            color: '#64748b',
                            callback: function(value) {
                                if(value >= 1000000) return (value / 1000000).toFixed(1) + 'Jt';
                                if(value >= 1000) return (value / 1000).toFixed(0) + 'Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // 2. Pie Chart (Kondisi Aset)
        const conditionData = {!! json_encode($chartKondisi) !!};
        const pieLabels = Object.keys(conditionData);
        const pieSeries = Object.values(conditionData).map(val => parseInt(val));

        const pieColors = pieLabels.map(label => {
            if(label === 'Baik') return '#10b981'; // emerald-500
            if(label === 'Rusak Ringan') return '#f59e0b'; // amber-500
            if(label === 'Rusak Berat') return '#f43f5e'; // rose-500
            return '#6366f1'; // indigo-500 fallback
        });
        
        const pieHoverColors = pieLabels.map(label => {
            if(label === 'Baik') return '#059669'; // emerald-600
            if(label === 'Rusak Ringan') return '#d97706'; // amber-600
            if(label === 'Rusak Berat') return '#e11d48'; // rose-600
            return '#4f46e5'; // indigo-600 fallback
        });

        const ctxPie = document.getElementById('conditionPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieSeries,
                    backgroundColor: pieColors,
                    hoverBackgroundColor: pieHoverColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 13,
                                weight: '600'
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1e293b',
                        bodyColor: '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 14, weight: 'bold' },
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.parsed} Aset`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    var width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;

                    ctx.restore();
                    var fontSize = (height / 160).toFixed(2);
                    ctx.font = "bold " + fontSize + "em 'Plus Jakarta Sans', sans-serif";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#1e293b";

                    var total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    var text = total,
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2.3;

                    ctx.fillText(text, textX, textY);
                    
                    ctx.font = "600 " + (fontSize * 0.4).toFixed(2) + "em 'Plus Jakarta Sans', sans-serif";
                    ctx.fillStyle = "#64748b";
                    var text2 = "Total",
                        text2X = Math.round((width - ctx.measureText(text2).width) / 2),
                        text2Y = height / 2.3 + (height * 0.1);
                    ctx.fillText(text2, text2X, text2Y);
                    
                    ctx.save();
                }
            }]
        });
    });
</script>
@endsection

