<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pemeliharaan Alat - {{ $room->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            margin: 20px;
        }
        h2 { text-align: center; text-transform: uppercase; }
        .info { margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
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
            font-weight: bold;
        }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print Dokumen</button>

    <h2>KARTU PEMELIHARAAN ALAT</h2>
    <div class="info">
        <strong>RUANGAN:</strong> {{ strtoupper($room->name) }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Alat (Merk)</th>
                <th style="width: 15%;">NIBAR</th>
                <th style="width: 10%;">Tgl Pemeliharaan</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 15%;">Penyedia / Pelaksana</th>
                <th style="width: 25%;">Keterangan / Hasil</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $hasData = false; @endphp
            @foreach($room->assets as $asset)
                @foreach($asset->maintenances as $m)
                    @php $hasData = true; @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $asset->name }} ({{ $asset->merk ?? '-' }})</td>
                        <td>{{ $asset->asset_code }}</td>
                        <td class="text-center">{{ $m->tanggal_pelaksanaan ? \Carbon\Carbon::parse($m->tanggal_pelaksanaan)->format('d/m/Y') : \Carbon\Carbon::parse($m->tanggal_jadwal)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $m->jenis_pemeliharaan }}</td>
                        <td>{{ $m->penyedia ?? '-' }}</td>
                        <td>{{ $m->catatan_hasil ?? $m->catatan }}</td>
                    </tr>
                @endforeach
            @endforeach
            
            @if(!$hasData)
            <tr>
                <td colspan="7" class="text-center">Belum ada riwayat pemeliharaan alat di ruangan ini.</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
