<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pemeliharaan Ruangan - A4 (21cm x 29.7cm)</title>
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

        .preview-wrapper {
            margin-top: 65px;
            padding: 20px 10px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 28px;
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

        /* Tabel Utama Pemeliharaan Ruangan */
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
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .main-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            height: 25px;
            vertical-align: middle;
            line-height: 1.15;
        }
        .main-table td {
            height: 20px;
            vertical-align: middle;
        }
        .main-table tr.blank-row td {
            height: 20px; /* Tinggi baris pas & proporsional untuk kertas A4 */
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
        }
        .ttd-box {
            width: 47%;
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
        }
        .ttd-box .ttd-jabatan {
            font-weight: bold;
            display: inline-block;
            margin-top: 1px;
        }
        .ttd-space {
            height: 48px; /* Spasi tanda tangan lega & pas untuk A4 */
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

        /* Mode Print Presisi Tanpa Terpotong */
        @media print {
            body {
                background: white !important;
            }
            .toolbar, .sheet-control-banner {
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
</head>
<body>

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
    @endphp

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
            <!-- Pengatur Tanggal Interaktif -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="datePicker">Tanggal Lembar:</label>
                <input type="date" id="datePicker" value="{{ $defaultDateInput }}" onchange="changeDocDate(this.value)" title="Ubah tanggal tanda tangan lembar pemeliharaan">
            </div>

            <!-- Target Ruangan yang Diatur Toolbar -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="targetRoomSelect">Target Lembar:</label>
                <select id="targetRoomSelect" onchange="onTargetRoomChange(this.value)" style="max-width: 190px;" title="Pilih apakah ingin mengubah semua ruangan atau ruangan spesifik">
                    <option value="all">🌐 Semua Ruangan ({{ $roomList->count() }})</option>
                    @foreach($roomList as $idx => $r)
                        <option value="{{ $r->id }}">#{{ $idx + 1 }} {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pengatur Jumlah Baris (Dropdown & Input Manual) -->
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.08); padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15);">
                <label for="presetRowSelect">Baris:</label>
                
                <!-- Pilihan Cepat Dropdown -->
                <select id="presetRowSelect" onchange="onPresetChange(this.value)" title="Pilih preset jumlah baris">
                    <option value="18" {{ $defaultTargetRows == 18 ? 'selected' : '' }}>18 Baris</option>
                    <option value="20" {{ $defaultTargetRows == 20 ? 'selected' : '' }}>20 Baris</option>
                    <option value="22" {{ $defaultTargetRows == 22 ? 'selected' : '' }}>22 Baris</option>
                    <option value="24" {{ $defaultTargetRows == 24 ? 'selected' : '' }}>24 Baris (A4)</option>
                    <option value="26" {{ $defaultTargetRows == 26 ? 'selected' : '' }}>26 Baris</option>
                    <option value="custom">Kustom...</option>
                </select>

                <!-- Input Manual Angka -->
                <div style="display: inline-flex; align-items: center; gap: 4px;">
                    <input type="number" id="manualRowInput" min="1" max="45" value="{{ $defaultTargetRows }}" oninput="onManualRowInput(this.value)" style="width: 52px; text-align: center;" title="Ketik angka manual jumlah baris yang diinginkan">
                    <span style="font-size: 11px; color: #cbd5e1;">baris</span>
                </div>

                <!-- Tombol Terapkan ke Semua -->
                <button type="button" onclick="applyRowsToAllFromToolbar()" class="btn btn-tool-action" title="Terapkan jumlah baris ini ke semua ruangan sekaligus">
                    Set Semua
                </button>
            </div>

            <!-- Tombol Cetak -->
            <button onclick="window.print()" class="btn btn-print">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar A4
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
                        <input type="number" id="sheet-input-{{ $currentRoom->id }}" min="1" max="45" value="{{ $defaultTargetRows }}" class="sheet-row-input" oninput="applySingleSheetInput('{{ $currentRoom->id }}', this.value)" title="Ketik angka manual untuk lembar ini">
                        <button type="button" class="btn-step" onclick="stepSheetRow('{{ $currentRoom->id }}', 1)" title="Tambah 1 baris">&plus;</button>
                    </div>

                    <!-- Dropdown Cepat per Lembar -->
                    <select id="sheet-select-{{ $currentRoom->id }}" class="sheet-quick-select" onchange="applySingleSheetInput('{{ $currentRoom->id }}', this.value)" title="Pilih baris preset">
                        <option value="18" {{ $defaultTargetRows == 18 ? 'selected' : '' }}>18</option>
                        <option value="20" {{ $defaultTargetRows == 20 ? 'selected' : '' }}>20</option>
                        <option value="22" {{ $defaultTargetRows == 22 ? 'selected' : '' }}>22</option>
                        <option value="24" {{ $defaultTargetRows == 24 ? 'selected' : '' }}>24 (A4)</option>
                        <option value="26" {{ $defaultTargetRows == 26 ? 'selected' : '' }}>26</option>
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
                        <h2>LEMBAR PEMELIHARAAN RUANGAN</h2>
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

                    <!-- Tabel Utama Pemeliharaan Ruangan (A4 Pas Tanpa Terpotong) -->
                    <table class="main-table" data-existing="{{ $existingCount }}">
                        <thead>
                            <tr>
                                <th style="width: 4%;">NO</th>
                                <th style="width: 11%;">TANGGAL</th>
                                <th style="width: 24%;">NAMA BARANG</th>
                                <th style="width: 7%;">VOLUME</th>
                                <th style="width: 7%;">SATUAN</th>
                                <th style="width: 19%;">NAMA PIHAK PEMELIHARA</th>
                                <th style="width: 19%;">KETERANGAN</th>
                                <th style="width: 9%;">PARAF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rowNo = 1; @endphp

                            {{-- Tampilkan data yang sudah tercatat bila ada --}}
                            @foreach($maintenances as $item)
                            <tr class="data-row">
                                <td class="text-center">{{ $rowNo++ }}</td>
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
                                <td class="text-center" style="color: #64748b;">{{ $rowNo++ }}</td>
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
                    <!-- Kolom Tanda Tangan Luas & Lega -->
                    <div class="ttd-wrapper">
                        <div class="ttd-box">
                            Mengetahui,<br>
                            <span class="ttd-jabatan">Kepala Puskesmas Mantup</span>
                            <div class="ttd-space"></div>
                            <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                            <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                        </div>
                        <div class="ttd-box">
                            Mantup, <span class="doc-date-display">{{ $initialFormattedDate }}</span><br>
                            <span class="ttd-jabatan">Pengurus Barang Pembantu</span>
                            <div class="ttd-space"></div>
                            <div class="ttd-nama">ANDIKA DAFA PENTA P.</div>
                            <div class="ttd-nip">NIP. 19990728 202504 1 003</div>
                        </div>
                    </div>

                    <div class="footer-note">
                        <span>* Lembar ini wajib diisi dan ditandatangani petugas setiap kali pemeliharaan dilakukan.</span>
                        <span>Ruangan: {{ $currentRoom->name }} | Puskesmas Mantup</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Script Pengatur Tanggal, Dropdown Cepat & Input Manual Baris Dinamis -->
    <script>
        const sheetRowCounts = {};
        const defaultRows = {{ $defaultTargetRows }};

        // Inisialisasi awal target baris untuk tiap sheet
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.page-sheet').forEach(sheet => {
                const id = sheet.getAttribute('data-room-id');
                if (id) {
                    sheetRowCounts[id] = defaultRows;
                }
            });
        });

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
                    sheetSelect.value = ['18', '20', '22', '24', '26'].includes(String(targetCount)) ? String(targetCount) : 'custom';
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
                    <td class="text-center" style="color: #64748b;">${rowNumber}</td>
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
            if (['18', '20', '22', '24', '26'].includes(String(count))) {
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

            // Baca nilai baris terkini ruangan tersebut
            const currentCount = sheetRowCounts[roomId] || defaultRows;
            document.getElementById('manualRowInput').value = currentCount;
            const presetSel = document.getElementById('presetRowSelect');
            if (['18', '20', '22', '24', '26'].includes(String(currentCount))) {
                presetSel.value = String(currentCount);
            } else {
                presetSel.value = 'custom';
            }

            // Scroll otomatis menuju lembar ruangan yang dipilih
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

            // Jika toolbar saat ini sedang mengarah ke ruangan ini, sinkronkan nilai di toolbar
            const currentToolbarTarget = document.getElementById('targetRoomSelect').value;
            if (currentToolbarTarget === roomId) {
                document.getElementById('manualRowInput').value = count;
                const presetSel = document.getElementById('presetRowSelect');
                if (['18', '20', '22', '24', '26'].includes(String(count))) {
                    presetSel.value = String(count);
                } else {
                    presetSel.value = 'custom';
                }
            }
        }

        function stepSheetRow(roomId, delta) {
            const current = sheetRowCounts[roomId] || defaultRows;
            const nextCount = Math.max(1, Math.min(45, current + delta));
            applySingleSheetInput(roomId, nextCount);
        }
    </script>
</body>
</html>
