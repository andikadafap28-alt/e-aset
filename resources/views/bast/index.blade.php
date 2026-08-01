@extends('layouts.app')
@section('title', 'Bukti Serah Terima (BAST)')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Daftar Bukti Serah Terima (BAST)</h1>
        <a href="{{ route('bast.create') }}" class="px-4 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Buat BAST Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-200 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap w-24">TANGGAL</th>
                        <th class="px-4 py-3">NAMA BARANG</th>
                        <th class="px-4 py-3">PENERIMA</th>
                        <th class="px-4 py-3">KEPERLUAN</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap w-32">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($handovers as $bast)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-slate-800 align-middle">{{ \Carbon\Carbon::parse($bast->handover_date)->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 align-middle">
                            @if($bast->items && $bast->items->count() > 1)
                                <span class="block font-medium text-slate-800">{{ $bast->items->first()->asset->name ?? '-' }} <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full ml-1">+{{ $bast->items->count() - 1 }} lainnya</span></span>
                                <span class="text-xs text-slate-500">{{ $bast->items->count() }} barang</span>
                            @elseif($bast->items && $bast->items->count() == 1)
                                <span class="block font-medium text-slate-800">{{ $bast->items->first()->asset->name ?? '-' }}</span>
                                <span class="text-xs text-slate-500">{{ $bast->items->first()->asset->asset_code ?? '-' }}</span>
                            @else
                                <span class="block font-medium text-slate-800">{{ $bast->asset->name ?? '-' }}</span>
                                <span class="text-xs text-slate-500">{{ $bast->asset->asset_code ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-middle">{{ $bast->employee ? str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], ucwords(strtolower($bast->employee->name), " .-")) : '-' }}</td>
                        <td class="px-4 py-3 align-middle">{{ $bast->keperluan }}</td>
                        <td class="px-4 py-3 align-middle">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('bast.show', $bast->id) }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center" title="Cetak">
                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                </a>
                                <form action="{{ route('bast.destroy', $bast->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus BAST ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data BAST</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
