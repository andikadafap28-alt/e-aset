@extends('layouts.app')
@section('title', 'Bukti Serah Terima (BAST)')

@section('content')
@php
    if (!function_exists('formatEmpNameBast')) {
        function formatEmpNameBast($name) {
            if (!$name) return '-';
            $name = ucwords(strtolower($name), " .-");
            $name = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $name);
            return $name;
        }
    }
    $totalHandovers = $handovers->count();
    $totalTensimeters = isset($tensimeterLoans) ? $tensimeterLoans->count() : 0;
    $totalAll = $totalHandovers + $totalTensimeters;
@endphp

<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('aset.data.items') }}" class="hover:text-blue-600 transition-colors">Aset & Inventaris</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Bukti Serah Terima (BAST)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Bukti Serah Terima (BAST)</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau dan cetak seluruh BAST barang inventaris ruangan serta BAST tensimeter Ponkesdes / Pustu dalam satu tempat.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('bast.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Buat BAST Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 flex items-center justify-between gap-3 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stat Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Keseluruhan BAST</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalAll }}</h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">Seluruh berkas serah terima</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">assignment</span>
            </div>
        </div>

        <!-- Stat Inventaris -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">BAST Barang / Inventaris</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $totalHandovers }}</h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">Laptop, Kursi Tunggu, & Ruangan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">chair</span>
            </div>
        </div>

        <!-- Stat Tensimeter -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">BAST Tensimeter Ponkesdes</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalTensimeters }}</h3>
                <span class="text-xs text-slate-500 mt-0.5 inline-block">Ponkesdes / Pustu binaan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">medical_services</span>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Filter Tabs & Search Bar -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
            <!-- Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-200/70 rounded-xl text-xs font-semibold">
                <button type="button" onclick="filterTab('all')" id="tabBtnAll" class="bast-tab-btn active px-3.5 py-2 rounded-lg bg-white text-slate-800 shadow-sm transition-all flex items-center gap-1.5">
                    <span>Semua BAST</span>
                    <span class="px-1.5 py-0.2 bg-slate-100 text-slate-600 rounded-md font-mono text-[10px]">{{ $totalAll }}</span>
                </button>
                <button type="button" onclick="filterTab('handover')" id="tabBtnHandover" class="bast-tab-btn px-3.5 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition-all flex items-center gap-1.5">
                    <span>Barang &amp; Inventaris</span>
                    <span class="px-1.5 py-0.2 bg-blue-100 text-blue-700 rounded-md font-mono text-[10px]">{{ $totalHandovers }}</span>
                </button>
                <button type="button" onclick="filterTab('tensimeter')" id="tabBtnTensimeter" class="bast-tab-btn px-3.5 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition-all flex items-center gap-1.5">
                    <span>Tensimeter (17 Unit)</span>
                    <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-700 rounded-md font-mono text-[10px]">{{ $totalTensimeters }}</span>
                </button>
            </div>

            <!-- Instant Search -->
            <div class="relative w-full md:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" id="bastSearchInput" onkeyup="filterRows()" placeholder="Cari penerima, barang, reg..." class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-slate-700 placeholder-slate-400">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600" id="bastTable">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap w-28">TANGGAL</th>
                        <th class="px-4 py-3.5 whitespace-nowrap w-36">KATEGORI</th>
                        <th class="px-4 py-3.5">NAMA BARANG &amp; REGISTER</th>
                        <th class="px-4 py-3.5">PENERIMA</th>
                        <th class="px-4 py-3.5">KEPERLUAN / LOKASI</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap w-36">AKSI CETAK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- SECTION 1: BAST INVENTARIS / HANDOVER -->
                    @forelse($handovers as $bast)
                        @php
                            $isKepalaPuskesmas = ($bast->employee_id == 1) 
                                || str_contains(strtolower($bast->employee->name ?? ''), 'sunaryadi') 
                                || str_contains(strtolower($bast->employee->jabatan ?? ''), 'kepala puskesmas');

                            $empName = formatEmpNameBast($bast->employee->name ?? ($isKepalaPuskesmas ? 'dr. Muhammad Sunaryadi' : '-'));
                            $rawNip = $bast->employee->nip ?? ($isKepalaPuskesmas ? '196903132002121007' : '');
                            
                            $cleanNip = preg_replace('/[^0-9]/', '', (string)$rawNip);
                            $isPppk = in_array($bast->employee_id, [58, 59, 60]) 
                                || str_contains(strtolower($bast->employee->name ?? ''), 'dhendhy')
                                || str_contains(strtolower($bast->employee->name ?? ''), 'almira')
                                || str_contains(strtolower($bast->employee->name ?? ''), 'suharsono')
                                || (strlen($cleanNip) == 18 && (substr($cleanNip, 12, 2) === '21' || substr($cleanNip, 14, 2) === '21'));
                            
                            $nipLabel = $isPppk ? 'NIPPPK' : 'NIP';
                            $empJabatan = $bast->employee->jabatan ?? ($isKepalaPuskesmas ? 'Kepala Puskesmas Mantup' : ($bast->employee->golongan ?: '-'));

                            // Cek nama & item
                            $itemsCount = $bast->items ? $bast->items->count() : 1;
                            $firstAsset = $bast->items && $bast->items->first() ? $bast->items->first()->asset : $bast->asset;
                            $firstAssetName = $firstAsset ? $firstAsset->name : 'Barang Milik Daerah';
                            
                            $regs = $bast->items ? $bast->items->map(function($i) {
                                return $i->asset && $i->asset->no_register !== null ? (int)$i->asset->no_register : null;
                            })->filter()->sort()->values() : collect();

                            if ($regs->count() > 1) {
                                $regText = 'Reg: ' . str_pad($regs->first(), 3, '0', STR_PAD_LEFT) . ' s/d ' . str_pad($regs->last(), 3, '0', STR_PAD_LEFT);
                            } elseif ($regs->count() == 1) {
                                $regText = 'Reg: ' . str_pad($regs->first(), 3, '0', STR_PAD_LEFT);
                            } else {
                                $regText = $firstAsset && $firstAsset->no_register ? 'Reg: ' . str_pad($firstAsset->no_register, 3, '0', STR_PAD_LEFT) : '';
                            }

                            $searchContent = strtolower("{$empName} {$rawNip} {$empJabatan} {$firstAssetName} {$bast->keperluan} {$regText} inventaris barang");
                        @endphp
                        <tr class="bast-row hover:bg-slate-50/80 transition-colors" data-type="handover" data-search="{{ $searchContent }}">
                            <td class="px-4 py-3.5 font-medium text-slate-800 align-middle whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($bast->handover_date)->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                    <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                    Inventaris
                                </span>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                @if($itemsCount > 1)
                                    <div class="font-semibold text-slate-800 leading-snug">
                                        {{ $firstAssetName }}
                                        <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">+{{ $itemsCount - 1 }} lainnya</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span class="font-medium text-blue-600">{{ $itemsCount }} unit barang</span>
                                        @if($regText)
                                            <span class="font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $regText }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="font-semibold text-slate-800 leading-snug">{{ $firstAssetName }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                        @if($firstAsset && $firstAsset->merk)
                                            <span>{{ $firstAsset->merk }}</span>
                                        @endif
                                        @if($regText)
                                            <span class="font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $regText }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-semibold text-slate-800">{{ $empName }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    @if($rawNip)
                                        <span class="font-mono font-medium {{ $isPppk ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-slate-600 bg-slate-100' }} px-1.5 py-0.2 rounded text-[11px]">
                                            {{ $nipLabel }}: {{ $rawNip }}
                                        </span>
                                    @endif
                                    <span>&bull; {{ $empJabatan }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-middle text-slate-700 text-xs">
                                <span class="font-medium text-slate-800">{{ $bast->keperluan ?: '-' }}</span>
                                @if($bast->keterangan)
                                    <span class="block text-slate-400 mt-0.5">{{ $bast->keterangan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-middle text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('bast.show', $bast->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-xs font-semibold" title="Buka Dokumen &amp; Cetak BAST">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                        Cetak BAST
                                    </a>
                                    <form action="{{ route('bast.destroy', $bast->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data BAST ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 text-slate-500 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus BAST">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    <!-- SECTION 2: BAST TENSIMETER (17 UNIT DARI ASSET LOAN) -->
                    @if(isset($tensimeterLoans))
                        @foreach($tensimeterLoans as $loan)
                            @php
                                $asset = $loan->asset;
                                $assetName = $asset->name ?? 'Tensimeter';
                                $regFormatted = $asset && $asset->no_register ? 'Reg: ' . str_pad($asset->no_register, 3, '0', STR_PAD_LEFT) : '';
                                $borrowerName = formatEmpNameBast($loan->borrower_name ?: 'Bidan Ponkesdes / Pustu');
                                $borrowerPosition = $loan->borrower_position ?: 'Bidan / Perawat Ponkesdes';
                                $notes = $loan->notes ?: 'Pelayanan Ponkesdes / Pustu';
                                $searchContent = strtolower("{$borrowerName} {$loan->borrower_nip} {$borrowerPosition} {$assetName} {$notes} {$regFormatted} tensimeter aneroid ponkesdes pustu");
                            @endphp
                            <tr class="bast-row hover:bg-emerald-50/40 transition-colors" data-type="tensimeter" data-search="{{ $searchContent }}">
                                <td class="px-4 py-3.5 font-medium text-slate-800 align-middle whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <span class="material-symbols-outlined text-[14px]">medical_services</span>
                                        Tensimeter
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <div class="font-semibold text-slate-800 leading-snug">{{ $assetName }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                        @if($asset && $asset->merk)
                                            <span>{{ $asset->merk }}</span>
                                        @endif
                                        @if($regFormatted)
                                            <span class="font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-semibold">{{ $regFormatted }}</span>
                                        @endif
                                        @if($asset && $asset->condition)
                                            <span class="text-slate-400">({{ $asset->condition }})</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <div class="font-semibold text-slate-800">{{ $borrowerName }}</div>
                                    <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                        <span>{{ $borrowerPosition }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle text-slate-700 text-xs">
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 bg-emerald-50/80 px-2 py-0.5 rounded-md">
                                        <span class="material-symbols-outlined text-[13px] text-emerald-600">location_on</span>
                                        {{ $notes }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-middle text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('aset.peminjaman.print', $loan->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors shadow-sm text-xs font-semibold" title="Buka Dokumen &amp; Cetak BAST Tensimeter (Lengkap dengan overlay sumber)">
                                            <span class="material-symbols-outlined text-[16px]">print</span>
                                            Cetak BAST
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <tr id="emptyRow" style="display: none;">
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[36px] block text-slate-300 mb-1">search_off</span>
                            Tidak ditemukan data BAST yang cocok dengan pencarian atau filter tab.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let activeFilter = 'all';

    function filterTab(type) {
        activeFilter = type;

        // Atur tombol tab active
        document.querySelectorAll('.bast-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
            btn.classList.add('text-slate-600');
        });

        if (type === 'all') {
            const btn = document.getElementById('tabBtnAll');
            btn.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            btn.classList.remove('text-slate-600');
        } else if (type === 'handover') {
            const btn = document.getElementById('tabBtnHandover');
            btn.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            btn.classList.remove('text-slate-600');
        } else if (type === 'tensimeter') {
            const btn = document.getElementById('tabBtnTensimeter');
            btn.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            btn.classList.remove('text-slate-600');
        }

        filterRows();
    }

    function filterRows() {
        const query = (document.getElementById('bastSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.bast-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const type = row.getAttribute('data-type');
            const searchContent = row.getAttribute('data-search') || '';

            const matchType = (activeFilter === 'all' || type === activeFilter);
            const matchSearch = (query === '' || searchContent.includes(query));

            if (matchType && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyRow = document.getElementById('emptyRow');
        if (visibleCount === 0) {
            emptyRow.style.display = '';
        } else {
            emptyRow.style.display = 'none';
        }
    }
</script>
@endsection
