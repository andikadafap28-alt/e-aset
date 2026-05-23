@extends('layouts.app')

@section('header_title', 'Stock Opname - ' . $nama_kategori)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <a href="/{{ $kategori_besar }}/items" class="text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-2 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar {{ $nama_kategori }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Stock Opname</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Lakukan penyesuaian stok fisik gudang dengan data di sistem.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="/{{ $kategori_besar }}/opname" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                            <th class="py-4 px-6">Nama Barang</th>
                            <th class="py-4 px-6">Harga Satuan</th>
                            <th class="py-4 px-6 text-center">Stok Sistem</th>
                            <th class="py-4 px-6">Stok Fisik Gudang</th>
                            <th class="py-4 px-6">Keterangan / Alasan Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $item->nama_barang }}
                                <div class="text-xs font-normal text-slate-500 mt-0.5">{{ $item->kategori }} &bull; {{ $item->satuan }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-medium">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="font-bold text-slate-800 text-base">{{ $item->stok_sekarang }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <input type="number" 
                                       name="opname[{{ $item->id }}][stok_fisik]" 
                                       value="{{ $item->stok_sekarang }}" 
                                       required
                                       min="0"
                                       class="w-24 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium text-slate-700">
                            </td>
                            <td class="py-4 px-6">
                                <input type="text" 
                                       name="opname[{{ $item->id }}][keterangan]" 
                                       placeholder="Alasan jika ada selisih..." 
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-slate-500">
                                Belum ada data barang untuk di-opname.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($items) > 0)
            <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Hasil Opname
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
