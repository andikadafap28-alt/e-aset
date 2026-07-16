<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        table { w-full border-collapse; margin-bottom: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Puskesmas Mantup</h2>
        <h2>{{ $title }}</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <!-- Intra Assets -->
    <div class="section-title">1. Aset Intrakomptabel (Nilai Perolehan &ge; Rp 500.000)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Aset</th>
                <th width="30%">Nama Aset</th>
                <th width="20%">Kategori</th>
                <th width="10%" class="text-center">Tahun</th>
                <th width="20%" class="text-right">Nilai Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalIntra = 0; @endphp
            @forelse($intraAssets as $index => $asset)
                @php $totalIntra += $asset->harga_perolehan; @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category ? $asset->category->nama_kategori : '-' }}</td>
                    <td class="text-center">{{ $asset->year_purchased }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data aset intrakomptabel.</td></tr>
            @endforelse
        </tbody>
        @if($intraAssets->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Nilai Intrakomptabel:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalIntra, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Ekstra Assets -->
    <div class="section-title">2. Aset Ekstrakomptabel (Nilai Perolehan &lt; Rp 500.000)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Aset</th>
                <th width="30%">Nama Aset</th>
                <th width="20%">Kategori</th>
                <th width="10%" class="text-center">Tahun</th>
                <th width="20%" class="text-right">Nilai Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalEkstra = 0; @endphp
            @forelse($ekstraAssets as $index => $asset)
                @php $totalEkstra += $asset->harga_perolehan; @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category ? $asset->category->nama_kategori : '-' }}</td>
                    <td class="text-center">{{ $asset->year_purchased }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data aset ekstrakomptabel.</td></tr>
            @endforelse
        </tbody>
        @if($ekstraAssets->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Nilai Ekstrakomptabel:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalEkstra, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="section-title">3. Rekapitulasi Total</div>
    <table>
        <tbody>
            <tr>
                <td width="80%">Total Nilai Intrakomptabel ({{ $intraAssets->count() }} Aset)</td>
                <td width="20%" class="text-right">Rp {{ number_format($totalIntra ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Nilai Ekstrakomptabel ({{ $ekstraAssets->count() }} Aset)</td>
                <td class="text-right">Rp {{ number_format($totalEkstra ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Keseluruhan ({{ $intraAssets->count() + $ekstraAssets->count() }} Aset)</strong></td>
                <td class="text-right"><strong>Rp {{ number_format(($totalIntra ?? 0) + ($totalEkstra ?? 0), 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh Sistem RAKSA pada {{ $date }}</p>
    </div>
</body>
</html>
