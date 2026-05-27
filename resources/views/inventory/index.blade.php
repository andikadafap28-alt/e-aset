@extends('layouts.app')

@section('header_title', 'Manajemen ' . $nama_kategori)

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showImportModal: false }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Barang {{ $nama_kategori }}</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola data master barang dan pantau mutasi aset secara real-time.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            
            <!-- Menu Dropdown -->
            <div x-data="{ open: false }" class="relative z-50">
                <button @click="open = !open" @click.away="open = false" class="text-sm font-medium bg-white hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg border border-slate-200 flex items-center gap-2 shadow-sm transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    Menu Lainnya
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden z-50" style="display: none;">
                    <a href="/{{ $kategori_besar }}/master" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Data Master Barang
                    </a>
                    <a href="/{{ $kategori_besar }}/hutang" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Manajemen Hutang (SPJ)
                    </a>
                    <a href="/{{ $kategori_besar }}/opname" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Stock Opname
                    </a>
                    <div class="border-t border-slate-100"></div>
                    <button @click="open = false; showImportModal = true" class="w-full flex items-center gap-2 px-4 py-3 text-sm font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Import Data Excel
                    </button>
                    <a href="/{{ $kategori_besar }}/export" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-emerald-700 hover:bg-emerald-50 transition-colors border-t border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4-4m4 4V4"></path></svg>
                        Export Laporan Excel
                    </a>
                </div>
            </div>
            
            <a href="/{{ $kategori_besar }}/transaksi/tambah" class="text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg shadow-sm shadow-emerald-600/20 flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Catat Transaksi Mutasi
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif
    
    @if($errors->any())
    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-sm font-medium">{{ $errors->first() }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <th class="py-4 px-6">Kode Barang</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Harga Satuan</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Stok Tersedia</th>
                        <th class="py-4 px-6">Satuan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr onclick="window.location='/{{ $kategori_besar }}/{{ $item->id }}/detail'" class="hover:bg-slate-50/50 transition-colors cursor-pointer group">
                        <td class="py-4 px-6 text-slate-500 font-medium">
                            <div class="flex items-center gap-2">
                                <span>{{ $item->kode_barang ?? '-' }}</span>
                                @if($item->kode_barang)
                                <a href="/{{ $kategori_besar }}/{{ $item->id }}/label" target="_blank" onclick="event.stopPropagation()" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Cetak Label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $item->nama_barang }}</td>
                        <td class="py-4 px-6 text-slate-600 font-medium">Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <span class="bg-slate-100 border border-slate-200 text-slate-700 px-2.5 py-1 rounded-md text-xs font-medium">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-bold {{ $item->stok_sekarang < 10 ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ number_format($item->stok_sekarang, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-500">{{ $item->satuan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 px-6 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900">Belum ada data barang</h3>
                            <p class="text-sm text-slate-500 mt-1">Silakan catat transaksi baru untuk memulai pencatatan aset di kategori ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div x-show="showImportModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showImportModal = false" class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl transform transition-all">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Import Data Barang Excel</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="/{{ $kategori_besar }}/import" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bulan Import <span class="text-rose-500">*</span></label>
                    <input type="month" name="bulan_import" required value="{{ date('Y-m') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-700">
                    <p class="text-xs text-slate-500 mt-1">Digunakan sebagai tanggal transaksi untuk pencatatan stok.</p>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Upload File Excel (.xlsx, .csv) <span class="text-rose-500">*</span></label>
                    <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors cursor-pointer border border-slate-200 rounded-lg">
                    
                    <div class="mt-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <p class="text-xs font-semibold text-slate-700 mb-2">Format Header Kolom (Baris 1):</p>
                        <ul class="text-xs text-slate-500 list-disc list-inside space-y-1">
                            <li><strong>Nama Barang</strong> (wajib)</li>
                            <li><strong>Satuan</strong> (opsional)</li>
                            <li><strong>Harga Satuan</strong> (wajib, angka saja)</li>
                            <li><strong>Stok Awal</strong> (opsional)</li>
                            <li><strong>Penerimaan</strong> (opsional)</li>
                            <li><strong>Pemakaian</strong> (opsional)</li>
                            <li><strong>Total Stok Akhir</strong> (wajib)</li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm shadow-blue-600/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection