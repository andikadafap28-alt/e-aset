@extends('layouts.app')

@section('header_title', 'Laporan Penyusutan Aset')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <a href="{{ route('laporan.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Penyusutan Aset
            </h2>
            <p class="text-slate-500 text-sm mt-1 ml-8">Daftar depresiasi dan nilai buku seluruh aset tetap aktif.</p>
        </div>
        <div class="flex gap-3">
            @if(count($assets) > 0)
            <form action="{{ route('laporan.generate') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="report_type" value="penyusutan">
                <input type="hidden" name="export_format" value="pdf">
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
                <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download PDF
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('error'))
    <div class="bg-rose-50 text-rose-600 px-4 py-3 rounded-lg text-sm font-medium border border-rose-200">
        {{ session('error') }}
    </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form action="{{ route('laporan.penyusutan') }}" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Filter Kategori</label>
                <select name="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex justify-center items-center gap-2 h-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider w-16">No</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Aset</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider text-center">Thn Beli / Umur</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider text-right">Harga Perolehan</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-600 uppercase tracking-wider text-right">Akum. Penyusutan</th>
                        <th class="py-3 px-4 text-xs font-bold text-emerald-600 uppercase tracking-wider text-right">Nilai Buku</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $index => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 text-sm font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="py-3 px-4">
                            <p class="text-sm font-bold text-slate-800">{{ $item->name }}</p>
                            <p class="text-xs text-slate-500">{{ $item->asset_code }}</p>
                        </td>
                        <td class="py-3 px-4 text-sm text-slate-700">{{ $item->category ? $item->category->nama_kategori : '-' }}</td>
                        <td class="py-3 px-4 text-sm text-slate-700 text-center">
                            {{ $item->year_purchased }} / {{ $item->category ? $item->category->umur_ekonomis : '-' }} Thn
                        </td>
                        <td class="py-3 px-4 text-sm text-slate-700 text-right">Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm text-amber-600 text-right">Rp {{ number_format($item->accumulated_depreciation, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm text-emerald-600 text-right font-bold">Rp {{ number_format($item->book_value, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Tidak ada data untuk ditampilkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($assets) > 0)
                <tfoot>
                    <tr class="bg-slate-100 border-t border-slate-200 font-bold">
                        <td colspan="4" class="py-3 px-4 text-sm text-slate-800 text-right">TOTAL KESELURUHAN</td>
                        <td class="py-3 px-4 text-sm text-slate-700 text-right">Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm text-amber-700 text-right">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm text-emerald-700 text-right">Rp {{ number_format($totalBookValue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
