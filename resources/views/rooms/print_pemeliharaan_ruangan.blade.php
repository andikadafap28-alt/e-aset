<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pemeliharaan Alat - A4 (21cm x 29.7cm)</title>
    <style>
        @page {
            size: A4 portrait; /* Standar Ukuran Kertas A4: 210mm x 297mm */
            margin: 8mm 8mm 6mm 8mm; /* Margin cetak seimbang agar seluruh tabel & garis kanan utuh */
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        :root {
            --ttd-shift-y: 0mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Toolbar Layar (Tidak Tercetak) */
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #0f172a;
            color: white;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.35);
            flex-wrap: wrap;
            gap: 10px;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar label {
            font-size: 11.5px;
            font-weight: 600;
            color: #cbd5e1;
        }

        .toolbar input[type="date"],
        .toolbar input[type="number"],
        .toolbar select {
            background: #1e293b;
            color: #ffffff;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 12.5px;
            font-weight: bold;
            outline: none;
        }

        .toolbar input[type="date"]:focus,
        .toolbar input[type="number"]:focus,
        .toolbar select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px #38bdf8;
        }

        .toolbar .btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12.5px;
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
            box-shadow: 0 2px 6px rgba(37,99,235,0.4);
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #334155;
            color: white;
        }
        .btn-back:hover {
            background: #475569;
        }

        .btn-tool-action {
            background: #0284c7;
            color: white;
            padding: 5px 10px !important;
            font-size: 11.5px !important;
        }
        .btn-tool-action:hover {
            background: #0369a1;
        }

        .btn-emerald {
            background: #059669;
            color: white;
        }
        .btn-emerald:hover {
            background: #047857;
        }

        .preview-wrapper {
            margin-top: 65px;
            padding: 20px 10px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 28px;
        }

        /* Banner Informasi Mode Cetak Susulan TTD */
        .banner-susulan {
            width: 210mm;
            max-width: 95vw;
            background: #451a03;
            border: 1.5px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 16px;
            color: #fef3c7;
            font-size: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.3);
            margin-bottom: -15px;
            box-sizing: border-box;
        }

        /* Pembungkus per Lembar Ruangan (Layar) */
        .sheet-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        /* Banner Pengatur Baris Khusus per Lembar (Layar) */
        .sheet-control-banner {
            width: 210mm;
            background: #1e293b;
            color: #f8fafc;
            border-radius: 8px;
            padding: 6px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: 1px solid #334155;
            box-sizing: border-box;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sheet-control-banner .sheet-title-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sheet-control-banner .sheet-num {
            background: #3b82f6;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .sheet-control-banner .sheet-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
        }

        .sheet-control-banner .sheet-action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sheet-control-banner label {
            font-size: 11.5px;
            color: #cbd5e1;
            font-weight: 600;
        }

        .stepper-box {
            display: inline-flex;
            align-items: center;
            background: #0f172a;
            border: 1px solid #475569;
            border-radius: 6px;
            overflow: hidden;
        }

        .btn-step {
            background: #334155;
            color: white;
            border: none;
            width: 26px;
            height: 28px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .btn-step:hover {
            background: #475569;
        }

        .sheet-row-input {
            width: 44px;
            height: 28px;
            background: #0f172a;
            color: #ffffff;
            border: none;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            outline: none;
            -moz-appearance: textfield;
        }
        .sheet-row-input::-webkit-outer-spin-button,
        .sheet-row-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .sheet-quick-select {
            background: #0f172a;
            color: #ffffff;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 4px 6px;
            font-size: 12px;
            font-weight: bold;
            outline: none;
        }

        .sheet-status-badge {
            background: #065f46;
            color: #a7f3d0;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #059669;
        }

        /* Lembar Kertas A4 (21cm x 29.7cm) di Layar */
        .page-sheet {
            background: white;
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            padding: 8mm 9mm 6mm 9mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }

        /* Kop Surat Resmi Puskesmas Mantup */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px double #000;
            padding-bottom: 3px;
            margin-bottom: 4px;
            table-layout: fixed;
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
            max-width: 56px;
            max-height: 56px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            line-height: 1.2;
            padding: 0 6px;
        }
        .kop-text .line-1 {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kop-text .line-2 {
            font-size: 10.5pt;
            font-weight: bold;
        }
        .kop-text .line-3 {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 1px 0;
        }
        .kop-text .line-4 {
            font-size: 8pt;
            color: #111;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 3px 0 5px;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 1.5px 0 0;
            font-size: 8pt;
            font-style: italic;
            color: #333;
        }

        /* Info Ruangan */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 8.5pt;
            table-layout: fixed;
        }
        .info-table td {
            padding: 1px 3px;
            vertical-align: top;
            border: none;
        }
        .info-label {
            width: 145px;
            font-weight: bold;
        }
        .info-sep {
            width: 10px;
            font-weight: bold;
        }
        .info-value {
            font-weight: bold;
        }

        /* Tabel Utama Pemeliharaan Alat */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7.8pt;
            margin-bottom: 3px;
            border: 1px solid #000;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            box-sizing: border-box;
        }
        .main-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            height: 28px;
            vertical-align: middle;
            line-height: 1.2;
            padding: 2px 2px;
            word-break: normal;
            overflow-wrap: normal;
        }
        .main-table td {
            height: 20px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .main-table tr.blank-row td {
            height: 20px; /* Tinggi baris pas & proporsional untuk kertas A4 */
        }
        .main-table td.col-no {
            color: #000000 !important;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }

        /* Tanda Tangan - Lebar & Lega */
        .ttd-wrapper {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
            page-break-inside: avoid;
            break-inside: avoid;
            transform: translateY(var(--ttd-shift-y, 0mm));
            transition: transform 0.1s ease;
        }
        .ttd-box {
            width: 47%;
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
            position: relative;
        }
        .ttd-box .ttd-jabatan {
            font-weight: bold;
            display: inline-block;
            margin-top: 1px;
        }
        .ttd-space {
            height: 48px; /* Spasi tanda tangan lega & pas untuk A4 */
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8.8pt;
        }
        .ttd-nip {
            font-size: 7.8pt;
            margin-top: 1px;
        }

        /* Gambar Tanda Tangan Digital Presisi */
        .ttd-img {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            pointer-events: none;
            display: none;
            object-fit: contain;
        }
        .ttd-img-kapus {
            max-height: 52px;
            max-width: 130px;
        }
        .ttd-img-pengurus {
            max-height: 50px;
            max-width: 125px;
        }

        /* Catatan Kaki */
        .footer-note {
            margin-top: 3px;
            padding-top: 2px;
            border-top: 1px dashed #cbd5e1;
            font-size: 6.5pt;
            color: #475569;
            display: flex;
            justify-content: space-between;
        }

        /* ========================================================
           MODE HANYA CETAK TTD (SUSULAN DI KERTAS JADI)
           Tabel, Kop, dan info tetap memakan ruang (visibility: hidden)
           sehingga posisi TTD di bawah 100% PERSIS sama!
           ======================================================== */
        body.mode-ttd-only .kop-table,
        body.mode-ttd-only .doc-title,
        body.mode-ttd-only .info-table,
        body.mode-ttd-only .main-table,
        body.mode-ttd-only .footer-note {
            visibility: hidden !important;
        }

        /* Opsi sembunyikan teks nama/NIP jika di kertas fisik sudah tercetak teks namanya */
        body.mode-ttd-only.hide-text-ttd .ttd-box .ttd-prefix,
        body.mode-ttd-only.hide-text-ttd .ttd-box .doc-date-display,
        body.mode-ttd-only.hide-text-ttd .ttd-box .ttd-jabatan,
        body.mode-ttd-only.hide-text-ttd .ttd-box .ttd-nama,
        body.mode-ttd-only.hide-text-ttd .ttd-box .ttd-nip,
        body.mode-ttd-only.hide-text-ttd .ttd-box br {
            visibility: hidden !important;
        }

        /* Mode Print Presisi Tanpa Terpotong */
        @media print {
            body {
                background: white !important;
            }
            .toolbar, .sheet-control-banner, .banner-susulan, .modal-backdrop {
                display: none !important;
            }
            .preview-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            .sheet-wrapper {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                gap: 0 !important;
            }
            .page-sheet {
                width: 100% !important;
                max-width: 100% !important;
                height: 283mm !important; /* Presisi A4 (297mm - 14mm margin atas-bawah) */
                min-height: 283mm !important;
                max-height: 283mm !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: visible !important;
            }
            .sheet-wrapper:last-child .page-sheet,
            .page-sheet:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
@php
    $roomList = isset($rooms) ? $rooms : collect([$room]);
    $defaultDateInput = $docDate ?? '2026-01-02';
    
    // Format tanggal Indonesia default "02 Januari 2026"
    $carbonDate = \Carbon\Carbon::parse($defaultDateInput)->locale('id');
    $dayFormatted = sprintf('%02d', $carbonDate->day);
    $monthFormatted = $carbonDate->isoFormat('MMMM');
    $yearFormatted = $carbonDate->year;
    $initialFormattedDate = $dayFormatted . ' ' . $monthFormatted . ' ' . $yearFormatted;

    // Target baris tabel (default: 24 baris optimal untuk kertas A4 1 halaman)
    $defaultTargetRows = $targetRows ?? 24;
    $initialPrintMode = $printMode ?? request('mode', 'full');
@endphp
</head>
<body class="{{ $initialPrintMode === 'ttd_only' ? 'mode-ttd-only hide-text-ttd' : '' }}">

    <!-- Toolbar Atas Interaktif (Hanya tampil di layar) -->
    <div class="toolbar">
        <div class="toolbar-group">
            <a href="{{ route('rooms.lembar_pemeliharaan') }}" class="btn btn-back">
                &larr; Kembali
            </a>
            <span style="font-size: 13px; font-weight: bold; color: #38bdf8;">
                Kertas: A4 (21 x 29.7 cm) &bull; {{ $roomList->count() }} Ruangan
            </span>
        </div>

        <div class="toolbar-group">
            <!-- Mode Cetak: Dokumen Lengkap vs Hanya TTD Susulan -->
            <div style="display: flex; align-items: center; gap: 5px; background: rgba(30, 41, 59, 0.9); padding: 3px 8px; border-radius: 6px; border: 1px solid #475569;">
                <label for="printModeSelect" style="color: #facc15; font-weight: 700;">Mode Cetak:</label>
                <select id="printModeSelect" onchange="changePrintMode(this.value)" style="border-color: #f59e0b; color: #fef08a;" title="Pilih apakah ingin mencetak dokumen lengkap atau hanya susulan TTD pada kertas yang sudah ada">
                    <option value="full" {{ $initialPrintMode === 'full' ? 'selected' : '' }}>📄 Dokumen Lengkap</option>
                    <option value="ttd_only" {{ $initialPrintMode === 'ttd_only' ? 'selected' : '' }}>✍️ Hanya TTD (Susulan di Kertas Jadi)</option>
                </select>
            </div>

            <!-- Tombol Buka Panel TTD Digital & Stempel -->
            <button type="button" onclick="openTtdModal()" class="btn btn-emerald" title="Atur Gambar Tanda Tangan & Cap Stempel">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Atur TTD & Cap
            </button>

            <!-- Pengatur Tanggal Interaktif -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="datePicker">Tanggal:</label>
                <input type="date" id="datePicker" value="{{ $defaultDateInput }}" onchange="changeDocDate(this.value)" title="Ubah tanggal tanda tangan lembar pemeliharaan alat">
            </div>

            <!-- Target Ruangan yang Diatur Toolbar -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="targetRoomSelect">Target:</label>
                <select id="targetRoomSelect" onchange="onTargetRoomChange(this.value)" style="max-width: 170px;" title="Pilih apakah ingin mengubah semua ruangan atau ruangan spesifik">
                    <option value="all">🌐 Semua Ruangan</option>
                    @foreach($roomList as $idx => $r)
                        <option value="{{ $r->id }}">#{{ $idx + 1 }} {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pengatur Jumlah Baris (Dropdown & Input Manual, termasuk opsi 30 & 35 baris) -->
            <div style="display: flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.08); padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15);">
                <label for="presetRowSelect">Baris:</label>
                
                <!-- Pilihan Cepat Dropdown -->
                <select id="presetRowSelect" onchange="onPresetChange(this.value)" title="Pilih preset jumlah baris">
                    <option value="18" {{ $defaultTargetRows == 18 ? 'selected' : '' }}>18 Baris</option>
                    <option value="20" {{ $defaultTargetRows == 20 ? 'selected' : '' }}>20 Baris</option>
                    <option value="22" {{ $defaultTargetRows == 22 ? 'selected' : '' }}>22 Baris</option>
                    <option value="24" {{ $defaultTargetRows == 24 ? 'selected' : '' }}>24 Baris (A4)</option>
                    <option value="26" {{ $defaultTargetRows == 26 ? 'selected' : '' }}>26 Baris</option>
                    <option value="30" {{ $defaultTargetRows == 30 ? 'selected' : '' }}>30 Baris</option>
                    <option value="35" {{ $defaultTargetRows == 35 ? 'selected' : '' }}>35 Baris (Maksimal)</option>
                    <option value="custom">Kustom...</option>
                </select>

                <!-- Input Manual Angka -->
                <div style="display: inline-flex; align-items: center; gap: 3px;">
                    <input type="number" id="manualRowInput" min="1" max="50" value="{{ $defaultTargetRows }}" oninput="onManualRowInput(this.value)" style="width: 48px; text-align: center;" title="Ketik angka manual jumlah baris yang diinginkan">
                    <span style="font-size: 11px; color: #cbd5e1;">baris</span>
                </div>

                <!-- Tombol Terapkan ke Semua -->
                <button type="button" onclick="applyRowsToAllFromToolbar()" class="btn btn-tool-action" title="Terapkan jumlah baris ini ke semua ruangan sekaligus">
                    Set Semua
                </button>
            </div>

            <!-- Tombol Cetak -->
            <button id="btnPrintMain" onclick="window.print()" class="btn btn-print" style="{{ $initialPrintMode === 'ttd_only' ? 'background: #059669;' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span id="btnPrintText">{{ $initialPrintMode === 'ttd_only' ? 'Cetak TTD Saja' : 'Cetak Lembar A4' }}</span>
            </button>
        </div>
    </div>

    <div class="preview-wrapper">
        <!-- Banner Peringatan & Pengaturan Mode Susulan TTD (Hanya Tampil di Layar Saat Mode Susulan Aktif) -->
        <div id="banner-susulan-info" class="banner-susulan" style="display: {{ $initialPrintMode === 'ttd_only' ? 'block' : 'none' }};">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <span style="font-size: 20px; line-height: 1;">✍️</span>
                    <div>
                        <strong style="color: #fef08a; font-size: 13px;">MODE SUSULAN TTD DIAKTIFKAN:</strong>
                        <p style="margin: 3px 0 0; line-height: 1.4; color: #fde68a;">
                            Tabel, Kop, dan info ruangan <strong>disembunyikan transparan</strong> agar tata letak tetap presisi (tinggi & posisi sama persis). Masukkan kembali kertas A4 yang sudah tercetak tabelnya ke dalam printer, lalu klik <strong>Cetak TTD Saja</strong>.
                        </p>
                        <div style="display: flex; align-items: center; gap: 16px; margin-top: 8px; flex-wrap: wrap;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: bold; color: #ffffff;">
                                <input type="checkbox" id="chkHideTextTtd" onchange="toggleHideTtdText(this.checked)" {{ $initialPrintMode === 'ttd_only' ? 'checked' : '' }}>
                                Sembunyikan teks nama & NIP (Jika di kertas fisik sudah tercetak teks namanya)
                            </label>

                            <!-- Geser Halus Vertikal (Fine Tuning Printer) -->
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,0.3); padding: 2px 8px; border-radius: 4px;">
                                <span style="font-size: 11px; color: #cbd5e1;">Geser Posisi TTD:</span>
                                <button type="button" onclick="adjustTtdShift(-1)" style="padding: 2px 6px; background: #78350f; color: white; border: 1px solid #b45309; border-radius: 3px; cursor: pointer; font-weight: bold;">&minus;</button>
                                <span id="txtTtdShift" style="font-weight: bold; color: #fef08a; min-width: 38px; text-align: center;">0 mm</span>
                                <button type="button" onclick="adjustTtdShift(1)" style="padding: 2px 6px; background: #78350f; color: white; border: 1px solid #b45309; border-radius: 3px; cursor: pointer; font-weight: bold;">&plus;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="changePrintMode('full')" style="background: none; border: none; color: #fde68a; cursor: pointer; font-size: 18px; font-weight: bold;">&times;</button>
            </div>
        </div>

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
                                'nama_barang' => $asset->name . ($asset->merk ? ' (' . $asset->merk . ')' : ''),
                                'volume' => '1',
                                'satuan' => $asset->satuan ?? 'Unit',
                                'pihak_pemelihara' => $m->penyedia ?: 'Teknisi',
                                'keterangan' => ($m->jenis_pemeliharaan ? '[' . $m->jenis_pemeliharaan . '] ' : '') . ($m->catatan_hasil ?: $m->catatan ?: 'Pemeliharaan berkala'),
                                'paraf' => ''
                            ]);
                        }
                    }
                }
            }

            $existingCount = $maintenances->count();
            $blankRowsToRender = max(0, $defaultTargetRows - $existingCount);
        @endphp

        <!-- Kontainer Lembar Ruangan -->
        <div class="sheet-wrapper" id="wrapper-{{ $currentRoom->id }}">
            
            <!-- Banner Pengatur Baris Individual untuk Ruangan Ini (Hanya Muncul di Layar) -->
            <div class="sheet-control-banner">
                <div class="sheet-title-info">
                    <span class="sheet-num">Lembar #{{ $index + 1 }}</span>
                    <span class="sheet-name">{{ $currentRoom->name }}</span>
                    @if($existingCount > 0)
                        <span style="font-size: 11px; color: #94a3b8;">({{ $existingCount }} data tercatat)</span>
                    @endif
                </div>
                
                <div class="sheet-action-group">
                    <label>Baris Lembar Ini:</label>
                    <div class="stepper-box">
                        <button type="button" class="btn-step" onclick="stepSheetRow('{{ $currentRoom->id }}', -1)" title="Kurangi 1 baris">&minus;</button>
                        <input type="number" id="sheet-input-{{ $currentRoom->id }}" min="1" max="50" value="{{ $defaultTargetRows }}" class="sheet-row-input" oninput="applySingleSheetInput('{{ $currentRoom->id }}', this.value)" title="Ketik angka manual untuk lembar ini">
                        <button type="button" class="btn-step" onclick="stepSheetRow('{{ $currentRoom->id }}', 1)" title="Tambah 1 baris">&plus;</button>
                    </div>

                    <!-- Dropdown Cepat per Lembar -->
                    <select id="sheet-select-{{ $currentRoom->id }}" class="sheet-quick-select" onchange="applySingleSheetInput('{{ $currentRoom->id }}', this.value)" title="Pilih baris preset">
                        <option value="18" {{ $defaultTargetRows == 18 ? 'selected' : '' }}>18</option>
                        <option value="20" {{ $defaultTargetRows == 20 ? 'selected' : '' }}>20</option>
                        <option value="22" {{ $defaultTargetRows == 22 ? 'selected' : '' }}>22</option>
                        <option value="24" {{ $defaultTargetRows == 24 ? 'selected' : '' }}>24 (A4)</option>
                        <option value="26" {{ $defaultTargetRows == 26 ? 'selected' : '' }}>26</option>
                        <option value="30" {{ $defaultTargetRows == 30 ? 'selected' : '' }}>30</option>
                        <option value="35" {{ $defaultTargetRows == 35 ? 'selected' : '' }}>35</option>
                        <option value="custom">Kustom</option>
                    </select>

                    <span class="sheet-status-badge" id="sheet-badge-{{ $currentRoom->id }}">{{ $defaultTargetRows }} baris</span>
                </div>
            </div>

            <!-- Halaman Cetak Kertas A4 -->
            <div class="page-sheet" id="sheet-{{ $currentRoom->id }}" data-room-id="{{ $currentRoom->id }}">
                <div>
                    <!-- Kop Surat Resmi Puskesmas Mantup -->
                    <table class="kop-table">
                        <tr>
                            <td class="kop-logo">
                                <img src="{{ asset('img/logo-lamongan.png') }}" alt="Logo Lamongan" onerror="this.style.display='none'">
                            </td>
                            <td class="kop-text">
                                <div class="line-1">PEMERINTAH KABUPATEN LAMONGAN</div>
                                <div class="line-2">DINAS KESEHATAN</div>
                                <div class="line-3">PUSKESMAS MANTUP</div>
                                <div class="line-4">Jl. Raya Mantup No. 55 Mantup, Kab. Lamongan, Jawa Timur 62283</div>
                                <div class="line-4">Telp. (0322) 4670302 &bull; Email: puskesmasmantup98@gmail.com</div>
                            </td>
                            <td class="kop-logo">
                                <img src="{{ asset('img/logo-husada.png') }}" alt="Logo Husada" onerror="this.style.display='none'">
                            </td>
                        </tr>
                    </table>

                    <!-- Judul Dokumen -->
                    <div class="doc-title">
                        <h2>LEMBAR PEMELIHARAAN ALAT</h2>
                        <p>(Lembar kontrol fisik sarana, prasarana, dan alat yang ditempel di setiap ruangan)</p>
                    </div>

                    <!-- Info Ruangan -->
                    <table class="info-table">
                        <tr>
                            <td class="info-label">RUANGAN</td>
                            <td class="info-sep">:</td>
                            <td class="info-value" style="font-size: 9.5pt;">{{ strtoupper($currentRoom->name) }}</td>
                            <td class="info-label" style="width: 135px;">TAHUN / PERIODE</td>
                            <td class="info-sep">:</td>
                            <td class="info-value"><span class="doc-year-display">{{ $yearFormatted }}</span></td>
                        </tr>
                        <tr>
                            <td class="info-label">SATUAN KERJA</td>
                            <td class="info-sep">:</td>
                            <td>PUSKESMAS MANTUP</td>
                            <td class="info-label">PENANGGUNG JAWAB</td>
                            <td class="info-sep">:</td>
                            <td>{{ $currentRoom->penanggung_jawab ?: '..........................................' }}</td>
                        </tr>
                    </table>

                    <!-- Tabel Utama Pemeliharaan Alat (A4 Pas Tanpa Terpotong) -->
                    <table class="main-table" data-existing="{{ $existingCount }}">
                        <thead>
                            <tr>
                                <th style="width: 4%;">NO</th>
                                <th style="width: 11%;">TANGGAL</th>
                                <th style="width: 23%;">NAMA BARANG</th>
                                <th style="width: 8.5%; white-space: nowrap;">VOLUME</th>
                                <th style="width: 8%; white-space: nowrap;">SATUAN</th>
                                <th style="width: 18.5%;">NAMA PIHAK<br>PEMELIHARA</th>
                                <th style="width: 18.5%;">KETERANGAN</th>
                                <th style="width: 8.5%; white-space: nowrap;">PARAF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rowNo = 1; @endphp

                            {{-- Tampilkan data yang sudah tercatat bila ada --}}
                            @foreach($maintenances as $item)
                            <tr class="data-row">
                                <td class="text-center col-no">{{ $rowNo++ }}</td>
                                <td class="text-center">{{ $item->tanggal }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td class="text-center">{{ $item->volume }}</td>
                                <td class="text-center">{{ $item->satuan }}</td>
                                <td>{{ $item->pihak_pemelihara }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-center"></td>
                            </tr>
                            @endforeach

                            {{-- Baris kosong bergaris untuk pengisian manual tulisan tangan saat tertempel di ruangan --}}
                            @for($i = 0; $i < $blankRowsToRender; $i++)
                            <tr class="blank-row">
                                <td class="text-center col-no">{{ $rowNo++ }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
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
                    <!-- Kolom Tanda Tangan Luas & Lega (Dapat Dilengkapi Gambar TTD & Cap Digital) -->
                    <div class="ttd-wrapper">
                        <div class="ttd-box">
                            <span class="ttd-prefix">Mengetahui,</span><br>
                            <span class="ttd-jabatan">Kepala Puskesmas Mantup</span>
                            
                            <!-- Area TTD dr. Sunaryadi (Asli Tanpa Stempel) -->
                            <div class="ttd-space">
                                <img src="{{ asset('images/ttd_kapus_nostamp.png') }}" class="ttd-img ttd-img-kapus" alt="TTD dr. Sunaryadi">
                            </div>
                            
                            <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                            <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                        </div>

                        <div class="ttd-box">
                            <span class="ttd-prefix">Mantup, </span><span class="doc-date-display">{{ $initialFormattedDate }}</span><br>
                            <span class="ttd-jabatan">Pengurus Barang Pembantu</span>
                            
                            <!-- Area TTD Andika Dafa Penta P. (asd.png) -->
                            <div class="ttd-space">
                                <img src="{{ asset('images/ttd_andika.png') }}" class="ttd-img ttd-img-pengurus" alt="TTD Andika Dafa">
                            </div>
                            
                            <div class="ttd-nama">ANDIKA DAFA PENTA P.</div>
                            <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                        </div>
                    </div>

                    <div class="footer-note">
                        <span>* Lembar ini wajib diisi dan ditandatangani petugas setiap kali pemeliharaan alat dilakukan.</span>
                        <span>Ruangan: {{ $currentRoom->name }} | Puskesmas Mantup</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Pengaturan TTD & Cap Stempel Digital -->
    <div id="modalTtd" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: #1e293b; color: white; border-radius: 12px; width: 520px; max-width: 95vw; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid #334155;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 15px; font-weight: bold; color: #38bdf8; display: flex; align-items: center; gap: 8px;">
                    <span>✍️</span> Pengaturan Tanda Tangan & Cap Stempel
                </h3>
                <button type="button" onclick="closeTtdModal()" style="background: none; border: none; color: #94a3b8; font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <!-- TTD Kepala Puskesmas (dr. Muhamad Sunaryadi) -->
                <div style="background: #0f172a; padding: 12px; border-radius: 8px; border: 1px solid #334155;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #ffffff; font-size: 13px;">1. TTD dr. Muhamad Sunaryadi</strong>
                            <div style="font-size: 11px; color: #94a3b8;">Kepala Puskesmas Mantup (Asli Tanpa Stempel)</div>
                        </div>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: bold;">
                            <input type="checkbox" id="chkEnableKapusTtd" onchange="toggleKapusTtd(this.checked)" checked>
                            <span style="color: #38bdf8;">Tampilkan</span>
                        </label>
                    </div>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.05); padding: 8px; border-radius: 6px;">
                        <img id="preview-modal-kapus" src="{{ asset('images/ttd_kapus_nostamp.png') }}" style="max-height: 48px; max-width: 100px; background: white; padding: 2px; border-radius: 4px; object-fit: contain;" alt="TTD dr. Sunaryadi">
                        <div style="font-size: 11.5px; color: #cbd5e1; flex: 1;">
                            <div style="font-weight: bold; color: #f8fafc;">Tanda Tangan Tinta Biru dr. Sunaryadi</div>
                            <div style="display: flex; gap: 12px; margin-top: 5px;">
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 11px; color: #e2e8f0;">
                                    <input type="radio" name="kapus_type" value="nostamp" checked onchange="changeKapusTtdType(this.value)">
                                    Tanpa Stempel (Aktif)
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 11px; color: #94a3b8;">
                                    <input type="radio" name="kapus_type" value="stamp" onchange="changeKapusTtdType(this.value)">
                                    Dengan Cap Stempel
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TTD Pengurus Barang Pembantu (Andika Dafa Penta P.) -->
                <div style="background: #0f172a; padding: 12px; border-radius: 8px; border: 1px solid #334155;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #ffffff; font-size: 13px;">2. TTD Andika Dafa Penta P.</strong>
                            <div style="font-size: 11px; color: #94a3b8;">Pengurus Barang Pembantu (asd.png)</div>
                        </div>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: bold;">
                            <input type="checkbox" id="chkEnablePengurusTtd" onchange="togglePengurusTtd(this.checked)" checked>
                            <span style="color: #38bdf8;">Tampilkan</span>
                        </label>
                    </div>

                    <!-- Preview & Tombol Upload / Buat TTD -->
                    <div style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between; gap: 10px; background: rgba(255,255,255,0.05); padding: 8px; border-radius: 6px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div id="preview-ttd-pengurus-box" style="width: 100px; height: 48px; background: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #475569;">
                                <img id="modal-preview-pengurus" src="{{ asset('images/ttd_andika.png') }}" style="max-height: 45px; max-width: 95px; object-fit: contain;">
                                <span id="modal-preview-placeholder" style="font-size: 10px; color: #64748b; font-style: italic; display: none;">Belum ada TTD</span>
                            </div>
                            <span id="txt-status-ttd-pengurus" style="font-size: 11.5px; color: #cbd5e1;"><span style="color: #4ade80; font-weight: bold;">✓ TTD Andika Siap Digunakan</span></span>
                        </div>

                        <div style="display: flex; gap: 6px;">
                            <button type="button" onclick="document.getElementById('fileTtdPengurus').click()" class="btn" style="padding: 5px 10px; font-size: 11px; background: #3b82f6; color: white;">
                                📁 Ganti Foto
                            </button>
                            <button type="button" onclick="openPadModal()" class="btn" style="padding: 5px 10px; font-size: 11px; background: #8b5cf6; color: white;">
                                ✍️ Gambar Ulang
                            </button>
                            <button type="button" onclick="resetAndikaTtdToDefault()" class="btn" style="padding: 5px 10px; font-size: 11px; background: #334155; color: #cbd5e1;" title="Kembalikan ke TTD asli bawaan">
                                ↺ Reset Asli
                            </button>
                        </div>
                        <input type="file" id="fileTtdPengurus" accept="image/*" onchange="handlePengurusFileUpload(event)" style="display: none;">
                    </div>
                </div>
            </div>

            <div style="margin-top: 18px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeTtdModal()" class="btn btn-emerald" style="padding: 7px 18px;">Simpan & Terapkan</button>
            </div>
        </div>
    </div>

    <!-- Modal Gambar Tanda Tangan di Layar (Canvas Pad) -->
    <div id="modalPad" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 10001; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: #1e293b; color: white; border-radius: 12px; width: 440px; max-width: 95vw; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid #334155;">
            <h3 style="margin: 0 0 8px; font-size: 15px; font-weight: bold; color: #a78bfa;">
                ✍️ Gambar Tanda Tangan Anda (Andika Dafa)
            </h3>
            <p style="margin: 0 0 12px; font-size: 11.5px; color: #94a3b8;">
                Tarik kursor mouse atau goreskan jari pada layar sentuh untuk menandatangani:
            </p>
            
            <div style="background: white; border-radius: 8px; border: 2px dashed #64748b; overflow: hidden; display: flex; justify-content: center;">
                <canvas id="sigCanvas" width="400" height="160" style="touch-action: none; cursor: crosshair;"></canvas>
            </div>

            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <button type="button" onclick="clearSignatureCanvas()" class="btn" style="background: #475569; color: white; padding: 6px 12px; font-size: 12px;">Hapus Ulang</button>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="closePadModal()" class="btn" style="background: #334155; color: white; padding: 6px 12px; font-size: 12px;">Batal</button>
                    <button type="button" onclick="saveSignatureFromPad()" class="btn btn-emerald" style="padding: 6px 14px; font-size: 12px;">Gunakan TTD Ini</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pengatur Tanggal, Dropdown Cepat, Input Manual Baris & Mode Susulan TTD -->
    <script>
        const sheetRowCounts = {};
        const defaultRows = {{ $defaultTargetRows }};
        let currentTtdShift = 0; // dalam mm
        const defaultKapusNoStamp = "{{ asset('images/ttd_kapus_nostamp.png') }}";
        const defaultKapusStamp = "{{ asset('images/ttd_baru.png') }}";
        const defaultAndikaTtd = "{{ asset('images/ttd_andika.png') }}";

        // Inisialisasi awal target baris untuk tiap sheet
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.page-sheet').forEach(sheet => {
                const id = sheet.getAttribute('data-room-id');
                if (id) {
                    sheetRowCounts[id] = defaultRows;
                }
            });

            // 1. Tampilkan TTD dr. Sunaryadi (Asli Tanpa Stempel) secara default
            toggleKapusTtd(true);
            const chkKapus = document.getElementById('chkEnableKapusTtd');
            if (chkKapus) chkKapus.checked = true;

            // 2. Tampilkan TTD Andika Dafa (asd.png) secara default (atau dari localStorage jika ada kustomisasi)
            const savedTtdAndika = localStorage.getItem('e_aset_ttd_andika');
            if (savedTtdAndika) {
                applyPengurusSignatureSrc(savedTtdAndika);
            } else {
                applyPengurusSignatureSrc(defaultAndikaTtd);
            }
            togglePengurusTtd(true);
            const chkPengurus = document.getElementById('chkEnablePengurusTtd');
            if (chkPengurus) chkPengurus.checked = true;

            initSignatureCanvas();

            // Cek inisialisasi mode dari server / url query
            const urlParams = new URLSearchParams(window.location.search);
            const initMode = '{{ $initialPrintMode }}' || urlParams.get('mode') || 'full';
            if (initMode === 'ttd_only') {
                changePrintMode('ttd_only');
            }
        });

        function changeKapusTtdType(type) {
            const src = (type === 'stamp') ? defaultKapusStamp : defaultKapusNoStamp;
            document.querySelectorAll('.ttd-img-kapus').forEach(img => {
                img.src = src;
                if (type === 'stamp') {
                    img.style.maxHeight = '64px';
                    img.style.maxWidth = '135px';
                } else {
                    img.style.maxHeight = '52px';
                    img.style.maxWidth = '130px';
                }
            });
            const preview = document.getElementById('preview-modal-kapus');
            if (preview) preview.src = src;
        }

        function resetAndikaTtdToDefault() {
            localStorage.removeItem('e_aset_ttd_andika');
            applyPengurusSignatureSrc(defaultAndikaTtd);
        }

        // ==========================================
        // FITUR MODE CETAK & SUSULAN TTD DI KERTAS JADI
        // ==========================================
        function changePrintMode(mode) {
            const body = document.body;
            const banner = document.getElementById('banner-susulan-info');
            const btnPrintText = document.getElementById('btnPrintText');
            const btnPrintMain = document.getElementById('btnPrintMain');
            const modeSelect = document.getElementById('printModeSelect');
            const chkHide = document.getElementById('chkHideTextTtd');

            if (modeSelect) modeSelect.value = mode;

            if (mode === 'ttd_only') {
                body.classList.add('mode-ttd-only');
                if (banner) banner.style.display = 'block';
                if (btnPrintText) btnPrintText.textContent = 'Cetak TTD Saja';
                if (btnPrintMain) {
                    btnPrintMain.style.background = '#059669';
                    btnPrintMain.title = 'Mencetak hanya tanda tangan & cap ke atas kertas A4 yang sudah jadi';
                }
                if (chkHide) {
                    chkHide.checked = true;
                    toggleHideTtdText(true);
                }
                // Pastikan gambar TTD aktif
                toggleKapusTtd(true);
                const chkKapus = document.getElementById('chkEnableKapusTtd');
                if (chkKapus) chkKapus.checked = true;

                togglePengurusTtd(true);
                const chkPengurus = document.getElementById('chkEnablePengurusTtd');
                if (chkPengurus) chkPengurus.checked = true;
            } else {
                body.classList.remove('mode-ttd-only');
                body.classList.remove('hide-text-ttd');
                if (chkHide) chkHide.checked = false;
                if (banner) banner.style.display = 'none';
                if (btnPrintText) btnPrintText.textContent = 'Cetak Lembar A4';
                if (btnPrintMain) {
                    btnPrintMain.style.background = '#2563eb';
                    btnPrintMain.title = 'Mencetak dokumen lengkap (Kop, Tabel, dan TTD)';
                }
            }
        }

        function toggleHideTtdText(hide) {
            if (hide) {
                document.body.classList.add('hide-text-ttd');
            } else {
                document.body.classList.remove('hide-text-ttd');
            }
        }

        function adjustTtdShift(delta) {
            currentTtdShift += delta;
            document.documentElement.style.setProperty('--ttd-shift-y', currentTtdShift + 'mm');
            const txt = document.getElementById('txtTtdShift');
            if (txt) {
                txt.textContent = (currentTtdShift > 0 ? '+' : '') + currentTtdShift + ' mm';
            }
        }

        // ==========================================
        // MODAL TTD & UPLOAD / GAMBAR TTD
        // ==========================================
        function openTtdModal() {
            const modal = document.getElementById('modalTtd');
            if (modal) modal.style.display = 'flex';
        }

        function closeTtdModal() {
            const modal = document.getElementById('modalTtd');
            if (modal) modal.style.display = 'none';
        }

        function toggleKapusTtd(show) {
            document.querySelectorAll('.ttd-img-kapus').forEach(img => {
                img.style.display = show ? 'block' : 'none';
            });
        }

        function togglePengurusTtd(show) {
            document.querySelectorAll('.ttd-img-pengurus').forEach(img => {
                if (show && img.getAttribute('src')) {
                    img.style.display = 'block';
                } else {
                    img.style.display = 'none';
                }
            });
        }

        function handlePengurusFileUpload(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt) {
                savePengurusSignature(evt.target.result);
            };
            reader.readAsDataURL(file);
        }

        function savePengurusSignature(dataUrl) {
            try {
                localStorage.setItem('e_aset_ttd_andika', dataUrl);
            } catch (err) {
                console.warn('Storage quota exceeded', err);
            }
            applyPengurusSignatureSrc(dataUrl);
            const chk = document.getElementById('chkEnablePengurusTtd');
            if (chk) chk.checked = true;
            togglePengurusTtd(true);
        }

        function applyPengurusSignatureSrc(src) {
            document.querySelectorAll('.ttd-img-pengurus').forEach(img => {
                img.src = src;
            });
            const preview = document.getElementById('modal-preview-pengurus');
            const placeholder = document.getElementById('modal-preview-placeholder');
            const statusText = document.getElementById('txt-status-ttd-pengurus');
            if (preview && placeholder) {
                if (src) {
                    preview.src = src;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    if (statusText) statusText.innerHTML = '<span style="color: #4ade80; font-weight: bold;">✓ TTD Anda Siap Digunakan</span>';
                } else {
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                    if (statusText) statusText.textContent = 'Upload foto TTD atau buat langsung.';
                }
            }
        }

        // ==========================================
        // CANVAS SIGNATURE PAD (GAMBAR TTD DI LAYAR)
        // ==========================================
        let sigCanvas, sigCtx, isDrawing = false;

        function openPadModal() {
            const modal = document.getElementById('modalPad');
            if (modal) modal.style.display = 'flex';
            setTimeout(() => {
                clearSignatureCanvas();
            }, 50);
        }

        function closePadModal() {
            const modal = document.getElementById('modalPad');
            if (modal) modal.style.display = 'none';
        }

        function initSignatureCanvas() {
            sigCanvas = document.getElementById('sigCanvas');
            if (!sigCanvas) return;
            sigCtx = sigCanvas.getContext('2d');
            sigCtx.lineWidth = 2.8;
            sigCtx.lineCap = 'round';
            sigCtx.lineJoin = 'round';
            sigCtx.strokeStyle = '#000080'; // Biru tinta pena dinas

            function getPos(e) {
                const rect = sigCanvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * (sigCanvas.width / rect.width),
                    y: (clientY - rect.top) * (sigCanvas.height / rect.height)
                };
            }

            function start(e) {
                isDrawing = true;
                const pos = getPos(e);
                sigCtx.beginPath();
                sigCtx.moveTo(pos.x, pos.y);
                e.preventDefault();
            }

            function move(e) {
                if (!isDrawing) return;
                const pos = getPos(e);
                sigCtx.lineTo(pos.x, pos.y);
                sigCtx.stroke();
                e.preventDefault();
            }

            function end() {
                if (isDrawing) {
                    isDrawing = false;
                    sigCtx.closePath();
                }
            }

            sigCanvas.addEventListener('mousedown', start);
            sigCanvas.addEventListener('mousemove', move);
            window.addEventListener('mouseup', end);

            sigCanvas.addEventListener('touchstart', start, { passive: false });
            sigCanvas.addEventListener('touchmove', move, { passive: false });
            window.addEventListener('touchend', end);
        }

        function clearSignatureCanvas() {
            if (!sigCanvas || !sigCtx) return;
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
        }

        function saveSignatureFromPad() {
            if (!sigCanvas || !sigCtx) return;
            const pixelBuffer = new Uint32Array(
                sigCtx.getImageData(0, 0, sigCanvas.width, sigCanvas.height).data.buffer
            );
            const hasDrawn = pixelBuffer.some(color => color !== 0);
            if (!hasDrawn) {
                alert('Silakan goreskan tanda tangan Anda terlebih dahulu pada area kanvas.');
                return;
            }

            const dataUrl = sigCanvas.toDataURL('image/png');
            savePengurusSignature(dataUrl);
            closePadModal();
        }

        // ==========================================
        // FORMAT TANGGAL & ROW MANAGEMENT
        // ==========================================
        function formatIndonesianDate(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const year = parts[0];
            const monthIdx = parseInt(parts[1], 10) - 1;
            const day = String(parts[2]).padStart(2, '0');
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const monthName = months[monthIdx] || parts[1];
            return `${day} ${monthName} ${year}`;
        }

        function changeDocDate(val) {
            if (!val) return;
            const formatted = formatIndonesianDate(val);
            const year = val.split('-')[0];

            document.querySelectorAll('.doc-date-display').forEach(el => {
                el.textContent = formatted;
            });
            document.querySelectorAll('.doc-year-display').forEach(el => {
                el.textContent = year;
            });
        }

        // Fungsi Inti: Menerapkan Jumlah Baris ke Elemen Sheet Spesifik
        function applyRowsToSheet(sheetElement, count) {
            const table = sheetElement.querySelector('.main-table');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            const dataRows = tbody.querySelectorAll('tr.data-row');
            const existingDataCount = dataRows.length;
            const targetCount = Math.max(existingDataCount, Math.max(1, parseInt(count, 10) || defaultRows));

            const roomId = sheetElement.getAttribute('data-room-id');
            if (roomId) {
                sheetRowCounts[roomId] = targetCount;

                // Sinkronkan input pada banner per-sheet
                const sheetInput = document.getElementById('sheet-input-' + roomId);
                if (sheetInput && parseInt(sheetInput.value, 10) !== targetCount) {
                    sheetInput.value = targetCount;
                }
                const sheetSelect = document.getElementById('sheet-select-' + roomId);
                if (sheetSelect) {
                    sheetSelect.value = ['18', '20', '22', '24', '26', '30', '35'].includes(String(targetCount)) ? String(targetCount) : 'custom';
                }
                const badge = document.getElementById('sheet-badge-' + roomId);
                if (badge) {
                    badge.textContent = targetCount + ' baris';
                }
            }

            // Hapus baris kosong lama
            tbody.querySelectorAll('tr.blank-row').forEach(row => row.remove());

            // Tambahkan baris kosong baru sesuai target 8 kolom
            const neededBlank = Math.max(0, targetCount - existingDataCount);
            const fragment = document.createDocumentFragment();
            for (let i = 0; i < neededBlank; i++) {
                const rowNumber = existingDataCount + i + 1;
                const tr = document.createElement('tr');
                tr.className = 'blank-row';
                tr.innerHTML = `
                    <td class="text-center col-no">${rowNumber}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                `;
                fragment.appendChild(tr);
            }
            tbody.appendChild(fragment);
        }

        // --- PENGATUR DI TOOLBAR ATAS ---

        // Toolbar: Saat dropdown preset diubah
        function onPresetChange(val) {
            if (val === 'custom') {
                const input = document.getElementById('manualRowInput');
                input.focus();
                input.select();
                return;
            }
            const count = parseInt(val, 10);
            if (!count) return;
            document.getElementById('manualRowInput').value = count;
            applyToolbarRowCount(count);
        }

        // Toolbar: Saat input angka manual diketik langsung
        function onManualRowInput(val) {
            const count = parseInt(val, 10);
            if (!count || count < 1) return;

            // Sinkronkan dropdown preset jika nilainya pas
            const presetSel = document.getElementById('presetRowSelect');
            if (['18', '20', '22', '24', '26', '30', '35'].includes(String(count))) {
                presetSel.value = String(count);
            } else {
                presetSel.value = 'custom';
            }

            applyToolbarRowCount(count);
        }

        // Toolbar: Menerapkan jumlah baris ke Target (Semua Ruangan atau Ruangan Tertentu)
        function applyToolbarRowCount(count) {
            const target = document.getElementById('targetRoomSelect').value;
            if (target === 'all') {
                document.querySelectorAll('.page-sheet').forEach(sheet => {
                    applyRowsToSheet(sheet, count);
                });
            } else {
                const targetSheet = document.getElementById('sheet-' + target);
                if (targetSheet) {
                    applyRowsToSheet(targetSheet, count);
                }
            }
        }

        // Toolbar: Tombol "Set Semua"
        function applyRowsToAllFromToolbar() {
            const val = document.getElementById('manualRowInput').value;
            const count = parseInt(val, 10) || defaultRows;
            document.querySelectorAll('.page-sheet').forEach(sheet => {
                applyRowsToSheet(sheet, count);
            });
            document.getElementById('targetRoomSelect').value = 'all';
        }

        // Toolbar: Saat mengganti target lembar ruangan pada dropdown toolbar
        function onTargetRoomChange(roomId) {
            if (roomId === 'all') return;

            const currentCount = sheetRowCounts[roomId] || defaultRows;
            document.getElementById('manualRowInput').value = currentCount;
            const presetSel = document.getElementById('presetRowSelect');
            if (['18', '20', '22', '24', '26', '30', '35'].includes(String(currentCount))) {
                presetSel.value = String(currentCount);
            } else {
                presetSel.value = 'custom';
            }

            const targetWrapper = document.getElementById('wrapper-' + roomId);
            if (targetWrapper) {
                targetWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // --- PENGATUR BANNER PER-LEMBAR / PER-RUANGAN ---

        function applySingleSheetInput(roomId, val) {
            if (val === 'custom') {
                const input = document.getElementById('sheet-input-' + roomId);
                if (input) {
                    input.focus();
                    input.select();
                }
                return;
            }
            const count = parseInt(val, 10);
            if (!count || count < 1) return;

            const sheet = document.getElementById('sheet-' + roomId);
            if (sheet) {
                applyRowsToSheet(sheet, count);
            }

            const currentToolbarTarget = document.getElementById('targetRoomSelect').value;
            if (currentToolbarTarget === roomId) {
                document.getElementById('manualRowInput').value = count;
                const presetSel = document.getElementById('presetRowSelect');
                if (['18', '20', '22', '24', '26', '30', '35'].includes(String(count))) {
                    presetSel.value = String(count);
                } else {
                    presetSel.value = 'custom';
                }
            }
        }

        function stepSheetRow(roomId, delta) {
            const current = sheetRowCounts[roomId] || defaultRows;
            const nextCount = Math.max(1, Math.min(50, current + delta));
            applySingleSheetInput(roomId, nextCount);
        }
    </script>
</body>
</html>
