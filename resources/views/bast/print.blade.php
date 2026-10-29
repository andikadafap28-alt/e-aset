<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST - {{ $bast->employee->name ?? 'Penerima' }} - {{ \Carbon\Carbon::parse($bast->handover_date)->format('d-m-Y') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            color: #111827;
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

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .kop-logo {
            width: 75px;
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
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .kop-teks p {
            margin: 1px 0;
            font-size: 8.5pt;
        }

        /* Judul Dokumen */
        .doc-title-container {
            text-align: center;
            margin-bottom: 16px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        /* Paragraf Pembuka */
        .content-p {
            text-align: justify;
            margin: 8px 0;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        /* Tabel Pihak */
        .table-parties {
            width: 100%;
            margin: 6px 0 10px 0;
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
            width: 170px;
        }

        .table-parties td.colon {
            width: 15px;
            text-align: center;
        }

        .table-parties td.val {
            font-weight: 500;
        }

        /* Area Tanda Tangan */
        .ttd-wrapper {
            margin-top: 22px;
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
            text-decoration: underline;
            font-size: 11pt;
        }

        .ttd-nip {
            font-size: 10pt;
            margin-top: 2px;
        }

        /* Lembar Lampiran */
        .lampiran-meta {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 12px;
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
            margin-bottom: 15px;
        }

        .lampiran-title h3 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .lampiran-title p {
            margin: 3px 0 0 0;
            font-size: 9.5pt;
            color: #374151;
        }

        /* Tabel Rincian Barang */
        .tabel-rincian {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
        }

        .tabel-rincian th, .tabel-rincian td {
            border: 1px solid #1f2937;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .tabel-rincian th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .tabel-rincian td.center {
            text-align: center;
        }

        .tabel-rincian td.font-mono {
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 8.5pt;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
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

            .hidden-lampiran {
                display: none !important;
            }

            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }

            .tabel-rincian th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .total-row {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Mode Cetak HANYA Kolom Sumber */
            body.print-only-sumber-mode {
                background: transparent !important;
            }
            body.print-only-sumber-mode .page-sheet {
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
            }
            body.print-only-sumber-mode * {
                visibility: hidden !important;
                border-color: transparent !important;
                color: transparent !important;
                background: transparent !important;
                box-shadow: none !important;
            }
            body.print-only-sumber-mode .print-sumber-target,
            body.print-only-sumber-mode .print-sumber-target * {
                visibility: visible !important;
                color: #000000 !important;
                font-weight: 700 !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body>
    @php
        if (!function_exists('formatName')) {
            function formatName($name) {
                if (!$name) return '-';
                $name = ucwords(strtolower($name), " .-");
                $name = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $name);
                return $name;
            }
        }

        if (!function_exists('terbilang')) {
            function terbilang($angka) {
                $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                if ($angka < 12) {
                    return $bilangan[$angka];
                } elseif ($angka < 20) {
                    return terbilang($angka - 10) . ' Belas';
                } elseif ($angka < 100) {
                    return terbilang(intval($angka / 10)) . ' Puluh' . (($angka % 10 != 0) ? ' ' . terbilang($angka % 10) : '');
                } elseif ($angka < 200) {
                    return 'Seratus' . (($angka - 100 != 0) ? ' ' . terbilang($angka - 100) : '');
                } elseif ($angka < 1000) {
                    return terbilang(intval($angka / 100)) . ' Ratus' . (($angka % 100 != 0) ? ' ' . terbilang($angka % 100) : '');
                }
                return (string)$angka;
            }
        }

        $dateCarbon = \Carbon\Carbon::parse($bast->handover_date)->locale('id');
        $namaHari = $dateCarbon->isoFormat('dddd');
        $tglFormat = $dateCarbon->isoFormat('D MMMM Y');
        
        // Cek apakah penerima adalah Kepala Puskesmas
        $isKepalaPuskesmas = ($bast->employee_id == 1) 
            || str_contains(strtolower($bast->employee->name ?? ''), 'sunaryadi') 
            || str_contains(strtolower($bast->employee->jabatan ?? ''), 'kepala puskesmas');

        // Cek apakah penerima adalah PPPK (khusus Dhendhy, Almira, Suharsono, atau NIP PPPK)
        $isPppk = false;
        if ($bast->employee) {
            $empNameLower = strtolower($bast->employee->name ?? '');
            $empJabatanLower = strtolower($bast->employee->jabatan ?? '');
            $rawNip = preg_replace('/[^0-9]/', '', (string)$bast->employee->nip);
            if (in_array($bast->employee_id, [58, 59, 60]) 
                || str_contains($empNameLower, 'dhendhy') 
                || str_contains($empNameLower, 'almira') 
                || str_contains($empNameLower, 'suharsono')) {
                $isPppk = true;
            } elseif (str_contains($empJabatanLower, 'pppk') || str_contains($empJabatanLower, 'p3k')) {
                $isPppk = true;
            } elseif (strlen($rawNip) == 18 && (substr($rawNip, 12, 2) === '21' || substr($rawNip, 14, 2) === '21')) {
                $isPppk = true;
            }
        }
        $nipLabel = $isPppk ? 'NIPPPK' : 'NIP';

        $empName = formatName($bast->employee->name ?? ($isKepalaPuskesmas ? 'dr. Muhammad Sunaryadi' : '-'));
        $empNip = $bast->employee->nip ?? ($isKepalaPuskesmas ? '196903132002121007' : '-');
        
        if ($isKepalaPuskesmas) {
            $empJabatan = 'Kepala Puskesmas Mantup';
        } else {
            $empJabatan = $bast->employee->jabatan ?: ($bast->employee->golongan ?: 'Staf Puskesmas Mantup');
        }

        $keperluan = $bast->keperluan ?: ($isKepalaPuskesmas ? 'Operasional Kedinasan Kepala Puskesmas' : 'Operasional Pelayanan');
        $totalItemsCount = $bast->items ? $bast->items->count() : 1;

        // Tentukan nama barang & judul lampiran secara dinamis
        $firstItem = $bast->items ? $bast->items->first() : null;
        $firstAsset = $firstItem ? $firstItem->asset : $bast->asset;
        $firstAssetName = $firstAsset ? $firstAsset->name : 'Barang Milik Daerah';

        if ($totalItemsCount == 1) {
            $namaSpesifik = $firstAssetName;
            $merkOrKet = $firstAsset ? ($firstAsset->merk ?: $firstAsset->keterangan) : '';
            if ($merkOrKet && trim($merkOrKet) != '') {
                $namaSpesifik .= ' (' . trim($merkOrKet) . ')';
            }
            $jenisBarangTeks = 'berupa ' . $namaSpesifik;
            $judulLampiran = strtoupper($firstAssetName);
        } else {
            $categories = $bast->items->map(function($i) {
                return $i->asset->category ?? $i->asset->name ?? null;
            })->filter()->unique();

            if ($categories->count() == 1) {
                $jenisBarangTeks = 'berupa ' . $categories->first();
                $judulLampiran = strtoupper($categories->first());
            } else {
                $jenisBarangTeks = 'berupa Barang Milik Daerah (BMD)';
                $judulLampiran = 'BARANG MILIK DAERAH (BMD)';
            }
        }
    @endphp

    <!-- Top Action Toolbar (Layar Monitor Sahaja, Disembunyikan Saat Cetak) -->
    <div class="top-toolbar">
        <div class="toolbar-group">
            <a href="{{ route('bast.index') }}" class="btn-action btn-back">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
            <span style="font-size: 13px; font-weight: 500; color: #cbd5e1; border-left: 1px solid #475569; padding-left: 12px;">
                BAST: <strong>{{ $empName }}</strong> ({{ $tglFormat }}) &bull; {{ $totalItemsCount }} Barang
            </span>
        </div>

        <div class="toolbar-group">
            @if($totalItemsCount <= 3)
                <button type="button" id="btnPageMode" onclick="toggleLampiranSheet()" class="btn-action btn-toggle" title="Sertakan atau sembunyikan lembar lampiran terpisah">
                    <span class="material-symbols-outlined text-[16px]">file_copy</span>
                    <span id="txtLampiranBtn">Sertakan Lembar Lampiran</span>
                </button>
            @else
                <span style="font-size: 12px; color: #94a3b8; margin-right: 4px;">Pilihan Lampiran:</span>
                <button type="button" id="btnModeGrouped" onclick="switchViewMode('grouped')" class="btn-action btn-toggle active" title="Tampilkan rincian dikelompokkan per jenis spesifikasi">
                    <span class="material-symbols-outlined text-[16px]">view_list</span>
                    Ringkasan Spesifikasi ({{ $groupedItems->count() }} Kelompok)
                </button>
                <button type="button" id="btnModeDetailed" onclick="switchViewMode('detailed')" class="btn-action btn-toggle" title="Tampilkan seluruh register satu per satu">
                    <span class="material-symbols-outlined text-[16px]">format_list_numbered</span>
                    Detail Lengkap ({{ $allItems->count() }} Unit)
                </button>
            @endif
            <button type="button" onclick="printOnlySumber()" class="btn-action" style="background-color: #d97706; color: white;" title="Cetak HANYA teks kolom sumber untuk menimpa lembaran fisik yang sudah ada tanda tangan">
                <span class="material-symbols-outlined text-[16px]">format_color_text</span>
                Cetak HANYA Teks Sumber
            </button>
            <button type="button" onclick="printFullDocument()" class="btn-action btn-print" style="margin-left: 4px;">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Print Dokumen
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LEMBAR PERTAMA: SURAT BUKTI SERAH TERIMA  -->
    <!-- Memuat Keterangan Serah Terima & Tanda Tangan -->
    <!-- ========================================== -->
    <div class="page-sheet">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('img/logo-lamongan.png') }}" class="kop-logo" alt="Logo Lamongan" onerror="this.style.display='none'">
            <div class="kop-teks">
                <h4>PEMERINTAH KABUPATEN LAMONGAN</h4>
                <h3>DINAS KESEHATAN</h3>
                <h2>PUSKESMAS MANTUP</h2>
                <p>Jln. Raya Mantup No. 55 Mantup Lamongan Telp. (0322) 4670302</p>
                <p>Email: puskesmasmantup98@gmail.com &bull; Kode Pos 62283</p>
            </div>
            <img src="{{ asset('img/logo-husada.png') }}" class="kop-logo" alt="Logo Bakti Husada" onerror="this.style.display='none'">
        </div>

        <!-- Judul Berita Acara -->
        <div class="doc-title-container">
            <h1 class="doc-title">BUKTI SERAH TERIMA BARANG</h1>
        </div>

        <!-- Pembuka -->
        <p class="content-p">
            Pada hari ini <strong>{{ $namaHari }}</strong>, tanggal <strong>{{ $dateCarbon->translatedFormat('d F Y') }}</strong> bertempat di Puskesmas Mantup, kami yang bertanda tangan di bawah ini :
        </p>

        <!-- Pihak Kesatu & Kedua -->
        <table class="table-parties">
            <tr>
                <td class="num">1.</td>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="val"><strong>Andika Dafa Penta Pratama</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td class="val">199907282025041003</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="val">Pengurus Barang Pembantu Puskesmas Mantup</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 3px; font-style: italic; color: #374151;">
                    Selanjutnya disebut sebagai <strong>PIHAK KESATU</strong> (Yang Menyerahkan)
                </td>
            </tr>

            <tr><td colspan="4" style="height: 8px;"></td></tr>

            <tr>
                <td class="num">2.</td>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="val"><strong>{{ $empName }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="label">{{ $nipLabel }}</td>
                <td class="colon">:</td>
                <td class="val">{{ $empNip }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="val">{{ $empJabatan }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Untuk Keperluan</td>
                <td class="colon">:</td>
                <td class="val"><strong>{{ $keperluan }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 3px; font-style: italic; color: #374151;">
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima)
                </td>
            </tr>
        </table>

        <!-- Klausul Serah Terima -->
        @if($totalItemsCount <= 3)
            <p class="content-p">
                <strong>PIHAK KESATU</strong> telah menyerahkan kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> menyatakan telah menerima dari <strong>PIHAK KESATU</strong> Barang Milik Daerah (BMD) {{ $jenisBarangTeks }} sejumlah <strong>{{ $totalItemsCount }} ({{ terbilang($totalItemsCount) }}) unit</strong> dengan rincian spesifikasi sebagai berikut :
            </p>

            <!-- Tabel Rincian Langsung di Halaman 1 untuk Barang Sedikit (<= 3) -->
            <table class="tabel-rincian" style="margin: 8px 0 12px 0;">
                <thead>
                    <tr>
                        <th style="width: 28px;">NO</th>
                        <th style="width: 130px;">KODE BARANG (108)</th>
                        <th style="width: 70px;">NO. REG</th>
                        <th>NAMA BARANG</th>
                        <th>MERK / SPESIFIKASI</th>
                        <th style="width: 45px;">QTY</th>
                        <th style="width: 60px;">KONDISI</th>
                        <th style="width: 90px;">SUMBER DANA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allItems as $idx => $item)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="font-mono center">{{ $item->asset->kode_108 ?? ($item->asset->full_kode_108 ?? '-') }}</td>
                            <td class="font-mono center">{{ $item->asset->no_register ? str_pad($item->asset->no_register, 3, '0', STR_PAD_LEFT) : '-' }}</td>
                            <td><strong>{{ $item->asset->name ?? '-' }}</strong></td>
                            <td>{{ $item->asset->merk && trim($item->asset->merk) ? $item->asset->merk : ($item->asset->keterangan ?: '-') }}</td>
                            <td class="center font-bold">1 unit</td>
                            <td class="center">{{ $item->asset->condition ?: 'Baik' }}</td>
                            <td class="center font-medium print-sumber-target">{{ $bast->sumber_dana ?: ($item->asset->source ?? 'BMD / APBD 2026') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="6" style="text-align: right; padding-right: 12px; text-transform: uppercase;">Total Barang Diserahkan :</td>
                        <td class="center font-bold">{{ $totalItemsCount }} unit</td>
                        <td class="center">Lengkap (Baik)</td>
                    </tr>
                </tbody>
            </table>

            <p class="content-p">
                Barang tersebut diserahkan dalam kondisi <strong>BAIK</strong> dan lengkap untuk dipergunakan sebagaimana mestinya dalam rangka menunjang kelancaran operasional kedinasan di Puskesmas Mantup. Segala pemeliharaan, kebersihan, dan pengamanan fisik barang selanjutnya menjadi tanggung jawab penerima barang.
            </p>
        @else
            <p class="content-p">
                <strong>PIHAK KESATU</strong> telah menyerahkan kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> menyatakan telah menerima dari <strong>PIHAK KESATU</strong> Barang Milik Daerah (BMD) {{ $jenisBarangTeks }} sejumlah <strong>{{ $totalItemsCount }} ({{ terbilang($totalItemsCount) }}) unit</strong> dengan rincian spesifikasi, nomor register, dan kode barang sebagaimana tercantum dalam <strong>Daftar Rincian Barang (Lembar Lampiran)</strong> yang merupakan bagian tidak terpisahkan dari Berita Acara ini.
            </p>

            <p class="content-p">
                Barang-barang tersebut diserahkan dalam kondisi <strong>BAIK</strong> dan lengkap untuk dipergunakan sebagaimana mestinya dalam rangka menunjang kelancaran operasional pelayanan di <strong>{{ $keperluan }}</strong> Puskesmas Mantup. Segala pemeliharaan, kebersihan, dan pengamanan fisik barang selanjutnya menjadi tanggung jawab penerima barang.
            </p>
        @endif

        <p class="content-p">
            Demikian Bukti Serah Terima Barang ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <!-- Area Tanda Tangan -->
        <div class="ttd-wrapper">
            <table class="ttd-table" style="margin-bottom: {{ $isKepalaPuskesmas ? '10px' : '20px' }};">
                <tr style="vertical-align: top;">
                    <td style="width: 50%;">
                        <br>
                        Yang Menyerahkan,<br>
                        <span class="ttd-role">Pengurus Barang Pembantu<br>Puskesmas Mantup</span>
                    </td>
                    <td style="width: 50%;">
                        Mantup, {{ $tglFormat }}<br>
                        Yang Menerima,<br>
                        <span class="ttd-role">{{ $empJabatan }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="height: 65px;"></td>
                    <td style="height: 65px;"></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>
                        <div class="ttd-name">Andika Dafa Penta Pratama</div>
                        <div class="ttd-nip">NIP. 199907282025041003</div>
                    </td>
                    <td>
                        <div class="ttd-name">{{ $empName }}</div>
                        <div class="ttd-nip">{{ $nipLabel }}. {{ $empNip }}</div>
                    </td>
                </tr>
            </table>

            @if(!$isKepalaPuskesmas)
                <!-- Mengetahui Kepala Puskesmas (Hanya tampil jika penerima BUKAN Kepala Puskesmas) -->
                <table class="ttd-table">
                    <tr style="vertical-align: top;">
                        <td>
                            Mengetahui,<br>
                            <span class="ttd-role">Kepala Puskesmas Mantup</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 60px;"></td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>
                            <div class="ttd-name">dr. Muhammad Sunaryadi</div>
                            <div class="ttd-nip">NIP. 196903132002121007</div>
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LEMBAR KEDUA: LAMPIRAN RINCIAN BARANG      -->
    <!-- ========================================== -->
    <div class="page-sheet page-break {{ $totalItemsCount <= 3 ? 'hidden-lampiran' : '' }}" id="lembarKedua" style="{{ $totalItemsCount <= 3 ? 'display: none;' : '' }}">
        <!-- Header Lampiran -->
        <div class="lampiran-meta">
            <table>
                <tr>
                    <td>Lampiran</td>
                    <td>: Bukti Serah Terima Barang</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ $tglFormat }}</td>
                </tr>
                <tr>
                    <td>Penerima</td>
                    <td>: {{ $empName }} ({{ $empJabatan }})</td>
                </tr>
                <tr>
                    <td>Keperluan</td>
                    <td>: {{ $keperluan }}</td>
                </tr>
            </table>
        </div>

        <div class="lampiran-title">
            <h3>DAFTAR RINCIAN {{ $judulLampiran }}</h3>
            <p>PUSKESMAS MANTUP TAHUN {{ $dateCarbon->format('Y') }}</p>
        </div>

        <!-- MODE 1: RINGKASAN PER SPESIFIKASI -->
        <div id="viewGrouped">
            <table class="tabel-rincian">
                <thead>
                    <tr>
                        <th style="width: 28px;">NO</th>
                        <th style="width: 130px;">KODE BARANG (108)</th>
                        <th style="width: 85px;">NO. REG</th>
                        <th>NAMA KELOMPOK BARANG</th>
                        <th>SPESIFIKASI / NAMA BARANG</th>
                        <th style="width: 50px;">QTY</th>
                        <th style="width: 65px;">KONDISI</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumQty = 0; @endphp
                    @foreach($groupedItems as $idx => $item)
                        @php $sumQty += $item->qty; @endphp
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="font-mono center">{{ $item->asset->full_kode_108 ?? ($item->asset->kode_108 ?? '-') }}</td>
                            <td class="font-mono center">{{ $item->registers ?? '-' }}</td>
                            <td><strong>{{ $item->asset->name ?? '-' }}</strong></td>
                            <td>{{ $item->asset->keterangan ?: ($item->asset->merk ?: '-') }}</td>
                            <td class="center font-bold">{{ $item->qty }} unit</td>
                            <td class="center">{{ $item->asset->condition ?: 'Baik' }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right; padding-right: 12px; text-transform: uppercase;">Total Keseluruhan Barang Diserahkan :</td>
                        <td class="center font-bold">{{ $sumQty }} unit</td>
                        <td class="center">Lengkap (Baik)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MODE 2: DETAIL SELURUH BARANG LENGKAP -->
        <div id="viewDetailed" style="display: none;">
            <table class="tabel-rincian">
                <thead>
                    <tr>
                        <th style="width: 25px;">NO</th>
                        <th style="width: 105px;">KODE BARANG</th>
                        <th style="width: 40px;">REG</th>
                        <th style="width: 130px;">NIBAR</th>
                        <th>NAMA BARANG</th>
                        <th>SPESIFIKASI / KETERANGAN</th>
                        <th style="width: 50px;">KONDISI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allItems as $idx => $item)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="font-mono center" style="font-size: 7.5pt;">{{ $item->asset->full_kode_108 ?? ($item->asset->kode_108 ?? '-') }}</td>
                            <td class="font-mono center">{{ $item->asset->no_register ? str_pad($item->asset->no_register, 3, '0', STR_PAD_LEFT) : '-' }}</td>
                            <td class="font-mono" style="font-size: 7pt; word-break: break-all;">{{ $item->asset->asset_code ?? '-' }}</td>
                            <td style="font-size: 8.5pt;"><strong>{{ $item->asset->name ?? '-' }}</strong></td>
                            <td style="font-size: 8.5pt;">{{ $item->asset->keterangan ?: ($item->asset->merk ?: '-') }}</td>
                            <td class="center" style="font-size: 8pt;">{{ $item->asset->condition ?: 'Baik' }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right; padding-right: 10px;">TOTAL:</td>
                        <td colspan="3"><strong>{{ $allItems->count() }} Unit {{ $judulLampiran }}</strong> (Kondisi Baik)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Lampiran -->
        <div class="ttd-wrapper" style="margin-top: 25px;">
            <table class="ttd-table">
                <tr style="vertical-align: top;">
                    <td style="width: 50%;">
                        <br>
                        Yang Menyerahkan,<br>
                        <span class="ttd-role">Pengurus Barang Pembantu</span>
                    </td>
                    <td style="width: 50%;">
                        Mantup, {{ $tglFormat }}<br>
                        Yang Menerima,<br>
                        <span class="ttd-role">{{ $empJabatan }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="height: 60px;"></td>
                    <td style="height: 60px;"></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>
                        <div class="ttd-name">Andika Dafa Penta Pratama</div>
                        <div class="ttd-nip">NIP. 199907282025041003</div>
                    </td>
                    <td>
                        <div class="ttd-name">{{ $empName }}</div>
                        <div class="ttd-nip">{{ $nipLabel }}. {{ $empNip }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Script View Mode & Toggle Lampiran -->
    <script>
        function toggleLampiranSheet() {
            const sheet2 = document.getElementById('lembarKedua');
            const txtBtn = document.getElementById('txtLampiranBtn');
            const btn = document.getElementById('btnPageMode');

            if (sheet2.style.display === 'none' || sheet2.classList.contains('hidden-lampiran')) {
                sheet2.style.display = 'block';
                sheet2.classList.remove('hidden-lampiran');
                btn.classList.add('active');
                txtBtn.textContent = 'Sembunyikan Lembar Lampiran (Cukup 1 Lembar)';
            } else {
                sheet2.style.display = 'none';
                sheet2.classList.add('hidden-lampiran');
                btn.classList.remove('active');
                txtBtn.textContent = 'Sertakan Lembar Lampiran';
            }
        }

        function switchViewMode(mode) {
            const viewGrouped = document.getElementById('viewGrouped');
            const viewDetailed = document.getElementById('viewDetailed');
            const btnGrouped = document.getElementById('btnModeGrouped');
            const btnDetailed = document.getElementById('btnModeDetailed');

            if (!viewGrouped || !viewDetailed) return;

            if (mode === 'grouped') {
                viewGrouped.style.display = 'block';
                viewDetailed.style.display = 'none';
                if (btnGrouped) btnGrouped.classList.add('active');
                if (btnDetailed) btnDetailed.classList.remove('active');
            } else {
                viewGrouped.style.display = 'none';
                viewDetailed.style.display = 'block';
                if (btnGrouped) btnGrouped.classList.remove('active');
                if (btnDetailed) btnDetailed.classList.add('active');
            }
        }

        function printFullDocument() {
            document.body.classList.remove('print-only-sumber-mode');
            window.print();
        }

        function printOnlySumber() {
            document.body.classList.add('print-only-sumber-mode');
            window.print();

            window.addEventListener('afterprint', function handleAfterPrint() {
                document.body.classList.remove('print-only-sumber-mode');
                window.removeEventListener('afterprint', handleAfterPrint);
            });
        }
    </script>
</body>
</html>
