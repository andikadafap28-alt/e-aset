@extends('layouts.app')

@section('header_title', 'Catat Transaksi - ' . $nama_kategori)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/items" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar {{ $nama_kategori }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Catat Transaksi Mutasi</h2>
        <p class="text-sm text-slate-500 mt-1">Catat barang masuk atau keluar untuk memperbarui stok secara otomatis.</p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <form action="/{{ $kategori_besar }}/transaksi/tambah" method="POST" class="space-y-6 text-sm">
            @csrf

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-xl mb-6">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Gagal Menyimpan Transaksi
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_transaksi" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jenis Transaksi <span class="text-rose-500">*</span></label>
                    <select name="jenis_transaksi" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                    </select>
                </div>
            </div>

            <!-- Expired Date Check (Hanya relevan untuk Barang Masuk) -->
            <div id="expired_date_container" class="grid grid-cols-1 md:grid-cols-2 gap-6 transition-opacity duration-200">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Kadaluarsa (ED) <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="date" name="expired_date" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                    <p class="text-xs text-slate-500 mt-1">Hanya diisi untuk kategori Obat / Vaksin saat barang masuk.</p>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Pilih Barang <span class="text-rose-500">*</span></label>
                <select name="item_id" id="item_id" required onchange="toggleNewItemFields()" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                    <option value="">-- Pilih dari Master Barang --</option>
                    <option value="new" class="font-bold text-emerald-600">+ Input Barang Baru</option>
                    
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-harga="{{ $item->harga_satuan }}">
                            {{ $item->nama_barang }} (Sisa: {{ $item->stok_sekarang }} {{ $item->satuan }}) - Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fields for New Item -->
            <div id="new_item_fields" class="hidden p-6 bg-slate-50 border border-slate-200 rounded-xl space-y-5 mt-4">
                <div class="flex items-center gap-2 text-emerald-600 font-bold mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Data Barang Baru
                </div>
                
                <div>
                    <label class="block font-medium text-slate-700 mb-1.5">Nama Barang Baru <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_barang_manual" id="nama_barang_manual" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                </div>



                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1.5">Kategori</label>
                        <select name="kategori" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                            <option value="Non Medis (ATK)">Non Medis (ATK)</option>
                            <option value="Obat">Obat</option>
                            <option value="Alat Kesehatan">Alat Kesehatan</option>
                            <option value="BHP Medis">BHP Medis</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-1.5">Satuan</label>
                        <select name="satuan" id="satuan_select" onchange="toggleSatuanLainnya()" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                            <option value="Rim">Rim</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Box">Box</option>
                            <option value="Botol">Botol</option>
                            <option value="Unit">Unit</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Ampul">Ampul</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <input type="text" name="satuan_lainnya" id="satuan_lainnya" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 mt-2 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all hidden" placeholder="Ketik satuan lainnya...">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jumlah Mutasi <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" min="1" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="text" name="harga_satuan" id="harga_satuan" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                    <p class="text-xs text-slate-500 mt-1">Sistem akan otomatis membuat profil barang baru jika Anda memasukkan harga yang berbeda dari master.</p>
                </div>
            </div>

            <!-- Hutang Checkbox (Hanya relevan untuk Barang Masuk) -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-3 transition-opacity duration-200">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="status_hutang" id="hutang" class="w-4 h-4 text-emerald-600 bg-white border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                </div>
                <label for="hutang" class="cursor-pointer">
                    <span class="block font-semibold text-slate-800">Tandai sebagai Pengadaan Hutang</span>
                    <span class="block text-slate-500 mt-0.5 text-xs">Centang opsi ini jika transaksi ini belum dilunasi (belum di-SPJ-kan). Anda bisa mengonversi statusnya di menu Manajemen Hutang nanti.</span>
                </label>
            </div>

            @if($kategori_besar === 'pengadaan')
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Keterangan / Metode Pengadaan</label>
                <select name="keterangan" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                    <option value="Pengadaan Langsung">Pengadaan Langsung</option>
                    <option value="E-Purchasing">E-Purchasing</option>
                </select>
            </div>
            @else
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Keterangan / Sumber Dana</label>
                <textarea name="keterangan" rows="2" placeholder="Contoh: Pembelian APBD, Bantuan Dinas, dsb." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all"></textarea>
            </div>
            @endif

            <div class="pt-4 flex justify-end border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium shadow-sm shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleNewItemFields() {
        var select = document.getElementById('item_id');
        var newFields = document.getElementById('new_item_fields');
        var manualInput = document.getElementById('nama_barang_manual');
        var hargaInput = document.getElementById('harga_satuan');
        
        if (select.value === 'new') {
            newFields.classList.remove('hidden');
            manualInput.setAttribute('required', 'required'); 
            hargaInput.value = '';
        } else {
            newFields.classList.add('hidden');
            manualInput.removeAttribute('required'); 
            
            // Auto fill price if item selected
            if(select.selectedIndex > 0) {
                var selectedOption = select.options[select.selectedIndex];
                var harga = selectedOption.getAttribute('data-harga');
                if(harga) {
                    hargaInput.value = harga;
                }
            } else {
                hargaInput.value = '';
            }
        }
    }

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
        // Auto fill price logic
        const select = document.getElementById('item_id');
        select.addEventListener('change', toggleNewItemFields);
        
        const jenisTransaksiSelect = document.querySelector('select[name="jenis_transaksi"]');
        const hutangCheckbox = document.getElementById('hutang');
        const hutangContainer = hutangCheckbox.closest('.p-4');
        const expiredDateContainer = document.getElementById('expired_date_container');

        function checkHutangState() {
            if (jenisTransaksiSelect.value === 'keluar') {
                hutangCheckbox.checked = false;
                hutangCheckbox.disabled = true;
                hutangContainer.style.opacity = '0.5';
                hutangContainer.style.pointerEvents = 'none';
                
                if (expiredDateContainer) {
                    expiredDateContainer.style.opacity = '0.5';
                    expiredDateContainer.style.pointerEvents = 'none';
                    expiredDateContainer.querySelector('input').value = '';
                }
            } else {
                hutangCheckbox.disabled = false;
                hutangContainer.style.opacity = '1';
                hutangContainer.style.pointerEvents = 'auto';
                
                if (expiredDateContainer) {
                    expiredDateContainer.style.opacity = '1';
                    expiredDateContainer.style.pointerEvents = 'auto';
                }
            }
        }

        jenisTransaksiSelect.addEventListener('change', checkHutangState);
        checkHutangState(); // Run on initial load
    });
</script>
@endsection