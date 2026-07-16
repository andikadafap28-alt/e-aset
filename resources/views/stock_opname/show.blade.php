@extends('layouts.app')

@section('header_title', 'Hasil Stock Opname')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('stock-opname.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Laporan Stock Opname</h2>
                <p class="text-slate-500 text-sm mt-1">Tanggal: {{ $stockOpname->tanggal->format('d M Y') }} | Lokasi: <span class="font-bold">{{ $stockOpname->location ?? 'Global' }}</span></p>
            </div>
        </div>
        <div>
            @if($stockOpname->status == 'In Progress')
            <a href="{{ route('stock-opname.scan', $stockOpname->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                Lanjutkan Scan
            </a>
            @else
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full border border-emerald-200">
                Selesai
            </span>
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Dievaluasi</p>
            <p class="text-3xl font-black text-slate-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-200 shadow-sm text-center">
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Sesuai (Matched)</p>
            <p class="text-3xl font-black text-emerald-700">{{ $stats['matched'] }}</p>
        </div>
        <div class="bg-amber-50 p-5 rounded-2xl border border-amber-200 shadow-sm text-center">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Salah Tempat</p>
            <p class="text-3xl font-black text-amber-700">{{ $stats['misplaced'] }}</p>
        </div>
        <div class="bg-rose-50 p-5 rounded-2xl border border-rose-200 shadow-sm text-center">
            <p class="text-xs font-bold text-rose-600 uppercase tracking-wider mb-1">Hilang (Missing)</p>
            <p class="text-3xl font-black text-rose-700">{{ $stats['missing'] }}</p>
        </div>
    </div>

    <!-- Tabel Rincian -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Rincian Aset</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Kode Aset</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Nama Barang</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Lokasi Seharusnya</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Lokasi Scan</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockOpname->details as $detail)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 text-sm font-medium text-slate-700">{{ $detail->asset->asset_code ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm font-bold text-slate-800">{{ $detail->asset->name ?? 'Aset Dihapus' }}</td>
                        <td class="py-3 px-4 text-sm text-slate-600">{{ $detail->expected_location ?? '-' }}</td>
                        <td class="py-3 px-4 text-sm text-slate-600">{{ $detail->actual_location ?? '-' }}</td>
                        <td class="py-3 px-4">
                            @if($detail->status == 'Matched')
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-md">Sesuai</span>
                            @elseif($detail->status == 'Misplaced')
                                <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-md">Salah Tempat</span>
                            @elseif($detail->status == 'Missing')
                                <span class="px-2 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-md">Hilang / Tidak Di-Scan</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-800 text-xs font-bold rounded-md">{{ $detail->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">Belum ada aset yang dievaluasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
