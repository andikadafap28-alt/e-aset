@extends('layouts.app')

@section('header_title', 'Laporan Penyusutan Aset')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header & Filter -->
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Laporan Penyusutan BMD Peralatan Mesin</h2>
            <p class="text-sm text-slate-500">Intrakomptabel dan Ekstrakomptabel</p>
        </div>
        
        <form method="GET" action="{{ route('aset.penyusutan.index') }}" class="flex items-center gap-3">
            <div class="flex flex-col">
                <label for="month" class="text-xs font-semibold text-slate-500 uppercase mb-1">Bulan</label>
                <select name="month" id="month" class="border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex flex-col">
                <label for="year" class="text-xs font-semibold text-slate-500 uppercase mb-1">Tahun</label>
                <input type="number" name="year" id="year" value="{{ $year }}" class="border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors w-24">
            </div>
            
            <div class="flex flex-col justify-end mt-5">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold text-center border-r border-slate-200">No</th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200">Kode Barang</th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200">Nama Barang</th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200">Tanggal Perolehan</th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-right">Nilai Perolehan<br><span class="text-xs font-normal text-slate-500">(Rp)</span></th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-center">Masa Manfaat<br><span class="text-xs font-normal text-slate-500">(Bulan)</span></th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-right">Penyusutan per Bulan<br><span class="text-xs font-normal text-slate-500">(Rp)</span></th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-center">Bulan Dilalui</th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-right">Akumulasi Penyusutan<br><span class="text-xs font-normal text-slate-500">(Rp)</span></th>
                    <th scope="col" class="px-4 py-3 font-semibold border-r border-slate-200 text-right">Nilai Buku<br><span class="text-xs font-normal text-slate-500">(Rp)</span></th>
                    <th scope="col" class="px-4 py-3 font-semibold text-center">Sisa Umur<br><span class="text-xs font-normal text-slate-500">(Bulan)</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php
                    $grandTotalPerolehan = 0;
                    $grandTotalPenyusutanBulan = 0;
                    $grandTotalAkumulasi = 0;
                    $grandTotalNilaiBuku = 0;
                @endphp

                @forelse($groupedAssets as $kategori => $items)
                    @php
                        $subTotalPerolehan = 0;
                        $subTotalPenyusutanBulan = 0;
                        $subTotalAkumulasi = 0;
                        $subTotalNilaiBuku = 0;
                    @endphp

                    <!-- Group Header -->
                    <tr class="bg-slate-100/50">
                        <td colspan="4" class="px-4 py-3 font-bold text-slate-700 border-r border-slate-200">
                            {{ $kategori }}
                        </td>
                        <!-- Placeholder for subtotals to be filled by JS or calculated loop -->
                        <td class="px-4 py-3 font-bold text-right text-slate-700 border-r border-slate-200 subtotal-perolehan-{{ Str::slug($kategori) }}"></td>
                        <td class="px-4 py-3 border-r border-slate-200"></td>
                        <td class="px-4 py-3 font-bold text-right text-slate-700 border-r border-slate-200 subtotal-penyusutan-{{ Str::slug($kategori) }}"></td>
                        <td class="px-4 py-3 border-r border-slate-200"></td>
                        <td class="px-4 py-3 font-bold text-right text-slate-700 border-r border-slate-200 subtotal-akumulasi-{{ Str::slug($kategori) }}"></td>
                        <td class="px-4 py-3 font-bold text-right text-slate-700 border-r border-slate-200 subtotal-nilaibuku-{{ Str::slug($kategori) }}"></td>
                        <td class="px-4 py-3"></td>
                    </tr>

                    @foreach($items as $index => $asset)
                        @php
                            $subTotalPerolehan += $asset->harga_perolehan;
                            $subTotalPenyusutanBulan += $asset->penyusutan_per_bulan;
                            $subTotalAkumulasi += $asset->akumulasi_penyusutan;
                            $subTotalNilaiBuku += $asset->nilai_buku;

                            $grandTotalPerolehan += $asset->harga_perolehan;
                            $grandTotalPenyusutanBulan += $asset->penyusutan_per_bulan;
                            $grandTotalAkumulasi += $asset->akumulasi_penyusutan;
                            $grandTotalNilaiBuku += $asset->nilai_buku;
                        @endphp
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-4 py-3 text-center text-slate-500 border-r border-slate-200">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700 border-r border-slate-200">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-3 text-slate-600 border-r border-slate-200">
                                <div class="font-medium text-slate-700">{{ $asset->name }}</div>
                                @if($asset->merk)<div class="text-xs text-slate-400">{{ $asset->merk }}</div>@endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 border-r border-slate-200">
                                {{ $asset->tanggal_bast ? \Carbon\Carbon::parse($asset->tanggal_bast)->format('d/m/Y') : ($asset->year_purchased ? '01/01/'.$asset->year_purchased : '-') }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-slate-700 border-r border-slate-200">
                                {{ number_format($asset->harga_perolehan, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600 border-r border-slate-200">
                                {{ $asset->masa_manfaat_bulan }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600 border-r border-slate-200">
                                {{ number_format($asset->penyusutan_per_bulan, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600 border-r border-slate-200">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded bg-slate-100 text-xs font-semibold text-slate-600">
                                    {{ $asset->masa_manfaat_dilalui }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-amber-600 border-r border-slate-200">
                                {{ number_format($asset->akumulasi_penyusutan, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-emerald-600 border-r border-slate-200">
                                {{ number_format($asset->nilai_buku, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-slate-200">
                                @if($asset->sisa_masa_manfaat == 0)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-1 rounded-md">
                                        Habis
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-slate-600">{{ $asset->sisa_masa_manfaat }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <!-- Script to inject subtotals -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelector('.subtotal-perolehan-{{ Str::slug($kategori) }}').innerText = '{{ number_format($subTotalPerolehan, 2, ",", ".") }}';
                            document.querySelector('.subtotal-penyusutan-{{ Str::slug($kategori) }}').innerText = '{{ number_format($subTotalPenyusutanBulan, 2, ",", ".") }}';
                            document.querySelector('.subtotal-akumulasi-{{ Str::slug($kategori) }}').innerText = '{{ number_format($subTotalAkumulasi, 2, ",", ".") }}';
                            document.querySelector('.subtotal-nilaibuku-{{ Str::slug($kategori) }}').innerText = '{{ number_format($subTotalNilaiBuku, 2, ",", ".") }}';
                        });
                    </script>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">inventory_2</span>
                                <p>Tidak ada data aset ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            @if($groupedAssets->count() > 0)
            <tfoot class="bg-slate-900 text-white font-bold">
                <tr>
                    <td colspan="4" class="px-4 py-4 text-right border-r border-slate-700">TOTAL KESELURUHAN</td>
                    <td class="px-4 py-4 text-right border-r border-slate-700">{{ number_format($grandTotalPerolehan, 2, ',', '.') }}</td>
                    <td class="px-4 py-4 border-r border-slate-700"></td>
                    <td class="px-4 py-4 text-right border-r border-slate-700">{{ number_format($grandTotalPenyusutanBulan, 2, ',', '.') }}</td>
                    <td class="px-4 py-4 border-r border-slate-700"></td>
                    <td class="px-4 py-4 text-right text-amber-300 border-r border-slate-700">{{ number_format($grandTotalAkumulasi, 2, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right text-emerald-300 border-r border-slate-700">{{ number_format($grandTotalNilaiBuku, 2, ',', '.') }}</td>
                    <td class="px-4 py-4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
