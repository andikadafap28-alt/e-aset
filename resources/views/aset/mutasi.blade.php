@extends('layouts.app')

@section('header_title', 'Mutasi Aset')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Mutasi / Perpindahan Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Riwayat perpindahan lokasi dan penanggung jawab aset</p>
    </div>
    <a href="{{ route('aset.mutasi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Mutasi Baru
    </a>
</div>

@if(session('success'))
<div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-emerald-200 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                    <th class="py-3 px-5">Tgl Mutasi</th>
                    <th class="py-3 px-5">Aset</th>
                    <th class="py-3 px-5">Lokasi & PJ Lama</th>
                    <th class="py-3 px-5">Lokasi & PJ Baru</th>
                    <th class="py-3 px-5">Keterangan</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($mutations as $mutasi)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-5 whitespace-nowrap">{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->translatedFormat('d M Y') }}</td>
                    <td class="py-3 px-5">
                        <a href="{{ route('aset.show', $mutasi->asset_id) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $mutasi->asset->name }}</a>
                        <div class="text-xs text-slate-500">{{ $mutasi->asset->asset_code }}</div>
                    </td>
                    <td class="py-3 px-5">
                        <div class="text-slate-800">{{ $mutasi->lokasi_lama }}</div>
                        <div class="text-xs text-slate-500">{{ $mutasi->penanggung_jawab_lama ?: 'Tidak ada PJ' }}</div>
                    </td>
                    <td class="py-3 px-5">
                        <div class="text-indigo-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            {{ $mutasi->lokasi_baru }}
                        </div>
                        <div class="text-xs text-slate-500">{{ $mutasi->penanggung_jawab_baru ?: 'Tidak ada PJ' }}</div>
                    </td>
                    <td class="py-3 px-5 text-slate-600 max-w-xs truncate" title="{{ $mutasi->keterangan }}">
                        {{ $mutasi->keterangan ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500">
                        Belum ada riwayat mutasi aset.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
