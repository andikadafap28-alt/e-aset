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
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
                <th width="24%">Nama Aset</th>
                <th width="15%">Kategori</th>
                <th width="10%">Tahun</th>
                <th width="10%">Kondisi</th>
                <th width="10%">Lokasi</th>
                <th width="15%">Harga Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPrice = 0; @endphp
            @forelse($data as $index => $row)
            @php $totalPrice += $row->harga_perolehan; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row->asset_code }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->category ? $row->category->nama_kategori : ($row->getAttribute('category') ?: '-') }}</td>
                <td class="text-center">{{ $row->year_purchased }}</td>
                <td class="text-center">{{ $row->condition }}</td>
                <td>{{ $row->location }}</td>
                <td class="text-right">Rp {{ number_format($row->harga_perolehan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" class="text-right">Total Keseluruhan</th>
                <th class="text-right">Rp {{ number_format($totalPrice, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
