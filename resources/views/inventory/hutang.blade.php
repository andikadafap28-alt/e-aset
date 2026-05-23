@extends('layouts.app')

@section('header_title', 'Manajemen Hutang - ' . $nama_kategori)

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showModal: false, selectedId: null }">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/items" class="text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-2 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar {{ $nama_kategori }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Hutang (SPJ)</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Daftar transaksi barang masuk yang belum diselesaikan (belum ada SPJ).</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <th class="py-4 px-6">Tanggal Masuk</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6 text-center">Jumlah</th>
                        <th class="py-4 px-6">Keterangan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-slate-600 font-medium">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }}</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            {{ $trx->item->nama_barang }}
                            <div class="text-xs font-normal text-slate-500 mt-0.5">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="font-bold text-slate-800 text-base">{{ $trx->jumlah }}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600 italic text-xs">{{ $trx->keterangan ?? '-' }}</td>
                        <td class="py-4 px-6 text-right">
                            <button @click="showModal = true; selectedId = {{ $trx->id }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-medium px-4 py-2 rounded-lg transition-colors text-xs border border-indigo-200">
                                Ubah ke SPJ
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-slate-500">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900">Tidak ada hutang</h3>
                            <p class="text-sm text-slate-500 mt-1">Semua transaksi barang masuk sudah diselesaikan dengan SPJ.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showModal = false" class="relative w-full max-w-md p-4 bg-white rounded-2xl shadow-xl transform transition-all">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Pelunasan Hutang (SPJ)</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="'/{{ $kategori_besar }}/hutang/' + selectedId + '/spj'" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SPJ / Pelunasan</label>
                    <input type="date" name="tanggal_spj" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium text-slate-700">
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-5 rounded-lg shadow-sm shadow-indigo-600/20 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
