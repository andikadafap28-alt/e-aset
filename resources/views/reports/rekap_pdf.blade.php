<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        .meta-info { margin-bottom: 20px; font-size: 12px; }
        .meta-info table { width: 100%; }
        .meta-info td { vertical-align: top; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        table.data-table td.center { text-align: center; }
        table.data-table td.right { text-align: right; }
        table.data-table tr.footer { font-weight: bold; background-color: #e2e8f0; }
        .footer-note { margin-top: 30px; text-align: right; font-size: 11px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Aplikasi Manajemen Aset Puskesmas Mantup</p>
    </div>

    <div class="meta-info">
        <table style="width: 50%">
            <tr>
                <td style="width: 120px;"><strong>Tanggal Cetak</strong></td>
                <td>: {{ $date }}</td>
            </tr>
            <tr>
                <td><strong>Dikelompokkan Berdasarkan</strong></td>
                <td>: {{ $groupBy === 'location' ? 'Ruangan / Lokasi' : 'Kategori Aset' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">{{ $groupBy === 'location' ? 'Ruangan / Lokasi' : 'Kategori Aset' }}</th>
                <th style="width: 13%;">Total Aset</th>
                <th style="width: 13%;">Kondisi Baik</th>
                <th style="width: 13%;">Rusak Ringan</th>
                <th style="width: 13%;">Rusak Berat</th>
                <th style="width: 13%;">Total Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapResult as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item['group_name'] }}</td>
                <td class="center">{{ $item['total_aset'] }}</td>
                <td class="center">{{ $item['baik'] }}</td>
                <td class="center">{{ $item['rusak_ringan'] }}</td>
                <td class="center">{{ $item['rusak_berat'] }}</td>
                <td class="right">{{ number_format($item['total_nilai'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer">
                <td colspan="2" class="right">TOTAL KESELURUHAN</td>
                <td class="center">{{ array_sum(array_column($rekapResult, 'total_aset')) }}</td>
                <td class="center">{{ array_sum(array_column($rekapResult, 'baik')) }}</td>
                <td class="center">{{ array_sum(array_column($rekapResult, 'rusak_ringan')) }}</td>
                <td class="center">{{ array_sum(array_column($rekapResult, 'rusak_berat')) }}</td>
                <td class="right">{{ number_format(array_sum(array_column($rekapResult, 'total_nilai')), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        <p>Dokumen ini dihasilkan secara otomatis oleh sistem e-Aset.</p>
    </div>

</body>
</html>
