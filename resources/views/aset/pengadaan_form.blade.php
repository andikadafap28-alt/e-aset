@extends('layouts.app')

@section('header_title', 'Input Pengadaan Aset')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Input Pengadaan Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Tambahkan pengadaan baru yang akan langsung tercatat sebagai BMD</p>
    </div>
    <a href="{{ route('aset.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6">
        <form action="{{ route('aset.pengadaan.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kiri -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kode Barang (KIB) <span class="text-rose-500">*</span></label>
                        <select name="kode_108" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">-- Pilih Kode Barang --</option>
                            @foreach($masterKode108 as $kode)
                                <option value="{{ $kode->kode }}" {{ old('kode_108') == $kode->kode ? 'selected' : '' }}>
                                    {{ $kode->kode }} - {{ $kode->uraian }}
                                </option>
                            @endforeach
                        </select>
                        @error('kode_108') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Alat <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: AC 1 PK / Tensimeter Digital" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        @error('name') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Merek / Spesifikasi</label>
                        <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Contoh: Daikin / Onemed" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        @error('merk') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Aset <span class="text-rose-500">*</span></label>
                            <select name="category_id" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                <option value="">-- Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Perolehan <span class="text-rose-500">*</span></label>
                            <input type="number" name="year_purchased" value="{{ old('year_purchased', date('Y')) }}" required min="1900" max="2100" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            @error('year_purchased') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Kanan -->
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Alat <span class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" required min="1" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <p class="text-[10px] text-slate-500 mt-1">Sistem akan membuat data aset sebanyak jumlah ini.</p>
                            @error('jumlah') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="harga_perolehan" value="{{ old('harga_perolehan') }}" required min="0" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            @error('harga_perolehan') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal BAST <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_bast" value="{{ old('tanggal_bast', date('Y-m-d')) }}" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            @error('tanggal_bast') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi Aset <span class="text-rose-500">*</span></label>
                            <input type="text" name="location" value="{{ old('location', 'Puskesmas Induk') }}" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            @error('location') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Penyedia / Vendor</label>
                        <input type="text" name="penyedia" value="{{ old('penyedia') }}" placeholder="Contoh: CV. Bintang Medika" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        @error('penyedia') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                        <select name="condition" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="Baik" {{ old('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('condition') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('condition') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('condition') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                    Reset Form
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Pengadaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
