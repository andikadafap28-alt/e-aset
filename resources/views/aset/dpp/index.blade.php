@extends('layouts.app')
@section('header_title', 'Dokumen Persiapan Pengadaan (DPP)')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-800">Daftar DPP</h3>
        @if(auth()->user()?->role !== 'arsiparis')
        <a href="{{ route('dpp.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
            + Buat DPP Baru
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 text-sm font-medium border border-emerald-100">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-y border-slate-200">
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase">Nomor Surat</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase">Kode RUP</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase">Tanggal DPP</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dpps as $dpp)
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4">
                        <p class="text-sm font-semibold text-slate-800">{{ str_contains($dpp->nomor_surat, 'PPBJ') ? $dpp->nomor_surat : '000.3.1/' . $dpp->nomor_surat . '/PPBJ/413.102.5.18/' . date('Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $dpp->nama_paket ?? '-' }}</p>
                    </td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $dpp->kode_rup }}</td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $dpp->tanggal_dpp->format('d/m/Y') }}</td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dpp.show', $dpp->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Detail">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </a>
                            <a href="{{ route('dpp.pdf', $dpp->id) }}" target="_blank" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Cetak PDF">
                                <span class="material-symbols-outlined text-xl">picture_as_pdf</span>
                            </a>
                            @if(auth()->user()?->role !== 'arsiparis')
                            <form action="{{ route('dpp.destroy', $dpp->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini beserta lampirannya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Belum ada dokumen DPP yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
