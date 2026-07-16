@extends('layouts.app')

@section('header_title', 'Laporan Berita Acara Rekonsiliasi')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined">library_books</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Berita Acara Rekonsiliasi</h2>
                <p class="text-sm text-slate-500">Pemisahan Aset Intrakomptabel & Ekstrakomptabel (Batas Rp 500.000)</p>
            </div>
        </div>

        <form action="{{ route('laporan.rekonsiliasi') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <select name="category_id" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm w-full sm:w-48 bg-white">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
            </div>
        </form>
    </div>

    <!-- Intra Assets -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Aset Intrakomptabel (&#8805; Rp 500.000)
            </h3>
            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full">{{ $intraAssets->count() }} Aset</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <th class="py-3 px-5">Kode/Nama Aset</th>
                        <th class="py-3 px-5">Kategori</th>
                        <th class="py-3 px-5">Tahun</th>
                        <th class="py-3 px-5 text-right">Nilai Perolehan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @php $totalIntra = 0; @endphp
                    @forelse($intraAssets as $asset)
                        @php $totalIntra += $asset->harga_perolehan; @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-5">
                                <span class="font-medium text-slate-800">{{ $asset->name }}</span><br>
                                <span class="text-xs text-slate-500">{{ $asset->asset_code }}</span>
                            </td>
                            <td class="py-3 px-5 text-slate-600">{{ $asset->category ? $asset->category->nama_kategori : '-' }}</td>
                            <td class="py-3 px-5 text-slate-600">{{ $asset->year_purchased }}</td>
                            <td class="py-3 px-5 text-right font-medium text-emerald-600">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-slate-500">Tidak ada aset Intrakomptabel.</td></tr>
                    @endforelse
                </tbody>
                @if($intraAssets->count() > 0)
                <tfoot>
                    <tr class="bg-emerald-50 border-t border-emerald-100 font-bold">
                        <td colspan="3" class="py-3 px-5 text-right text-emerald-800">Total Nilai Intrakomptabel:</td>
                        <td class="py-3 px-5 text-right text-emerald-700">Rp {{ number_format($totalIntra, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Ekstra Assets -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Aset Ekstrakomptabel (&lt; Rp 500.000)
            </h3>
            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full">{{ $ekstraAssets->count() }} Aset</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <th class="py-3 px-5">Kode/Nama Aset</th>
                        <th class="py-3 px-5">Kategori</th>
                        <th class="py-3 px-5">Tahun</th>
                        <th class="py-3 px-5 text-right">Nilai Perolehan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @php $totalEkstra = 0; @endphp
                    @forelse($ekstraAssets as $asset)
                        @php $totalEkstra += $asset->harga_perolehan; @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-5">
                                <span class="font-medium text-slate-800">{{ $asset->name }}</span><br>
                                <span class="text-xs text-slate-500">{{ $asset->asset_code }}</span>
                            </td>
                            <td class="py-3 px-5 text-slate-600">{{ $asset->category ? $asset->category->nama_kategori : '-' }}</td>
                            <td class="py-3 px-5 text-slate-600">{{ $asset->year_purchased }}</td>
                            <td class="py-3 px-5 text-right font-medium text-amber-600">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-slate-500">Tidak ada aset Ekstrakomptabel.</td></tr>
                    @endforelse
                </tbody>
                @if($ekstraAssets->count() > 0)
                <tfoot>
                    <tr class="bg-amber-50 border-t border-amber-100 font-bold">
                        <td colspan="3" class="py-3 px-5 text-right text-amber-800">Total Nilai Ekstrakomptabel:</td>
                        <td class="py-3 px-5 text-right text-amber-700">Rp {{ number_format($totalEkstra, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    
    <!-- Action Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <p class="text-sm text-slate-500">
            Total Seluruh Aset: <strong class="text-slate-800">{{ $intraAssets->count() + $ekstraAssets->count() }}</strong>
            | Total Nilai: <strong class="text-slate-800">Rp {{ number_format(isset($totalIntra) ? $totalIntra + $totalEkstra : 0, 0, ',', '.') }}</strong>
        </p>
        <form action="{{ route('laporan.rekonsiliasi.export') }}" method="POST">
            @csrf
            <input type="hidden" name="category_id" value="{{ $categoryId }}">
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Download PDF Berita Acara
            </button>
        </form>
    </div>

</div>
@endsection
