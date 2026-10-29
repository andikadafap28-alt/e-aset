<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIR - {{ $room->name ?? 'Kartu Inventaris Ruangan' }} (F4 - 1 Halaman)</title>
    <style>
        /* Pengaturan Standar Kertas F4 / Folio (216mm x 330mm) */
        @page {
            size: 216mm 330mm;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
            background-color: #0b1120;
            margin: 0;
            padding: 0;
        }

        /* Toolbar Layar Interaktif */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #f8fafc;
            padding: 8px 16px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 12.5px;
            border-bottom: 1px solid #1e293b;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease-in-out;
            line-height: 1.2;
        }

        .btn-back {
            background: #334155;
            color: #f8fafc;
            border-color: #475569;
        }
        .btn-back:hover { background: #475569; color: #fff; }

        .btn-print {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 2px 6px rgba(37,99,235,0.4);
        }
        .btn-print:hover { background: #1d4ed8; }

        .btn-download {
            background: #059669;
            color: #fff;
        }
        .btn-download:hover { background: #047857; }

        .btn-upload {
            background: #7c3aed;
            color: #fff;
        }
        .btn-upload:hover { background: #6d28d9; }

        .btn-preset {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 3px 7px;
            font-size: 11px;
            border-radius: 4px;
        }
        .btn-preset:hover {
            background: #334155;
            color: #38bdf8;
            border-color: #38bdf8;
        }

        .toolbar-label {
            color: #94a3b8;
            font-weight: 500;
            font-size: 11.5px;
        }

        .select-room, .input-date {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 4px 7px;
            font-size: 12px;
            outline: none;
        }
        .select-room:focus, .input-date:focus {
            border-color: #38bdf8;
        }

        /* Status Badge Muat 1 Halaman */
        .fit-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
        }
        .fit-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.4);
        }
        .fit-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.4);
        }
        .fit-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            display: inline-block;
        }

        .btn-autofit {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
            cursor: pointer;
        }
        .btn-autofit.active {
            background: #0f172a;
            border-color: #22c55e;
            color: #86efac;
            box-shadow: 0 0 0 1px #22c55e;
        }

        /* Lembar Dokumen Fisik F4 di Layar */
        .sheet-wrapper {
            padding: 24px 10px 60px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #0b1120;
            min-height: 100vh;
        }

        /* Kertas F4 Standard: 216mm x 330mm */
        .page-sheet {
            background: #ffffff;
            width: 216mm;
            height: 330mm;
            max-height: 330mm;
            min-height: 330mm;
            padding: 10mm 12mm 10mm 12mm;
            box-shadow: 0 8px 30px rgba(0,0,0,0.35);
            position: relative;
            margin-bottom: 30px;
            box-sizing: border-box;
            overflow: hidden; /* Mencegah elemen keluar batas lembar fisik */
        }

        /* Container Dalam yang Menampung Seluruh Konten & Menerima Auto-Scale */
        .page-sheet-inner {
            width: 100%;
            height: 100%;
            max-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            transform-origin: top center;
            box-sizing: border-box;
        }

        /* Header Dokumen KIR */
        .kir-title {
            text-align: center;
            font-size: 13.5pt;
            font-weight: bold;
            letter-spacing: 1.2px;
            margin-bottom: 14px;
            text-transform: uppercase;
            line-height: 1.15;
        }

        table.header-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        table.header-meta td {
            vertical-align: top;
            padding: 1px 0;
            border: none;
        }

        table.header-meta td.label {
            width: 235px;
            font-weight: normal;
            letter-spacing: 0.3px;
        }

        table.header-meta td.separator {
            width: 16px;
            text-align: center;
        }

        table.header-meta td.value {
            font-weight: normal;
        }

        /* Tabel Data KIR Sesuai Format Resmi Permendagri */
        table.kir-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9pt;
        }

        table.kir-table th, table.kir-table td {
            border: 1px solid #000;
            padding: 3.5px 4.5px;
            vertical-align: middle;
            word-break: break-word;
        }

        table.kir-table th {
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            background-color: #fff;
        }

        table.kir-table tr.col-numbers th {
            font-weight: normal;
            font-size: 7.5pt;
            padding: 1.5px;
        }

        table.kir-table td.text-center { text-align: center; }
        table.kir-table td.text-left { text-align: left; }
        table.kir-table td.text-right { text-align: right; }

        /* Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 16px;
            page-break-inside: avoid;
            display: flex;
            justify-content: space-between;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            font-size: 9.5pt;
            line-height: 1.2;
        }

        .ttd-box-left {
            float: left;
        }

        .ttd-box-right {
            float: right;
        }

        .ttd-title {
            margin-bottom: 2px;
        }

        .ttd-jabatan {
            margin-bottom: 4px;
        }

        .ttd-image-area {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px 0;
        }

        .ttd-image-area img {
            max-height: 52px;
            max-width: 175px;
            object-fit: contain;
        }

        .ttd-nama {
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 3px;
        }

        .ttd-nip {
            margin-top: 1px;
            letter-spacing: 0.3px;
        }

        .doc-date-display {
            cursor: pointer;
            border-bottom: 1px dashed #94a3b8;
            padding: 0 2px;
        }
        .doc-date-display:hover {
            background-color: #fef08a;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* =========================================================
           LEVEL KERAPATAN OTOMATIS (TIERED DENSITY CLASSES)
           Memastikan dari 1 sampai 70+ barang tetap muat tepat 1 lembar F4!
           ========================================================= */

        /* 1. Normal (<= 18 barang) */
        .density-normal .kir-title { font-size: 13.5pt; margin-bottom: 14px; }
        .density-normal table.header-meta { font-size: 9.5pt; margin-bottom: 12px; }
        .density-normal table.header-meta td { padding: 1px 0; }
        .density-normal table.kir-table { font-size: 9pt; margin-bottom: 16px; }
        .density-normal table.kir-table th, .density-normal table.kir-table td { padding: 3.5px 4.5px; }
        .density-normal .ttd-wrapper { margin-top: 18px; }
        .density-normal .ttd-box { font-size: 9.5pt; }
        .density-normal .ttd-image-area { height: 56px; }
        .density-normal .ttd-image-area img { max-height: 52px; }

        /* 2. Rapat (Compact: 19 - 28 barang) */
        .density-compact .kir-title { font-size: 12pt; margin-bottom: 8px; letter-spacing: 1px; }
        .density-compact table.header-meta { font-size: 8.5pt; margin-bottom: 7px; }
        .density-compact table.header-meta td { padding: 0.6px 0; }
        .density-compact table.kir-table { font-size: 8pt; margin-bottom: 10px; }
        .density-compact table.kir-table th, .density-compact table.kir-table td { padding: 2px 3.5px; line-height: 1.12; }
        .density-compact table.kir-table tr.col-numbers th { font-size: 7pt; padding: 1px; }
        .density-compact .ttd-wrapper { margin-top: 10px; }
        .density-compact .ttd-box { font-size: 8.8pt; }
        .density-compact .ttd-jabatan { margin-bottom: 3px; }
        .density-compact .ttd-image-area { height: 44px; margin: 1px 0; }
        .density-compact .ttd-image-area img { max-height: 40px; }
        .density-compact .ttd-nama { margin-top: 2px; }

        /* 3. Sangat Rapat (Dense: 29 - 40 barang) */
        .density-dense .kir-title { font-size: 11pt; margin-bottom: 5px; letter-spacing: 0.8px; }
        .density-dense table.header-meta { font-size: 8pt; margin-bottom: 5px; }
        .density-dense table.header-meta td { padding: 0.3px 0; }
        .density-dense table.kir-table { font-size: 7.2pt; margin-bottom: 6px; }
        .density-dense table.kir-table th, .density-dense table.kir-table td { padding: 1.3px 2.5px; line-height: 1.08; }
        .density-dense table.kir-table tr.col-numbers th { font-size: 6.5pt; padding: 0.6px; }
        .density-dense .ttd-wrapper { margin-top: 6px; }
        .density-dense .ttd-box { font-size: 8pt; }
        .density-dense .ttd-title { margin-bottom: 1px; }
        .density-dense .ttd-jabatan { margin-bottom: 1px; }
        .density-dense .ttd-image-area { height: 36px; margin: 1px 0; }
        .density-dense .ttd-image-area img { max-height: 34px; }
        .density-dense .ttd-nama { margin-top: 1px; }

        /* 4. Ultra Rapat (Ultra: 41+ barang, contoh: 48 barang di R. Gigi) */
        .density-ultra .kir-title { font-size: 10pt; margin-bottom: 3px; letter-spacing: 0.5px; }
        .density-ultra table.header-meta { font-size: 7.2pt; margin-bottom: 4px; }
        .density-ultra table.header-meta td { padding: 0.1px 0; }
        .density-ultra table.kir-table { font-size: 6.5pt; margin-bottom: 5px; }
        .density-ultra table.kir-table th, .density-ultra table.kir-table td { padding: 0.8px 2px; line-height: 1.04; }
        .density-ultra table.kir-table tr.col-numbers th { font-size: 5.8pt; padding: 0.3px; }
        .density-ultra .ttd-wrapper { margin-top: 5px; }
        .density-ultra .ttd-box { font-size: 7.2pt; }
        .density-ultra .ttd-title { margin-bottom: 0px; }
        .density-ultra .ttd-jabatan { margin-bottom: 1px; }
        .density-ultra .ttd-image-area { height: 30px; margin: 0; }
        .density-ultra .ttd-image-area img { max-height: 28px; }
        .density-ultra .ttd-nama { margin-top: 1px; }
        /* Opsi Sembunyikan KEDUA Gambar Tanda Tangan (dr. MUHAMAD SUNARYADI & ANDIKA DAFA PENTA PRATAMA)
           Menyisakan ruang kosong yang rapi untuk tanda tangan basah (manual) kedua belah pihak */
        body.hide-ttd .ttd-box-left .ttd-image-area img,
        body.hide-ttd .ttd-box-right .ttd-image-area img,
        body.hide-ttd .ttd-image-area img,
        .hide-ttd .ttd-image-area img {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        body.hide-ttd .ttd-image-area,
        .hide-ttd .ttd-image-area {
            visibility: hidden !important;
            min-height: 48px !important;
            height: 48px !important;
        }

        /* Mode Tampilan 2 Halaman vs 1 Halaman */
        .room-sheet-container.mode-two-pages .view-two-pages {
            display: block !important;
        }
        .room-sheet-container.mode-two-pages .view-single-page {
            display: none !important;
        }

        .room-sheet-container.mode-single-page .view-two-pages {
            display: none !important;
        }
        .room-sheet-container.mode-single-page .view-single-page {
            display: block !important;
        }

        /* Footer Halaman KIR */
        .kir-page-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7.5pt;
            color: #475569;
            border-top: 1px dashed #94a3b8;
            margin-top: 8px;
            padding-top: 3px;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            background: #0f172a;
            color: #fff;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 { margin: 0; font-size: 15px; font-weight: 600; }
        .modal-close {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }
        .modal-close:hover { color: #fff; }

        .modal-body {
            padding: 18px;
            color: #334155;
            font-size: 13px;
        }

        .modal-footer {
            background: #f8fafc;
            padding: 12px 18px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e2e8f0;
        }

        /* Notification Alert */
        .alert-box {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 3000;
            padding: 12px 18px;
            border-radius: 8px;
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #059669; }
        .alert-error { background: #dc2626; }

        /* =========================================================
           MEDIA PRINT KHUSUS F4 - DIJAMIN TEPAT 1 HALAMAN PER RUANGAN
           ========================================================= */
        @media print {
            @page {
                size: 216mm 330mm;
                margin: 10mm 12mm 10mm 12mm;
            }

            html, body {
                background: #fff !important;
                color: #000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .toolbar, .modal-overlay, .alert-box, .no-print {
                display: none !important;
            }

            .sheet-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                background: #fff !important;
            }

            /* Tiap ruangan mengunci tinggi 310mm (330mm - margin 20mm) sehingga 100% PASTI 1 Halaman */
            .page-sheet {
                width: 100% !important;
                height: 310mm !important;
                max-height: 310mm !important;
                min-height: 310mm !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                box-sizing: border-box !important;
                overflow: hidden !important; /* MUTLAK: Mencegah browser print engine membuat halaman 2 */
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .page-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .page-sheet-inner {
                width: 100% !important;
                height: 100% !important;
                max-height: 310mm !important;
                overflow: hidden !important;
            }

            .doc-date-display {
                border-bottom: none !important;
                padding: 0 !important;
            }

            /* Sembunyikan KEDUA Gambar TTD (dr. Muhamad Sunaryadi & Andika Dafa) saat di-print untuk Tanda Tangan Basah/Manual */
            body.hide-ttd .ttd-box-left .ttd-image-area img,
            body.hide-ttd .ttd-box-right .ttd-image-area img,
            body.hide-ttd .ttd-image-area img,
            .hide-ttd .ttd-image-area img {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }

            body.hide-ttd .ttd-image-area,
            .hide-ttd .ttd-image-area {
                visibility: hidden !important;
                min-height: 48px !important;
                height: 48px !important;
            }

            /* Mode 2 Halaman vs 1 Halaman saat Print */
            .room-sheet-container.mode-two-pages .view-single-page {
                display: none !important;
            }
            .room-sheet-container.mode-two-pages .view-two-pages {
                display: block !important;
            }

            .room-sheet-container.mode-single-page .view-two-pages {
                display: none !important;
            }
            .room-sheet-container.mode-single-page .view-single-page {
                display: block !important;
            }
        }
    </style>
</head>
<body class="{{ (isset($showTtd) && !$showTtd) ? 'hide-ttd' : '' }}">

    <!-- Notification Alert jika ada session flash -->
    @if(session('success'))
        <div class="alert-box alert-success" id="toastAlert">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-box alert-error" id="toastAlert">
            <span>⚠ {{ session('error') }}</span>
        </div>
    @endif

    <!-- Toolbar Layar Atas -->
    <div class="toolbar no-print">
        <div class="toolbar-group">
            <a href="{{ route('kir.room_items', $room->id) }}" class="btn btn-back">
                &larr; Kembali
            </a>
            
            <!-- Pemilih Ruangan Cepat -->
            <div style="display: flex; align-items: center; gap: 5px;">
                <span class="toolbar-label">Ruangan:</span>
                <select id="roomSelector" class="select-room" onchange="switchRoom(this.value)">
                    @foreach($allRooms as $r)
                        <option value="{{ $r->id }}" {{ $r->id == $room->id ? 'selected' : '' }}>
                            {{ $r->name }} ({{ $r->kirItems()->count() }} barang)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @php
            $isRoomGigi = ($room->id == 6) || (stripos($room->name, 'gigi') !== false);
            $itemCountMain = $room->kirItems()->count();
            $defaultTwoPages = (isset($modePages) && $modePages === '2') || (!isset($modePages) && ($itemCountMain > 35 || $isRoomGigi));
            if (isset($modePages) && $modePages === '1') {
                $defaultTwoPages = false;
            }
        @endphp

        <!-- Group Kontrol: Fitur Halaman & Tanda Tangan -->
        <div class="toolbar-group" style="background: rgba(15, 23, 42, 0.8); padding: 3px 8px; border-radius: 8px; border: 1px solid #334155;">
            <!-- Badge Status Muat Halaman -->
            <div id="pageFitBadge" class="fit-badge fit-success" title="Status muat dokumen pada kertas F4">
                <span class="fit-badge-dot"></span>
                <span id="pageFitText">✓ Pas F4 (Auto)</span>
            </div>

            <!-- Tombol Toggle Mode 1 Halaman vs 2 Halaman -->
            <button type="button" id="togglePageModeBtn" onclick="togglePageMode()" class="btn" style="background: #1e293b; color: #f8fafc; border: 1px solid #38bdf8;" title="Pilih format cetak: 1 Halaman (Ultra Rapat) atau 2 Halaman (Lega & Rapi)">
                📄 Format: <strong id="pageModeText" style="color: #38bdf8;">{{ $defaultTwoPages ? '2 HALAMAN' : '1 HALAMAN' }}</strong>
            </button>

            <!-- Tombol Toggle Gambar Tanda Tangan (TTD) -->
            <button type="button" id="toggleTtdBtn" onclick="toggleTtd()" class="btn" style="background: #1e293b; color: #f8fafc; border: 1px solid {{ (isset($showTtd) && !$showTtd) ? '#f87171' : '#475569' }};" title="Tampilkan kedua gambar tanda tangan atau kosongkan keduanya (ttd dr. Sunaryadi & ttd Andika Dafa) untuk tanda tangan basah secara manual">
                ✍ Gambar TTD: <strong id="ttdStatusText" style="color: {{ (isset($showTtd) && !$showTtd) ? '#f87171' : '#4ade80' }};">{{ (isset($showTtd) && !$showTtd) ? 'KOSONG KEDUANYA (TTD BASAH)' : 'DITAMPILKAN' }}</strong>
            </button>

            <!-- Tombol Toggle Kunci 1 Halaman -->
            <button type="button" id="toggleAutoFitBtn" onclick="toggleAutoFit()" class="btn btn-autofit active" title="Kunci dokumen agar selalu pas tepat pada kertas F4">
                ⚡ Auto-Fit: <strong style="color: #4ade80;">AKTIF</strong>
            </button>

            <!-- Dropdown Kerapatan Teks / Density -->
            <div style="display: flex; align-items: center; gap: 4px;">
                <span class="toolbar-label" style="font-size: 11px;">Kerapatan:</span>
                <select id="densitySelector" class="select-room" onchange="changeDensity(this.value)" style="font-size: 11.5px; padding: 3px 6px;">
                    <option value="auto">⚡ Otomatis</option>
                    <option value="density-normal">Standar (<= 18)</option>
                    <option value="density-compact">Rapat (19-28)</option>
                    <option value="density-dense">Sangat Rapat (29-40)</option>
                    <option value="density-ultra">Ultra Rapat (41+)</option>
                </select>
            </div>

            <!-- Dropdown Skala Zoom -->
            <div style="display: flex; align-items: center; gap: 4px;">
                <span class="toolbar-label" style="font-size: 11px;">Skala:</span>
                <select id="zoomSelector" class="select-room" onchange="changeZoom(this.value)" style="font-size: 11.5px; padding: 3px 6px;">
                    <option value="auto">⚡ Auto-Fit</option>
                    <option value="1">100%</option>
                    <option value="0.95">95%</option>
                    <option value="0.90">90%</option>
                    <option value="0.85">85%</option>
                    <option value="0.80">80%</option>
                    <option value="0.75">75%</option>
                    <option value="0.70">70%</option>
                </select>
            </div>
        </div>

        <div class="toolbar-group">
            <!-- Menu Pengubah Tanggal Cetak (KIR diupdate tiap 6 bulan sekali) -->
            <div style="display: flex; align-items: center; gap: 5px; background: rgba(30, 41, 59, 0.85); padding: 3px 6px; border-radius: 6px; border: 1px solid #334155;">
                <span class="toolbar-label" style="color: #38bdf8; font-weight: 600; font-size: 11.5px;">📅 Tgl Cetak:</span>
                <input type="date" id="datePicker" class="input-date" value="{{ $docDate }}" onchange="changeDocDate(this.value)" style="padding: 3px 6px; font-size: 11.5px;" title="Ubah tanggal tanda tangan KIR (Wajib diupdate tiap 6 bulan sekali)">
                
                <!-- Preset Cepat 6 Bulan Sekali -->
                <button type="button" class="btn btn-preset" onclick="setPresetDate('sem1')" title="Set semester 1 (02 Januari)">
                    02 Jan
                </button>
                <button type="button" class="btn btn-preset" onclick="setPresetDate('sem2')" title="Set semester 2 (01 Juli)">
                    01 Jul
                </button>
                <button type="button" class="btn btn-preset" onclick="setPresetDate('today')" title="Set tanggal hari ini">
                    Hari Ini
                </button>
            </div>

            <!-- Tombol Tambah Barang Manual -->
            <button type="button" onclick="openAddItemModal()" class="btn" style="background: #4f46e5; color: #fff;" title="Tambah barang satu per satu secara manual ke ruangan ini">
                + Tambah
            </button>

            <!-- Tombol Download Template Excel -->
            <a href="{{ route('rooms.kir.template', ['room_id' => $room->id]) }}" class="btn btn-download" title="Download Format Excel KIR Ruangan Ini untuk Diedit / Diisi">
                Download Excel
            </a>

            <!-- Tombol Upload / Import Excel -->
            <button type="button" onclick="openImportModal()" class="btn btn-upload" title="Unggah file Excel KIR yang telah diisi untuk langsung generate kartu inventaris">
                Upload Excel
            </button>

            <!-- Tombol Cetak Dokumen F4 -->
            <button type="button" onclick="printDoc()" class="btn btn-print" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); font-weight: 700; padding: 6px 14px; border: 1px solid #60a5fa;" title="Cetak Dokumen KIR pada Kertas F4">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak KIR (F4 - <span id="printBtnPageText">{{ $defaultTwoPages ? '2 Halaman' : '1 Halaman' }}</span>)
            </button>
        </div>
    </div>

    <!-- Container Lembar Cetak Dokumen -->
    <div class="sheet-wrapper">
        @php
            $renderRooms = isset($isBatch) && $isBatch ? $rooms : collect([$room]);
        @endphp

        @foreach($renderRooms as $currentRoom)
            @php
                $items = isset($isBatch) && $isBatch ? ($groupedItemsMap[$currentRoom->id] ?? collect()) : $groupedItems;
                $itemCount = count($items);
                $isGigiRoom = ($currentRoom->id == 6) || (stripos($currentRoom->name, 'gigi') !== false);
                $hasTwoPages = ($itemCount > 35) || $isGigiRoom;
                $roomTwoPages = (isset($modePages) && $modePages === '2') || (!isset($modePages) && $hasTwoPages);
                if (isset($modePages) && $modePages === '1') {
                    $roomTwoPages = false;
                }

                // Split index untuk 2 halaman (Halaman 1 maksimal 40 baris agar pas F4)
                $splitIndex = 40;
                if ($itemCount < 70 && $itemCount > 35) {
                    $splitIndex = (int)ceil($itemCount / 2);
                }
                $itemsPage1 = $items->slice(0, $splitIndex);
                $itemsPage2 = $items->slice($splitIndex);

                // Level kerapatan otomatis untuk mode 1 halaman
                $autoDensitySingle = $itemCount > 40 ? 'density-ultra' : ($itemCount > 28 ? 'density-dense' : ($itemCount > 18 ? 'density-compact' : 'density-normal'));
                // Level kerapatan untuk mode 2 halaman (karena per halaman hanya ~35-40 item)
                $densityPage1 = count($itemsPage1) > 28 ? 'density-dense' : 'density-compact';
                $densityPage2 = count($itemsPage2) > 28 ? 'density-dense' : 'density-compact';
            @endphp

            @if($hasTwoPages)
                <div class="room-sheet-container {{ $roomTwoPages ? 'mode-two-pages' : 'mode-single-page' }}" data-room-id="{{ $currentRoom->id }}">
                    <!-- VIEW 2 HALAMAN (LEGA, RAPI & MUDAH DIBACA) -->
                    <div class="view-two-pages">
                        <!-- LEMBAR 1 DARI 2 -->
                        <div class="page-sheet {{ $densityPage1 }} page-sheet-1" data-room-id="{{ $currentRoom->id }}" data-item-count="{{ count($itemsPage1) }}" data-auto-density="{{ $densityPage1 }}">
                            <div class="page-sheet-inner">
                                <div class="kir-title">KARTU INVENTARIS RUANGAN</div>
                                <table class="header-meta">
                                    <tr>
                                        <td class="label">PROPINSI</td>
                                        <td class="separator">:</td>
                                        <td class="value">JAWA TIMUR</td>
                                    </tr>
                                    <tr>
                                        <td class="label">KABUPATEN/KOTAMADYA</td>
                                        <td class="separator">:</td>
                                        <td class="value">LAMONGAN</td>
                                    </tr>
                                    <tr>
                                        <td class="label">UNIT</td>
                                        <td class="separator">:</td>
                                        <td class="value">DINAS KESEHATAN</td>
                                    </tr>
                                    <tr>
                                        <td class="label">SATUAN KERJA</td>
                                        <td class="separator">:</td>
                                        <td class="value">PUSKESMAS MANTUP</td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="letter-spacing: 2px;">R U A N G A N</td>
                                        <td class="separator">:</td>
                                        <td class="value" style="font-weight: bold;">{{ strtoupper($currentRoom->name) }}</td>
                                    </tr>
                                </table>

                                <!-- Tabel Bagian 1 (Items 1 s/d splitIndex) -->
                                <table class="kir-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width: 5%;">No. Urut</th>
                                            <th rowspan="2" style="width: 25%;">Jenis Barang / Nama Barang</th>
                                            <th rowspan="2" style="width: 15%;">Merek / Model</th>
                                            <th rowspan="2" style="width: 11%;">Bahan</th>
                                            <th rowspan="2" style="width: 8%;">Jumlah<br>Barang</th>
                                            <th colspan="3" style="width: 22%;">Keadaan Barang</th>
                                            <th rowspan="2" style="width: 14%;">Keterangan<br>Mutasi dll.</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 7.3%;">Baik</th>
                                            <th style="width: 7.3%;">Kurang<br>Baik</th>
                                            <th style="width: 7.3%;">Rusak<br>Berat</th>
                                        </tr>
                                        <tr class="col-numbers">
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>6</th>
                                            <th>9</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($itemsPage1 as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->no }}</td>
                                            <td class="text-left">{{ $item->name }}</td>
                                            <td class="text-left">{{ $item->merk ?: '' }}</td>
                                            <td class="text-left">{{ $item->bahan ?: 'Campuran' }}</td>
                                            <td class="text-center">{{ $item->total }}</td>
                                            <td class="text-center">{{ $item->baik > 0 ? $item->baik : '' }}</td>
                                            <td class="text-center">{{ $item->kurang_baik > 0 ? $item->kurang_baik : '' }}</td>
                                            <td class="text-center">{{ $item->rusak_berat > 0 ? $item->rusak_berat : '' }}</td>
                                            <td class="text-left">{{ $item->keterangan ?: '' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Footer Halaman 1 -->
                                <div class="kir-page-footer">
                                    <span>KIR Puskesmas Mantup &bull; {{ $currentRoom->name }}</span>
                                    <span style="font-weight: bold;">Halaman 1 dari 2 (Bersambung ke Halaman 2) &rarr;</span>
                                </div>
                            </div>
                        </div>

                        <!-- LEMBAR 2 DARI 2 -->
                        <div class="page-sheet {{ $densityPage2 }} page-sheet-2" data-room-id="{{ $currentRoom->id }}" data-item-count="{{ count($itemsPage2) }}" data-auto-density="{{ $densityPage2 }}">
                            <div class="page-sheet-inner">
                                <div class="kir-title" style="margin-bottom: 4px;">KARTU INVENTARIS RUANGAN (LANJUTAN)</div>
                                <table class="header-meta" style="margin-bottom: 6px;">
                                    <tr>
                                        <td class="label" style="width: 140px;">SATUAN KERJA</td>
                                        <td class="separator">:</td>
                                        <td class="value">PUSKESMAS MANTUP</td>
                                        <td style="text-align: right; font-weight: bold; color: #1e293b; font-size: 8.5pt;">HALAMAN 2 DARI 2</td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="letter-spacing: 2px;">R U A N G A N</td>
                                        <td class="separator">:</td>
                                        <td class="value" style="font-weight: bold;">{{ strtoupper($currentRoom->name) }}</td>
                                        <td style="text-align: right; font-size: 8pt; color: #64748b;">(Lanjutan Nomor {{ $splitIndex + 1 }} s/d {{ $itemCount }})</td>
                                    </tr>
                                </table>

                                <!-- Tabel Bagian 2 (Items splitIndex+1 s/d end) -->
                                <table class="kir-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width: 5%;">No. Urut</th>
                                            <th rowspan="2" style="width: 25%;">Jenis Barang / Nama Barang</th>
                                            <th rowspan="2" style="width: 15%;">Merek / Model</th>
                                            <th rowspan="2" style="width: 11%;">Bahan</th>
                                            <th rowspan="2" style="width: 8%;">Jumlah<br>Barang</th>
                                            <th colspan="3" style="width: 22%;">Keadaan Barang</th>
                                            <th rowspan="2" style="width: 14%;">Keterangan<br>Mutasi dll.</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 7.3%;">Baik</th>
                                            <th style="width: 7.3%;">Kurang<br>Baik</th>
                                            <th style="width: 7.3%;">Rusak<br>Berat</th>
                                        </tr>
                                        <tr class="col-numbers">
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>6</th>
                                            <th>9</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($itemsPage2 as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->no }}</td>
                                            <td class="text-left">{{ $item->name }}</td>
                                            <td class="text-left">{{ $item->merk ?: '' }}</td>
                                            <td class="text-left">{{ $item->bahan ?: 'Campuran' }}</td>
                                            <td class="text-center">{{ $item->total }}</td>
                                            <td class="text-center">{{ $item->baik > 0 ? $item->baik : '' }}</td>
                                            <td class="text-center">{{ $item->kurang_baik > 0 ? $item->kurang_baik : '' }}</td>
                                            <td class="text-center">{{ $item->rusak_berat > 0 ? $item->rusak_berat : '' }}</td>
                                            <td class="text-left">{{ $item->keterangan ?: '' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Kolom Tanda Tangan Sesuai Posisi & Format Resmi (Bisa Disembunyikan) -->
                                <div class="ttd-wrapper clearfix">
                                    <div class="ttd-box ttd-box-left">
                                        <div class="ttd-title">Mengetahui</div>
                                        <div class="ttd-jabatan">Kepala Puskesmas Mantup</div>
                                        <div class="ttd-image-area">
                                            <img src="{{ asset('images/ttd_kapus_nostamp.png') }}?v={{ file_exists(public_path('images/ttd_kapus_nostamp.png')) ? filemtime(public_path('images/ttd_kapus_nostamp.png')) : time() }}" alt="TTD dr. Sunaryadi">
                                        </div>
                                        <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                                        <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                                    </div>

                                    <div class="ttd-box ttd-box-right">
                                        <div class="ttd-title">
                                            Mantup, <span class="doc-date-display" contenteditable="true" title="Klik untuk edit teks manual jika diperlukan">{{ $formattedDate }}</span>
                                        </div>
                                        <div class="ttd-jabatan">Pengurus Barang Puskesmas Mantup</div>
                                        <div class="ttd-image-area">
                                            <img src="{{ asset('images/ttd_andika.png') }}?v={{ file_exists(public_path('images/ttd_andika.png')) ? filemtime(public_path('images/ttd_andika.png')) : time() }}" alt="TTD Andika Dafa">
                                        </div>
                                        <div class="ttd-nama">ANDIKA DAFA PENTA PRATAMA</div>
                                        <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                                    </div>
                                </div>

                                <!-- Footer Halaman 2 -->
                                <div class="kir-page-footer">
                                    <span>KIR Puskesmas Mantup &bull; {{ $currentRoom->name }}</span>
                                    <span style="font-weight: bold;">Halaman 2 dari 2 (Selesai)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 1 HALAMAN (ALTERNATIF RAPAT) -->
                    <div class="view-single-page">
                        <div class="page-sheet {{ $autoDensitySingle }}" data-room-id="{{ $currentRoom->id }}" data-item-count="{{ $itemCount }}" data-auto-density="{{ $autoDensitySingle }}">
                            <div class="page-sheet-inner">
                                <div class="kir-title">KARTU INVENTARIS RUANGAN</div>
                                <table class="header-meta">
                                    <tr>
                                        <td class="label">PROPINSI</td>
                                        <td class="separator">:</td>
                                        <td class="value">JAWA TIMUR</td>
                                    </tr>
                                    <tr>
                                        <td class="label">KABUPATEN/KOTAMADYA</td>
                                        <td class="separator">:</td>
                                        <td class="value">LAMONGAN</td>
                                    </tr>
                                    <tr>
                                        <td class="label">UNIT</td>
                                        <td class="separator">:</td>
                                        <td class="value">DINAS KESEHATAN</td>
                                    </tr>
                                    <tr>
                                        <td class="label">SATUAN KERJA</td>
                                        <td class="separator">:</td>
                                        <td class="value">PUSKESMAS MANTUP</td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="letter-spacing: 2px;">R U A N G A N</td>
                                        <td class="separator">:</td>
                                        <td class="value" style="font-weight: bold;">{{ strtoupper($currentRoom->name) }}</td>
                                    </tr>
                                </table>

                                <table class="kir-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width: 5%;">No. Urut</th>
                                            <th rowspan="2" style="width: 25%;">Jenis Barang / Nama Barang</th>
                                            <th rowspan="2" style="width: 15%;">Merek / Model</th>
                                            <th rowspan="2" style="width: 11%;">Bahan</th>
                                            <th rowspan="2" style="width: 8%;">Jumlah<br>Barang</th>
                                            <th colspan="3" style="width: 22%;">Keadaan Barang</th>
                                            <th rowspan="2" style="width: 14%;">Keterangan<br>Mutasi dll.</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 7.3%;">Baik</th>
                                            <th style="width: 7.3%;">Kurang<br>Baik</th>
                                            <th style="width: 7.3%;">Rusak<br>Berat</th>
                                        </tr>
                                        <tr class="col-numbers">
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>6</th>
                                            <th>9</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->no }}</td>
                                            <td class="text-left">{{ $item->name }}</td>
                                            <td class="text-left">{{ $item->merk ?: '' }}</td>
                                            <td class="text-left">{{ $item->bahan ?: 'Campuran' }}</td>
                                            <td class="text-center">{{ $item->total }}</td>
                                            <td class="text-center">{{ $item->baik > 0 ? $item->baik : '' }}</td>
                                            <td class="text-center">{{ $item->kurang_baik > 0 ? $item->kurang_baik : '' }}</td>
                                            <td class="text-center">{{ $item->rusak_berat > 0 ? $item->rusak_berat : '' }}</td>
                                            <td class="text-left">{{ $item->keterangan ?: '' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="ttd-wrapper clearfix">
                                    <div class="ttd-box ttd-box-left">
                                        <div class="ttd-title">Mengetahui</div>
                                        <div class="ttd-jabatan">Kepala Puskesmas Mantup</div>
                                        <div class="ttd-image-area">
                                            <img src="{{ asset('images/ttd_kapus_nostamp.png') }}?v={{ file_exists(public_path('images/ttd_kapus_nostamp.png')) ? filemtime(public_path('images/ttd_kapus_nostamp.png')) : time() }}" alt="TTD dr. Sunaryadi">
                                        </div>
                                        <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                                        <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                                    </div>
                                    <div class="ttd-box ttd-box-right">
                                        <div class="ttd-title">
                                            Mantup, <span class="doc-date-display" contenteditable="true">{{ $formattedDate }}</span>
                                        </div>
                                        <div class="ttd-jabatan">Pengurus Barang Puskesmas Mantup</div>
                                        <div class="ttd-image-area">
                                            <img src="{{ asset('images/ttd_andika.png') }}?v={{ file_exists(public_path('images/ttd_andika.png')) ? filemtime(public_path('images/ttd_andika.png')) : time() }}" alt="TTD Andika Dafa">
                                        </div>
                                        <div class="ttd-nama">ANDIKA DAFA PENTA PRATAMA</div>
                                        <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Ruangan Normal (<= 35 item): Standar 1 Halaman -->
                <div class="page-sheet {{ $autoDensitySingle }}" data-room-id="{{ $currentRoom->id }}" data-item-count="{{ $itemCount }}" data-auto-density="{{ $autoDensitySingle }}">
                    <div class="page-sheet-inner">
                        <div class="kir-title">KARTU INVENTARIS RUANGAN</div>
                        <table class="header-meta">
                            <tr>
                                <td class="label">PROPINSI</td>
                                <td class="separator">:</td>
                                <td class="value">JAWA TIMUR</td>
                            </tr>
                            <tr>
                                <td class="label">KABUPATEN/KOTAMADYA</td>
                                <td class="separator">:</td>
                                <td class="value">LAMONGAN</td>
                            </tr>
                            <tr>
                                <td class="label">UNIT</td>
                                <td class="separator">:</td>
                                <td class="value">DINAS KESEHATAN</td>
                            </tr>
                            <tr>
                                <td class="label">SATUAN KERJA</td>
                                <td class="separator">:</td>
                                <td class="value">PUSKESMAS MANTUP</td>
                            </tr>
                            <tr>
                                <td class="label" style="letter-spacing: 2px;">R U A N G A N</td>
                                <td class="separator">:</td>
                                <td class="value" style="font-weight: bold;">{{ strtoupper($currentRoom->name) }}</td>
                            </tr>
                        </table>

                        <table class="kir-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 5%;">No. Urut</th>
                                    <th rowspan="2" style="width: 25%;">Jenis Barang / Nama Barang</th>
                                    <th rowspan="2" style="width: 15%;">Merek / Model</th>
                                    <th rowspan="2" style="width: 11%;">Bahan</th>
                                    <th rowspan="2" style="width: 8%;">Jumlah<br>Barang</th>
                                    <th colspan="3" style="width: 22%;">Keadaan Barang</th>
                                    <th rowspan="2" style="width: 14%;">Keterangan<br>Mutasi dll.</th>
                                </tr>
                                <tr>
                                    <th style="width: 7.3%;">Baik</th>
                                    <th style="width: 7.3%;">Kurang<br>Baik</th>
                                    <th style="width: 7.3%;">Rusak<br>Berat</th>
                                </tr>
                                <tr class="col-numbers">
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>6</th>
                                    <th>9</th>
                                    <th>10</th>
                                    <th>11</th>
                                    <th>12</th>
                                    <th>13</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td class="text-center">{{ $item->no }}</td>
                                    <td class="text-left">{{ $item->name }}</td>
                                    <td class="text-left">{{ $item->merk ?: '' }}</td>
                                    <td class="text-left">{{ $item->bahan ?: 'Campuran' }}</td>
                                    <td class="text-center">{{ $item->total }}</td>
                                    <td class="text-center">{{ $item->baik > 0 ? $item->baik : '' }}</td>
                                    <td class="text-center">{{ $item->kurang_baik > 0 ? $item->kurang_baik : '' }}</td>
                                    <td class="text-center">{{ $item->rusak_berat > 0 ? $item->rusak_berat : '' }}</td>
                                    <td class="text-left">{{ $item->keterangan ?: '' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center" style="padding: 24px 15px;">
                                        <div style="color: #64748b; font-size: 10pt; margin-bottom: 10px; font-style: italic;">
                                            Belum ada data barang di ruangan {{ $currentRoom->name }}.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="ttd-wrapper clearfix">
                            <div class="ttd-box ttd-box-left">
                                <div class="ttd-title">Mengetahui</div>
                                <div class="ttd-jabatan">Kepala Puskesmas Mantup</div>
                                <div class="ttd-image-area">
                                    <img src="{{ asset('images/ttd_kapus_nostamp.png') }}?v={{ file_exists(public_path('images/ttd_kapus_nostamp.png')) ? filemtime(public_path('images/ttd_kapus_nostamp.png')) : time() }}" alt="TTD dr. Sunaryadi">
                                </div>
                                <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                                <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                            </div>
                            <div class="ttd-box ttd-box-right">
                                <div class="ttd-title">
                                    Mantup, <span class="doc-date-display" contenteditable="true">{{ $formattedDate }}</span>
                                </div>
                                <div class="ttd-jabatan">Pengurus Barang Puskesmas Mantup</div>
                                <div class="ttd-image-area">
                                    <img src="{{ asset('images/ttd_andika.png') }}?v={{ file_exists(public_path('images/ttd_andika.png')) ? filemtime(public_path('images/ttd_andika.png')) : time() }}" alt="TTD Andika Dafa">
                                </div>
                                <div class="ttd-nama">ANDIKA DAFA PENTA PRATAMA</div>
                                <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Modal Tambah Barang Manual -->
    <div id="addItemModal" class="modal-overlay" onclick="if(event.target === this) closeAddItemModal()">
        <div class="modal-content">
            <div class="modal-header" style="background: #1e1b4b;">
                <h3>➕ Tambah Barang ke Ruangan {{ $room->name }}</h3>
                <button type="button" class="modal-close" onclick="closeAddItemModal()">&times;</button>
            </div>
            <form action="{{ route('rooms.kir.add_item', $room->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="margin-top: 0; line-height: 1.4; color: #475569; font-size: 12px; margin-bottom: 14px;">
                        Masukkan data barang satu per satu. Data akan langsung tercatat di sistem e-Aset dan ditampilkan pada Kartu Inventaris Ruangan (KIR).
                    </p>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Nama / Jenis Barang <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" required placeholder="Cth: AC, TV LED, Kursi, Lemari Kaca..." style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Merek / Model</label>
                            <input type="text" name="merk" placeholder="Cth: Panasonic, Polaris, Dell..." style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Bahan</label>
                            <input type="text" name="bahan" value="Campuran" placeholder="Cth: Campuran, Besi, Kayu..." list="bahanList" style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                            <datalist id="bahanList">
                                <option value="Campuran">
                                <option value="Besi">
                                <option value="Kayu">
                                <option value="Plastik">
                                <option value="Kaca">
                                <option value="Alumunium">
                                <option value="Karet">
                            </datalist>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Jumlah Barang <span style="color: #ef4444;">*</span></label>
                            <input type="number" name="jumlah" min="1" value="1" required style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Keadaan / Kondisi <span style="color: #ef4444;">*</span></label>
                            <select name="condition" required style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                                <option value="Baik" selected>Baik</option>
                                <option value="Kurang Baik">Kurang Baik</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 8px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px;">Keterangan Mutasi dll.</label>
                        <input type="text" name="keterangan" placeholder="Opsional (cth: Hibah, Mutasi, dll.)" style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeAddItemModal()">Batal</button>
                    <button type="submit" class="btn" style="background: #4f46e5; color: #fff;">
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Upload Excel KIR -->
    <div id="importModal" class="modal-overlay" onclick="if(event.target === this) closeImportModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Unggah File Excel KIR</h3>
                <button type="button" class="modal-close" onclick="closeImportModal()">&times;</button>
            </div>
            <form action="{{ route('rooms.kir.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p style="margin-top: 0; line-height: 1.4; color: #475569;">
                        Pilih file Excel (<strong>.xlsx</strong> / <strong>.xls</strong> / <strong>.csv</strong>) yang berisi daftar barang inventaris. Sistem akan langsung memproses dan meng-generate Kartu Inventaris Ruangan (KIR) dengan format resmi.
                    </p>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12.5px;">Target Ruangan:</label>
                        <select name="room_id" class="select-room" style="width: 100%; background: #f8fafc; color: #0f172a; border-color: #cbd5e1;">
                            <option value="">-- Sesuai Kolom 'Nama Ruangan' di File Excel --</option>
                            @foreach($allRooms as $r)
                                <option value="{{ $r->id }}" {{ $r->id == $room->id ? 'selected' : '' }}>
                                    Ruangan Saat Ini: {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                        <span style="display: block; font-size: 11px; color: #64748b; margin-top: 3px;">
                            Jika file Excel memuat banyak ruangan, sistem akan mengelompokkan ke masing-masing ruangan secara otomatis.
                        </span>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 12.5px;">Pilih File Excel:</label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" required style="width: 100%; font-size: 12.5px; border: 1px dashed #94a3b8; padding: 10px; border-radius: 6px; background: #f8fafc;">
                    </div>

                    <div style="background: #f1f5f9; padding: 10px 12px; border-radius: 6px; margin-bottom: 14px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 12px; color: #1e293b;">
                            <input type="checkbox" name="replace_existing" value="1" checked style="margin-top: 2px;">
                            <span>
                                <strong>Perbarui data berkala (Timpa data sebelumnya)</strong><br>
                                <span style="color: #64748b; font-size: 11px;">
                                    Centang opsi ini saat update 6 bulan sekali agar daftar barang lama di ruangan digantikan dengan data terkini dari file Excel.
                                </span>
                            </span>
                        </label>
                    </div>

                    <div style="font-size: 11.5px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                        💡 Belum punya formatnya?
                        <a href="{{ route('rooms.kir.template', ['room_id' => $room->id]) }}" style="color: #2563eb; font-weight: 600; text-decoration: underline;">
                            Download Template Excel Resmi Di Sini
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeImportModal()">Batal</button>
                    <button type="submit" class="btn btn-download">
                        Unggah & Generate KIR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Interaktif untuk Auto-Fit, Mode Halaman (1 vs 2 Halaman), Opsi TTD, Tanggal, Ruangan & Modal -->
    <script>
        // State Konfigurasi Auto-Fit & Layout
        let isAutoFitEnabled = true;
        let selectedDensity = 'auto'; // 'auto' | 'density-normal' | 'density-compact' | 'density-dense' | 'density-ultra'
        let selectedZoom = 'auto';    // 'auto' | '1' | '0.95' | ...
        let isTtdShown = {{ (isset($showTtd) && !$showTtd) ? 'false' : 'true' }};
        let isTwoPagesMode = {{ (isset($defaultTwoPages) && $defaultTwoPages) ? 'true' : 'false' }};

        // Auto-dismiss alert toast
        setTimeout(() => {
            const toast = document.getElementById('toastAlert');
            if (toast) toast.style.display = 'none';
        }, 5000);

        // Format tanggal Indonesia
        function formatIndoDate(dateString) {
            if (!dateString) return '';
            const parts = dateString.split('-');
            if (parts.length !== 3) return dateString;
            const day = parts[2].padStart(2, '0');
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const month = months[parseInt(parts[1], 10) - 1] || '';
            const year = parts[0];
            return `${day} ${month} ${year}`;
        }

        // Ubah tanggal dinamis secara instan
        function changeDocDate(newDate) {
            if (!newDate) return;
            const formatted = formatIndoDate(newDate);
            document.querySelectorAll('.doc-date-display').forEach(el => {
                el.textContent = formatted;
            });
            localStorage.setItem('e_aset_kir_date', newDate);

            // Update URL query tanpa refresh halaman
            const url = new URL(window.location.href);
            url.searchParams.set('date', newDate);
            window.history.replaceState({}, '', url.toString());
        }

        // Quick Preset 6 Bulan Sekali
        function setPresetDate(type) {
            const currentYear = new Date().getFullYear();
            let targetDate = '';
            if (type === 'sem1') {
                targetDate = `${currentYear}-01-02`;
            } else if (type === 'sem2') {
                targetDate = `${currentYear}-07-01`;
            } else if (type === 'today') {
                const now = new Date();
                const m = String(now.getMonth() + 1).padStart(2, '0');
                const d = String(now.getDate()).padStart(2, '0');
                targetDate = `${now.getFullYear()}-${m}-${d}`;
            }

            document.getElementById('datePicker').value = targetDate;
            changeDocDate(targetDate);
        }

        // Ganti Ruangan dari Toolbar
        function switchRoom(roomId) {
            if (!roomId) return;
            const currentDate = document.getElementById('datePicker').value || '2026-01-02';
            const ttdParam = isTtdShown ? '1' : '0';
            const pagesParam = isTwoPagesMode ? '2' : '1';
            window.location.href = `/rooms/${roomId}/kir?date=${currentDate}&ttd=${ttdParam}&pages=${pagesParam}`;
        }

        // Buka / Tutup Modal Import Excel
        function openImportModal() {
            document.getElementById('importModal').style.display = 'flex';
        }

        function closeImportModal() {
            document.getElementById('importModal').style.display = 'none';
        }

        // Buka / Tutup Modal Tambah Barang Manual
        function openAddItemModal() {
            document.getElementById('addItemModal').style.display = 'flex';
        }

        function closeAddItemModal() {
            document.getElementById('addItemModal').style.display = 'none';
        }

        /* =========================================================
           KONTROL INTERAKTIF: TOGGLE TANDA TANGAN (TTD)
           ========================================================= */
        function toggleTtd() {
            isTtdShown = !isTtdShown;
            applyTtdState();
        }

        function applyTtdState() {
            const btn = document.getElementById('toggleTtdBtn');
            const text = document.getElementById('ttdStatusText');
            const body = document.body;

            if (isTtdShown) {
                body.classList.remove('hide-ttd');
                if (text) {
                    text.textContent = 'DITAMPILKAN';
                    text.style.color = '#4ade80';
                }
                if (btn) btn.style.borderColor = '#475569';
            } else {
                body.classList.add('hide-ttd');
                if (text) {
                    text.textContent = 'KOSONG KEDUANYA (TTD BASAH)';
                    text.style.color = '#f87171';
                }
                if (btn) btn.style.borderColor = '#f87171';
            }

            localStorage.setItem('e_aset_kir_show_ttd', isTtdShown ? '1' : '0');

            const url = new URL(window.location.href);
            url.searchParams.set('ttd', isTtdShown ? '1' : '0');
            window.history.replaceState({}, '', url.toString());

            applyAutoFit();
        }

        /* =========================================================
           KONTROL INTERAKTIF: TOGGLE 1 HALAMAN VS 2 HALAMAN
           ========================================================= */
        function togglePageMode() {
            isTwoPagesMode = !isTwoPagesMode;
            applyPageModeState();
        }

        function applyPageModeState() {
            const text = document.getElementById('pageModeText');
            const printText = document.getElementById('printBtnPageText');
            const containers = document.querySelectorAll('.room-sheet-container');

            containers.forEach(container => {
                if (isTwoPagesMode) {
                    container.classList.remove('mode-single-page');
                    container.classList.add('mode-two-pages');
                } else {
                    container.classList.remove('mode-two-pages');
                    container.classList.add('mode-single-page');
                }
            });

            if (text) {
                text.textContent = isTwoPagesMode ? '2 HALAMAN' : '1 HALAMAN';
                text.style.color = isTwoPagesMode ? '#38bdf8' : '#fbbf24';
            }
            if (printText) {
                printText.textContent = isTwoPagesMode ? '2 Halaman' : '1 Halaman';
            }

            localStorage.setItem('e_aset_kir_pages_mode', isTwoPagesMode ? '2' : '1');

            const url = new URL(window.location.href);
            url.searchParams.set('pages', isTwoPagesMode ? '2' : '1');
            window.history.replaceState({}, '', url.toString());

            applyAutoFit();
        }

        /* =========================================================
           MESIN AUTO-FIT 1/2 HALAMAN PRESISI TINGGI (F4)
           ========================================================= */
        function applyAutoFit() {
            const sheets = document.querySelectorAll('.page-sheet');
            let minScaleUsed = 1;
            let totalSheets = 0;
            let maxOverflowFound = 0;

            sheets.forEach((sheet) => {
                // Abaikan sheet yang tidak aktif / tersembunyi
                if (sheet.offsetParent === null && window.getComputedStyle(sheet).display === 'none') {
                    return;
                }
                totalSheets++;

                const inner = sheet.querySelector('.page-sheet-inner');
                if (!inner) return;

                // 1. Terapkan kerapatan (density)
                sheet.classList.remove('density-normal', 'density-compact', 'density-dense', 'density-ultra');
                if (selectedDensity === 'auto') {
                    const autoDensity = sheet.dataset.autoDensity || 'density-normal';
                    sheet.classList.add(autoDensity);
                } else {
                    sheet.classList.add(selectedDensity);
                }

                // Reset zoom sementara untuk pengukuran tinggi murni
                inner.style.zoom = '1';
                inner.style.transform = 'none';

                // 2. Jika Auto-Fit Tidak Aktif dan ada manual zoom
                if (!isAutoFitEnabled) {
                    if (selectedZoom !== 'auto') {
                        inner.style.zoom = selectedZoom;
                    }
                    return;
                }

                // 3. Jika Skala Manual dipilih oleh user
                if (selectedZoom !== 'auto') {
                    inner.style.zoom = selectedZoom;
                    return;
                }

                // 4. Hitung tinggi area cetak F4 yang tersedia
                const availableHeight = sheet.clientHeight;
                const contentHeight = inner.scrollHeight;

                if (contentHeight > availableHeight && availableHeight > 0) {
                    let scale = (availableHeight - 4) / contentHeight;
                    scale = Math.max(0.55, Math.floor(scale * 1000) / 1000);

                    inner.style.zoom = scale;
                    inner.dataset.appliedScale = scale;

                    if (scale < minScaleUsed) {
                        minScaleUsed = scale;
                    }
                    maxOverflowFound = Math.max(maxOverflowFound, contentHeight - availableHeight);
                } else {
                    inner.style.zoom = '1';
                    inner.dataset.appliedScale = '1';
                }
            });

            // Update badge indikator di toolbar
            updateBadgeStatus(minScaleUsed, totalSheets);
        }

        // Perbarui status badge di toolbar
        function updateBadgeStatus(minScale, totalSheets) {
            const badge = document.getElementById('pageFitBadge');
            const text = document.getElementById('pageFitText');
            if (!badge || !text) return;

            if (!isAutoFitEnabled) {
                badge.className = 'fit-badge fit-warning';
                text.textContent = '⚠ Auto-Fit Dimatikan (Manual)';
                return;
            }

            badge.className = 'fit-badge fit-success';
            const percent = Math.round(minScale * 100);

            if (isTwoPagesMode) {
                if (minScale < 1) {
                    text.textContent = `✓ 2 Halaman F4 (Skala ${percent}%)`;
                } else {
                    text.textContent = `✓ 2 Halaman F4 (100% Pas & Lega)`;
                }
            } else {
                if (minScale < 1) {
                    text.textContent = `✓ 1 Halaman F4 (Skala ${percent}%)`;
                } else {
                    text.textContent = `✓ 1 Halaman F4 (100% Pas)`;
                }
            }
        }

        // Toggle Tombol Kunci 1 Halaman
        function toggleAutoFit() {
            isAutoFitEnabled = !isAutoFitEnabled;
            const btn = document.getElementById('toggleAutoFitBtn');

            if (isAutoFitEnabled) {
                btn.className = 'btn btn-autofit active';
                btn.innerHTML = '⚡ Auto-Fit: <strong style="color: #4ade80;">AKTIF</strong>';
            } else {
                btn.className = 'btn btn-autofit';
                btn.innerHTML = '⚡ Auto-Fit: <strong style="color: #f87171;">NONAKTIF</strong>';
            }

            applyAutoFit();
        }

        // Ganti Kerapatan Manual
        function changeDensity(val) {
            selectedDensity = val;
            applyAutoFit();
        }

        // Ganti Skala Zoom Manual
        function changeZoom(val) {
            selectedZoom = val;
            applyAutoFit();
        }

        // Fungsi Cetak dengan Auto-Fit Eksekusi Terlebih Dahulu
        function printDoc() {
            applyAutoFit();
            setTimeout(() => {
                window.print();
            }, 60);
        }

        window.addEventListener('beforeprint', () => {
            applyAutoFit();
        });

        window.addEventListener('resize', () => {
            applyAutoFit();
        });

        // Inisialisasi awal saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);

            // Cek URL params untuk TTD
            if (urlParams.has('ttd')) {
                isTtdShown = urlParams.get('ttd') === '1';
            } else {
                const savedTtd = localStorage.getItem('e_aset_kir_show_ttd');
                if (savedTtd !== null) {
                    isTtdShown = savedTtd === '1';
                }
            }
            applyTtdState();

            // Cek URL params untuk Mode Halaman
            if (urlParams.has('pages')) {
                isTwoPagesMode = urlParams.get('pages') === '2';
            } else {
                const savedPages = localStorage.getItem('e_aset_kir_pages_mode');
                if (savedPages !== null) {
                    isTwoPagesMode = savedPages === '2';
                }
            }
            applyPageModeState();

            // Cek tanggal dari localStorage
            const urlDate = urlParams.get('date');
            if (!urlDate) {
                const savedDate = localStorage.getItem('e_aset_kir_date');
                if (savedDate && savedDate !== '{{ $docDate }}') {
                    document.getElementById('datePicker').value = savedDate;
                    changeDocDate(savedDate);
                }
            }

            setTimeout(applyAutoFit, 100);

            window.addEventListener('load', () => {
                applyAutoFit();
            });
        });
    </script>
</body>
</html>
