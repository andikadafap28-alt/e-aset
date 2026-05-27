@extends('layouts.app')

@section('header_title', 'Edit Transaksi - ' . $nama_kategori)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/{{ $transaksi->item_id }}/detail" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal Edit
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Detail Transaksi</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Mengedit informasi pendukung transaksi. <strong class="text-rose-500">Jumlah dan jenis transaksi tidak dapat diedit demi keamanan stok matematis.</strong></p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <!-- Informasi Barang (Read Only) -->
        <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Informasi Transaksi (Read-Only)</div>
            <div class="font-bold text-slate-800 text-lg">{{ $transaksi->item->nama_barang }}</div>
            <div class="flex items-center gap-3 mt-2 text-sm">
                <span class="px-2.5 py-1 rounded-md font-medium {{ $transaksi->jenis_transaksi == 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }}">
                    Barang {{ ucfirst($transaksi->jenis_transaksi) }}
                </span>
                <span class="font-semibold text-slate-600">Jumlah: {{ $transaksi->jumlah }} {{ $transaksi->item->satuan }}</span>
            </div>
        </div>

        <form action="/{{ $kategori_besar }}/transaksi/{{ $transaksi->id }}" method="POST" class="space-y-6 text-sm">
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
                <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Transaksi (Fisik) <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_transaksi" required value="{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
            </div>

            @if($transaksi->jenis_transaksi == 'masuk')
            <div x-data="{ isHutang: {{ $transaksi->status_hutang ? 'true' : 'false' }} }" class="space-y-4">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-3">
                    <div class="flex items-center h-5">
                        <input type="checkbox" x-model="isHutang" name="status_hutang" id="hutang" class="w-4 h-4 text-teal-600 bg-white border-slate-300 rounded focus:ring-teal-500 focus:ring-2">
                    </div>
                    <label for="hutang" class="cursor-pointer">
                        <span class="block font-semibold text-slate-800">Status Pengadaan Masih Hutang</span>
                        <span class="block text-slate-500 mt-0.5 text-xs">Centang opsi ini jika transaksi ini adalah hutang yang belum di-SPJ-kan.</span>
                    </label>
                </div>

                <div x-show="!isHutang" x-transition>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal SPJ (Pelunasan)</label>
                    <input type="date" name="tanggal_spj" value="{{ $transaksi->tanggal_spj ? \Carbon\Carbon::parse($transaksi->tanggal_spj)->format('Y-m-d') : '' }}" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                    <p class="text-xs text-slate-500 mt-1">Hanya diisi jika sudah lunas. Jika dikosongkan, akan otomatis disamakan dengan Tanggal Transaksi.</p>
                </div>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Keterangan / Sumber Dana</label>
                <textarea name="keterangan" rows="2" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">{{ $transaksi->keterangan }}</textarea>
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

