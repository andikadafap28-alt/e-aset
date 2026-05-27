@extends('layouts.app')

@section('header_title', 'Detail Aset')

@section('content')
<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-slate-800">{{ $asset->name }}</h2>
            @if($asset->condition === 'Baik')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">Baik</span>
            @elseif($asset->condition === 'Rusak Ringan')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">Rusak Ringan</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">Rusak Berat</span>
            @endif
        </div>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <p class="text-slate-500 text-sm">Kode: <span class="font-semibold">{{ $asset->asset_code }}</span></p>
            @if(!$asset->status_aktif)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white uppercase tracking-wider">Telah Dihapus / Disposed</span>
            @endif
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
        @if($asset->status_aktif)
            <button @click="$dispatch('open-modal-disposal')" class="text-rose-600 hover:text-white border border-rose-200 hover:bg-rose-600 hover:border-rose-600 bg-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Penghapusan Aset
            </button>
        @endif
        <a href="{{ route('aset.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

<!-- Tabs Container -->
<div x-data="{ activeTab: 'tab1' }" class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Tab Navigation -->
    <div class="border-b border-slate-200 flex overflow-x-auto">
        <button @click="activeTab = 'tab1'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab1', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300': activeTab !== 'tab1' }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors focus:outline-none">
            Informasi & Lokasi
        </button>
        <button @click="activeTab = 'tab2'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab2', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300': activeTab !== 'tab2' }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors focus:outline-none">
            Riwayat Pemeliharaan
        </button>
        <button @click="activeTab = 'tab3'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab3', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300': activeTab !== 'tab3' }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors focus:outline-none">
            Mutasi
        </button>
        <button @click="activeTab = 'tab_penyusutan'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab_penyusutan', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300': activeTab !== 'tab_penyusutan' }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors focus:outline-none">
            Penyusutan Nilai
        </button>
        <button @click="activeTab = 'tab4'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab4', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300': activeTab !== 'tab4' }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors focus:outline-none">
            QR Code Label
        </button>

    <!-- Tab Contents -->
    <div class="p-6">
        <!-- Tab 1: Informasi & Lokasi -->
        <div x-show="activeTab === 'tab1'" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Data -->
            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Spesifikasi Aset</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Kategori Master</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ is_object($asset->category) ? $asset->category->nama_kategori : ($asset->getAttribute('category') ?: '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tahun Pengadaan</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ $asset->year_purchased }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tanggal Terakhir Kalibrasi</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">
                            @if($asset->last_calibration)
                                {{ \Carbon\Carbon::parse($asset->last_calibration)->format('d F Y') }}
                            @else
                                <span class="italic text-slate-400">Belum pernah kalibrasi</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Jadwal Kalibrasi Berikutnya</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">
                            @if($asset->next_calibration)
                                {{ \Carbon\Carbon::parse($asset->next_calibration)->format('d F Y') }}
                            @else
                                <span class="italic text-slate-400">Belum dijadwalkan</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Jadwal Servis Berikutnya</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">
                            @if($asset->next_service)
                                {{ \Carbon\Carbon::parse($asset->next_service)->format('d F Y') }}
                            @else
                                <span class="italic text-slate-400">Belum dijadwalkan</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Lokasi Penempatan</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ $asset->location }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Penanggung Jawab Ruangan</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ $asset->penanggung_jawab ?: '-' }}</p>
                    </div>
                    @if($asset->document_link)
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Dokumen Pengadaan</p>
                        <a href="{{ $asset->document_link }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-medium mt-1 inline-block">Lihat Dokumen &rarr;</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Map -->
            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Titik Koordinat (GPS)</h3>
                <div id="map" class="w-full h-64 rounded-xl border border-slate-200 shadow-sm z-10 relative"></div>
                
                @if($asset->latitude && $asset->longitude)
                <div class="mt-4">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $asset->latitude }},{{ $asset->longitude }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Buka di Google Maps
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Tab 2: Riwayat Pemeliharaan -->
        <div x-show="activeTab === 'tab2'" style="display: none;" class="py-12 text-center text-slate-500">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <p>Riwayat belum tersedia.</p>
        </div>

        <!-- Tab 3: Mutasi -->
        <div x-show="activeTab === 'tab3'" style="display: none;" class="p-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                            <th class="py-3 px-5">Tgl Mutasi</th>
                            <th class="py-3 px-5">Lokasi & PJ Lama</th>
                            <th class="py-3 px-5">Lokasi & PJ Baru</th>
                            <th class="py-3 px-5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($asset->mutations()->latest('tanggal_mutasi')->get() as $mutasi)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-5 whitespace-nowrap">{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-5">
                                <div class="text-slate-800">{{ $mutasi->lokasi_lama }}</div>
                                <div class="text-xs text-slate-500">{{ $mutasi->penanggung_jawab_lama ?: 'Tidak ada PJ' }}</div>
                            </td>
                            <td class="py-3 px-5">
                                <div class="text-indigo-700 font-medium flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    {{ $mutasi->lokasi_baru }}
                                </div>
                                <div class="text-xs text-slate-500">{{ $mutasi->penanggung_jawab_baru ?: 'Tidak ada PJ' }}</div>
                            </td>
                            <td class="py-3 px-5 text-slate-600 max-w-xs truncate" title="{{ $mutasi->keterangan }}">
                                {{ $mutasi->keterangan ?: '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                <p>Data mutasi belum tersedia.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Penyusutan -->
        <div x-show="activeTab === 'tab_penyusutan'" style="display: none;" class="space-y-6">
            @if($asset->harga_perolehan)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wider mb-1">Harga Perolehan</p>
                        <p class="text-xl font-bold text-slate-800">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-sky-50/50 p-4 rounded-xl border border-sky-100">
                        <p class="text-xs text-sky-600 font-semibold uppercase tracking-wider mb-1">Umur Ekonomis</p>
                        <p class="text-xl font-bold text-slate-800">{{ is_object($asset->category) ? $asset->category->umur_ekonomis : '-' }} Tahun</p>
                    </div>
                    <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                        <p class="text-xs text-amber-600 font-semibold uppercase tracking-wider mb-1">Akumulasi Penyusutan</p>
                        <p class="text-xl font-bold text-slate-800">Rp {{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wider mb-1">Nilai Buku Saat Ini</p>
                        <p class="text-xl font-bold text-slate-800">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h4 class="font-semibold text-slate-800 mb-4">Informasi Depresiasi (Garis Lurus)</h4>
                    <div class="space-y-3 text-sm text-slate-600">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span>Metode Penyusutan</span>
                            <span class="font-medium text-slate-800">Garis Lurus (Straight Line)</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span>Penyusutan per Tahun</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($asset->annual_depreciation, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span>Tahun Penggunaan</span>
                            <span class="font-medium text-slate-800">{{ max(0, date('Y') - $asset->year_purchased) }} Tahun</span>
                        </div>
                        <div class="flex justify-between pb-2">
                            <span>Nilai Residu Maksimal</span>
                            <span class="font-medium text-slate-800">Rp 1</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-12 text-center text-slate-500 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p>Harga Perolehan belum diatur untuk aset ini.</p>
                    <a href="{{ route('aset.edit', $asset->id) }}" class="inline-block mt-3 text-indigo-600 font-medium hover:underline">Edit Aset</a>
                </div>
            @endif
        </div>

        <!-- Tab 4: QR Code Label -->
        <div x-show="activeTab === 'tab4'" style="display: none;" class="py-12 flex flex-col items-center">
            <!-- Area Cetak Stiker -->
            <div id="area-stiker-label" class="bg-white border-2 border-gray-800 p-4 w-48 h-48 flex flex-col items-center justify-center text-center mx-auto shadow-sm">
                <h4 class="font-bold text-xs mb-2 text-black">RAKSA | PUSKESMAS MANTUP</h4>
                <div class="mb-2">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(url('/item/' . $asset->asset_code)) !!}
                </div>
                <p class="text-[10px] font-bold text-black uppercase">KODE: {{ $asset->asset_code }}</p>
                <p class="text-[9px] text-black leading-tight overflow-hidden text-ellipsis whitespace-nowrap w-full">{{ $asset->name }}</p>
            </div>

            <!-- Tombol Cetak (Luar Area Cetak) -->
            <div class="mt-8">
                <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Stiker Label
                </button>
            </div>
        </div>
    </div>
</div>

@if(!$asset->status_aktif && $asset->disposals->count() > 0)
    <div class="mt-6 bg-rose-50/50 border border-rose-200 rounded-xl p-6">
        <h3 class="text-rose-800 font-bold text-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Data Penghapusan Aset
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-rose-500 font-semibold uppercase tracking-wider mb-1">Tanggal Dihapus</p>
                <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($asset->disposals->last()->tanggal_penghapusan)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-rose-500 font-semibold uppercase tracking-wider mb-1">Alasan</p>
                <p class="text-sm font-medium text-slate-800">{{ $asset->disposals->last()->alasan }}</p>
            </div>
            <div class="sm:col-span-3">
                <p class="text-xs text-rose-500 font-semibold uppercase tracking-wider mb-1">Catatan</p>
                <p class="text-sm text-slate-700 bg-white p-3 rounded-lg border border-rose-100">{{ $asset->disposals->last()->catatan ?: '-' }}</p>
            </div>
            @if($asset->disposals->last()->ba_path)
            <div class="sm:col-span-3">
                <a href="{{ asset('storage/' . $asset->disposals->last()->ba_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Berita Acara (PDF)
                </a>
            </div>
            @endif
        </div>
    </div>
@endif

<!-- Modal Penghapusan Aset -->
@if($asset->status_aktif)
<div x-data="{ showModal: false }" @open-modal-disposal.window="showModal = true" x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm" style="display: none;">
    <div @click.away="showModal = false" class="relative w-full max-w-lg p-6 bg-white rounded-2xl shadow-xl transform transition-all">
        <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Penghapusan Aset (Disposal)</h3>
                <p class="text-xs text-slate-500 mt-1">Status aset akan diubah menjadi tidak aktif.</p>
            </div>
            <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('aset.disposal.store', $asset->id) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Penghapusan <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_penghapusan" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penghapusan <span class="text-rose-500">*</span></label>
                    <select name="alasan" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="Rusak Berat">Rusak Berat / Tidak Bisa Diperbaiki</option>
                        <option value="Dihibahkan">Dihibahkan ke Instansi Lain</option>
                        <option value="Dijual / Dilelang">Dijual / Dilelang</option>
                        <option value="Hilang / Dicuri">Hilang / Dicuri</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan / Detail Kronologi</label>
                    <textarea name="catatan" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Jelaskan secara singkat kronologi kerusakan atau alasan penghapusan..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Proses Penghapusan
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah aset memiliki koordinat
        var hasLocation = {{ $asset->latitude && $asset->longitude ? 'true' : 'false' }};
        
        if (hasLocation) {
            var lat = {{ $asset->latitude ?? 0 }};
            var lng = {{ $asset->longitude ?? 0 }};
            
            var map = L.map('map').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $asset->name }}</b><br>{{ $asset->location }}").openPopup();
            
            // Fix map size when alpine tab changes visibility
            setTimeout(function() { map.invalidateSize(); }, 200);
            
            // Watch for alpine tab changes and re-render map layout
            document.querySelectorAll('button[\\@click]').forEach(button => {
                button.addEventListener('click', () => {
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 50);
                });
            });
        } else {
            document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 bg-slate-50 text-sm">Koordinat GPS belum diatur untuk aset ini.</div>';
        }
    });
</script>

<style>
@media print {
    /* Sembunyikan seluruh elemen halaman */
    body * {
        visibility: hidden;
    }
    /* Tampilkan kembali hanya area stiker label dan isinya */
    #area-stiker-label, #area-stiker-label * {
        visibility: visible;
    }
    #area-stiker-label {
        position: absolute;
        left: 0;
        top: 0;
        width: 2.5in;
        height: 2.5in;
        border: none !important;
        box-shadow: none !important;
        background: white !important;
        color: black !important;
        padding: 10px;
        margin: 0;
    }
    
    /* Hilangkan URL/Date bawaan browser saat print */
    @page { margin: 0; }
}
</style>
@endsection
