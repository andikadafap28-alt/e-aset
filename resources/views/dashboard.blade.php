@extends('layouts.app')

@section('header_title', 'Dashboard RAKSA')

@section('content')
<div class="space-y-md">
    
    <!-- Hero Greeting Banner -->
    <section class="relative overflow-hidden glass-card mesh-gradient rounded-lg p-lg flex flex-col md:flex-row items-center justify-between border-none">
        <div class="z-10 text-center md:text-left">
            <h2 class="font-display-lg text-display-lg text-on-surface mb-xs">Selamat Datang, {{ auth()->user()?->name ?? 'Admin' }}</h2>
            <div class="flex items-center justify-center md:justify-start gap-sm">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <p class="font-body-lg text-body-lg text-on-surface-variant">Semua sistem manajemen aset berjalan optimal.</p>
            </div>
        </div>
        <div class="hidden lg:block z-10">
            <div class="flex gap-md">
                <div class="bg-white/80 p-sm rounded-lg shadow-sm text-center min-w-[120px]">
                    <p class="text-[10px] uppercase tracking-widest text-on-surface-variant mb-1 font-bold">Total Kategori</p>
                    <p class="text-headline-sm font-bold text-primary">{{ count($kategoriList) }}</p>
                </div>
                <div class="bg-white/80 p-sm rounded-lg shadow-sm text-center min-w-[120px]">
                    <p class="text-[10px] uppercase tracking-widest text-on-surface-variant mb-1 font-bold">Total Nilai Masuk (Bln Ini)</p>
                    <p class="text-headline-sm font-bold text-primary">Rp {{ number_format($masukBulanIni / 1000000, 1) }}M</p>
                </div>
            </div>
        </div>
        <!-- Abstract elements for background -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-secondary-container/10 rounded-full blur-3xl"></div>
    </section>

    <!-- Top Metrics Grid (Bento Box) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-md">
        <!-- Metric 1 -->
        <div class="bento-card glass-card p-lg rounded-lg group">
            <div class="flex items-start justify-between mb-md">
                <div class="p-sm bg-primary/10 rounded-lg group-hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-primary group-hover:text-white" style="font-variation-settings: 'FILL' 1;">payments</span>
                </div>
                <span class="text-primary font-bold text-sm bg-primary/5 px-2 py-1 rounded">Aset</span>
            </div>
            <p class="font-label-md text-label-md text-on-surface-variant mb-xs">Total Nilai Perolehan</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">Rp {{ number_format($assetStats['total_purchase'], 0, ',', '.') }}</h3>
            <p class="text-[12px] text-on-surface-variant mt-sm">Total pengadaan aset kumulatif</p>
        </div>
        <!-- Metric 2 -->
        <div class="bento-card glass-card p-lg rounded-lg group">
            <div class="flex items-start justify-between mb-md">
                <div class="p-sm bg-secondary-container/20 rounded-lg group-hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-secondary group-hover:text-white" style="font-variation-settings: 'FILL' 1;">trending_down</span>
                </div>
                <span class="text-secondary font-bold text-sm bg-secondary-container/10 px-2 py-1 rounded">Beban</span>
            </div>
            <p class="font-label-md text-label-md text-on-surface-variant mb-xs">Akumulasi Penyusutan</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">Rp {{ number_format($assetStats['total_depreciation'], 0, ',', '.') }}</h3>
            <p class="text-[12px] text-on-surface-variant mt-sm">Total depresiasi hingga hari ini</p>
        </div>
        <!-- Metric 3 -->
        <div class="bento-card glass-card p-lg rounded-lg group">
            <div class="flex items-start justify-between mb-md">
                <div class="p-sm bg-primary-fixed/20 rounded-lg group-hover:bg-primary-container transition-colors">
                    <span class="material-symbols-outlined text-primary group-hover:text-white" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                </div>
                <span class="text-primary font-bold text-sm bg-primary-fixed/10 px-2 py-1 rounded">Bersih</span>
            </div>
            <p class="font-label-md text-label-md text-on-surface-variant mb-xs">Nilai Buku Saat Ini</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">Rp {{ number_format($assetStats['total_book_value'], 0, ',', '.') }}</h3>
            <p class="text-[12px] text-on-surface-variant mt-sm">Estimasi nilai aset saat ini</p>
        </div>
    </section>

    <!-- Asset Health Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-md">
        <div class="bg-white p-md rounded-lg shadow-sm border border-outline-variant/30 flex items-center gap-md">
            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-on-surface-variant">list_alt</span>
            </div>
            <div>
                <p class="text-label-sm font-label-sm text-on-surface-variant">Total Aset Aktif</p>
                <p class="text-headline-sm font-headline-sm text-on-surface">{{ number_format($assetStats['aktif'], 0, ',', '.') }} <span class="text-xs font-normal text-on-surface-variant">unit</span></p>
            </div>
        </div>
        <div class="bg-white p-md rounded-lg shadow-sm border border-outline-variant/30 flex items-center gap-md">
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div>
                <p class="text-label-sm font-label-sm text-on-surface-variant">Kondisi Baik</p>
                <p class="text-headline-sm font-headline-sm text-on-surface">{{ number_format($assetStats['baik'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white p-md rounded-lg shadow-sm border border-outline-variant/30 flex items-center gap-md">
            <div class="w-12 h-12 rounded-full bg-error-container/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">cancel</span>
            </div>
            <div>
                <p class="text-label-sm font-label-sm text-on-surface-variant">Rusak</p>
                <p class="text-headline-sm font-headline-sm text-on-surface">{{ number_format($assetStats['rusak'], 0, ',', '.') }} <span class="text-xs font-normal text-error">Unit</span></p>
            </div>
        </div>
        <div class="bg-white p-md rounded-lg shadow-sm border border-outline-variant/30 flex items-center gap-md relative overflow-hidden group cursor-help">
            <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">build</span>
            </div>
            <div>
                <p class="text-label-sm font-label-sm text-on-surface-variant">Perlu Kalibrasi</p>
                <p class="text-headline-sm font-headline-sm text-on-surface">{{ number_format($assetStats['perlu_kalibrasi'], 0, ',', '.') }} <span class="text-xs font-normal text-secondary">Aset</span></p>
            </div>
            <div class="absolute top-0 right-0 w-1 h-full bg-secondary"></div>
        </div>
    </section>

    <!-- ApexCharts Integration Area -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-md">
        <!-- Main Chart: Mutasi Persediaan -->
        <div class="lg:col-span-2 bg-white rounded-lg p-lg shadow-sm border border-outline-variant/30">
            <div class="flex items-center justify-between mb-sm">
                <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface" id="chartTitle">Tren Mutasi Logistik</h4>
                    <p class="text-label-sm text-on-surface-variant mt-1">Pilih kategori untuk melihat pergerakan dana bulanan.</p>
                </div>
                <div class="flex gap-xs">
                    <!-- Dropdown Kategori untuk Chart -->
                    <select id="categorySelector" onchange="updateChart(this.value)" class="text-sm font-medium bg-surface rounded-lg border-outline-variant/50 focus:ring-primary focus:border-primary px-3 py-1.5">
                        @foreach($kategoriList as $key => $kat)
                        <option value="{{ $key }}">{{ $kat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="expenseChart" class="w-full h-[300px]"></div>
        </div>

        <!-- Donut Chart: Kategori Aset -->
        <div class="bg-white rounded-lg p-lg shadow-sm border border-outline-variant/30 flex flex-col justify-center items-center">
            <h4 class="font-headline-sm text-headline-sm text-on-surface w-full text-left mb-sm">Distribusi Kategori Aset</h4>
            <div id="assetCategoryChart" class="w-full h-[250px] flex justify-center mt-2 flex-1 items-center"></div>
        </div>
    </section>

    <!-- Activity & Alerts Panel -->
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-md">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg p-lg shadow-sm border border-outline-variant/30">
            <div class="flex items-center justify-between mb-lg">
                <h4 class="font-headline-sm text-headline-sm text-on-surface">Transaksi Persediaan Terakhir</h4>
                <button class="text-primary font-label-md text-label-md hover:underline">Lihat Semua</button>
            </div>
            <div class="space-y-sm max-h-[350px] overflow-y-auto pr-2">
                
                @forelse($recentTransactions as $trx)
                <div class="flex items-center justify-between p-md hover:bg-surface transition-colors rounded-lg group">
                    <div class="flex items-center gap-md">
                        @if($trx->jenis_transaksi == 'masuk')
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold">
                            <span class="material-symbols-outlined">add</span>
                        </div>
                        @else
                        <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center text-error font-bold">
                            <span class="material-symbols-outlined">remove</span>
                        </div>
                        @endif
                        <div>
                            <p class="font-label-md text-label-md text-on-surface">{{ $trx->item ? $trx->item->nama_barang : 'Barang Dihapus' }}</p>
                            <p class="text-body-sm text-on-surface-variant">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($trx->jenis_transaksi == 'masuk')
                        <p class="font-bold text-primary">+{{ number_format($trx->jumlah, 0, ',', '.') }}</p>
                        @else
                        <p class="font-bold text-error">-{{ number_format($trx->jumlah, 0, ',', '.') }}</p>
                        @endif
                        <p class="text-[12px] text-on-surface-variant">Unit/Box</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-on-surface-variant font-medium">Belum ada transaksi logistik.</div>
                @endforelse

            </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white rounded-lg p-lg shadow-sm border border-outline-variant/30">
            <div class="flex items-center justify-between mb-lg">
                <h4 class="font-headline-sm text-headline-sm text-on-surface">Peringatan Sistem</h4>
                <div class="flex gap-xs">
                    @if(count($lowStockItems) > 0)
                    <span class="bg-error-container text-on-error-container text-[10px] font-bold px-2 py-1 rounded-full uppercase">{{ count($lowStockItems) }} Critical</span>
                    @endif
                    @if(count($calibrationReminders) > 0)
                    <span class="bg-secondary-container text-on-secondary-container text-[10px] font-bold px-2 py-1 rounded-full uppercase">{{ count($calibrationReminders) }} Warning</span>
                    @endif
                </div>
            </div>
            <div class="space-y-sm max-h-[350px] overflow-y-auto pr-2">
                
                @forelse($lowStockItems as $item)
                <!-- Alert Stok -->
                <div class="p-md rounded-lg bg-error-container/20 border border-error-container flex gap-md">
                    <span class="material-symbols-outlined text-error mt-1">warning</span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-label-md text-label-md text-on-error-container">Stok Menipis: {{ $item->nama_barang }}</p>
                            <span class="text-[10px] text-on-error-container/70 font-bold">CRITICAL</span>
                        </div>
                        <p class="text-body-sm text-on-error-container/80 mt-1">Tersisa {{ number_format($item->stok_sekarang, 0, ',', '.') }} unit. Harap segera lakukan pengadaan.</p>
                    </div>
                </div>
                @empty
                @endforelse

                @forelse($calibrationReminders as $asset)
                <!-- Alert Kalibrasi -->
                <div class="p-md rounded-lg bg-secondary-container/20 border border-secondary-container flex gap-md">
                    <span class="material-symbols-outlined text-secondary mt-1">event_repeat</span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-label-md text-label-md text-on-secondary-container">Kalibrasi: {{ $asset->name }}</p>
                            <span class="text-[10px] text-on-secondary-container/70 font-bold">{{ \Carbon\Carbon::parse($asset->next_calibration)->translatedFormat('d M') }}</span>
                        </div>
                        <p class="text-body-sm text-on-secondary-container/80 mt-1">Kode aset: {{ $asset->asset_code }}. Wajib kalibrasi berkala.</p>
                    </div>
                </div>
                @empty
                @endforelse

                @if(count($lowStockItems) == 0 && count($calibrationReminders) == 0)
                <div class="p-md rounded-lg bg-surface-container flex gap-md opacity-70">
                    <span class="material-symbols-outlined text-outline mt-1">inventory</span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-label-md text-label-md text-on-surface">Sistem Stabil</p>
                        </div>
                        <p class="text-body-sm text-on-surface-variant mt-1">Tidak ada peringatan stok atau kalibrasi untuk hari ini.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const allChartData = {!! json_encode($allChartData) !!};
    const chartMaxData = {!! json_encode($chartMaxData) !!};
    const chartLabels = {!! json_encode($chartLabels) !!};
    const categoryNames = {!! json_encode(array_map(function($k) { return $k['label']; }, $kategoriList)) !!};
    
    const yAxisFormatter = function(value) {
        let num = Number(value); 
        if (isNaN(num) || num === 0) return "0";
        if(num >= 1000000000000) return (num / 1000000000000).toFixed(1).replace(/\.0$/, '') + " T";
        else if(num >= 1000000000) return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + " M";
        else if(num >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, '') + " jt";
        else if(num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + " rb";
        return num.toString();
    };

    let chart;
    
    function updateChart(category) {
        const masukData = allChartData[category].masuk;
        const keluarData = allChartData[category].keluar;
        const newMax = chartMaxData[category] || {{ $chartMax }};

        chart.updateOptions({
            yaxis: {
                max: newMax,
                tickAmount: 5,
                labels: { formatter: yAxisFormatter, style: { colors: '#595f66', fontWeight: 600 } }
            }
        });

        chart.updateSeries([
            { name: 'Masuk (Rp)', data: masukData },
            { name: 'Keluar (Rp)', data: keluarData }
        ]);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Main Area Chart
        var options = {
            series: [
                { name: 'Masuk (Rp)', data: [0,0,0,0,0,0] },
                { name: 'Keluar (Rp)', data: [0,0,0,0,0,0] }
            ],
            chart: {
                height: 300,
                type: 'area',
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#006c49', '#ba1a1a'], // Primary & Error colors from Stitch config
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            xaxis: {
                categories: chartLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#595f66', fontWeight: 600 } }
            },
            yaxis: {
                min: 0,
                max: {{ $chartMax ?? 'undefined' }},
                tickAmount: 5,
                labels: { formatter: yAxisFormatter, style: { colors: '#595f66', fontWeight: 600 } }
            },
            grid: { borderColor: '#e0e3e5', strokeDashArray: 4, yaxis: { lines: { show: true } } },
            tooltip: { y: { formatter: function (value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value)); } } }
        };

        chart = new ApexCharts(document.querySelector("#expenseChart"), options);
        chart.render();

        const firstCategory = Object.keys(categoryNames)[0];
        updateChart(firstCategory);

        // Asset Category Donut Chart
        const chartKategori = {!! json_encode($chartKategori) !!};
        const categoryLabels = Object.keys(chartKategori);
        const categoryData = Object.values(chartKategori);
        
        if(categoryData.length > 0) {
            var categoryChartOptions = {
                series: categoryData,
                chart: { type: 'donut', height: 250, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                labels: categoryLabels,
                colors: ['#006c49', '#10b981', '#06b6d4', '#ba1a1a', '#f59e0b', '#8b5cf6', '#ec4899'],
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 700, color: '#595f66' },
                                value: { fontSize: '20px', fontWeight: 800, color: '#191c1e' }
                            }
                        }
                    }
                },
                stroke: { width: 0 },
                legend: { show: false }
            };
            var categoryChart = new ApexCharts(document.querySelector("#assetCategoryChart"), categoryChartOptions);
            categoryChart.render();
        } else {
            document.querySelector("#assetCategoryChart").innerHTML = '<p class="text-xs text-on-surface-variant font-medium text-center w-full">Belum ada data aset</p>';
        }
    });
</script>
@endsection
