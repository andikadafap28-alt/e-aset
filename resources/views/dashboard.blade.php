@extends('layouts.app')

@section('header_title', 'Dashboard RAKSA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
    
    <!-- Gen Z / Neo-Bento Banner -->
    <div x-show="show" x-transition.duration.500ms.opacity.translate.y.-20px class="bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl p-8 shadow-sm relative overflow-hidden group">
        <!-- Mesh Gradient Background Effect -->
        <div class="absolute -right-20 -top-20 w-72 h-72 bg-teal-400/20 rounded-full blur-[80px] group-hover:bg-teal-400/30 transition-colors duration-700"></div>
        <div class="absolute right-40 -bottom-20 w-72 h-72 bg-emerald-400/20 rounded-full blur-[80px] group-hover:bg-emerald-400/30 transition-colors duration-700"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-xs font-bold mb-4">
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                Sistem Aktif & Real-time
            </div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat datang di RAKSA</h1>
            <p class="text-slate-500 max-w-xl text-sm leading-relaxed font-medium">Respons Akurat Kelola Seluruh Aset. Sistem manajemen logistik terintegrasi untuk Puskesmas Mantup. Pantau pergerakan aset secara real-time dari satu tempat.</p>
        </div>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div x-show="show" x-transition.delay.100ms.duration.500ms.opacity.translate.y.20px class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($kategoriList as $key => $kat)
        <div onclick="updateChart('{{ $key }}')" class="cursor-pointer bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group category-card" id="card-{{ $key }}">
            
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($kat['jenis'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Jenis {{ $kat['label'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-{{ $kat['icon'] }}-50 flex items-center justify-center text-{{ $kat['icon'] }}-500 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <div class="w-full h-1.5 bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-{{ $kat['icon'] }}-500 w-full transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out"></div>
            </div>
            <p class="text-xs text-slate-500 mt-3 font-medium">Total <span class="font-bold text-slate-800">{{ number_format($kat['total'], 0, ',', '.') }}</span> item</p>
        </div>
        @endforeach
    </div>

    <!-- Charts & Financials Bento -->
    <div x-show="show" x-transition.delay.200ms.duration.500ms.opacity.translate.y.20px class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Main Chart -->
        <div class="xl:col-span-2 bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 relative overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight" id="chartTitle">Tren Mutasi</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Pilih kategori di atas untuk melihat detail</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 bg-teal-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Masuk</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Keluar</span>
                </div>
            </div>
            <div id="expenseChart" class="w-full h-80"></div>
        </div>

        <!-- Financial Summary Bento Column -->
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-teal-500 to-emerald-600 rounded-[32px] p-6 text-white shadow-md relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2"></div>
                <h3 class="text-[11px] font-bold text-teal-100 uppercase tracking-widest mb-4">Nilai Masuk Bulan Ini</h3>
                <h2 class="text-3xl font-extrabold tracking-tight">Rp {{ number_format($masukBulanIni, 0, ',', '.') }}</h2>
                <div class="mt-4 pt-4 border-t border-teal-400/30 flex justify-between items-center text-sm font-medium">
                    <span class="text-teal-100">Bulan Lalu</span>
                    <span>Rp {{ number_format($masukBulanLalu, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[32px] p-6 text-white shadow-md relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2"></div>
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Nilai Keluar Bulan Ini</h3>
                <h2 class="text-3xl font-extrabold tracking-tight">Rp {{ number_format($keluarBulanIni, 0, ',', '.') }}</h2>
                <div class="mt-4 pt-4 border-t border-slate-700/50 flex justify-between items-center text-sm font-medium">
                    <span class="text-slate-400">Bulan Lalu</span>
                    <span>Rp {{ number_format($keluarBulanLalu, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Summary Bento Box -->
    <div x-show="show" x-transition.delay.300ms.duration.500ms.opacity.translate.y.20px class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    Manajemen Aset Tetap
                </h3>
            </div>
            <a href="{{ route('laporan.aset.pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-2xl hover:bg-slate-800 transition-colors shadow-md hover:shadow-xl hover:-translate-y-0.5 transform duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Laporan PDF
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-slate-50/50 border border-slate-100 p-5 rounded-3xl hover:bg-slate-50 transition-colors">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Nilai Perolehan</p>
                <h4 class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($assetStats['total_purchase'], 0, ',', '.') }}</h4>
            </div>
            <div class="bg-rose-50/50 border border-rose-100/50 p-5 rounded-3xl hover:bg-rose-50 transition-colors">
                <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mb-1">Akumulasi Penyusutan</p>
                <h4 class="text-2xl font-extrabold text-rose-900">Rp {{ number_format($assetStats['total_depreciation'], 0, ',', '.') }}</h4>
            </div>
            <div class="bg-teal-50/50 border border-teal-100/50 p-5 rounded-3xl hover:bg-teal-50 transition-colors">
                <p class="text-[10px] text-teal-600 font-bold uppercase tracking-widest mb-1">Nilai Buku Saat Ini</p>
                <h4 class="text-2xl font-extrabold text-teal-900">Rp {{ number_format($assetStats['total_book_value'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Stat 1 -->
            <div class="bg-blue-50/50 border border-blue-100/50 p-5 rounded-3xl">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Aset</p>
                    <span class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">{{ number_format($assetStats['total'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs font-medium text-slate-500 mt-4">
                    <span>Aktif: <strong class="text-slate-800">{{ number_format($assetStats['aktif'], 0, ',', '.') }}</strong></span>
                    <span>Dihapus: <strong class="text-slate-800">{{ number_format($assetStats['disposed'], 0, ',', '.') }}</strong></span>
                </div>
            </div>
            
            <!-- Stat 2 -->
            <div class="bg-emerald-50/50 border border-emerald-100/50 p-5 rounded-3xl">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Kondisi Baik</p>
                </div>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-2">{{ number_format($assetStats['baik'], 0, ',', '.') }}</h3>
            </div>

            <!-- Stat 3 -->
            <div class="bg-rose-50/50 border border-rose-100/50 p-5 rounded-3xl">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">Rusak</p>
                </div>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-2">{{ number_format($assetStats['rusak'], 0, ',', '.') }}</h3>
            </div>

            <!-- Stat 4 -->
            <div class="bg-amber-50/50 border border-amber-100/50 p-5 rounded-3xl">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Kalibrasi</p>
                </div>
                <h3 class="text-3xl font-extrabold text-amber-600 mt-2">{{ number_format($assetStats['perlu_kalibrasi'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Bottom Widgets Grid (Bento) -->
    <div x-show="show" x-transition.delay.400ms.duration.500ms.opacity.translate.y.20px class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        
        <!-- Transaction Widget -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 flex flex-col">
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                Transaksi Terakhir
            </h3>
            <div class="flex-1 overflow-y-auto pr-2 space-y-3 max-h-[300px]">
                @forelse($recentTransactions as $trx)
                <div class="flex justify-between items-center p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $trx->item ? $trx->item->nama_barang : 'Barang Dihapus' }}</p>
                        <p class="text-[10px] font-medium text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</p>
                    </div>
                    @if($trx->jenis_transaksi == 'masuk')
                        <span class="font-bold text-teal-600 text-sm bg-teal-50 px-3 py-1 rounded-xl">+{{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                    @else
                        <span class="font-bold text-rose-600 text-sm bg-rose-50 px-3 py-1 rounded-xl">-{{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                    @endif
                </div>
                @empty
                <div class="py-10 text-center text-slate-400 font-medium text-sm">Belum ada transaksi.</div>
                @endforelse
            </div>
        </div>

        <!-- Reminders Widget -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 flex flex-col">
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                Notifikasi Sistem
            </h3>
            <div class="flex-1 overflow-y-auto pr-2 space-y-3 max-h-[300px]">
                
                @foreach($lowStockItems as $item)
                <div class="p-3 rounded-2xl bg-rose-50/50 border border-rose-100 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest block mb-0.5">Stok Menipis</span>
                        <p class="text-sm font-bold text-slate-800">{{ $item->nama_barang }}</p>
                    </div>
                    <span class="text-lg font-extrabold text-rose-600">{{ number_format($item->stok_sekarang, 0, ',', '.') }}</span>
                </div>
                @endforeach

                @foreach($calibrationReminders as $asset)
                <div class="p-3 rounded-2xl bg-amber-50/50 border border-amber-100 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest block mb-0.5">Jadwal Kalibrasi</span>
                        <p class="text-sm font-bold text-slate-800 truncate max-w-[120px]">{{ $asset->name }}</p>
                    </div>
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-1 rounded-lg">{{ \Carbon\Carbon::parse($asset->next_calibration)->translatedFormat('d M') }}</span>
                </div>
                @endforeach

                @if(count($lowStockItems) === 0 && count($calibrationReminders) === 0)
                <div class="py-10 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-teal-50 rounded-full flex items-center justify-center text-teal-500 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-600">Semua Terkendali</p>
                    <p class="text-xs text-slate-400 font-medium mt-1">Tidak ada peringatan stok atau kalibrasi.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Charts Donut Widget -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-6 flex flex-col justify-center xl:col-span-1 lg:col-span-2">
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </span>
                Distribusi Kategori Aset
            </h3>
            <div id="assetCategoryChart" class="w-full flex justify-center mt-2 flex-1 items-center min-h-[200px]"></div>
        </div>

    </div>
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
        if(num >= 1000000000000) {
            return (num / 1000000000000).toFixed(1).replace(/\.0$/, '') + " T";
        } else if(num >= 1000000000) {
            return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + " M";
        } else if(num >= 1000000) {
            return (num / 1000000).toFixed(1).replace(/\.0$/, '') + " jt";
        } else if(num >= 1000) {
            return (num / 1000).toFixed(1).replace(/\.0$/, '') + " rb";
        }
        return num.toString();
    };

    let chart;
    
    function updateChart(category) {
        document.getElementById('chartTitle').innerText = 'Tren Mutasi: ' + categoryNames[category];
        
        document.querySelectorAll('.category-card').forEach(el => {
            el.classList.remove('ring-4', 'ring-teal-500/20', 'bg-teal-50/30', 'scale-[1.02]');
            el.querySelector('.transform').classList.remove('scale-x-100');
        });
        const activeCard = document.getElementById('card-' + category);
        activeCard.classList.add('ring-4', 'ring-teal-500/20', 'bg-teal-50/30', 'scale-[1.02]');
        activeCard.querySelector('.transform').classList.add('scale-x-100');

        const masukData = allChartData[category].masuk;
        const keluarData = allChartData[category].keluar;
        const newMax = chartMaxData[category] || {{ $chartMax }};

        chart.updateOptions({
            yaxis: {
                max: newMax,
                tickAmount: 5,
                labels: { formatter: yAxisFormatter }
            }
        });

        chart.updateSeries([
            { name: 'Masuk (Rp)', data: masukData },
            { name: 'Keluar (Rp)', data: keluarData }
        ]);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [
                { name: 'Masuk (Rp)', data: [0,0,0,0,0,0] },
                { name: 'Keluar (Rp)', data: [0,0,0,0,0,0] }
            ],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#0d9488', '#e11d48'], // Teal 600, Rose 600
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: chartLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
            },
            yaxis: {
                min: 0,
                max: {{ $chartMax ?? 'undefined' }},
                tickAmount: 5,
                labels: {
                    formatter: yAxisFormatter,
                    style: { colors: '#94a3b8', fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value));
                    }
                }
            }
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
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                },
                labels: categoryLabels,
                colors: ['#0ea5e9', '#0d9488', '#f59e0b', '#e11d48', '#8b5cf6', '#64748b', '#ec4899'],
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 700, color: '#64748b' },
                                value: { fontSize: '20px', fontWeight: 800, color: '#0f172a' }
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
            document.querySelector("#assetCategoryChart").innerHTML = '<p class="text-xs text-slate-400 font-medium text-center w-full">Belum ada data aset</p>';
        }
    });
</script>
@endsection
