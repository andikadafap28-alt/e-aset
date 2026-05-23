@extends('layouts.app')

@section('header_title', 'Master Kategori Aset')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ showModal: false, isEdit: false, formAction: '', categoryId: '', namaKategori: '', umurEkonomis: '' }">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Master Kategori Aset</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola kategori aset dan umur ekonomis untuk penyusutan (Depreciation).</p>
        </div>
        <button @click="showModal = true; isEdit = false; formAction = '{{ route('aset.categories.store') }}'; namaKategori = ''; umurEkonomis = ''" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kategori
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-emerald-200">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-rose-50 text-rose-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-rose-200">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Umur Ekonomis</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Aset Terkait</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                @forelse($categories as $index => $category)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-5">{{ $index + 1 }}</td>
                    <td class="py-3 px-5 font-semibold text-slate-800">{{ $category->nama_kategori }}</td>
                    <td class="py-3 px-5">{{ $category->umur_ekonomis }} Tahun</td>
                    <td class="py-3 px-5">
                        <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-medium">{{ $category->assets_count }} Aset</span>
                    </td>
                    <td class="py-3 px-5 text-right flex justify-end gap-2">
                        <button @click="showModal = true; isEdit = true; formAction = '/aset/categories/{{ $category->id }}'; namaKategori = '{{ $category->nama_kategori }}'; umurEkonomis = '{{ $category->umur_ekonomis }}'" class="text-indigo-600 hover:text-indigo-800 font-medium px-2 py-1 bg-indigo-50 hover:bg-indigo-100 rounded transition-colors">Edit</button>
                        <form action="{{ route('aset.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium px-2 py-1 bg-rose-50 hover:bg-rose-100 rounded transition-colors">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data kategori aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showModal = false" class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 mb-5" x-text="isEdit ? 'Edit Kategori Aset' : 'Tambah Kategori Aset'"></h3>
            
            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_kategori" x-model="namaKategori" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Cth: Alat Elektronik">
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Umur Ekonomis (Tahun) <span class="text-rose-500">*</span></label>
                    <input type="number" name="umur_ekonomis" x-model="umurEkonomis" required min="1" max="100" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Cth: 5">
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm" x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Kategori'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
