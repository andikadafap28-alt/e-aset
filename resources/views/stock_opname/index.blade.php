@extends('layouts.app')

@section('header_title', 'Stock Opname')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Stock Opname</h2>
        <p class="text-slate-500 text-sm mt-1">Lakukan audit fisik aset secara berkala.</p>
    </div>
    <a href="{{ route('stock-opname.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Mulai Stock Opname Baru
    </a>
</div>

@if(session('success'))
<div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-emerald-200">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Tanggal</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Lokasi / Ruangan</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Pelaksana</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stockOpnames as $opname)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-sm font-medium text-slate-800">{{ $opname->tanggal->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $opname->location ?? 'Global (Semua Ruangan)' }}</td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $opname->user->name ?? 'Sistem' }}</td>
                    <td class="py-3 px-4">
                        @if($opname->status == 'Completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                Sedang Berjalan
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right">
                        @if($opname->status == 'In Progress')
                            <a href="{{ route('stock-opname.scan', $opname->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm transition-colors px-2 py-1 bg-indigo-50 hover:bg-indigo-100 rounded">Lanjutkan Scan</a>
                        @else
                            <a href="{{ route('stock-opname.show', $opname->id) }}" class="text-sky-600 hover:text-sky-800 font-medium text-sm transition-colors px-2 py-1 bg-sky-50 hover:bg-sky-100 rounded">Lihat Laporan</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500">
                        Belum ada riwayat Stock Opname.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-200">
        {{ $stockOpnames->links() }}
    </div>
</div>
@endsection
