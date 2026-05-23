<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Stok - {{ $item->nama_barang }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 4px; vertical-align: top; border: none; font-size: 12px; }
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
        <h1>KARTU STOK PERSEDIAAN</h1>
        <p>{{ strtoupper($nama_kategori) }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Barang</strong></td>
            <td width="2%">:</td>
            <td width="33%">{{ $item->nama_barang }}</td>
            
            <td width="15%"><strong>Satuan</strong></td>
            <td width="2%">:</td>
            <td width="33%">{{ $item->satuan }}</td>
        </tr>
        <tr>
            <td><strong>Kategori</strong></td>
            <td>:</td>
            <td>{{ $item->kategori }}</td>
            
            <td><strong>Sisa Stok Akhir</strong></td>
            <td>:</td>
            <td><strong>{{ $item->stok_sekarang }}</strong></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="10%">Masuk</th>
                <th width="10%">Keluar</th>
                <th width="10%">Sisa</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($item->transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                <td>{{ $tx->keterangan ?: '-' }}</td>
                <td class="text-center">{{ $tx->jenis_transaksi == 'masuk' ? $tx->jumlah : '-' }}</td>
                <td class="text-center">{{ $tx->jenis_transaksi == 'keluar' ? $tx->jumlah : '-' }}</td>
                <td class="text-center"><strong>{{ $tx->running_balance }}</strong></td>
                <td class="text-center">{{ $tx->status_hutang ? 'Hutang' : 'Lunas/SPJ' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada riwayat mutasi/transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
