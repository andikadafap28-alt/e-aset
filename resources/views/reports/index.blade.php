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
                                <input type="radio" name="report_type" value="inventaris" checked class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Inventaris Utama</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Daftar lengkap aset aktif beserta kondisinya.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="report_type" value="penyusutan" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Laporan Penyusutan (Depreciation)</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Rincian harga perolehan, akumulasi penyusutan, dan nilai buku aset.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="report_type" value="disposal" class="mt-1 text-indigo-600 focus:ring-indigo-500">
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
                        <select name="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">-- Semua Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kondisi Aset</label>
                        <select name="condition" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                            <input type="number" name="year_start" placeholder="Tahun Awal (Mis: 2018)" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <span class="text-slate-500">-</span>
                            <input type="number" name="year_end" placeholder="Tahun Akhir (Mis: {{ date('Y') }})" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="reset" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reset Filter</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
