<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pemeliharaan Ruangan - F4 (21cm x 33cm)</title>
    <style>
        @page {
            size: 210mm 330mm; /* Kertas F4: 21cm x 33cm */
            margin: 7mm 12mm 7mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
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
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            flex-wrap: wrap;
            gap: 12px;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar label {
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
        }

        .toolbar input[type="date"],
        .toolbar select {
            background: #1e293b;
            color: #ffffff;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: bold;
            outline: none;
        }

        .toolbar input[type="date"]:focus,
        .toolbar select:focus {
            border-color: #3b82f6;
        }

        .toolbar .btn {
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 700;
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

        .preview-wrapper {
            margin-top: 65px;
            padding: 20px 10px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        /* Lembar Kertas F4 (21cm x 33cm) */
        .page-sheet {
            background: white;
            width: 210mm;
            min-height: 330mm;
            height: 330mm;
            padding: 8mm 12mm 6mm 12mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Kop Surat Resmi Puskesmas Mantup (Bukan UPT) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #000;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .kop-logo {
            width: 58px;
            text-align: center;
        }
        .kop-logo img {
            max-width: 52px;
            max-height: 52px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            line-height: 1.15;
            padding: 0 6px;
        }
        .kop-text .line-1 {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kop-text .line-2 {
            font-size: 9.5pt;
            font-weight: bold;
        }
        .kop-text .line-3 {
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 1px 0;
        }
        .kop-text .line-4 {
            font-size: 7.5pt;
            color: #111;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 3px 0 5px;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 1px 0 0;
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
        }
        .info-table td {
            padding: 1px 3px;
            vertical-align: top;
            border: none;
        }
        .info-label {
            width: 150px;
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
            font-size: 8pt;
            margin-bottom: 4px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 2.5px 5px;
        }
        .main-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            height: 24px;
            vertical-align: middle;
        }
        .main-table td {
            height: 22.5px;
            vertical-align: middle;
        }
        .main-table tr.blank-row td {
            height: 23px; /* Tinggi baris proporsional tulisan tangan */
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
            height: 65px; /* Spasi tanda tangan diperlebar */
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9pt;
        }
        .ttd-nip {
            font-size: 8pt;
            margin-top: 1px;
        }

        /* Catatan Kaki */
        .footer-note {
            margin-top: 4px;
            padding-top: 2px;
            border-top: 1px dashed #cbd5e1;
            font-size: 6.8pt;
            color: #475569;
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
                width: 210mm !important;
                height: 316mm !important;
                min-height: 316mm !important;
                max-height: 316mm !important;
                padding: 0 !important;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page;
                overflow: hidden;
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
        $defaultDateInput = $docDate ?? '2026-01-02';
        
        // Format tanggal Indonesia default "02 Januari 2026"
        $carbonDate = \Carbon\Carbon::parse($defaultDateInput)->locale('id');
        $dayFormatted = sprintf('%02d', $carbonDate->day);
        $monthFormatted = $carbonDate->isoFormat('MMMM');
        $yearFormatted = $carbonDate->year;
        $initialFormattedDate = $dayFormatted . ' ' . $monthFormatted . ' ' . $yearFormatted;

        // Target baris tabel (default: 26 baris untuk mem-press tabel hingga menyisakan area TTD)
        $defaultTargetRows = $targetRows ?? 26;
    @endphp

    <!-- Toolbar Atas Interaktif (Hanya tampil di layar) -->
    <div class="toolbar">
        <div class="toolbar-group">
            <a href="{{ route('rooms.lembar_pemeliharaan') }}" class="btn btn-back">
                &larr; Kembali
            </a>
            <span style="font-size: 13px; font-weight: bold; color: #38bdf8;">
                Kertas: F4 (21 x 33 cm) &bull; {{ $roomList->count() }} Ruangan
            </span>
        </div>

        <div class="toolbar-group">
            <!-- Pengatur Tanggal Interaktif -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="datePicker">Tanggal Lembar:</label>
                <input type="date" id="datePicker" value="{{ $defaultDateInput }}" onchange="changeDocDate(this.value)" title="Ubah tanggal tanda tangan lembar pemeliharaan">
            </div>

            <!-- Pengatur Jumlah Baris (Press Tabel) -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <label for="rowSelector">Jumlah Baris:</label>
                <select id="rowSelector" onchange="changeRowCount(this.value)">
                    <option value="24" {{ $defaultTargetRows == 24 ? 'selected' : '' }}>24 Baris</option>
                    <option value="25" {{ $defaultTargetRows == 25 ? 'selected' : '' }}>25 Baris</option>
                    <option value="26" {{ $defaultTargetRows == 26 ? 'selected' : '' }}>26 Baris (Rekomendasi Presisi)</option>
                    <option value="27" {{ $defaultTargetRows == 27 ? 'selected' : '' }}>27 Baris (Padat Maksimal)</option>
                    <option value="28" {{ $defaultTargetRows == 28 ? 'selected' : '' }}>28 Baris</option>
                </select>
            </div>

            <!-- Tombol Cetak -->
            <button onclick="window.print()" class="btn btn-print">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar F4
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

            $existingCount = $maintenances->count();
            $blankRowsToRender = max(0, $defaultTargetRows - $existingCount);
        @endphp

        <div class="page-sheet" id="sheet-{{ $currentRoom->id }}">
            <div>
                <!-- Kop Surat Resmi Puskesmas Mantup (Bukan UPT) -->
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
                        <td class="info-value">{{ strtoupper($currentRoom->name) }}</td>
                        <td class="info-label" style="width: 140px;">TAHUN / PERIODE</td>
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

                <!-- Tabel Utama Pemeliharaan Ruangan (Dipress mengisi halaman) -->
                <table class="main-table" data-existing="{{ $existingCount }}">
                    <thead>
                        <tr>
                            <th style="width: 4%;">NO</th>
                            <th style="width: 12%;">TANGGAL</th>
                            <th style="width: 33%;">NAMA ALAT / BAGIAN YANG DILAKUKAN PEMELIHARAAN</th>
                            <th style="width: 37%;">KETERANGAN / TINDAKAN PEMELIHARAAN</th>
                            <th style="width: 14%;">TANDA TANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNo = 1; @endphp

                        {{-- Tampilkan data yang sudah tercatat bila ada --}}
                        @foreach($maintenances as $item)
                        <tr class="data-row">
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
        @endforeach
    </div>

    <!-- Script Pengatur Tanggal & Jumlah Baris Dinamis -->
    <script>
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

        function changeRowCount(targetCount) {
            const count = parseInt(targetCount, 10);
            document.querySelectorAll('.page-sheet').forEach(sheet => {
                const table = sheet.querySelector('.main-table');
                const tbody = table.querySelector('tbody');
                const dataRows = tbody.querySelectorAll('tr.data-row');
                const existingDataCount = dataRows.length;

                // Hapus semua baris kosong lama
                tbody.querySelectorAll('tr.blank-row').forEach(row => row.remove());

                // Tambahkan baris kosong baru sesuai target
                const neededBlank = Math.max(0, count - existingDataCount);
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
                    `;
                    tbody.appendChild(tr);
                }
            });
        }
    </script>
</body>
</html>
