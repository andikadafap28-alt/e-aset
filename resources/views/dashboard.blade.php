@extends('layouts.app')

@section('header_title', 'Dashboard RAKSA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-8 text-white shadow-lg shadow-indigo-600/20 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">Selamat datang di RAKSA</h1>
            <p class="text-indigo-100 max-w-xl">Respons Akurat Kelola Seluruh Aset. Sistem manajemen logistik terintegrasi untuk Puskesmas Mantup. Pantau pergerakan aset Anda secara real-time.</p>
        </div>
        <!-- Decorative bg -->
        <div class="absolute right-0 top-0 w-64 h-full bg-white opacity-5 transform skew-x-12 -mr-16"></div>
        <div class="absolute right-32 top-0 w-32 h-full bg-white opacity-5 transform skew-x-12"></div>
    </div>

    <!-- Quick Stats (Dynamic Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($kategoriList as $key => $kat)
        <div onclick="updateChart('{{ $key }}')" class="cursor-pointer bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group category-card" id="card-{{ $key }}">
            <!-- Decorative accent line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-{{ $kat['icon'] }}-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 transition-active"></div>
            
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $kat['label'] }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($kat['jenis'], 0, ',', '.') }} <span class="text-sm font-medium text-slate-500">Jenis</span></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-{{ $kat['icon'] }}-50 flex items-center justify-center text-{{ $kat['icon'] }}-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <p class="text-sm text-slate-500">Total <span class="font-bold text-slate-700">{{ number_format($kat['total'], 0, ',', '.') }}</span> item aset.</p>
        </div>
        @endforeach
    </div>

    <!-- Asset Stats (Glassmorphism) -->
    <div class="mt-8 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Ringkasan Manajemen Aset Tetap
            </h3>
            <a href="{{ route('laporan.aset.pdf') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
        
        <!-- Financial Metrics (New) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl">
                <p class="text-xs text-indigo-600 font-semibold uppercase">Total Nilai Perolehan</p>
                <h4 class="text-xl font-bold text-indigo-900 mt-1">Rp {{ number_format($assetStats['total_purchase'], 0, ',', '.') }}</h4>
            </div>
            <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl">
                <p class="text-xs text-rose-600 font-semibold uppercase">Total Akumulasi Penyusutan</p>
                <h4 class="text-xl font-bold text-rose-900 mt-1">Rp {{ number_format($assetStats['total_depreciation'], 0, ',', '.') }}</h4>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                <p class="text-xs text-emerald-600 font-semibold uppercase">Total Nilai Buku Saat Ini</p>
                <h4 class="text-xl font-bold text-emerald-900 mt-1">Rp {{ number_format($assetStats['total_book_value'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left: 4 Stat Cards -->
            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white/60 backdrop-blur-md p-5 rounded-2xl border border-white/80 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider relative z-10">Total Aset</p>
                    <div class="flex items-end gap-3 mt-1 relative z-10">
                        <h3 class="text-3xl font-bold text-blue-600">{{ number_format($assetStats['total'], 0, ',', '.') }}</h3>
                        <div class="text-[11px] text-slate-500 pb-1 flex flex-col">
                            <span>Aktif: <span class="font-bold text-slate-700">{{ number_format($assetStats['aktif'], 0, ',', '.') }}</span></span>
                            <span>Dihapus: <span class="font-bold text-rose-600">{{ number_format($assetStats['disposed'], 0, ',', '.') }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="bg-white/60 backdrop-blur-md p-5 rounded-2xl border border-white/80 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider relative z-10">Kondisi Baik</p>
                    <h3 class="text-3xl font-bold text-emerald-600 mt-1 relative z-10">{{ number_format($assetStats['baik'], 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white/60 backdrop-blur-md p-5 rounded-2xl border border-white/80 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider relative z-10">Rusak</p>
                    <h3 class="text-3xl font-bold text-rose-600 mt-1 relative z-10">{{ number_format($assetStats['rusak'], 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white/60 backdrop-blur-md p-5 rounded-2xl border border-white/80 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider relative z-10">Perlu Kalibrasi</p>
                    <h3 class="text-3xl font-bold text-amber-600 mt-1 relative z-10">{{ number_format($assetStats['perlu_kalibrasi'], 0, ',', '.') }}</h3>
                </div>
            </div>
            <!-- Right: Doughnut Charts -->
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-center items-center h-full">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider w-full text-left mb-2">Kondisi Aset</h4>
                    <div id="assetConditionChart" class="w-full flex justify-center mt-2"></div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-center items-center h-full">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider w-full text-left mb-2">Kategori Aset</h4>
                    <div id="assetCategoryChart" class="w-full flex justify-center mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Financials -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-6" id="chartTitle">Tren Mutasi - Pilih Kategori</h3>
            <p class="text-sm text-slate-500 mb-4">Klik salah satu kartu kategori di atas untuk melihat rincian grafik pemasukan dan pengeluaran.</p>
            <div id="expenseChart" class="w-full h-80"></div>
        </div>

        <!-- Right Column: Financial Summary -->
        <div class="space-y-6">
            <!-- Financial Card 1 -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Nilai Mutasi Bulan Ini</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-500 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Barang Masuk (Aset)
                        </p>
                        <p class="text-xl font-bold text-slate-900">Rp {{ number_format($masukBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="border-t border-slate-100"></div>
                    <div>
                        <p class="text-sm text-slate-500 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Barang Keluar (Beban)
                        </p>
                        <p class="text-xl font-bold text-slate-900">Rp {{ number_format($keluarBulanIni, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Financial Card 2 -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 opacity-90">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Nilai Mutasi Bulan Lalu</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-500 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Barang Masuk (Aset)
                        </p>
                        <p class="text-xl font-bold text-slate-900">Rp {{ number_format($masukBulanLalu, 0, ',', '.') }}</p>
                    </div>
                    <div class="border-t border-slate-100"></div>
                    <div>
                        <p class="text-sm text-slate-500 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Barang Keluar (Beban)
                        </p>
                        <p class="text-xl font-bold text-slate-900">Rp {{ number_format($keluarBulanLalu, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Widgets: Recent Transactions & Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        
        <!-- Left Widget: Riwayat Transaksi Terakhir -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Transaksi Terakhir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-5">
                                <p class="font-medium text-slate-900">{{ $trx->item ? $trx->item->nama_barang : 'Barang Dihapus' }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="py-3 px-5 text-right">
                                @if($trx->jenis_transaksi == 'masuk')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                        +{{ number_format($trx->jumlah, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                        -{{ number_format($trx->jumlah, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-6 px-5 text-center text-slate-500 text-sm">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Widget: Peringatan Stok Kritis -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Peringatan Stok Kritis
                </h3>
            </div>
            <div class="p-5 space-y-4">
                @forelse($lowStockItems as $item)
                @php
                    // Hitung persentase untuk progress bar (misal batas aman adalah 10)
                    $percentage = ($item->stok_sekarang / 10) * 100;
                    if($percentage < 0) $percentage = 0;
                    if($percentage > 100) $percentage = 100;
                @endphp
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-sm font-medium text-slate-800">{{ $item->nama_barang }}</span>
                        <span class="text-xs font-bold text-rose-600">{{ number_format($item->stok_sekarang, 0, ',', '.') }} {{ $item->satuan }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <div class="py-4 text-center">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm text-slate-500">Semua stok barang dalam kondisi aman (≥ 10).</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Widgets: Reminders (Kalibrasi, Servis, ED) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        
        <!-- Widget: Peringatan Kalibrasi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Jadwal Kalibrasi
                </h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100">
                    @forelse($calibrationReminders as $asset)
                    <li class="p-4 hover:bg-slate-50 transition-colors">
                        <a href="{{ route('aset.show', $asset->id) }}" class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $asset->name }}</p>
                                <p class="text-xs text-slate-500">{{ $asset->asset_code }} - {{ $asset->location }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ \Carbon\Carbon::parse($asset->next_calibration)->isPast() ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ \Carbon\Carbon::parse($asset->next_calibration)->translatedFormat('d M Y') }}
                            </span>
                        </a>
                    </li>
                    @empty
                    <li class="p-6 text-center text-slate-500 text-sm">Tidak ada jadwal kalibrasi dalam 30 hari kedepan.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Widget: Peringatan Servis -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Jadwal Servis Berkala
                </h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100">
                    @forelse($serviceReminders as $asset)
                    <li class="p-4 hover:bg-slate-50 transition-colors">
                        <a href="{{ route('aset.show', $asset->id) }}" class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $asset->name }}</p>
                                <p class="text-xs text-slate-500">{{ $asset->asset_code }} - {{ $asset->location }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ \Carbon\Carbon::parse($asset->next_service)->isPast() ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ \Carbon\Carbon::parse($asset->next_service)->translatedFormat('d M Y') }}
                            </span>
                        </a>
                    </li>
                    @empty
                    <li class="p-6 text-center text-slate-500 text-sm">Tidak ada jadwal servis dalam 30 hari kedepan.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Widget: Peringatan Obat/Vaksin Kedaluwarsa -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Akan Kedaluwarsa (ED)
                </h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                    @forelse($expiryReminders as $trx)
                    <li class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $trx->item ? $trx->item->nama_barang : 'Barang Terhapus' }}</p>
                                <p class="text-xs text-slate-500">Masuk: {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ \Carbon\Carbon::parse($trx->expired_date)->isPast() ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800' }}">
                                ED: {{ \Carbon\Carbon::parse($trx->expired_date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="p-6 text-center text-slate-500 text-sm">Tidak ada barang yang akan kedaluwarsa dalam 90 hari.</li>
                    @endforelse
                </ul>
            </div>
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
        // Update Title
        document.getElementById('chartTitle').innerText = 'Tren Mutasi (6 Bulan Terakhir) - ' + categoryNames[category];
        
        // Highlight Selected Card
        document.querySelectorAll('.category-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-indigo-500', 'bg-indigo-50/50');
            el.querySelector('.transition-active').classList.remove('scale-x-100');
        });
        const activeCard = document.getElementById('card-' + category);
        activeCard.classList.add('ring-2', 'ring-indigo-500', 'bg-indigo-50/50');
        activeCard.querySelector('.transition-active').classList.add('scale-x-100');

        // Update Chart Data
        const masukData = allChartData[category].masuk;
        const keluarData = allChartData[category].keluar;
        const newMax = chartMaxData[category] || {{ $chartMax }};

        chart.updateOptions({
            yaxis: {
                max: newMax,
                tickAmount: 6,
                labels: {
                    formatter: yAxisFormatter
                }
            }
        });

        chart.updateSeries([
            {
                name: 'Pemasukan (Rp)',
                data: masukData
            },
            {
                name: 'Pengeluaran (Rp)',
                data: keluarData
            }
        ]);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [
                { name: 'Pemasukan (Rp)', data: [0,0,0,0,0,0] },
                { name: 'Pengeluaran (Rp)', data: [0,0,0,0,0,0] }
            ],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: ['#10b981', '#f43f5e'], // Emerald for In, Rose for Out
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: chartLabels,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#64748b'
                    }
                }
            },
            yaxis: {
                min: 0,
                max: {{ $chartMax ?? 'undefined' }},
                tickAmount: 6,
                labels: {
                    formatter: yAxisFormatter,
                    style: {
                        colors: '#64748b'
                    }
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
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

        // Initialize with first category
        const firstCategory = Object.keys(categoryNames)[0];
        updateChart(firstCategory);

        // Asset Condition Chart
        const chartKondisi = {!! json_encode($chartKondisi) !!};
        const conditionLabels = Object.keys(chartKondisi);
        const conditionData = Object.values(chartKondisi);
        
        if(conditionData.length > 0) {
            var assetChartOptions = {
                series: conditionData,
                chart: {
                    type: 'donut',
                    height: 180,
                    fontFamily: 'Inter, sans-serif',
                },
                labels: conditionLabels,
                colors: ['#10b981', '#f59e0b', '#f43f5e', '#3b82f6'],
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%'
                        }
                    }
                },
                stroke: {
                    width: 2
                },
                legend: {
                    position: 'right',
                    fontSize: '11px',
                    markers: {
                        width: 8,
                        height: 8
                    }
                }
            };

            var assetChart = new ApexCharts(document.querySelector("#assetConditionChart"), assetChartOptions);
            assetChart.render();
        } else {
            document.querySelector("#assetConditionChart").innerHTML = '<p class="text-xs text-slate-400 text-center py-6 w-full italic">Belum ada data aset</p>';
        }

        // Asset Category Chart
        const chartKategori = {!! json_encode($chartKategori) !!};
        const categoryLabels = Object.keys(chartKategori);
        const categoryData = Object.values(chartKategori);
        
        if(categoryData.length > 0) {
            var categoryChartOptions = {
                series: categoryData,
                chart: {
                    type: 'donut',
                    height: 180,
                    fontFamily: 'Inter, sans-serif',
                },
                labels: categoryLabels,
                colors: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#f43f5e'],
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%'
                        }
                    }
                },
                stroke: {
                    width: 2
                },
                legend: {
                    position: 'bottom',
                    fontSize: '10px',
                    markers: {
                        width: 8,
                        height: 8
                    }
                }
            };

            var categoryChart = new ApexCharts(document.querySelector("#assetCategoryChart"), categoryChartOptions);
            categoryChart.render();
        } else {
            document.querySelector("#assetCategoryChart").innerHTML = '<p class="text-xs text-slate-400 text-center py-6 w-full italic">Belum ada data aset</p>';
        }
    });
</script>
@endsection
