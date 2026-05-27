@extends('layouts.app')

@section('header_title', 'Pusat Laporan & Analytics')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pusat Laporan</h2>
        <p class="text-slate-500 text-sm mt-1">Hasilkan laporan inventaris, penyusutan, dan mutasi aset sesuai kebutuhan.</p>
    </div>

    @if(session('error'))
    <div class="bg-rose-50 text-rose-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-rose-200">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 mb-6">
        <a href="{{ route('laporan.rekap') }}" class="group block bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-6 text-white shadow-md hover:shadow-lg transition-all border border-indigo-400/30">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Laporan Rekapitulasi Aset Baru
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">Lihat ringkasan jumlah, kondisi, dan total nilai aset berdasarkan Kategori atau Ruangan.</p>
                </div>
                <div class="bg-white/20 p-2 rounded-full group-hover:bg-white/30 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </div>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('laporan.generate') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Bagian Kiri: Jenis Laporan & Format -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Jenis Laporan</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Pilih Kategori Laporan <span class="text-rose-500">*</span></label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="report_type" value="inventaris" checked class="mt-1 text-teal-600 focus:ring-teal-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Inventaris Utama</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Daftar lengkap aset aktif beserta kondisinya.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="report_type" value="penyusutan" class="mt-1 text-teal-600 focus:ring-teal-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Penyusutan (Depreciation)</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Rincian harga perolehan, akumulasi penyusutan, dan nilai buku aset.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="report_type" value="disposal" class="mt-1 text-teal-600 focus:ring-teal-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Penghapusan (Disposal)</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Daftar aset yang telah dihapus beserta alasan dan tanggalnya.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Format Ekspor <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50 hover:border-rose-200 transition-colors">
                                <input type="radio" name="export_format" value="pdf" checked class="text-rose-600 focus:ring-rose-500">
                                <span class="text-sm font-semibold text-slate-700">PDF</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors">
                                <input type="radio" name="export_format" value="excel" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-slate-700">Excel (.xlsx)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kanan: Filter Data -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Filter Data (Opsional)</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Aset</label>
                        <select name="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            <option value="all">-- Semua Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kondisi Aset</label>
                        <select name="condition" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            <option value="all">-- Semua Kondisi --</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Abaikan jika memilih Laporan Penghapusan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Rentang Tahun Pengadaan</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="year_start" placeholder="Tahun Awal (Mis: 2018)" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            <span class="text-slate-500">-</span>
                            <input type="number" name="year_end" placeholder="Tahun Akhir (Mis: {{ date('Y') }})" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="reset" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reset Filter</button>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate Laporan Aset
                </button>
            </div>
        </form>
    </div>

    <!-- Buku Persediaan Global (Logistik) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Buku Persediaan Gudang (Semua Kategori)
            </h3>
            <p class="text-slate-500 text-sm mt-1">Cetak laporan mutasi dan rekap barang persediaan/logistik untuk berbagai rentang bulan sekaligus.</p>
        </div>

        <form action="{{ route('laporan.persediaan-global.export') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Kiri: Rentang Waktu -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Rentang Periode <span class="text-rose-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <input type="month" name="bulan_awal" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                            <span class="text-slate-400 font-medium">s/d</span>
                            <input type="month" name="bulan_akhir" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Format Ekspor <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50 hover:border-rose-200 transition-colors has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50">
                                <input type="radio" name="export_format" value="pdf" checked class="text-rose-600 focus:ring-rose-500">
                                <span class="text-sm font-semibold text-slate-700">PDF Document</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="export_format" value="excel" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-slate-700">Excel (.xlsx)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Jenis Laporan -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Jenis Laporan <span class="text-rose-500">*</span></label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50/50">
                                <input type="radio" name="jenis_laporan" value="internal" checked class="mt-1 text-teal-600 focus:ring-teal-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Internal Puskesmas</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Mencatat seluruh mutasi barang fisik, termasuk transaksi yang statusnya masih hutang.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50/50">
                                <input type="radio" name="jenis_laporan" value="dinas" class="mt-1 text-teal-600 focus:ring-teal-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Dinas (SPJ)</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Hanya menampilkan transaksi yang sudah lunas (SPJ). Barang hutang akan diabaikan.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate Laporan Persediaan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat & Aktivitas Aset Tetap -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Laporan Riwayat & Aktivitas Aset Tetap
            </h3>
            <p class="text-slate-500 text-sm mt-1">Lacak seluruh penambahan aset baru, proses kalibrasi/servis, hingga penghapusan aset dalam satu buku.</p>
        </div>

        <form action="{{ route('laporan.aktivitas-aset.export') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Kiri: Rentang Waktu -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Rentang Periode <span class="text-rose-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <input type="month" name="bulan_awal" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
                            <span class="text-slate-400 font-medium">s/d</span>
                            <input type="month" name="bulan_akhir" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
                        </div>
                    </div>
                </div>

                <!-- Kanan: Format Ekspor -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Format Ekspor <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50 hover:border-rose-200 transition-colors has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50">
                                <input type="radio" name="export_format" value="pdf" checked class="text-rose-600 focus:ring-rose-500">
                                <span class="text-sm font-semibold text-slate-700">PDF Document</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="export_format" value="excel" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-slate-700">Excel (.xlsx)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate Riwayat Aset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

