@extends('layouts.app')

@section('header_title', 'Monitoring Global Aset')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Monitoring Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Pemantauan kondisi, lokasi, dan riwayat kalibrasi seluruh aset</p>
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Aset</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Aset</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lokasi</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kondisi</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Terakhir Kalibrasi</th>
                    <th class="py-3 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($assets as $index => $asset)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-5 text-sm text-slate-600">{{ $index + 1 }}</td>
                    <td class="py-3 px-5 text-sm font-medium text-slate-700">{{ $asset->asset_code }}</td>
                    <td class="py-3 px-5 text-sm font-semibold text-slate-800">{{ $asset->name }}</td>
                    <td class="py-3 px-5 text-sm text-slate-500">{{ $asset->category }}</td>
                    <td class="py-3 px-5 text-sm text-slate-600">{{ $asset->location }}</td>
                    <td class="py-3 px-5">
                        @if($asset->condition === 'Baik')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Baik
                            </span>
                        @elseif($asset->condition === 'Rusak Ringan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                Rusak Ringan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                Rusak Berat
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm text-slate-600">
                        @if($asset->last_calibration)
                            {{ \Carbon\Carbon::parse($asset->last_calibration)->format('d M Y') }}
                        @else
                            <span class="text-slate-400 italic">Belum pernah</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right">
                        <a href="{{ route('aset.show', $asset->id) }}" class="text-slate-500 hover:text-slate-800 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 font-medium text-xs px-3 py-1.5 rounded transition-all shadow-sm inline-block">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data aset untuk dimonitoring.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
