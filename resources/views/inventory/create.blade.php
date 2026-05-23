@extends('layouts.app')

@section('header_title', 'Tambah Master Barang - ' . $nama_kategori)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="/{{ $kategori_besar }}/master" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Master Barang
        </a>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Tambah Master Barang Baru</h2>
        
        <form action="/{{ $kategori_besar }}/tambah" method="POST" class="space-y-6">
            @csrf 

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang *</label>
                <input type="text" name="nama_barang" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Cth: Kertas HVS A4">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori *</label>
                    <select name="kategori" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="Non Medis (ATK)">Non Medis (ATK)</option>
                        <option value="Obat">Obat</option>
                        <option value="Alat Kesehatan">Alat Kesehatan</option>
                        <option value="BHP Medis">BHP Medis</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Satuan *</label>
                    <select name="satuan" id="satuan_select" onchange="toggleSatuanLainnya()" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="Pcs">Pcs</option>
                        <option value="Rim">Rim</option>
                        <option value="Box">Box</option>
                        <option value="Botol">Botol</option>
                        <option value="Unit">Unit</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <input type="text" name="satuan_lainnya" id="satuan_lainnya" class="w-full border border-slate-300 rounded-lg px-3 py-2 mt-2 focus:ring-2 focus:ring-emerald-500 outline-none hidden" placeholder="Ketik satuan lainnya...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Stok Awal *</label>
                    <input type="number" name="stok_sekarang" value="0" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Harga Satuan Awal (Rp)</label>
                    <input type="text" name="harga_satuan" value="0" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Pengadaan</label>
                    <input type="number" name="tahun_pengadaan" value="{{ date('Y') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="/{{ $kategori_besar }}/master" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium shadow-md transition-colors">Simpan Barang</button>
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

    document.addEventListener('DOMContentLoaded', function() {

    });
</script>
@endsection