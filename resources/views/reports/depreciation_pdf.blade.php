<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 12px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; }
        .filter-info span { display: inline-block; margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { width: 40%; float: right; border: 1px solid #000; padding: 10px; background-color: #f9f9f9; }
        .summary-box p { margin: 4px 0; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <div class="filter-info">
        <strong>Filter Diterapkan:</strong><br>
        <span>Kategori: {{ $filters['category'] }}</span>
        <span>Kondisi: {{ $filters['condition'] }}</span>
        <span>Tahun: {{ $filters['years'] }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode Aset</th>
                <th width="20%">Nama Aset</th>
                <th width="12%">Kategori</th>
                <th width="8%">Tahun Beli</th>
                <th width="8%">Umur<br>Eko.</th>
                <th width="12%">Harga Perolehan</th>
                <th width="12%">Akum. Penyusutan</th>
                <th width="12%">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row->asset_code }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->category ? $row->category->nama_kategori : ($row->getAttribute('category') ?: '-') }}</td>
                <td class="text-center">{{ $row->year_purchased }}</td>
                <td class="text-center">{{ $row->category ? $row->category->umur_ekonomis : '-' }} Thn</td>
                <td class="text-right">Rp {{ number_format($row->harga_perolehan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row->accumulated_depreciation, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row->book_value, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($data->isNotEmpty())
    <div class="summary-box">
        <p>Total Harga Perolehan: <span style="float:right">Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</span></p>
        <p>Total Akum. Penyusutan: <span style="float:right">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</span></p>
        <hr>
        <p>Total Nilai Buku: <span style="float:right">Rp {{ number_format($totalBookValue, 0, ',', '.') }}</span></p>
    </div>
    @endif

</body>
</html>
