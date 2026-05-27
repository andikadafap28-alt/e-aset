@extends('layouts.app')

@section('header_title', 'Edit Master - ' . $nama_kategori)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/master" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Master
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Data Master Barang</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Perbarui informasi barang seperti nama, harga, atau kategori. (Stok tidak dapat diubah di sini).</p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <form action="/{{ $kategori_besar }}/{{ $item->id }}/edit-master" method="POST" class="space-y-6 text-sm">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-xl mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Nama Barang <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_barang" required value="{{ $item->nama_barang }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Kategori</label>
                    <select name="kategori" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                        <option value="Non Medis (ATK)" {{ $item->kategori == 'Non Medis (ATK)' ? 'selected' : '' }}>Non Medis (ATK)</option>
                        <option value="Obat" {{ $item->kategori == 'Obat' ? 'selected' : '' }}>Obat</option>
                        <option value="Alat Kesehatan" {{ $item->kategori == 'Alat Kesehatan' ? 'selected' : '' }}>Alat Kesehatan</option>
                        <option value="BHP Medis" {{ $item->kategori == 'BHP Medis' ? 'selected' : '' }}>BHP Medis</option>
                        <option value="Umum" {{ $item->kategori == 'Umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Satuan</label>
                    <select name="satuan" id="satuan_select" onchange="toggleSatuanLainnya()" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                        <option value="Rim" {{ $item->satuan == 'Rim' ? 'selected' : '' }}>Rim</option>
                        <option value="Pcs" {{ $item->satuan == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="Box" {{ $item->satuan == 'Box' ? 'selected' : '' }}>Box</option>
                        <option value="Botol" {{ $item->satuan == 'Botol' ? 'selected' : '' }}>Botol</option>
                        <option value="Unit" {{ $item->satuan == 'Unit' ? 'selected' : '' }}>Unit</option>
                        <option value="Tablet" {{ $item->satuan == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="Ampul" {{ $item->satuan == 'Ampul' ? 'selected' : '' }}>Ampul</option>
                        @php
                            $standard_satuans = ['Rim', 'Pcs', 'Box', 'Botol', 'Unit', 'Tablet', 'Ampul'];
                            $is_lainnya = !in_array($item->satuan, $standard_satuans) && $item->satuan != '';
                        @endphp
                        <option value="Lainnya" {{ $is_lainnya ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <input type="text" name="satuan_lainnya" id="satuan_lainnya" value="{{ $is_lainnya ? $item->satuan : '' }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 mt-2 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all {{ $is_lainnya ? '' : 'hidden' }}" placeholder="Ketik satuan lainnya...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="text" name="harga_satuan" required value="{{ $item->harga_satuan }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tahun Pengadaan</label>
                    <input type="number" name="tahun_pengadaan" value="{{ $item->tahun_pengadaan }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium shadow-sm shadow-indigo-600/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSatuanLainnya() {
        var satuanSelect = document.getElementById('satuan_select');
        var satuanLainnya = document.getElementById('satuan_lainnya');
        if (satuanSelect.value === 'Lainnya') {
            satuanLainnya.classList.remove('hidden');
            satuanLainnya.setAttribute('required', 'required');
        } else {
            satuanLainnya.classList.add('hidden');
            satuanLainnya.removeAttribute('required');
        }
    }
</script>
@endsection

