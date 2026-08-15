<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Serah Terima Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat img {
            width: 80px;
        }
        .kop-teks {
            text-align: center;
            flex-grow: 1;
        }
        .kop-teks h3, .kop-teks h2 {
            margin: 0;
            font-weight: bold;
        }
        .kop-teks p {
            margin: 0;
            font-size: 11pt;
        }
        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .data-penerima table {
            margin-bottom: 20px;
        }
        .data-penerima td {
            padding: 3px 0;
            vertical-align: top;
        }
        .data-penerima td:first-child {
            width: 150px;
        }
        .tabel-barang {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tabel-barang th, .tabel-barang td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .tabel-barang th {
            text-align: center;
        }
        .teks-penutup {
            margin-bottom: 40px;
            text-align: justify;
        }
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .ttd-box {
            text-align: center;
            width: 30%;
        }
        .ttd-tengah {
            width: 40%;
            margin-top: -30px;
        }
        .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 80px;
        }
        @media print {
            body {
                padding: 0;
            }
            .btn-print {
                display: none;
            }
        }
        .btn-print {
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #4e73df;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    @php
        function formatName($name) {
            if (!$name) return '-';
            $name = ucwords(strtolower($name), " .-");
            $name = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $name);
            return $name;
        }
    @endphp
    <button class="btn-print" onclick="window.print()">Print Dokumen</button>

    <div class="kop-surat">
        <!-- Gunakan placeholder image jika logo belum ada -->
        <img src="{{ asset('img/logo-lamongan.png') }}" alt="Logo Lamongan" onerror="this.src='https://via.placeholder.com/80?text=Logo'">
        <div class="kop-teks">
            <h3>PEMERINTAH KABUPATEN LAMONGAN</h3>
            <h3>DINAS KESEHATAN</h3>
            <h2>PUSKESMAS MANTUP</h2>
            <p>Jln. Raya Mantup No. 55 Mantup Lamongan</p>
            <p>Telp. (0322) 4670302 Email : puskesmasmantup@yahoo.co.id</p>
        </div>
        <img src="{{ asset('img/logo-husada.png') }}" alt="Logo Husada" onerror="this.src='https://via.placeholder.com/80?text=Bakti+Husada'">
    </div>

    <div class="judul">
        BUKTI SERAH TERIMA BARANG
    </div>

    <p>Bersama ini telah kami serahkan kepada :</p>
    <div class="data-penerima">
        <table border="0">
            <tr>
                <td>Nama</td>
                <td>: {{ formatName($bast->employee->name ?? '') }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>: {{ $bast->employee->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $bast->employee->golongan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Untuk Keperluan</td>
                <td>: {{ $bast->keperluan }}</td>
            </tr>
        </table>
    </div>

    <p>Berupa barang yang tersebut di bawah ini :</p>
    <table class="tabel-barang">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>NAMA<br>BARANG</th>
                <th>MERK /<br>TYPE</th>
                <th>BANYAKNYA</th>
                <th>KET.</th>
                <th>SUMBER<br>DANA</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($groupedItems) && $groupedItems->count() > 0)
                @foreach($groupedItems as $index => $item)
                <tr>
                    @if($index === 0)
                    <td style="text-align: center;" rowspan="{{ $groupedItems->count() }}">{{ \Carbon\Carbon::parse($bast->handover_date)->isoFormat('D MMMM Y') }}</td>
                    @endif
                    <td>{{ $item->asset->name ?? '-' }}</td>
                    <td>{{ $item->asset->merk ?? 'Tanpa Merk' }} / {{ $item->asset->type ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->qty }} unit</td>
                    @if($index === 0)
                    <td rowspan="{{ $groupedItems->count() }}">{{ $bast->keterangan }}</td>
                    <td rowspan="{{ $groupedItems->count() }}">{{ $bast->sumber_dana }}</td>
                    @endif
                </tr>
                @endforeach
            @else
                <tr>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($bast->handover_date)->isoFormat('D MMMM Y') }}</td>
                    <td>{{ $bast->asset->name ?? '-' }}</td>
                    <td>{{ $bast->asset->merk ?? 'Tanpa Merk' }} / {{ $bast->asset->type ?? '-' }}</td>
                    <td style="text-align: center;">1 unit</td>
                    <td>{{ $bast->keterangan }}</td>
                    <td>{{ $bast->sumber_dana }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="teks-penutup">
        Yang selanjutnya akan diserahkan kepada koordinator yang ada di {{ $bast->keperluan }} dengan daftar nama terlampir.
    </div>

    <div class="ttd-container">
        <div class="ttd-box">
            <br>
            Pengurus barang Pembantu<br>
            Puskesmas Mantup
            <div class="nama-ttd">Andika Dafa Penta Pratama</div>
            NIP. .............................
        </div>
        
        <div class="ttd-box">
            Mantup, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
            Penerima Barang
            <div class="nama-ttd">{{ formatName($bast->employee->name ?? '') }}</div>
            NIP. {{ $bast->employee->nip ?? '-' }}
        </div>
    </div>

    <div class="ttd-container" style="justify-content: center;">
        <div class="ttd-box ttd-tengah">
            Mengetahui,<br>
            Kepala Puskesmas Mantup
            <div class="nama-ttd">dr. Muhamad Sunaryadi</div>
            NIP. 196903132002121007
        </div>
    </div>
</body>
</html>
