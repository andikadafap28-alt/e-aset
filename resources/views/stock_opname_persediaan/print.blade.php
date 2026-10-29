<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Stock Opname Persediaan - {{ $record->getTanggalFormatted() }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Google Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #000000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Screen Only) */
        .top-toolbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #1e293b;
            color: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.15);
            font-family: 'Inter', sans-serif;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
        }
        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-toggle {
            background-color: #334155;
            color: #e2e8f0;
            border: 1px solid #475569;
        }
        .btn-toggle:hover {
            background-color: #475569;
            color: white;
        }

        .btn-toggle.active {
            background-color: #0284c7;
            color: white;
            border-color: #38bdf8;
        }

        .btn-back {
            background-color: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
        }
        .btn-back:hover {
            background-color: #334155;
            color: white;
        }

        /* Paper Simulation on Screen */
        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            padding: 18mm 20mm;
            margin: 25px auto;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Page Break for Print */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        /* Kop Surat (Persis PDF) */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }

        .kop-logo {
            width: 72px;
            height: auto;
            object-fit: contain;
        }

        .kop-teks {
            text-align: center;
            flex-grow: 1;
            padding: 0 10px;
        }

        .kop-teks h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-teks h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-teks h2 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .kop-teks p {
            margin: 1px 0;
            font-size: 8pt;
        }

        /* Judul Dokumen */
        .doc-title-container {
            text-align: center;
            margin-bottom: 16px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        .doc-number {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 3px 0 0 0;
        }

        /* Paragraf Pembuka */
        .content-p {
            text-align: justify;
            margin: 10px 0;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        /* Tabel Pihak */
        .table-parties {
            width: 100%;
            margin: 8px 0 12px 0;
            font-size: 10.5pt;
            border-collapse: collapse;
        }

        .table-parties td {
            padding: 2px 0;
            vertical-align: top;
        }

        .table-parties td.num {
            width: 25px;
            font-weight: bold;
        }

        .table-parties td.label {
            width: 90px;
        }

        .table-parties td.colon {
            width: 15px;
            text-align: center;
        }

        .table-parties td.val {
            font-weight: 500;
        }

        /* Tabel Daftar Nilai Rekening (Hal 1) */
        .table-rekening-wrap {
            margin: 12px 0 16px 20px;
            max-width: 480px;
        }

        .table-rekening {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
        }

        .table-rekening td {
            padding: 3px 0;
            vertical-align: top;
        }

        .table-rekening td.r-num {
            width: 25px;
        }

        .table-rekening td.r-name {
            font-weight: normal;
        }

        .table-rekening td.r-colon {
            width: 18px;
            text-align: center;
        }

        .table-rekening td.r-curr {
            width: 32px;
            text-align: left;
        }

        .table-rekening td.r-val {
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
            white-space: nowrap;
        }

        .row-total-line td {
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        .row-total-val td {
            padding-top: 6px;
            font-weight: bold;
        }

        /* Area Tanda Tangan */
        .ttd-wrapper {
            margin-top: 25px;
            width: 100%;
        }

        .ttd-table {
            width: 100%;
            border: none !important;
            border-collapse: collapse;
            text-align: center;
            font-size: 10.5pt;
            line-height: 1.35;
        }

        .ttd-table td {
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .ttd-role {
            font-weight: normal;
        }

        .ttd-name {
            font-weight: bold;
            font-size: 10.5pt;
        }

        .ttd-nip {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 2px;
        }

        /* Lembar Lampiran (Halaman 2) */
        .lampiran-meta {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }

        .lampiran-meta table {
            border-collapse: collapse;
        }

        .lampiran-meta td {
            padding: 1px 4px;
        }

        .lampiran-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .lampiran-title h3 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .lampiran-title h4 {
            margin: 3px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
        }

        .lampiran-title p {
            margin: 2px 0 0 0;
            font-size: 9.5pt;
            color: #374151;
        }

        /* Tabel Rincian Lampiran */
        .tabel-lampiran {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 16px;
        }

        .tabel-lampiran th, .tabel-lampiran td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .tabel-lampiran th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
        }

        .tabel-lampiran td.center {
            text-align: center;
        }

        .tabel-lampiran td.right {
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
            white-space: nowrap;
        }

        .tabel-lampiran tr.sub-item td {
            padding-top: 3px;
            padding-bottom: 3px;
            background-color: #fafafa;
        }

        .tabel-lampiran tr.total-row {
            font-weight: bold;
            background-color: #f1f5f9;
        }

        /* Print Media Query */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .top-toolbar {
                display: none !important;
            }

            .page-sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }

            /* Filter mode cetak halaman */
            body.print-only-hal1 #lembarKedua {
                display: none !important;
            }

            body.print-only-hal2 #lembarPertama {
                display: none !important;
            }
            body.print-only-hal2 #lembarKedua {
                page-break-before: avoid !important;
                break-before: avoid !important;
            }
        }
    </style>
</head>
<body>
    @php
        $tahunAnggaran = \Carbon\Carbon::parse($record->tanggal_opname)->format('Y');
        $bulanAnggaran = $record->getBulanTerbilang();
        $tglFormat = $record->getTanggalFormatted();
        $rekeningList = $record->getRekeningSummaryList();
    @endphp

    <!-- Top Action Toolbar (Layar Monitor Saja) -->
    <div class="top-toolbar">
        <div class="toolbar-group">
            <a href="{{ route('ba-stock-opname.index') }}" class="btn-action btn-back">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Daftar
            </a>
            <span style="font-size: 13px; font-weight: 500; color: #cbd5e1; border-left: 1px solid #475569; padding-left: 12px;">
                Berita Acara Stok Opname: <strong>{{ $tglFormat }}</strong> &bull; Total: <strong>Rp. {{ number_format($record->total_nilai, 2, ',', '.') }}</strong>
            </span>
        </div>

        <div class="toolbar-group">
            <a href="{{ route('ba-stock-opname.edit', $record->id) }}" class="btn-action btn-toggle" title="Edit Nilai Rekening">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Data
            </a>
            <button type="button" onclick="printMode('all')" class="btn-action btn-print" title="Cetak Berita Acara dan Lampiran (2 Halaman)">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Cetak 2 Halaman Lengkap
            </button>
            <button type="button" onclick="printMode('hal1')" class="btn-action btn-toggle" title="Hanya Cetak Halaman 1 (Berita Acara)">
                <span class="material-symbols-outlined text-[16px]">looks_one</span>
                Hal. 1 Saja
            </button>
            <button type="button" onclick="printMode('hal2')" class="btn-action btn-toggle" title="Hanya Cetak Halaman 2 (Lampiran Rincian)">
                <span class="material-symbols-outlined text-[16px]">looks_two</span>
                Hal. 2 Saja
            </button>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- HALAMAN PERTAMA: BERITA ACARA STOK OPNAME PERSEDIAAN   -->
    <!-- (Persis 100% Mengikuti File PDF Lampiran Pengguna)     -->
    <!-- ======================================================= -->
    <div class="page-sheet" id="lembarPertama">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('img/logo-lamongan.png') }}" class="kop-logo" alt="Logo Lamongan" onerror="this.style.display='none'">
            <div class="kop-teks">
                <h4>PEMERINTAH KABUPATEN LAMONGAN</h4>
                <h3>DINAS KESEHATAN</h3>
                <h2>PUSKESMAS MANTUP</h2>
                <p>Alamat : Jln. Raya Mantup No 55 Mantup Lamongan 62283</p>
                <p>Telp. (0322) 4670302 Email:puskesmasmantup98@gmail.com</p>
            </div>
            <img src="{{ asset('img/logo-husada.png') }}" class="kop-logo" alt="Logo Bakti Husada" onerror="this.style.display='none'">
        </div>

        <!-- Judul Berita Acara -->
        <div class="doc-title-container">
            <h1 class="doc-title">BERITA ACARA STOK OPNAME PERSEDIAAN</h1>
            <div class="doc-number">Nomor :{{ $record->nomor_surat }}</div>
        </div>

        <!-- Paragraf Pembuka -->
        <p class="content-p">
            Pada hari ini, {{ $record->getHariTerbilang() }} tanggal {{ $record->getTanggalTerbilang() }} bulan {{ $record->getBulanTerbilang() }} tahun {{ $record->getTahunTerbilang() }}, Kami yang bertanda tangan dibawah ini :
        </p>

        <!-- Tabel Pihak 1 & Pihak 2 -->
        <table class="table-parties">
            <tr>
                <td class="num">1.</td>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->nama_pemeriksa }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->nip_pemeriksa }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->jabatan_pemeriksa }}</td>
            </tr>

            <tr><td colspan="4" style="height: 6px;"></td></tr>

            <tr>
                <td class="num">2.</td>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->nama_diperiksa }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->nip_diperiksa }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="val">{{ $record->jabatan_diperiksa }}</td>
            </tr>
        </table>

        <!-- Paragraf Pernyataan -->
        <p class="content-p">
            Menyatakan bahwa telah melakukan Stock Opname barang persediaan bulan {{ $bulanAnggaran }} Tahun Anggaran {{ $tahunAnggaran }}, sebagaimana terlampir dalam lampiran berita acara opname (Stock Opname). Adapun total Nilai Barang Persediaan berdasarkan Jenis Rekeningnya per {{ $tglFormat }} adalah :
        </p>

        <!-- Tabel Rekapitulasi Jenis Rekening (Persis PDF) -->
        <div class="table-rekening-wrap">
            <table class="table-rekening">
                @php $no = 1; @endphp
                @foreach($rekeningList as $idx => $rek)
                    <tr>
                        <td class="r-num">{{ $no++ }}.</td>
                        <td class="r-name">{{ $rek['name'] }}</td>
                        <td class="r-colon">:</td>
                        <td class="r-curr">Rp.</td>
                        <td class="r-val">{{ number_format($rek['val'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                
                <!-- Baris Garis Pemisah Jumlah -->
                <tr class="row-total-line">
                    <td colspan="4"><strong>JUMLAH</strong></td>
                    <td style="text-align: right; font-size: 8.5pt;">+</td>
                </tr>

                <!-- Baris Nilai Total Jumlah -->
                <tr class="row-total-val">
                    <td colspan="3"></td>
                    <td class="r-curr"><strong>Rp.</strong></td>
                    <td class="r-val"><strong>{{ number_format($record->total_nilai, 2, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Paragraf Penutup -->
        <p class="content-p" style="margin-top: 14px;">
            Demikian Berita Acara Persediaan ini dibuat untuk dapat diketahui dan dipergunakan sebagaimana mestinya.
        </p>

        <!-- Area Tanda Tangan -->
        <div class="ttd-wrapper">
            <table class="ttd-table">
                <tr style="vertical-align: top;">
                    <td style="width: 50%;">
                        <br>
                        Yang Memeriksa<br>
                        <span class="ttd-role">{{ $record->jabatan_pemeriksa }}</span>
                    </td>
                    <td style="width: 50%;">
                        Lamongan, {{ $tglFormat }}<br>
                        Yang Diperiksa,<br>
                        <span class="ttd-role">{{ $record->jabatan_diperiksa }}<br>Puskesmas Mantup</span>
                    </td>
                </tr>
                <tr>
                    <td style="height: 65px;"></td>
                    <td style="height: 65px;"></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>
                        <div class="ttd-name">{{ $record->nama_pemeriksa }}</div>
                        <div class="ttd-nip">NIP. {{ $record->nip_pemeriksa }}</div>
                    </td>
                    <td>
                        <div class="ttd-name">{{ $record->nama_diperiksa }}</div>
                        <div class="ttd-nip">NIP. {{ $record->nip_diperiksa }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- HALAMAN KEDUA: LAMPIRAN RINCIAN HASIL STOCK OPNAME     -->
    <!-- (Memuat Rincian Obat vs BMHP BLUD, APBD, APBN, ATK dll) -->
    <!-- ======================================================= -->
    <div class="page-sheet page-break" id="lembarKedua">
        <!-- Header Lampiran -->
        <div class="lampiran-meta">
            <table>
                <tr>
                    <td>Lampiran</td>
                    <td>: Berita Acara Stok Opname Persediaan</td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td>: {{ $record->nomor_surat }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ $tglFormat }}</td>
                </tr>
            </table>
        </div>

        <div class="lampiran-title">
            <h3>DAFTAR RINCIAN HASIL STOCK OPNAME BARANG PERSEDIAAN</h3>
            <h4>PUSKESMAS MANTUP TAHUN ANGGARAN {{ $tahunAnggaran }}</h4>
            <p>Posisi Fisik Barang Persediaan per {{ $tglFormat }}</p>
        </div>

        <!-- Tabel Rincian Lengkap Termasuk Sub-Komponen Obat & BMHP -->
        <table class="tabel-lampiran">
            <thead>
                <tr>
                    <th style="width: 28px;">NO</th>
                    <th style="width: 170px;">JENIS REKENING</th>
                    <th>KOMPONEN / RINCIAN SUB-BARANG</th>
                    <th style="width: 110px;">NILAI (RP)</th>
                    <th style="width: 125px;">SUBTOTAL REKENING (RP)</th>
                    <th style="width: 90px;">KONDISI</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. ATK -->
                <tr>
                    <td class="center font-bold">1</td>
                    <td><strong>Alat Tulis Kantor (ATK)</strong></td>
                    <td>Persediaan Alat Tulis Kantor &amp; Perlengkapan Administrasi</td>
                    <td class="right text-slate-400">-</td>
                    <td class="right font-semibold">{{ number_format($record->atk, 2, ',', '.') }}</td>
                    <td class="center">Baik / Lengkap</td>
                </tr>

                <!-- 2. Bahan Cetak -->
                <tr>
                    <td class="center font-bold">2</td>
                    <td><strong>Bahan Cetak</strong></td>
                    <td>Karcis Retribusi, Formulir Rekam Medis &amp; Dokumen Cetak</td>
                    <td class="right text-slate-400">-</td>
                    <td class="right font-semibold">{{ number_format($record->bahan_cetak, 2, ',', '.') }}</td>
                    <td class="center">Baik / Lengkap</td>
                </tr>

                <!-- 3. Benda Pos -->
                <tr>
                    <td class="center font-bold">3</td>
                    <td><strong>Benda Pos</strong></td>
                    <td>Materai, Perangko, &amp; Perlengkapan Pos</td>
                    <td class="right text-slate-400">-</td>
                    <td class="right font-semibold">{{ number_format($record->benda_pos, 2, ',', '.') }}</td>
                    <td class="center">Baik / Lengkap</td>
                </tr>

                <!-- 4. Bahan Komputer -->
                <tr>
                    <td class="center font-bold">4</td>
                    <td><strong>Bahan Komputer</strong></td>
                    <td>Tinta Printer, Toner, Ribbon &amp; Aksesoris IT</td>
                    <td class="right text-slate-400">-</td>
                    <td class="right font-semibold">{{ number_format($record->bahan_komputer, 2, ',', '.') }}</td>
                    <td class="center">Baik / Lengkap</td>
                </tr>

                <!-- 5. Obat BLUD (Breakdown Obat & BMHP) -->
                <tr>
                    <td class="center font-bold" rowspan="2">5</td>
                    <td rowspan="2">
                        <strong>Obat BLUD</strong>
                        <span style="display: block; font-size: 7.5pt; color: #475569; margin-top: 2px;">(Sumber: Dana BLUD)</span>
                    </td>
                    <td>&bull; Obat-Obatan BLUD</td>
                    <td class="right">{{ number_format($record->obat_blud, 2, ',', '.') }}</td>
                    <td class="right font-bold" rowspan="2" style="background-color: #f8fafc; vertical-align: middle;">
                        {{ number_format($record->total_obat_blud, 2, ',', '.') }}
                    </td>
                    <td class="center" rowspan="2" style="vertical-align: middle;">Baik / Lengkap</td>
                </tr>
                <tr>
                    <td>&bull; Bahan Medis Habis Pakai (BMHP) BLUD</td>
                    <td class="right">{{ number_format($record->bmhp_blud, 2, ',', '.') }}</td>
                </tr>

                <!-- 6. Obat APBD (Breakdown Obat & BMHP) -->
                <tr>
                    <td class="center font-bold" rowspan="2">6</td>
                    <td rowspan="2">
                        <strong>Obat APBD</strong>
                        <span style="display: block; font-size: 7.5pt; color: #475569; margin-top: 2px;">(Sumber: APBD Kabupaten)</span>
                    </td>
                    <td>&bull; Obat-Obatan APBD</td>
                    <td class="right">{{ number_format($record->obat_apbd, 2, ',', '.') }}</td>
                    <td class="right font-bold" rowspan="2" style="background-color: #f8fafc; vertical-align: middle;">
                        {{ number_format($record->total_obat_apbd, 2, ',', '.') }}
                    </td>
                    <td class="center" rowspan="2" style="vertical-align: middle;">Baik / Lengkap</td>
                </tr>
                <tr>
                    <td>&bull; Bahan Medis Habis Pakai (BMHP) APBD</td>
                    <td class="right">{{ number_format($record->bmhp_apbd, 2, ',', '.') }}</td>
                </tr>

                <!-- 7. Obat APBN (Breakdown Obat & BMHP) -->
                <tr>
                    <td class="center font-bold" rowspan="2">7</td>
                    <td rowspan="2">
                        <strong>Obat APBN</strong>
                        <span style="display: block; font-size: 7.5pt; color: #475569; margin-top: 2px;">(Sumber: APBN / Program Pusat)</span>
                    </td>
                    <td>&bull; Obat-Obatan APBN</td>
                    <td class="right">{{ number_format($record->obat_apbn, 2, ',', '.') }}</td>
                    <td class="right font-bold" rowspan="2" style="background-color: #f8fafc; vertical-align: middle;">
                        {{ number_format($record->total_obat_apbn, 2, ',', '.') }}
                    </td>
                    <td class="center" rowspan="2" style="vertical-align: middle;">Baik / Lengkap</td>
                </tr>
                <tr>
                    <td>&bull; Bahan Medis Habis Pakai (BMHP) APBN</td>
                    <td class="right">{{ number_format($record->bmhp_apbn, 2, ',', '.') }}</td>
                </tr>

                <!-- Baris Total Keseluruhan -->
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 12px; text-transform: uppercase;">
                        TOTAL KESELURUHAN NILAI HASIL STOCK OPNAME :
                    </td>
                    <td class="right" style="font-size: 10pt;">
                        <strong>Rp. {{ number_format($record->total_nilai, 2, ',', '.') }}</strong>
                    </td>
                    <td class="center">Lengkap (Baik)</td>
                </tr>
            </tbody>
        </table>

        <!-- Catatan Kaki Lampiran -->
        <p class="content-p" style="font-size: 9.5pt; margin-top: 10px;">
            Catatan: Seluruh barang persediaan di atas telah dilakukan pemeriksaan dan perhitungan fisik (Stock Opname) secara seksama dalam kondisi <strong>BAIK</strong> dan lengkap untuk dipergunakan sebagaimana mestinya dalam rangka menunjang kelancaran operasional pelayanan kesehatan di Puskesmas Mantup.
        </p>

        <!-- Area Tanda Tangan Lampiran -->
        <div class="ttd-wrapper" style="margin-top: 20px;">
            <table class="ttd-table">
                <tr style="vertical-align: top;">
                    <td style="width: 50%;">
                        <br>
                        Yang Memeriksa<br>
                        <span class="ttd-role">{{ $record->jabatan_pemeriksa }}</span>
                    </td>
                    <td style="width: 50%;">
                        Lamongan, {{ $tglFormat }}<br>
                        Yang Diperiksa,<br>
                        <span class="ttd-role">{{ $record->jabatan_diperiksa }}<br>Puskesmas Mantup</span>
                    </td>
                </tr>
                <tr>
                    <td style="height: 55px;"></td>
                    <td style="height: 55px;"></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>
                        <div class="ttd-name">{{ $record->nama_pemeriksa }}</div>
                        <div class="ttd-nip">NIP. {{ $record->nip_pemeriksa }}</div>
                    </td>
                    <td>
                        <div class="ttd-name">{{ $record->nama_diperiksa }}</div>
                        <div class="ttd-nip">NIP. {{ $record->nip_diperiksa }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Script Kontrol Cetak -->
    <script>
        function printMode(mode) {
            document.body.classList.remove('print-only-hal1', 'print-only-hal2');

            if (mode === 'hal1') {
                document.body.classList.add('print-only-hal1');
            } else if (mode === 'hal2') {
                document.body.classList.add('print-only-hal2');
            }

            window.print();

            window.addEventListener('afterprint', function handleAfterPrint() {
                document.body.classList.remove('print-only-hal1', 'print-only-hal2');
                window.removeEventListener('afterprint', handleAfterPrint);
            });
        }
    </script>
</body>
</html>
