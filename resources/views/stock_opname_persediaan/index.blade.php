@extends('layouts.app')
@section('title', 'Berita Acara Stock Opname Persediaan')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('laporan.index') }}" class="hover:text-blue-600 transition-colors">Pelaporan</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Berita Acara Stock Opname Persediaan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Berita Acara Stock Opname Persediaan</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan cetak Berita Acara Stok Opname barang persediaan (ATK, Bahan Cetak, Bahan Komputer, Obat &amp; BMHP BLUD/APBD/APBN) format 2 halaman resmi.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ba-stock-opname.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Buat Berita Acara Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 flex items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Berita Acara</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalDokumen }} Dokumen</h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">Riwayat opname tersimpan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">receipt_long</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Nilai Persediaan Terakhir</p>
                @php
                    $latest = $records->first();
                @endphp
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">
                    {{ $latest ? 'Rp. ' . number_format($latest->total_nilai, 2, ',', '.') : 'Rp. 0,00' }}
                </h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">
                    {{ $latest ? 'Periode ' . \Carbon\Carbon::parse($latest->tanggal_opname)->locale('id')->isoFormat('MMMM Y') : 'Belum ada data' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">payments</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Format Cetak</p>
                <h3 class="text-xl font-bold text-indigo-600 mt-1">2 Halaman Lengkap</h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">Hal 1: Berita Acara &bull; Hal 2: Lampiran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">auto_stories</span>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Dokumen Berita Acara Stok Opname</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $records->count() }} berkas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap w-36">TANGGAL OPNAME</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">NOMOR SURAT</th>
                        <th class="px-4 py-3.5">RINCIAN REKENING PERSEDIAAN</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-right">TOTAL JUMLAH (RP)</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap w-44">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        @php
                            $dateCarbon = \Carbon\Carbon::parse($rec->tanggal_opname)->locale('id');
                            $itemsSummary = $rec->getRekeningSummaryList();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $dateCarbon->isoFormat('D MMMM Y') }}</div>
                                <span class="text-xs text-slate-400">{{ $dateCarbon->isoFormat('dddd') }}</span>
                            </td>
                            <td class="px-4 py-3.5 align-top whitespace-nowrap font-mono text-xs text-slate-700">
                                <span class="bg-slate-100 px-2 py-1 rounded-md border border-slate-200">{{ $rec->nomor_surat }}</span>
                            </td>
                            <td class="px-4 py-3.5 align-top text-xs">
                                <div class="space-y-1">
                                    @foreach($itemsSummary as $it)
                                        <div class="flex items-center justify-between max-w-sm text-slate-600">
                                            <span>&bull; {{ $it['name'] }}</span>
                                            <span class="font-mono font-medium text-slate-800">Rp. {{ number_format($it['val'], 2, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-bold text-emerald-700 font-mono text-sm">
                                Rp. {{ number_format($rec->total_nilai, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 align-top text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('ba-stock-opname.show', $rec->id) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-xs font-semibold" title="Buka &amp; Cetak 2 Halaman">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                        Cetak
                                    </a>
                                    <a href="{{ route('ba-stock-opname.edit', $rec->id) }}" class="p-1.5 bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition-colors border border-amber-200" title="Edit Data">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </a>
                                    <form action="{{ route('ba-stock-opname.destroy', $rec->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Berita Acara ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors border border-rose-200" title="Hapus Berita Acara">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[40px] block text-slate-300 mb-1">inventory_2</span>
                                Belum ada data Berita Acara Stok Opname Persediaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
