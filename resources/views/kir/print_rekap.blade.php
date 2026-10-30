<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi KIR - Puskesmas Mantup (F4)</title>
    <style>
        /* Standar Kertas F4 Landscape (330mm x 216mm) */
        @page {
            size: 330mm 216mm;
            margin: 12mm 15mm 12mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.25;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Toolbar Layar */
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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

        .btn-back { background: #334155; color: #f8fafc; border-color: #475569; }
        .btn-back:hover { background: #475569; color: #fff; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-download { background: #059669; color: #fff; }
        .btn-download:hover { background: #047857; }

        .btn-preset {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 4px 8px;
            font-size: 11.5px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-preset:hover { background: #334155; color: #38bdf8; border-color: #38bdf8; }

        .input-date {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 12.5px;
            outline: none;
        }
        .input-date:focus { border-color: #38bdf8; }

        /* Lembar Fisik Dokumen */
        .sheet-wrapper {
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-sheet {
            background: #ffffff;
            width: 330mm;
            min-height: 216mm;
            padding: 12mm 15mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            position: relative;
            margin-bottom: 30px;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #000;
            margin-bottom: 14px;
            padding-bottom: 8px;
        }
        .kop-title {
            text-align: center;
            line-height: 1.2;
        }
        .kop-title .gov-name { font-size: 12pt; font-weight: bold; }
        .kop-title .dept-name { font-size: 14pt; font-weight: bold; }
        .kop-title .inst-name { font-size: 16pt; font-weight: bold; letter-spacing: 1px; }
        .kop-title .addr-text { font-size: 9pt; font-style: italic; }

        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 12px;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        /* Tabel Rekapitulasi Data */
        .kir-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 16px;
        }
        .kir-table th, .kir-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .kir-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 330px;
            text-align: center;
            font-size: 11pt;
            line-height: 1.3;
        }
        .ttd-image-area {
            height: 84px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 3px 0;
            position: relative;
            overflow: visible;
        }
        .ttd-image-area img {
            max-height: 80px;
            max-width: 180px;
            width: auto;
            height: auto;
            object-fit: contain;
            mix-blend-mode: multiply;
            filter: contrast(105%) brightness(98%);
            transition: transform 0.25s ease;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .sheet-wrapper { padding: 0 !important; margin: 0 !important; }
            .page-sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Layar Atas -->
    <div class="toolbar no-print">
        <div class="toolbar-group">
            <a href="{{ route('kir.rekap') }}" class="btn btn-back">
                &larr; Kembali ke Rekapitulasi
            </a>
            <span style="color: #94a3b8; font-size: 12px;">| Format Cetak Rekapitulasi KIR (F4 Landscape)</span>
        </div>

        <div class="toolbar-group">
            <!-- Ubah Tanggal Cetak (Update Tiap 6 Bulan) -->
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.85); padding: 3px 8px; border-radius: 6px; border: 1px solid #334155;">
                <span style="color: #38bdf8; font-weight: 600; font-size: 12px;">📅 Tanggal Dokumen:</span>
                <input type="date" id="datePicker" class="input-date" value="{{ $docDate }}" onchange="changeDocDate(this.value)">
                <button type="button" class="btn-preset" onclick="setPresetDate('sem1')" title="Semester 1">02 Jan</button>
                <button type="button" class="btn-preset" onclick="setPresetDate('sem2')" title="Semester 2">01 Jul</button>
                <button type="button" class="btn-preset" onclick="setPresetDate('today')" title="Hari ini">Hari Ini</button>
            </div>

            <!-- Tombol Acak Variasi TTD -->
            <button type="button" onclick="shuffleTtd()" class="btn" style="background: #1e293b; color: #f8fafc; border: 1px solid #c084fc;" title="Acak variasi gambar, posisi, kemiringan, dan ukuran tanda tangan dr. Sunaryadi agar tampak alami">
                🎲 Acak Variasi TTD
            </button>

            <!-- Export Excel -->
            <a href="{{ route('kir.rekap.export') }}" class="btn btn-download">
                Download Excel
            </a>

            <!-- Tombol Cetak -->
            <button onclick="window.print()" class="btn btn-print">
                Cetak Dokumen (F4)
            </button>
        </div>
    </div>

    <!-- Lembar Cetak Dokumen F4 -->
    <div class="sheet-wrapper">
        @php
            $kapusTtdDir = public_path('images/ttd_kapus');
            $kapusTtdList = [];
            if (is_dir($kapusTtdDir)) {
                $scannedFiles = glob($kapusTtdDir . '/*.{png,PNG,webp,WEBP}', GLOB_BRACE);
                if (!empty($scannedFiles)) {
                    natsort($scannedFiles);
                    foreach ($scannedFiles as $sf) {
                        if (is_file($sf) && basename($sf) !== 'ttd_kapus_1.png') {
                            $kapusTtdList[] = 'images/ttd_kapus/' . basename($sf);
                        }
                    }
                }
            }
            if (empty($kapusTtdList)) {
                $kapusTtdList = ['images/ttd_kapus_nostamp.png'];
            }

            $seed = crc32('rekap_kir_mantup_ttd');
            $ttdIndex = abs($seed) % count($kapusTtdList);
            $selectedKapusTtd = $kapusTtdList[$ttdIndex];
            $kapusTtdVersion = file_exists(public_path($selectedKapusTtd)) ? filemtime(public_path($selectedKapusTtd)) : time();

            // Variasi rotasi kemiringan: antara -2.2 derajat s/d +2.2 derajat
            $rotDeg = number_format(((abs($seed >> 3) % 45) - 22) / 10, 1);
            // Variasi posisi horizontal: -8px s/d +10px
            $shiftX = (abs($seed >> 7) % 19) - 8;
            // Variasi posisi vertikal: -4px s/d +5px
            $shiftY = (abs($seed >> 11) % 10) - 4;
            // Variasi skala ukuran: 94% s/d 106%
            $scale = number_format(0.95 + ((abs($seed >> 15) % 11) / 100), 2);

            $ttdTransform = "transform: translate({$shiftX}px, {$shiftY}px) rotate({$rotDeg}deg) scale({$scale}); transform-origin: center center;";
        @endphp
        <div class="page-sheet">
            <!-- Kop Surat -->
            <table class="kop-table">
                <tr>
                    <td class="kop-title">
                        <div class="gov-name">PEMERINTAH KABUPATEN LAMONGAN</div>
                        <div class="dept-name">DINAS KESEHATAN</div>
                        <div class="inst-name">PUSKESMAS MANTUP</div>
                        <div class="addr-text">Jl. Raya Mantup No. 27, Mantup, Kabupaten Lamongan, Jawa Timur 62283</div>
                    </td>
                </tr>
            </table>

            <div class="doc-title">
                REKAPITULASI KARTU INVENTARIS RUANGAN (KIR)
            </div>

            <table class="kir-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px;">No</th>
                        <th rowspan="2">Jenis Barang / Nama Barang</th>
                        <th rowspan="2" style="width: 140px;">Merek / Model</th>
                        <th rowspan="2" style="width: 90px;">Bahan</th>
                        <th rowspan="2" style="width: 65px;">Total Unit</th>
                        <th colspan="3" style="width: 150px;">Keadaan Barang</th>
                        <th rowspan="2">Persebaran Ruangan</th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">Baik (B)</th>
                        <th style="width: 50px;">KB</th>
                        <th style="width: 50px;">RB</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totFisik = 0;
                        $totB = 0;
                        $totKB = 0;
                        $totRB = 0;
                    @endphp
                    @forelse($rekapItems as $item)
                        @php
                            $totFisik += $item->total_jumlah;
                            $totB += $item->total_baik;
                            $totKB += $item->total_kurang_baik;
                            $totRB += $item->total_rusak_berat;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $item->no }}</td>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>{{ $item->merk }}</td>
                            <td>{{ $item->bahan }}</td>
                            <td class="text-center"><strong>{{ $item->total_jumlah }}</strong></td>
                            <td class="text-center">{{ $item->total_baik }}</td>
                            <td class="text-center">{{ $item->total_kurang_baik }}</td>
                            <td class="text-center">{{ $item->total_rusak_berat }}</td>
                            <td style="font-size: 8.5pt;">{{ $item->ruangan_str }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 16px;">Belum ada data barang di KIR.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($rekapItems->count() > 0)
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f2f2f2;">
                        <td colspan="4" class="text-center">JUMLAH KESELURUHAN</td>
                        <td class="text-center">{{ $totFisik }}</td>
                        <td class="text-center">{{ $totB }}</td>
                        <td class="text-center">{{ $totKB }}</td>
                        <td class="text-center">{{ $totRB }}</td>
                        <td>-</td>
                    </tr>
                </tfoot>
                @endif
            </table>

            <!-- Kolom Tanda Tangan Resmi Sesuai Format -->
            <div class="ttd-wrapper">
                <!-- TTD dr. Muhamad Sunaryadi (Tanpa Stempel) -->
                <div class="ttd-box">
                    <div>Mengetahui,</div>
                    <div><strong>Kepala Puskesmas Mantup</strong></div>
                    <div class="ttd-image-area">
                        <img src="{{ asset($selectedKapusTtd) }}?v={{ $kapusTtdVersion }}" alt="TTD dr. Sunaryadi" style="{{ $ttdTransform }}">
                    </div>
                    <div class="ttd-nama">dr. MUHAMAD SUNARYADI</div>
                    <div>NIP. 19690313 200212 1 007</div>
                </div>

                <!-- TTD Andika Dafa Penta Pratama (Tanggal Dinamis 6 Bulan Sekali) -->
                <div class="ttd-box">
                    <div>Mantup, <span id="docDateDisplay" contenteditable="true">{{ $formattedDate }}</span></div>
                    <div><strong>Pengurus Barang Pembantu</strong></div>
                    <div class="ttd-image-area">
                        <!-- Kosong untuk tanda tangan basah Pengurus Barang Pembantu -->
                    </div>
                    <div class="ttd-nama">ANDIKA DAFA PENTA PRATAMA</div>
                    <div>NIP. 19990728 202504 1 003</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const kapusTtdFiles = @json($kapusTtdList);

        function shuffleTtd() {
            if (!kapusTtdFiles || kapusTtdFiles.length === 0) return;
            document.querySelectorAll('.ttd-box .ttd-image-area img').forEach(img => {
                const randomFile = kapusTtdFiles[Math.floor(Math.random() * kapusTtdFiles.length)];
                img.src = '{{ asset('') }}' + randomFile + '?v=' + Date.now();
                const shiftX = Math.floor(Math.random() * 21) - 9;
                const shiftY = Math.floor(Math.random() * 10) - 4;
                const rot = ((Math.random() * 4.4) - 2.2).toFixed(1);
                const scale = (0.94 + Math.random() * 0.12).toFixed(2);
                img.style.transform = `translate(${shiftX}px, ${shiftY}px) rotate(${rot}deg) scale(${scale})`;
                img.style.transformOrigin = 'center center';
            });
        }

        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        function changeDocDate(val) {
            if (!val) return;
            const parts = val.split('-');
            if (parts.length === 3) {
                const year = parts[0];
                const month = parseInt(parts[1], 10) - 1;
                const day = parts[2];
                const formatted = day + ' ' + monthNames[month] + ' ' + year;
                document.getElementById('docDateDisplay').textContent = formatted;
            }
        }

        function setPresetDate(type) {
            const picker = document.getElementById('datePicker');
            const now = new Date();
            const year = now.getFullYear();

            if (type === 'sem1') {
                picker.value = year + '-01-02';
            } else if (type === 'sem2') {
                picker.value = year + '-07-01';
            } else if (type === 'today') {
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                picker.value = yyyy + '-' + mm + '-' + dd;
            }
            changeDocDate(picker.value);
        }
    </script>
</body>
</html>
