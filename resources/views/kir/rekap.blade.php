@extends('layouts.app')

@section('header_title', 'Rekapitulasi Total Barang KIR')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                    Submenu Rekapitulasi Total
                </span>
                <span class="text-xs text-slate-400">• Seluruh Ruangan Puskesmas Mantup</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Rekapitulasi Total Alat &amp; Barang KIR</h2>
            <p class="text-sm text-slate-500 mt-1 max-w-3xl">
                Rekapan gabungan seluruh barang/alat inventaris yang tercatat di semua ruangan KIR Puskesmas Mantup. Memudahkan pengecekan total kuantitas dan persebaran fisik barang di berbagai ruangan.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <!-- Kembali ke KIR Ruangan -->
            <a href="{{ route('kir.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[16px]">meeting_room</span>
                <span>KIR Ruangan</span>
            </a>

            <!-- Export Excel Rekap -->
            <a href="{{ route('kir.rekap.export', request()->all()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-600/20 transition-all" title="Download data rekapitulasi dalam file Excel (.xlsx)">
                <span class="material-symbols-outlined text-[16px]">file_download</span>
                <span>Export Excel Rekap</span>
            </a>

            <!-- Cetak Rekapitulasi F4 -->
            <a href="{{ route('kir.rekap.print') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-600/20 transition-all" title="Cetak Lembar Rekapitulasi Format Resmi F4">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Rekap (F4)</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Jenis Barang</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalJenisBarang }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Klasifikasi unik</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fisik Barang</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalFisikBarang }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Unit di seluruh ruangan</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 shadow-sm">
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Total Baik</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalBaik }}</p>
            <p class="text-[11px] text-emerald-600/70 mt-0.5">{{ $totalFisikBarang > 0 ? round(($totalBaik / $totalFisikBarang) * 100) : 0 }}% unit total</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/30 shadow-sm">
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Total Kurang Baik</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalKurangBaik }}</p>
            <p class="text-[11px] text-amber-600/70 mt-0.5">Perlu maintenance</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-rose-100 bg-rose-50/30 shadow-sm">
            <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Total Rusak Berat</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $totalRusakBerat }}</p>
            <p class="text-[11px] text-rose-600/70 mt-0.5">Usul afkir / gantikan</p>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('kir.rekap') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Input Cari -->
            <div class="md:col-span-5 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, merek, atau bahan..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <!-- Filter Ruangan -->
            <div class="md:col-span-4">
                <select name="room_id" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($allRooms as $r)
                        <option value="{{ $r->id }}" {{ $roomId == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} ({{ $r->kirItems()->count() }} jenis)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kondisi -->
            <div class="md:col-span-2">
                <select name="kondisi" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ $kondisi === 'baik' ? 'selected' : '' }}>Ada Kondisi Baik</option>
                    <option value="kurang_baik" {{ $kondisi === 'kurang_baik' ? 'selected' : '' }}>Ada Kurang Baik</option>
                    <option value="rusak_berat" {{ $kondisi === 'rusak_berat' ? 'selected' : '' }}>Ada Rusak Berat</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition-colors shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Rekapan Barang Seluruh KIR</h3>
            <span class="text-xs text-slate-400 font-medium">Menampilkan {{ $rekapItems->count() }} jenis barang teragregasi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="p-3.5 font-semibold w-12 text-center">No</th>
                        <th class="p-3.5 font-semibold">Jenis Barang / Nama Barang</th>
                        <th class="p-3.5 font-semibold">Merek / Model</th>
                        <th class="p-3.5 font-semibold">Bahan</th>
                        <th class="p-3.5 font-semibold text-center w-28">Total Fisik</th>
                        <th class="p-3.5 font-semibold text-center w-40">Rincian Kondisi</th>
                        <th class="p-3.5 font-semibold">Lokasi Persebaran Ruangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($rekapItems as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 text-center font-semibold text-slate-400">
                            {{ $index + 1 }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-900 text-sm block">{{ $item->name }}</span>
                            <span class="text-[10px] text-slate-400">Tersebar di {{ $item->room_count }} ruangan</span>
                        </td>
                        <td class="p-3.5 text-slate-700">
                            {{ $item->merk }}
                        </td>
                        <td class="p-3.5 text-slate-700">
                            {{ $item->bahan }}
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="font-bold text-slate-900 text-sm">{{ $item->total_jumlah }}</span>
                            <span class="text-[10px] text-slate-400 block">unit</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1 text-[10px] font-semibold">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Baik: {{ $item->total_baik }}">
                                    B: {{ $item->total_baik }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" title="Kurang Baik: {{ $item->total_kurang_baik }}">
                                    KB: {{ $item->total_kurang_baik }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800" title="Rusak Berat: {{ $item->total_rusak_berat }}">
                                    RB: {{ $item->total_rusak_berat }}
                                </span>
                            </div>
                        </td>
                        <td class="p-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->ruangan_list as $ruangan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ruangan }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400 mb-3">
                                <span class="material-symbols-outlined text-3xl">search_off</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak ada barang KIR yang cocok</h4>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
