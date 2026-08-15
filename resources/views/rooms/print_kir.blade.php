<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIR - {{ $room->name }}</title>
    <style>
        @page {
            size: 215.9mm 330.2mm; /* F4 / Folio */
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            background: white;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        table.header-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        table.header-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        table.header-table td.label {
            width: 250px;
        }
        table.header-table td.separator {
            width: 15px;
        }
        h2 {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        table.data-table thead {
            display: table-header-group;
        }
        table.data-table th, table.data-table td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: middle;
        }
        table.data-table th {
            text-align: center;
            font-weight: bold;
        }
        .ttd-container {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 40%;
            float: left;
            text-align: center;
        }
        .ttd-box-right {
            width: 40%;
            float: right;
            text-align: center;
        }
        .nama-ttd {
            margin-top: 70px;
            text-decoration: underline;
            font-weight: bold;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4e73df;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print Dokumen</button>

    <h2>KARTU INVENTARIS RUANGAN</h2>

    <table class="header-table">
        <tr>
            <td class="label">PROPINSI</td><td class="separator">:</td><td>JAWA TIMUR</td>
        </tr>
        <tr>
            <td class="label">KABUPATEN/KOTAMADYA</td><td class="separator">:</td><td>LAMONGAN</td>
        </tr>
        <tr>
            <td class="label">UNIT</td><td class="separator">:</td><td>DINAS KESEHATAN</td>
        </tr>
        <tr>
            <td class="label">SATUAN KERJA</td><td class="separator">:</td><td>PUSKESMAS MANTUP</td>
        </tr>
        <tr>
            <td class="label">R U A N G A N</td><td class="separator">:</td><td>{{ strtoupper($room->name) }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No. Urut</th>
                <th rowspan="2" style="width: 25%;">Jenis Barang / Nama Barang</th>
                <th rowspan="2" style="width: 15%;">Merek / Model</th>
                <th rowspan="2" style="width: 10%;">Bahan</th>
                <th rowspan="2" style="width: 10%;">Jumlah<br>Barang</th>
                <th colspan="3">Keadaan Barang</th>
                <th rowspan="2" style="width: 15%;">Keterangan<br>Mutasi dll.</th>
            </tr>
            <tr>
                <th style="width: 6%;">Baik</th>
                <th style="width: 6%;">Kurang<br>Baik</th>
                <th style="width: 6%;">Rusak<br>Berat</th>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <th style="font-weight: normal; font-size: 8pt;">1</th>
                <th style="font-weight: normal; font-size: 8pt;">2</th>
                <th style="font-weight: normal; font-size: 8pt;">3</th>
                <th style="font-weight: normal; font-size: 8pt;">6</th>
                <th style="font-weight: normal; font-size: 8pt;">9</th>
                <th style="font-weight: normal; font-size: 8pt;">10</th>
                <th style="font-weight: normal; font-size: 8pt;">11</th>
                <th style="font-weight: normal; font-size: 8pt;">12</th>
                <th style="font-weight: normal; font-size: 8pt;">13</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedItems as $item)
            <tr>
                <td class="text-center">{{ $item->no }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->merk }}</td>
                <td>{{ $item->bahan }}</td>
                <td class="text-center">{{ $item->total }}</td>
                <td class="text-center">{{ $item->baik > 0 ? $item->baik : '' }}</td>
                <td class="text-center">{{ $item->kurang_baik > 0 ? $item->kurang_baik : '' }}</td>
                <td class="text-center">{{ $item->rusak_berat > 0 ? $item->rusak_berat : '' }}</td>
                <td>{{ $item->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Belum ada barang di ruangan ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            Mengetahui<br>
            Kepala Puskesmas Mantup
            <div class="nama-ttd">dr. Muhamad Sunaryadi</div>
            NIP. 196903132002121007
        </div>
        <div class="ttd-box-right">
            Mantup, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
            Pengurus Barang Puskesmas Mantup
            <div class="nama-ttd">Andika Dafa Penta Pratama</div>
            NIP. .............................
        </div>
    </div>
</body>
</html>
