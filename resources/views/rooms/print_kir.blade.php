<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIR - {{ $room->name ?? 'Kartu Inventaris Ruangan' }} (F4)</title>
    <style>
        /* Pengaturan Kertas F4 (216mm x 330mm) */
        @page {
            size: 216mm 330mm;
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.25;
            color: #000;
            background-color: #f1f5f9;
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
            padding: 10px 18px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 13px;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12.5px;
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
            box-shadow: 0 2px 4px rgba(37,99,235,0.3);
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
            padding: 4px 8px;
            font-size: 11.5px;
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
        }

        .select-room, .input-date {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 12.5px;
            outline: none;
        }
        .select-room:focus, .input-date:focus {
            border-color: #38bdf8;
        }

        /* Lembar Dokumen Fisik F4 */
        .sheet-wrapper {
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-sheet {
            background: #ffffff;
            width: 216mm;
            min-height: 330mm;
            padding: 15mm 15mm 15mm 15mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            position: relative;
            margin-bottom: 30px;
        }

        /* Header Dokumen KIR */
        .kir-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            margin-bottom: 22px;
            text-transform: uppercase;
        }

        table.header-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }

        table.header-meta td {
            vertical-align: top;
            padding: 1.5px 0;
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
            margin-bottom: 24px;
            font-size: 9.5pt;
        }

        table.kir-table th, table.kir-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        table.kir-table th {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            background-color: #fff;
        }

        table.kir-table tr.col-numbers th {
            font-weight: normal;
            font-size: 8pt;
            padding: 2px;
        }

        table.kir-table td.text-center { text-align: center; }
        table.kir-table td.text-left { text-align: left; }
        table.kir-table td.text-right { text-align: right; }

        /* Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            display: flex;
            justify-content: space-between;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            font-size: 10.5pt;
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
            margin-bottom: 6px;
        }

        .ttd-image-area {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }

        .ttd-image-area img {
            max-height: 68px;
            max-width: 175px;
            object-fit: contain;
        }

        .ttd-nama {
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 4px;
        }

        .ttd-nip {
            margin-top: 2px;
            letter-spacing: 0.5px;
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

        /* Modal Import */
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

        /* Media Print Khusus F4 */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 10pt;
            }
            .toolbar, .modal-overlay, .alert-box {
                display: none !important;
            }
            .sheet-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always;
            }
            .page-sheet:last-child {
                page-break-after: auto;
            }
            .doc-date-display {
                border-bottom: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

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
                &larr; Kembali ke Ruangan
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

        <div class="toolbar-group">
            <!-- Menu Pengubah Tanggal Cetak (KIR diupdate tiap 6 bulan sekali) -->
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.85); padding: 3px 8px; border-radius: 6px; border: 1px solid #334155;">
                <span class="toolbar-label" style="color: #38bdf8; font-weight: 600;">📅 Tanggal Cetak:</span>
                <input type="date" id="datePicker" class="input-date" value="{{ $docDate }}" onchange="changeDocDate(this.value)" title="Ubah tanggal tanda tangan KIR (Wajib diupdate tiap 6 bulan sekali)">
                
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
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Tambah Barang
            </button>

            <!-- Tombol Download Template Excel -->
            <a href="{{ route('rooms.kir.template', ['room_id' => $room->id]) }}" class="btn btn-download" title="Download Format Excel KIR Ruangan Ini untuk Diedit / Diisi">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Excel
            </a>

            <!-- Tombol Upload / Import Excel -->
            <button type="button" onclick="openImportModal()" class="btn btn-upload" title="Unggah file Excel KIR yang telah diisi untuk langsung generate kartu inventaris">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload Excel
            </button>

            <!-- Tombol Cetak Dokumen F4 -->
            <button onclick="window.print()" class="btn btn-print" title="Cetak Dokumen KIR pada Kertas F4 (216x330 mm)">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak KIR (F4)
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
            @endphp

            <div class="page-sheet">
                <!-- Judul Utama Dokumen -->
                <div class="kir-title">KARTU INVENTARIS RUANGAN</div>

                <!-- Metadata Satuan Kerja & Ruangan Sesuai Standar Resmi -->
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

                <!-- Tabel Data KIR -->
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
                            <td colspan="9" class="text-center" style="padding: 28px 15px;">
                                <div style="color: #64748b; font-size: 11pt; margin-bottom: 12px; font-style: italic;">
                                    Belum ada data barang di ruangan {{ $currentRoom->name }}.
                                </div>
                                <div class="no-print" style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                    <button type="button" onclick="openAddItemModal()" class="btn" style="background: #4f46e5; color: #fff; font-size: 12px;">
                                        ➕ Tambah Barang Manual
                                    </button>
                                    <button type="button" onclick="openImportModal()" class="btn" style="background: #7c3aed; color: #fff; font-size: 12px;">
                                        📤 Upload File Excel
                                    </button>
                                    <a href="{{ route('rooms.kir.template', ['room_id' => $currentRoom->id]) }}" class="btn" style="background: #059669; color: #fff; font-size: 12px;">
                                        📥 Download Template Excel
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Kolom Tanda Tangan Sesuai Posisi & Format Resmi -->
                <div class="ttd-wrapper clearfix">
                    <!-- Tanda Tangan Kiri: dr. Muhamad Sunaryadi (Tanpa Stempel) -->
                    <div class="ttd-box ttd-box-left">
                        <div class="ttd-title">Mengetahui</div>
                        <div class="ttd-jabatan">Kepala Puskesmas Mantup</div>
                        <div class="ttd-image-area">
                            <img src="{{ asset('images/ttd_kapus_nostamp.png') }}?v={{ file_exists(public_path('images/ttd_kapus_nostamp.png')) ? filemtime(public_path('images/ttd_kapus_nostamp.png')) : time() }}" alt="TTD dr. Sunaryadi">
                        </div>
                        <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                        <div class="ttd-nip">NIP. 19690313 200212 1 007</div>
                    </div>

                    <!-- Tanda Tangan Kanan: Andika Dafa Penta Pratama (Tanggal Cetak Diupdate Tiap 6 Bulan) -->
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
            </div>
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
                        <input type="text" name="keterangan" placeholder="Opsional (cth: Pengadaan 2026, Hibah, dll.)" style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
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

    <!-- Script Interaktif untuk Tanggal, Ruangan & Modal -->
    <script>
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
            window.location.href = `/rooms/${roomId}/kir?date=${currentDate}`;
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

        // Periksa apakah ada tanggal tersimpan sebelumnya di localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const urlDate = urlParams.get('date');
            if (!urlDate) {
                const savedDate = localStorage.getItem('e_aset_kir_date');
                if (savedDate && savedDate !== '{{ $docDate }}') {
                    document.getElementById('datePicker').value = savedDate;
                    changeDocDate(savedDate);
                }
            }
        });
    </script>
</body>
</html>
