@extends('layouts.app')

@section('header_title', 'Data KIB (BMD)')

@section('content')
<div class="mb-6 flex justify-between items-center" x-data>
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Daftar Barang Milik Daerah (BMD)</h2>
        <p class="text-slate-500 text-sm mt-1">Sesuai klasifikasi Kartu Inventaris Barang</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button @click="$dispatch('open-modal-import')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import KIB (Excel)
        </button>
        <a href="{{ route('aset.bmd.template') }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template
        </a>
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


<div class="mb-4">
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <a href="{{ route('aset.bmd.index') }}" class="{{ !request('kib') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                Semua Aset
            </a>
            <a href="{{ route('aset.bmd.index', ['kib' => '1.3.1']) }}" class="{{ request('kib') == '1.3.1' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                KIB A (Tanah)
            </a>
            <a href="{{ route('aset.bmd.index', ['kib' => '1.3.2']) }}" class="{{ request('kib') == '1.3.2' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                KIB B (Peralatan)
            </a>
            <a href="{{ route('aset.bmd.index', ['kib' => '1.3.3']) }}" class="{{ request('kib') == '1.3.3' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                KIB C (Gedung)
            </a>
            <a href="{{ route('aset.bmd.index', ['kib' => '1.3.4']) }}" class="{{ request('kib') == '1.3.4' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                KIB D (Jalan/Jaringan)
            </a>
            <a href="{{ route('aset.bmd.index', ['kib' => '1.3.5']) }}" class="{{ request('kib') == '1.3.5' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                KIB E (Aset Lainnya)
            </a>
        </nav>
    </div>
</div>

<form action="{{ route('aset.bulk-action') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan aksi massal pada item yang dipilih?');">
    @csrf
    <div class="mb-4 flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200 shadow-sm">
        <span class="text-sm font-semibold text-slate-700">Aksi Massal:</span>
        <select name="bulk_action" required class="text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8">
            <option value="">-- Pilih Aksi --</option>
            <option value="delete">Hapus Aset Terpilih</option>
            <option value="set_baik">Ubah Kondisi: Baik</option>
            <option value="set_rusak_ringan">Ubah Kondisi: Rusak Ringan</option>
            <option value="set_rusak_berat">Ubah Kondisi: Rusak Berat</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Terapkan</button>
    </div>

<div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">
                        <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    </th>
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
                    <td class="py-3 px-5">
                        <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="asset-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    </td>
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
                    <td colspan="8" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data aset. <a href="{{ route('aset.create') }}" class="text-indigo-600 hover:underline">Tambahkan sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</form>

    <!-- Modal Import KIB -->
    <div x-data="{ showImportModal: false }" @open-modal-import.window="showImportModal = true" x-show="showImportModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showImportModal = false" class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl transform transition-all">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Import File Excel KIB (BMD)</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('aset.bmd.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="source" value="BMD">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Excel KIB (.xlsx) <span class="text-rose-500">*</span></label>
                    <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors cursor-pointer border border-slate-200 rounded-lg">
                    
                    <div class="mt-4 bg-amber-50 p-4 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-800 mb-1 font-semibold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Panduan Format:</p>
                        <p class="text-[11px] text-amber-700 mt-1">Sistem akan otomatis mendeteksi kolom berdasarkan header: <b>Kode Barang, NIBAR, Nomor Register, Nama Barang / Spesifikasi, Lokasi, Merek, Harga, Tanggal Perolehan</b>.</p>
                        <p class="text-[11px] text-amber-700 mt-1">Pastikan header KIB tersebut berada di satu baris yang sama. Sistem akan mulai membaca data setelah menemukan baris header tersebut.</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm transition-all flex items-center gap-2">Import Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.asset-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endsection
