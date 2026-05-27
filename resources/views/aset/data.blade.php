@extends('layouts.app')

@section('header_title', 'Manajemen Aset')

@section('content')
<div class="mb-6 flex justify-between items-center" x-data>
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Master Data Rekapitulasi Aset Puskesmas</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola seluruh data aset puskesmas</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button @click="$dispatch('open-modal-kode108')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import Kode 108
        </button>
        <a href="{{ route('aset.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Aset
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-emerald-200">
    {{ session('success') }}
</div>
@endif

<div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nomor</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Aset</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lokasi</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kondisi</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($assets as $index => $asset)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-5 text-sm text-slate-600">{{ $index + 1 }}</td>
                    <td class="py-3 px-5 text-sm font-medium text-slate-700">{{ $asset->asset_code }}</td>
                    <td class="py-3 px-5 text-sm font-semibold text-slate-800">
                        {{ $asset->name }}
                        @if(!$asset->status_aktif)
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wider">Disposed</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm text-slate-500">{{ is_object($asset->category) ? $asset->category->nama_kategori : ($asset->getAttribute('category') ?: '-') }}</td>
                    <td class="py-3 px-5 text-sm text-slate-600">{{ $asset->location }}</td>
                    <td class="py-3 px-5">
                        @if($asset->condition === 'Baik')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Baik
                            </span>
                        @elseif($asset->condition === 'Rusak Ringan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                Rusak Ringan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                Rusak Berat
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('aset.show', $asset->id) }}" class="text-sky-600 hover:text-sky-800 font-medium text-sm transition-colors px-2 py-1 bg-sky-50 hover:bg-sky-100 rounded">Detail/Edit</a>
                            
                            <form action="{{ route('aset.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium text-sm transition-colors px-2 py-1 bg-rose-50 hover:bg-rose-100 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data aset. <a href="{{ route('aset.create') }}" class="text-indigo-600 hover:underline">Tambahkan sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal Import Kode 108 -->
    <div x-data="{ showKode108Modal: false }" @open-modal-kode108.window="showKode108Modal = true" x-show="showKode108Modal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showKode108Modal = false" class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl transform transition-all">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Upload Master Kode 108</h3>
                <button @click="showKode108Modal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('aset.import-kode-108') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Excel (.xlsx) <span class="text-rose-500">*</span></label>
                    <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-colors cursor-pointer border border-slate-200 rounded-lg">
                    
                    <div class="mt-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <p class="text-xs text-slate-600 mb-1">Pastikan file memiliki 2 kolom (boleh tanpa header):</p>
                        <ul class="text-xs text-slate-500 list-disc list-inside space-y-1">
                            <li>Kolom 1: Kode (Contoh: 1.3.2.05.01)</li>
                            <li>Kolom 2: Uraian/Nama</li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showKode108Modal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm transition-all flex items-center gap-2">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endsection
