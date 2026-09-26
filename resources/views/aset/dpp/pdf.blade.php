<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dokumen Persiapan Pengadaan</title>
    <style>
        @page {
            margin: 1cm 2.54cm 1cm 3.54cm; /* Top Right Bottom Left */
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        p {
            margin: 2px 0;
            padding: 0;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 4px double black;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .kop-surat table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-surat td {
            text-align: center;
            vertical-align: middle;
        }
        .logo-kiri {
            width: 80px;
        }
        .logo-kanan {
            width: 80px;
        }
        .kop-teks {
            font-size: 12pt;
        }
        .kop-teks h1 {
            margin: 0;
            padding: 0;
            font-weight: bold;
            font-size: 14pt;
            line-height: 1.15;
        }
        .kop-teks h2 {
            margin: 0;
            padding: 0;
            font-weight: normal;
            font-size: 14pt;
            line-height: 1.15;
        }
        .kop-teks p {
            margin: 0;
            font-size: 10pt;
            line-height: 1.15;
        }
        .judul-surat {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .isi-surat {
            text-align: justify;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
        .table-data tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .table-data th, .table-data td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
            text-align: justify;
        }
        .ttd {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 300px;
            text-align: center;
            position: relative;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @php
        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanIndo = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
        
        $hasGd = extension_loaded('gd');
        $logoLamonganPath = public_path('img/logo-lamongan.png');
        $logoHusadaPath = public_path('img/logo-husada.png');
        $ttdPath = public_path('images/ttd_baru.png');
        
        $logoLamonganBase64 = ($hasGd && file_exists($logoLamonganPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoLamonganPath)) : '';
        $logoHusadaBase64 = ($hasGd && file_exists($logoHusadaPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoHusadaPath)) : '';
        $ttdBase64 = ($hasGd && file_exists($ttdPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($ttdPath)) : '';
    @endphp
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-kiri">
                    @if($hasGd && $logoLamonganBase64)
                        <img src="{{ $logoLamonganBase64 }}" style="width: 70px;">
                    @endif
                </td>
                <td class="kop-teks">
                    <h2>PEMERINTAH KABUPATEN LAMONGAN</h2>
                    <h2>DINAS KESEHATAN</h2>
                    <h1>PUSKESMAS MANTUP</h1>
                    <p>Alamat : Jln. Raya Mantup No 55 Mantup Lamongan 62283</p>
                    <p>Telp. (0322) 4670302 Email: puskesmasmantup98@gmail.com</p>
                </td>
                <td class="logo-kanan">
                    @if($hasGd && $logoHusadaBase64)
                        <img src="{{ $logoHusadaBase64 }}" style="width: 80px;">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="judul-surat">
        DOKUMEN PERSIAPAN PENGADAAN (DPP)<br>
        Nomor: {{ str_contains($dpp->nomor_surat, 'PPBJ') ? $dpp->nomor_surat : '000.3.1/' . $dpp->nomor_surat . '/PPBJ/413.102.5.18/' . date('Y') }}
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 120px;">Nama</td>
                <td style="width: 10px;">:</td>
                <td>dr. MUHAMAD SUNARYADI</td>
            </tr>
            <tr>
                <td>Selaku</td>
                <td>:</td>
                <td>Pejabat Pembuat Komitmen (PPK) pada Puskesmas Mantup</td>
            </tr>
        </table>
        
        <p>
            @php
                $tgl = \Carbon\Carbon::parse($dpp->tanggal_dpp);
                $hari = $hariIndo[$tgl->format('l')] ?? $tgl->format('l');
                $bulan = $bulanIndo[$tgl->format('F')] ?? $tgl->format('F');
            @endphp
            Pada hari ini {{ $hari }} tanggal {{ $tgl->format('d') }} bulan {{ $bulan }} tahun {{ $tgl->format('Y') }} menetapkan Dokumen Persiapan Pengadaan (DPP) sebagai berikut:
        </p>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 20px;">1.</td>
                <td style="width: 100px;">Nama Paket</td>
                <td style="width: 10px;">:</td>
                <td>{{ $dpp->nama_paket ?? 'Belanja Barang dan Jasa' }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Kode RUP</td>
                <td>:</td>
                <td>{{ $dpp->kode_rup }}</td>
            </tr>
        </table>

        <p>Spesifikasi Teknis, Pagu Anggaran, dan Rancangan Kontrak sebagai berikut:</p>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 35%; text-align: center;">Spesifikasi Teknis</th>
                    <th style="width: 15%; text-align: center;">Spesifikasi<br>Jumlah</th>
                    <th style="width: 25%; text-align: center;">Harga Satuan<br>termasuk Pajak</th>
                    <th style="width: 20%; text-align: center;">Pagu Anggaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $dpp->spesifikasi_teknis }}</td>
                    <td style="text-align: center;">{{ $dpp->jumlah }}</td>
                    <td style="text-align: right; white-space: nowrap;">Rp. {{ number_format($dpp->harga_satuan, 2, ',', '.') }}</td>
                    <td style="text-align: right; white-space: nowrap;">Rp. {{ number_format($dpp->pagu_anggaran, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5">
                        @php
                            $mulai = \Carbon\Carbon::parse($dpp->tanggal_mulai);
                            $selesai = \Carbon\Carbon::parse($dpp->tanggal_selesai);
                            $tglMulai = $mulai->format('j') . ' ' . ($bulanIndo[$mulai->format('F')] ?? $mulai->format('F')) . ' ' . $mulai->format('Y');
                            $tglSelesai = $selesai->format('j') . ' ' . ($bulanIndo[$selesai->format('F')] ?? $selesai->format('F')) . ' ' . $selesai->format('Y');
                        @endphp
                        <u>Spesifikasi Waktu:</u><br>
                        Pelaksanaan pekerjaan mulai tanggal {{ $tglMulai }} s.d. {{ $tglSelesai }},
                        spesifikasi jumlah yang tertuang bersifat perkiraan, jumlah pesanan sesuai yang tertuang
                        di Surat Undangan/pemberitahuan tertulis yang akan disampaikan kepada Penyedia
                        Barang/Jasa. Penyedia Barang/Jasa melakukan pengiriman barang/jasa setelah menerima
                        rekaman Surat Undangan/pemberitahuan tertulis.
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <u>Spesifikasi Layanan:</u><br>
                        Termasuk pengiriman sampai ke lokasi Puskesmas Mantup, Jln. Raya Mantup No. 55 Mantup Lamongan.
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        - Prioritas TKDN, PPK/PP memilih dengan urutan/prioritas:<br>
                        &nbsp;&nbsp;1. Produk dalam negeri dengan nilai TKDN paling sedikit 25%;<br>
                        &nbsp;&nbsp;2. Produk dalam negeri dengan nilai TKDN kurang dari 25%;<br>
                        &nbsp;&nbsp;3. produk dengan label PDN namun belum mempunyai nilai TKDN;<br>
                        &nbsp;&nbsp;4. Produk impor;<br>
                        &nbsp;&nbsp;5. Menggunakan metode lain selain <i>E-purchasing</i> Katalog.<br>
                        - Berdasarkan urutan/prioritas diatas, barang/jasa yang ditetapkan memiliki nilai TKDN 0%, dengan alasan: ....... (contoh: pada Katalog tidak terdapat PDN dengan nilai TKDN tetapi berdasarkan pernyataan Penyedia di Katalog menyatakan sebagai PDN sesuai dokumentasi <u>sebagaimana terlampir</u>).<br>
                        - Jika terdapat penyebutan merek barang/jasa, ditetapkan justifikasi teknis yang menjelaskan alasan, pertimbangan, bukti/fakta terhadap kebutuhan atas suatu merek tertentu. Justifikasi teknis sebagai berikut:<br>
                        &nbsp;&nbsp;a. Sesuai dengan hasil perencanaan pengadaan yang dituangkan pada DPA-SKPD.<br>
                        &nbsp;&nbsp;b. Mempertimbangkan nilai TKDN.<br>
                        &nbsp;&nbsp;c. .......<br>
                        - Referensi harga yang berfungsi sebagai referensi untuk melakukan Negosiasi Harga <u>sebagaimana terlampir</u>.<br>
                        - Rancangan kontrak ditetapkan menggunakan Surat Pesanan sebagaimana terlampir atau jika tidak ada maka menggunakan Surat Pesanan sesuai yang tertuang di aplikasi <i>E-purchasing</i>.
                    </td>
                </tr>
            </tbody>
        </table>

        <p>Demikian Dokumen Persiapan Pengadaan ini dibuat untuk digunakan sebagai dasar pelaksanaan pengadaan. Pelaksanaan pengadaan berdasarkan prioritas penggunaan produk dalam negeri dan prioritas penggunaan produk dari penyedia dengan kualifikasi usaha kecil serta koperasi.</p>

        <div class="ttd clearfix">
            <div class="ttd-box">
                Menetapkan,<br>
                Pejabat Pembuat Komitmen pada<br>
                Puskesmas Mantup<br>
                
                @if($hasGd && $ttdBase64)
                    <img src="{{ $ttdBase64 }}" style="width: 220px; position: absolute; top: -10px; left: -40px; z-index: -1;">
                @endif
                <br><br><br><br>
                
                <b><u>dr. MUHAMAD SUNARYADI</u></b><br>
                NIP. 19690313 200212 1 007
            </div>
        </div>
    </div>
</body>
</html>
