<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penghapusan Aset - {{ $asset->asset_code }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 30px; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; height: auto; position: absolute; top: 30px; left: 30px; }
        .header-title { font-weight: bold; font-size: 14pt; }
        .header-subtitle { font-size: 12pt; }
        .content { margin-top: 20px; text-align: justify; }
        .table-info { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table-info td { vertical-align: top; padding: 4px 0; }
        .table-info td:first-child { width: 25%; font-weight: bold; }
        .table-info td:nth-child(2) { width: 2%; }
        .signature-box { width: 100%; margin-top: 50px; }
        .signature-table { width: 100%; text-align: center; border-collapse: collapse; }
        .signature-table td { width: 50%; padding-top: 80px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">PEMERINTAH KABUPATEN LAMONGAN</div>
        <div class="header-subtitle">DINAS KESEHATAN</div>
        <div class="header-title" style="font-size: 16pt;">PUSKESMAS MANTUP</div>
        <div style="font-size: 10pt;">Jl. Raya Mantup No. XX, Mantup, Lamongan</div>
    </div>

    <div class="content">
        <h3 style="text-align: center; text-decoration: underline; margin-bottom: 5px;">BERITA ACARA PENGHAPUSAN ASET</h3>
        <p style="text-align: center; margin-top: 0;">Nomor: BA/PENGHAPUSAN/{{ date('Y/m/d') }}/{{ $asset->id }}</p>

        <p>Pada hari ini, tanggal {{ \Carbon\Carbon::parse($disposal->tanggal_penghapusan)->isoFormat('D MMMM Y') }}, bertempat di Puskesmas Mantup, telah dilakukan penghapusan Barang Milik Daerah/Puskesmas berupa:</p>

        <table class="table-info">
            <tr>
                <td>Kode Aset / NUP</td>
                <td>:</td>
                <td>{{ $asset->asset_code }}</td>
            </tr>
            <tr>
                <td>Nama Aset</td>
                <td>:</td>
                <td>{{ $asset->name }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $asset->category ? $asset->category->nama_kategori : ($asset->getAttribute('category') ?: '-') }}</td>
            </tr>
            <tr>
                <td>Tahun Pengadaan</td>
                <td>:</td>
                <td>{{ $asset->year_purchased }}</td>
            </tr>
            <tr>
                <td>Harga Perolehan</td>
                <td>:</td>
                <td>Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lokasi Terakhir</td>
                <td>:</td>
                <td>{{ $asset->location }}</td>
            </tr>
        </table>

        <p>Aset tersebut dinyatakan <strong>dihapus</strong> dari Daftar Inventaris Puskesmas Mantup dikarenakan:</p>
        <div style="border: 1px solid black; padding: 10px; margin: 10px 0; background-color: #f9f9f9;">
            <p style="margin: 0;"><strong>Alasan Utama:</strong> {{ $disposal->alasan }}</p>
            <p style="margin: 5px 0 0 0;"><strong>Catatan Tambahan:</strong><br/>{{ $disposal->catatan ?: '-' }}</p>
        </div>

        <p>Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="signature-box">
            <table class="signature-table">
                <tr>
                    <td>
                        <p style="margin-top: -60px;">Mengetahui,</p>
                        <p><strong>Kepala Puskesmas Mantup</strong></p>
                        <br><br><br>
                        <p style="text-decoration: underline;">(...............................................)</p>
                        <p>NIP. ........................................</p>
                    </td>
                    <td>
                        <p style="margin-top: -60px;">Mantup, {{ \Carbon\Carbon::parse($disposal->tanggal_penghapusan)->isoFormat('D MMMM Y') }}</p>
                        <p><strong>Pengurus Barang</strong></p>
                        <br><br><br>
                        <p style="text-decoration: underline;">(...............................................)</p>
                        <p>NIP. ........................................</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
