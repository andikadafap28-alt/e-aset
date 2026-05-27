@extends('layouts.app')

@section('header_title', 'Manajemen Master - ' . $nama_kategori)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/items" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar {{ $nama_kategori }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Data Master Barang</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Edit informasi barang atau hapus barang yang tidak digunakan (Hapus barang juga akan menghapus seluruh riwayat transaksinya).</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Harga Satuan</th>
                        <th class="py-4 px-6">Stok</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 font-semibold text-slate-900">{{ $item->nama_barang }}</td>
                        <td class="py-4 px-6 text-slate-600">{{ $item->kategori }}</td>
                        <td class="py-4 px-6 text-slate-600 font-medium">Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-800">{{ number_format($item->stok_sekarang, 0, ',', '.') }}</span> <span class="text-slate-500">{{ $item->satuan }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="/{{ $kategori_besar }}/{{ $item->id }}/edit-master" class="text-teal-600 hover:text-indigo-800 font-medium px-3 py-1.5 bg-teal-50 hover:bg-teal-50 rounded-lg transition-colors text-xs border border-indigo-200">
                                    Edit
                                </a>
                                <form action="/{{ $kategori_besar }}/{{ $item->id }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus barang ini juga akan menghapus SELURUH riwayat transaksinya! Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium px-3 py-1.5 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors text-xs border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-slate-500">Tidak ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

