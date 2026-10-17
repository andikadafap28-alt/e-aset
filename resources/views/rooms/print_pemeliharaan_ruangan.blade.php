<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pemeliharaan Ruangan - F4</title>
    <style>
        @page {
            size: 215mm 330mm; /* Folio / F4 */
            margin: 10mm 15mm 10mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Toolbar Screen */
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .toolbar .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #475569;
            color: white;
        }
        .btn-back:hover {
            background: #334155;
        }

        .preview-wrapper {
            margin-top: 60px;
            padding: 20px 10px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        /* Halaman Kertas F4 */
        .page-sheet {
            background: white;
            width: 215mm;
            min-height: 330mm;
            padding: 12mm 15mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .kop-logo {
            width: 65px;
            text-align: center;
        }
        .kop-logo img {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            line-height: 1.25;
            padding: 0 8px;
        }
        .kop-text .line-1 {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kop-text .line-2 {
            font-size: 10pt;
            font-weight: bold;
        }
        .kop-text .line-3 {
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 2px 0;
        }
        .kop-text .line-4 {
            font-size: 8pt;
            color: #111;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 6px 0 10px;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 2px 0 0;
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
        }

        /* Info Ruangan */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
            border: none;
        }
        .info-label {
            width: 160px;
            font-weight: bold;
        }
        .info-sep {
            width: 12px;
            font-weight: bold;
        }
        .info-value {
            font-weight: bold;
        }

        /* Tabel Pemeliharaan */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        .main-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 8.5pt;
            height: 28px;
            vertical-align: middle;
        }
        .main-table td {
            height: 24px;
            vertical-align: middle;
        }
        .main-table tr.blank-row td {
            height: 25px; /* Tinggi baris ideal untuk tulisan tangan */
        }
        .text-center {
            text-align: center;
        }

        /* Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 44%;
            text-align: center;
            font-size: 9pt;
            line-height: 1.3;
        }
        .ttd-box .ttd-jabatan {
            font-weight: bold;
        }
        .ttd-space {
            height: 52px;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9.5pt;
        }
        .ttd-nip {
            font-size: 8.5pt;
            margin-top: 1px;
        }

        /* Catatan Kaki */
        .footer-note {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px dashed #94a3b8;
            font-size: 7pt;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }

        /* Mode Print */
        @media print {
            body {
                background: white !important;
            }
            .toolbar {
                display: none !important;
            }
            .preview-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .page-sheet {
                width: 100% !important;
                min-height: 0 !important;
                height: 310mm !important;
                padding: 0 !important;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page;
            }
            .page-sheet:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
        }
    </style>
</head>
<body>

    @php
        $roomList = isset($rooms) ? $rooms : collect([$room]);
    @endphp

    <!-- Toolbar Atas (Hanya tampil di layar) -->
    <div class="toolbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('rooms.lembar_pemeliharaan') }}" class="btn btn-back">
                &larr; Kembali ke Menu
            </a>
            <span style="font-size: 14px; font-weight: 500;">
                Format Ukuran Kertas: <strong>F4 (Folio 215 x 330 mm)</strong> &bull; Total: <strong>{{ $roomList->count() }} Ruangan</strong>
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-print">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Pemeliharaan (F4)
            </button>
        </div>
    </div>

    <div class="preview-wrapper">
        @foreach($roomList as $index => $currentRoom)
        @php
            // Ambil riwayat pemeliharaan jika ada aset di ruangan ini
            $maintenances = collect();
            if ($currentRoom->relationLoaded('assets') || isset($currentRoom->assets)) {
                foreach($currentRoom->assets as $asset) {
                    if (isset($asset->maintenances)) {
                        foreach($asset->maintenances as $m) {
                            $maintenances->push((object)[
                                'tanggal' => $m->tanggal_pelaksanaan ? \Carbon\Carbon::parse($m->tanggal_pelaksanaan)->format('d/m/Y') : ($m->tanggal_jadwal ? \Carbon\Carbon::parse($m->tanggal_jadwal)->format('d/m/Y') : '-'),
                                'nama_alat' => $asset->name . ($asset->merk ? ' (' . $asset->merk . ')' : ''),
                                'keterangan' => ($m->jenis_pemeliharaan ? '[' . $m->jenis_pemeliharaan . '] ' : '') . ($m->catatan_hasil ?: $m->catatan ?: 'Pemeliharaan berkala'),
                                'petugas' => $m->penyedia ?: 'Teknisi'
                            ]);
                        }
                    }
                }
            }

            // Jumlah total baris yang pas di kertas F4 = 18 baris
            $totalTargetRows = 18;
            $existingCount = $maintenances->count();
            $blankRowsToRender = max(0, $totalTargetRows - $existingCount);
        @endphp

        <div class="page-sheet">
            <div>
                <!-- Kop Surat Resmi -->
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo">
                            <img src="{{ asset('img/logo-lamongan.png') }}" alt="Logo Lamongan" onerror="this.style.display='none'">
                        </td>
                        <td class="kop-text">
                            <div class="line-1">PEMERINTAH KABUPATEN LAMONGAN</div>
                            <div class="line-2">DINAS KESEHATAN</div>
                            <div class="line-3">UPT PUSKESMAS MANTUP</div>
                            <div class="line-4">Jl. Raya Mantup No. 55 Mantup, Kab. Lamongan, Jawa Timur 62283</div>
                            <div class="line-4">Telp. (0322) 4670302 &bull; Email: puskesmasmantup@yahoo.co.id</div>
                        </td>
                        <td class="kop-logo">
                            <img src="{{ asset('img/logo-husada.png') }}" alt="Logo Husada" onerror="this.style.display='none'">
                        </td>
                    </tr>
                </table>

                <!-- Judul -->
                <div class="doc-title">
                    <h2>LEMBAR PEMELIHARAAN RUANGAN</h2>
                    <p>(Lembar kontrol fisik sarana, prasarana, dan alat yang ditempel di setiap ruangan)</p>
                </div>

                <!-- Info Ruangan -->
                <table class="info-table">
                    <tr>
                        <td class="info-label">RUANGAN</td>
                        <td class="info-sep">:</td>
                        <td class="info-value">{{ strtoupper($currentRoom->name) }}</td>
                        <td class="info-label" style="width: 130px;">TAHUN / PERIODE</td>
                        <td class="info-sep">:</td>
                        <td class="info-value">{{ date('Y') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">UNIT / SATUAN KERJA</td>
                        <td class="info-sep">:</td>
                        <td>UPT PUSKESMAS MANTUP</td>
                        <td class="info-label">PENANGGUNG JAWAB</td>
                        <td class="info-sep">:</td>
                        <td>{{ $currentRoom->penanggung_jawab ?: '..........................................' }}</td>
                    </tr>
                </table>

                <!-- Tabel Utama Pemeliharaan Ruangan -->
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;">NO</th>
                            <th style="width: 12%;">TANGGAL</th>
                            <th style="width: 32%;">NAMA ALAT / BAGIAN YANG DILAKUKAN PEMELIHARAAN</th>
                            <th style="width: 38%;">KETERANGAN / TINDAKAN PEMELIHARAAN</th>
                            <th style="width: 14%;">TANDA TANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNo = 1; @endphp

                        {{-- Tampilkan data yang sudah tercatat di sistem bila ada --}}
                        @foreach($maintenances as $item)
                        <tr>
                            <td class="text-center">{{ $rowNo++ }}</td>
                            <td class="text-center">{{ $item->tanggal }}</td>
                            <td>{{ $item->nama_alat }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="text-center" style="font-size: 7.5pt; color: #555;">{{ $item->petugas }}</td>
                        </tr>
                        @endforeach

                        {{-- Baris kosong bergaris untuk pengisian manual tulisan tangan saat tertempel di ruangan --}}
                        @for($i = 0; $i < $blankRowsToRender; $i++)
                        <tr class="blank-row">
                            <td class="text-center" style="color: #64748b;">{{ $rowNo++ }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div>
                <!-- Kolom Tanda Tangan -->
                <div class="ttd-wrapper">
                    <div class="ttd-box">
                        Mengetahui,<br>
                        <span class="ttd-jabatan">Kepala Puskesmas Mantup</span>
                        <div class="ttd-space"></div>
                        <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                        <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                    </div>
                    <div class="ttd-box">
                        Mantup, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                        <span class="ttd-jabatan">Pengurus Barang Pembantu</span>
                        <div class="ttd-space"></div>
                        <div class="ttd-nama">ANDIKA DAFA PENTA P.</div>
                        <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                    </div>
                </div>

                <div class="footer-note">
                    <span>* Catatan: Lembar ini wajib diisi dan ditandatangani setiap kali dilakukan pemeliharaan pada fasilitas ruangan ini.</span>
                    <span>Ruangan: {{ $currentRoom->name }} | e-Aset Puskesmas Mantup</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</body>
</html>
