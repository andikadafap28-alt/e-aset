<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST Peminjaman Aset</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3, .header h2, .header p {
            margin: 0;
            padding: 0;
        }
        .header h3 {
            font-size: 16px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 20px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            font-size: 12px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 20px;
            text-align: justify;
        }
        table.info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table.info-table td {
            vertical-align: top;
            padding: 4px;
        }
        .signature-section {
            width: 100%;
            margin-top: 50px;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .qr-code {
            margin: 10px auto;
            width: 100px;
            height: 100px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>PEMERINTAH KABUPATEN LAMONGAN</h3>
        <h3>DINAS KESEHATAN</h3>
        <h2>PUSKESMAS MANTUP</h2>
        <p>Jl. Raya Mantup No. 12, Kec. Mantup, Kab. Lamongan, Jawa Timur</p>
    </div>

    <div class="title">BERITA ACARA SERAH TERIMA PEMINJAMAN ASET</div>
    <div class="subtitle">Nomor: BAST/PMJ/{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}/{{ date('Y') }}</div>

    <div class="content">
        <p>Pada hari ini, tanggal {{ $date }}, bertempat di Puskesmas Mantup, kami yang bertanda tangan di bawah ini:</p>
        
        <table class="info-table">
            <tr>
                <td width="3%">1.</td>
                <td width="27%">Nama Lengkap</td>
                <td width="2%">:</td>
                <td width="68%"><strong>{{ $loan->approver ? $loan->approver->name : 'Kepala Puskesmas' }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td>Pihak yang Menyerahkan (Kepala Puskesmas)</td>
            </tr>
        </table>

        <p>Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

        <table class="info-table">
            <tr>
                <td width="3%">2.</td>
                <td width="27%">Nama Lengkap</td>
                <td width="2%">:</td>
                <td width="68%"><strong>{{ $loan->borrower_name }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Kontak</td>
                <td>:</td>
                <td>{{ $loan->borrower_contact ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan / Unit</td>
                <td>:</td>
                <td>Pihak yang Menerima (Peminjam)</td>
            </tr>
        </table>

        <p>Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>

        <p><strong>PIHAK PERTAMA</strong> menyerahkan barang milik daerah kepada <strong>PIHAK KEDUA</strong> untuk keperluan dinas dengan rincian sebagai berikut:</p>

        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td width="25%">Nama Aset</td>
                <td width="2%">:</td>
                <td>{{ $loan->asset->name }}</td>
            </tr>
            <tr>
                <td>Kode Aset</td>
                <td>:</td>
                <td>{{ $loan->asset->asset_code }}</td>
            </tr>
            <tr>
                <td>Kondisi</td>
                <td>:</td>
                <td>{{ $loan->asset->condition }}</td>
            </tr>
            <tr>
                <td>Tgl. Pinjam</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tgl. Jatuh Tempo</td>
                <td>:</td>
                <td>{{ $loan->expected_return_date ? \Carbon\Carbon::parse($loan->expected_return_date)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Keperluan / Catatan</td>
                <td>:</td>
                <td>{{ $loan->notes ?? '-' }}</td>
            </tr>
        </table>

        <p><strong>PIHAK KEDUA</strong> bertanggung jawab atas perawatan dan keutuhan barang tersebut selama masa peminjaman, serta wajib mengembalikan tepat waktu.</p>
        
        <p>Demikian Berita Acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <table class="signature-section">
        <tr>
            <td>
                <p><strong>PIHAK KEDUA</strong></p>
                <p>Yang Menerima,</p>
                <br><br><br><br><br>
                <p class="signature-name">{{ strtoupper($loan->borrower_name) }}</p>
                <p>Peminjam</p>
            </td>
            <td>
                <p><strong>PIHAK PERTAMA</strong></p>
                <p>Yang Menyerahkan,</p>
                <div class="qr-code">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code Validasi" width="100" height="100">
                </div>
                <p class="signature-name">{{ strtoupper($loan->approver ? $loan->approver->name : 'KEPALA PUSKESMAS') }}</p>
                <p>Tanda Tangan Elektronik Valid</p>
            </td>
        </tr>
    </table>

</body>
</html>
