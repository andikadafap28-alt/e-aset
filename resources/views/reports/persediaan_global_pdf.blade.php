<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 11px; }
        .meta-info { margin-bottom: 15px; font-size: 11px; }
        .meta-info table { width: 100%; }
        .meta-info td { vertical-align: top; padding: 2px 0; }
        .kategori-title { background-color: #f8fafc; padding: 8px 12px; font-size: 13px; font-weight: bold; border: 1px solid #ddd; border-bottom: none; margin-top: 10px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        table.data-table td.center { text-align: center; }
        table.data-table td.right { text-align: right; }
        table.data-table tr.footer { font-weight: bold; background-color: #e2e8f0; }
        .footer-note { text-align: right; font-size: 10px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>

    @php $iteration = 0; $totalKategori = count($allData); @endphp
    @foreach($allData as $kategoriName => $items)
        @php $iteration++; @endphp

        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Aplikasi Manajemen Aset Puskesmas Mantup</p>
        </div>

        <div class="meta-info">
            <table style="width: 100%">
                <tr>
                    <td style="width: 130px;"><strong>Tanggal Cetak</strong></td>
                    <td style="width: 300px;">: {{ $date }}</td>
                    <td style="width: 130px;"><strong>Jenis Laporan</strong></td>
                    <td>: {{ $jenis === 'internal' ? 'Laporan Internal Puskesmas' : 'Laporan Dinas (SPJ / Lunas)' }}</td>
                </tr>
                <tr>
                    <td><strong>Periode Bulan</strong></td>
                    <td>: {{ $bulanAwal }} s/d {{ $bulanAkhir }}</td>
                    <td><strong>Kategori Barang</strong></td>
                    <td>: {{ $kategoriName }}</td>
                </tr>
            </table>
        </div>

        <div class="kategori-title">
            Daftar Persediaan: {{ $kategoriName }}
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 8%;">Satuan</th>
                    <th style="width: 12%;">Harga Satuan</th>
                    <th style="width: 10%;">Saldo Awal<br><small>({{ $bulanAwalRaw }})</small></th>
                    <th style="width: 10%;">Penerimaan</th>
                    <th style="width: 10%;">Pengeluaran</th>
                    <th style="width: 10%;">Sisa<br><small>({{ $bulanAkhirRaw }})</small></th>
                    <th style="width: 10%;">Total Nilai Sisa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td class="center">{{ $item['satuan'] }}</td>
                    <td class="right">{{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                    <td class="center">{{ $item['saldo_awal'] }}</td>
                    <td class="center">{{ $item['penerimaan'] }}</td>
                    <td class="center">{{ $item['pengeluaran'] }}</td>
                    <td class="center"><strong>{{ $item['sisa'] }}</strong></td>
                    <td class="right"><strong>{{ number_format($item['total_nilai'], 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="center" style="padding: 20px;">Tidak ada barang untuk kategori ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($items) > 0)
            <tfoot>
                <tr class="footer">
                    <td colspan="8" class="right">SUBTOTAL NILAI SISA {{ strtoupper($kategoriName) }}</td>
                    <td class="right">Rp {{ number_format($items->sum('total_nilai'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        @if($iteration < $totalKategori)
            <div class="page-break"></div>
        @else
            <div class="footer-note">
                <p>Dokumen ini dihasilkan secara otomatis oleh sistem e-Aset. Akhir dari laporan.</p>
            </div>
        @endif
    @endforeach

</body>
</html>
