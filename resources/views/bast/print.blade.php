<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST - {{ $bast->employee->name ?? 'Penerima' }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <style>
        :root {
            --offset-x: 0mm;
            --offset-y: 0mm;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f8fafc;
            color: #111827;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .document-a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 18mm 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            position: relative;
        }

        /* Area edit interaktif */
        .editable-field {
            display: inline-block;
            min-width: 20px;
            padding: 1px 4px;
            border-bottom: 1px dashed #cbd5e1;
            transition: all 0.2s;
            outline: none;
        }
        .editable-field:hover, .editable-field:focus {
            background-color: #f0fdf4;
            border-bottom: 1px solid #22c55e;
            border-radius: 2px;
        }

        /* Highlight khusus kolom sumber target */
        .print-sumber-target {
            display: inline-block;
            transition: transform 0.15s ease-out;
            transform: translate(var(--offset-x), var(--offset-y));
        }

        /* Mode Preview Timpa di Layar */
        body.screen-preview-timpa .document-a4 > *:not(.table-container-sumber) {
            opacity: 0.15 !important;
            filter: grayscale(100%) !important;
        }
        body.screen-preview-timpa .table-sumber-section td:not(.print-sumber-cell),
        body.screen-preview-timpa .table-sumber-section th {
            opacity: 0.15 !important;
        }
        body.screen-preview-timpa .table-sumber-section {
            border-color: #cbd5e1 !important;
        }
        body.screen-preview-timpa .print-sumber-target {
            background-color: #fef08a !important;
            color: #854d0e !important;
            font-weight: 700 !important;
            box-shadow: 0 0 0 3px #eab308;
            border-radius: 4px;
            padding: 2px 6px !important;
        }

        @media print {
            body {
                background-color: white !important;
            }
            .document-a4 {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding-top: 5mm !important;
                padding-left: 15mm !important;
                padding-right: 15mm !important;
                padding-bottom: 15mm !important;
                box-shadow: none !important;
                border: none !important;
            }
            .print-hidden {
                display: none !important;
            }
            .editable-field {
                border-bottom: none !important;
                padding: 0 !important;
            }

            /* ATURAN CETAK HANYA TEKS KOLOM SUMBER (TIMPA KERTAS FISIK) */
            body.print-only-sumber-mode {
                background: transparent !important;
            }
            body.print-only-sumber-mode .document-a4 {
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
            body.print-only-sumber-mode .print-sumber-target {
                transform: translate(var(--offset-x), var(--offset-y)) !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 py-8">
    @php
        if (!function_exists('formatName')) {
            function formatName($name) {
                if (!$name) return '-';
                $name = ucwords(strtolower($name), " .-");
                $name = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $name);
                return $name;
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
        $sumberDana = $bast->sumber_dana ?: 'BMD / APBD 2026';
        $totalItemsCount = $bast->items ? $bast->items->count() : 1;
    @endphp

    <!-- Controls (Not printed) -->
    <div class="max-w-[21cm] mx-auto mb-6 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 print-hidden space-y-4">
        <!-- Baris 1: Header & Tombol Utama -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bast.index') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-1 text-xs font-semibold hover:underline">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Kembali
                    </a>
                    <span class="text-slate-300">&bull;</span>
                    <h1 class="font-bold text-slate-800 text-base">Cetak BAST - {{ $empName }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium border border-blue-200">Mode Interaktif</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Semua teks bertanda garis putus-putus bisa diklik langsung untuk diedit sebelum dicetak.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="savePrintData()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-medium text-xs flex items-center gap-1.5 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span id="save-btn-text">Simpan Perubahan</span>
                </button>
                <button type="button" onclick="printFullDocument()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-semibold text-xs flex items-center gap-1.5 transition-all shadow-sm shadow-blue-600/20">
                    <span class="material-symbols-outlined text-[16px]">print</span>
                    Cetak Dokumen Lengkap
                </button>
            </div>
        </div>

        <!-- Baris 2: Panel Khusus Fitur Cetak Timpa Kolom Sumber -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4 text-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex items-start gap-2.5">
                    <div class="p-1.5 bg-amber-500 text-white rounded-lg mt-0.5 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">format_color_text</span>
                    </div>
                    <div>
                        <div class="font-bold text-amber-900 text-sm flex items-center gap-2">
                            <span>Fitur Timpa Cetak (Hanya Kolom Sumber)</span>
                            <span class="text-[10px] bg-amber-200 text-amber-800 font-semibold px-2 py-0.5 rounded-full">Solusi Kertas Fisik</span>
                        </div>
                        <div class="text-amber-800 text-[11px] mt-0.5">
                            Gunakan tombol ini jika Anda sudah punya lembaran BAST fisik yang <strong>sudah bertandatangan basah</strong> dan hanya ingin menambahkan teks pada kolom <em>"SUMBER DANA"</em> tanpa mencetak ulang seluruh surat.
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" id="btnPreviewTimpa" onclick="togglePreviewTimpa()" class="px-3 py-2 bg-white hover:bg-slate-100 text-slate-700 font-medium rounded-xl border border-slate-300 transition-colors flex items-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-[16px] text-slate-500">visibility</span>
                        <span id="txtBtnPreview">Preview Timpa</span>
                    </button>
                    <button type="button" onclick="printOnlySumber()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-sm shadow-amber-600/20 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        Cetak HANYA Teks Sumber
                    </button>
                </div>
            </div>

            <!-- Baris Tambahan: Pilihan Cepat Sumber & Kalibrasi Nudge Posisi -->
            <div class="mt-3 pt-3 border-t border-amber-200/70 flex flex-wrap items-center justify-between gap-3 text-[11px]">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-amber-900 font-semibold">Pilih Cepat Sumber:</span>
                    <button type="button" onclick="quickSetSumber('BMD / APBD 2026')" class="px-2.5 py-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg border border-amber-300 transition-colors font-medium">BMD / APBD 2026</button>
                    <button type="button" onclick="quickSetSumber('JKN 2026')" class="px-2.5 py-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg border border-amber-300 transition-colors font-medium">JKN 2026</button>
                    <button type="button" onclick="quickSetSumber('BOK 2026')" class="px-2.5 py-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg border border-amber-300 transition-colors font-medium">BOK 2026</button>
                    <button type="button" onclick="quickSetSumber('APBD Kab. Lamongan')" class="px-2.5 py-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg border border-amber-300 transition-colors font-medium">APBD Kab. Lamongan</button>
                    <button type="button" onclick="quickSetSumber('-')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg border border-slate-300 transition-colors">Kosongkan</button>
                </div>

                <!-- Kontrol Kalibrasi Posisi Nudge (+/- mm) -->
                <div class="flex items-center gap-2 bg-white/80 px-2.5 py-1 rounded-lg border border-amber-200 text-slate-700">
                    <span class="font-semibold text-amber-900">Pas Posisi (Nudge):</span>
                    <span class="text-slate-500">Y:</span>
                    <button type="button" onclick="adjustOffset('y', -1)" class="w-5 h-5 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded font-bold border border-slate-300" title="Geser ke Atas 1mm">&uarr;</button>
                    <span id="dispOffsetY" class="font-mono text-slate-800 font-semibold w-9 text-center">0 mm</span>
                    <button type="button" onclick="adjustOffset('y', 1)" class="w-5 h-5 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded font-bold border border-slate-300" title="Geser ke Bawah 1mm">&darr;</button>

                    <span class="text-slate-300 ml-1">|</span>
                    <span class="text-slate-500">X:</span>
                    <button type="button" onclick="adjustOffset('x', -1)" class="w-5 h-5 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded font-bold border border-slate-300" title="Geser ke Kiri 1mm">&larr;</button>
                    <span id="dispOffsetX" class="font-mono text-slate-800 font-semibold w-9 text-center">0 mm</span>
                    <button type="button" onclick="adjustOffset('x', 1)" class="w-5 h-5 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded font-bold border border-slate-300" title="Geser ke Kanan 1mm">&rarr;</button>
                    <button type="button" onclick="resetOffset()" class="ml-1 text-slate-400 hover:text-slate-600 text-[10px] underline">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- A4 Paper Sheet (Sesuai Format Gambar User) -->
    <div class="document-a4 text-[13px]">
        
        <!-- KOP SURAT -->
        <div class="border-b-[3px] border-black pb-2 mb-6 text-center relative flex justify-between items-center">
            <div class="w-24">
                <img src="{{ asset('img/logo-lamongan.png') }}" alt="Logo Lamongan" class="w-20 mx-auto" onerror="this.style.display='none'">
            </div>
            <div class="flex-1">
                <div class="font-bold text-base leading-snug">PEMERINTAH KABUPATEN LAMONGAN</div>
                <div class="font-bold text-lg leading-snug">DINAS KESEHATAN</div>
                <div class="font-bold text-xl leading-snug">PUSKESMAS MANTUP</div>
                <div class="mt-1">Jln. Raya Mantup No. 55 Mantup Lamongan</div>
                <div>Telp. (0322) 4670302 Email : puskesmasmantup98@gmail.com</div>
            </div>
            <div class="w-24">
                <img src="{{ asset('img/logo-husada.png') }}" alt="Bakti Husada" class="w-20 mx-auto" onerror="this.style.display='none'">
            </div>
        </div>
        
        <!-- TITLE -->
        <div class="text-center mb-6">
            <h2 class="font-bold text-[17px] editable-field" id="val_title" contenteditable="true">BUKTI SERAH TERIMA BARANG</h2>
        </div>

        <!-- CONTENT PEMBUKA -->
        <div class="mb-3 text-justify">
            <p>Bersama ini telah kami serahkan kepada :</p>
        </div>

        <!-- TABEL IDENTITAS PENERIMA -->
        <div class="mb-5">
            <table class="w-full max-w-xl text-[13px]">
                <tr>
                    <td class="w-32 align-top py-0.5">Nama</td>
                    <td class="w-4 align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field font-semibold" id="val_borrower_name" contenteditable="true">{{ $empName }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">{{ $nipLabel }}</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" id="val_borrower_nip" contenteditable="true">{{ $empNip }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Jabatan</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" id="val_borrower_position" contenteditable="true">{{ $empJabatan }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Untuk Keperluan</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field font-medium" id="val_notes" contenteditable="true">{{ $keperluan }}</span></td>
                </tr>
            </table>
        </div>

        <div class="mb-3">
            <p>Berupa barang yang tersebut di bawah ini :</p>
        </div>

        <!-- TABLE SECTION (RINCIAN BARANG) -->
        <div class="table-container-sumber">
            <table class="w-full border-collapse border border-black mb-6 table-sumber-section text-[12px]">
                <thead>
                    <tr>
                        <th class="border border-black p-2 font-bold text-center w-28">TANGGAL</th>
                        <th class="border border-black p-2 font-bold text-center">NAMA BARANG</th>
                        <th class="border border-black p-2 font-bold text-center">MERK / TYPE</th>
                        <th class="border border-black p-2 font-bold text-center w-24">BANYAKNYA</th>
                        <th class="border border-black p-2 font-bold text-center">KET.</th>
                        <th class="border border-black p-2 font-bold text-center bg-slate-50/50 w-36">SUMBER DANA</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($groupedItems) && $groupedItems->count() > 0)
                        @foreach($groupedItems as $idx => $item)
                            @php
                                $itemAsset = $item->asset;
                                $assetName = $itemAsset->name ?? '-';
                                $merkType = $itemAsset->merk && trim($itemAsset->merk) ? $itemAsset->merk : ($itemAsset->keterangan ?: '-');
                                $qtyText = ($item->qty ?? 1) . ' unit';
                                
                                if (isset($item->registers) && $item->registers != '-') {
                                    $ketText = 'Reg. ' . $item->registers;
                                } elseif ($itemAsset && $itemAsset->no_register) {
                                    $ketText = 'Reg. ' . str_pad($itemAsset->no_register, 3, '0', STR_PAD_LEFT);
                                } else {
                                    $ketText = $itemAsset->location ?: ($bast->keterangan ?: $keperluan);
                                }
                            @endphp
                            <tr>
                                <td class="border border-black p-2 text-center align-top"><span class="editable-field" id="val_tgl_{{ $idx }}" contenteditable="true">{{ $tglFormat }}</span></td>
                                <td class="border border-black p-2 align-top"><span class="editable-field font-semibold" id="val_asset_name_{{ $idx }}" contenteditable="true">{{ $assetName }}</span></td>
                                <td class="border border-black p-2 align-top"><span class="editable-field" id="val_asset_merk_{{ $idx }}" contenteditable="true">{{ $merkType }}</span></td>
                                <td class="border border-black p-2 text-center align-top font-bold"><span class="editable-field" id="val_asset_qty_{{ $idx }}" contenteditable="true">{{ $qtyText }}</span></td>
                                <td class="border border-black p-2 align-top"><span class="editable-field" id="val_asset_loc_{{ $idx }}" contenteditable="true">{{ $ketText }}</span></td>
                                <td class="border border-black p-2 align-top print-sumber-cell text-center font-medium">
                                    <span class="editable-field print-sumber-target font-semibold" id="val_asset_fund_{{ $idx }}" contenteditable="true">{{ $sumberDana }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @php
                            $firstAsset = $bast->asset;
                            $assetName = $firstAsset->name ?? 'Barang Milik Daerah';
                            $merkType = $firstAsset ? ($firstAsset->merk ?: ($firstAsset->keterangan ?: '-')) : '-';
                            $reg = $firstAsset && $firstAsset->no_register ? 'Reg. ' . str_pad($firstAsset->no_register, 3, '0', STR_PAD_LEFT) : ($bast->keterangan ?: $keperluan);
                        @endphp
                        <tr>
                            <td class="border border-black p-2 text-center align-top"><span class="editable-field" id="val_tgl" contenteditable="true">{{ $tglFormat }}</span></td>
                            <td class="border border-black p-2 align-top"><span class="editable-field font-semibold" id="val_asset_name" contenteditable="true">{{ $assetName }}</span></td>
                            <td class="border border-black p-2 align-top"><span class="editable-field" id="val_asset_merk" contenteditable="true">{{ $merkType }}</span></td>
                            <td class="border border-black p-2 text-center align-top font-bold"><span class="editable-field" id="val_asset_qty" contenteditable="true">1 unit</span></td>
                            <td class="border border-black p-2 align-top"><span class="editable-field" id="val_asset_loc" contenteditable="true">{{ $reg }}</span></td>
                            <td class="border border-black p-2 align-top print-sumber-cell text-center font-medium">
                                <span class="editable-field print-sumber-target font-semibold" id="val_asset_fund" contenteditable="true">{{ $sumberDana }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- FOOTER TEXT (PERSIS SEPERTI GAMBAR USER) -->
        <div class="mb-8 text-justify">
            <p class="editable-field" id="val_footer" contenteditable="true">
                @if($isKepalaPuskesmas)
                    Barang tersebut diserahkan dalam keadaan baik dan lengkap untuk dipergunakan sebagaimana mestinya dalam rangka menunjang kelancaran operasional kedinasan di Puskesmas Mantup.
                @else
                    Yang selanjutnya akan diserahkan kepada koordinator yang ada di {{ $keperluan }} dengan daftar nama terlampir.
                @endif
            </p>
        </div>

        <!-- AREA TANDA TANGAN (PERSIS GAMBAR USER) -->
        <div class="flex justify-between mt-8 relative items-start">
            <!-- PENGURUS BARANG (KIRI) -->
            <div class="text-center w-[250px] flex flex-col h-[170px]">
                <div>
                    <div>&nbsp;</div>
                    <div class="mb-0">Pengurus Barang Pembantu</div>
                    <div>Puskesmas Mantup</div>
                </div>
                <div class="mt-auto">
                    <div class="font-bold underline editable-field" id="val_ttd_pengurus_name" contenteditable="true">Andika Dafa Penta Pratama</div>
                    <div>NIP. <span class="editable-field" id="val_ttd_pengurus_nip" contenteditable="true">19990728 202504 1 003</span></div>
                </div>
            </div>
            
            <!-- PENERIMA BARANG (KANAN) -->
            <div class="text-center w-[250px] flex flex-col h-[170px]">
                <div>
                    <div class="mb-0">Mantup, <span class="editable-field" id="val_ttd_date" contenteditable="true">{{ $tglFormat }}</span></div>
                    <div>{{ $isKepalaPuskesmas ? 'Yang Menerima / Kepala Puskesmas' : 'Penerima Barang' }}</div>
                </div>
                <div class="mt-auto">
                    <div class="font-bold underline editable-field" id="val_ttd_penerima_name" contenteditable="true">{{ $empName }}</div>
                    <div><span class="editable-field" id="val_ttd_lbl_nip" contenteditable="true">{{ $nipLabel }}.</span> <span class="editable-field" id="val_ttd_penerima_nip" contenteditable="true">{{ $empNip }}</span></div>
                </div>
            </div>
        </div>

        @if(!$isKepalaPuskesmas)
        <!-- MENGETAHUI KEPALA PUSKESMAS (TENGAH BAWAH) -->
        <div class="flex justify-center mt-6">
            <div class="text-center w-[300px] flex flex-col h-[170px]">
                <div>
                    <div class="mb-0">Mengetahui,</div>
                    <div>Kepala Puskesmas Mantup</div>
                </div>
                <div class="mt-auto">
                    <div class="font-bold underline editable-field" id="val_ttd_kepala_name" contenteditable="true">dr. Muhammad Sunaryadi</div>
                    <div>NIP. <span class="editable-field" id="val_ttd_kepala_nip" contenteditable="true">19690313 200212 1 007</span></div>
                </div>
            </div>
        </div>
        @endif
        
    </div>

    <!-- Script Interaktif & Cetak -->
    <script>
        const STORAGE_KEY = 'bast_handover_edit_{{ $bast->id }}';
        let currentOffsetX = 0;
        let currentOffsetY = 0;

        function updateCssOffsets() {
            document.documentElement.style.setProperty('--offset-x', currentOffsetX + 'mm');
            document.documentElement.style.setProperty('--offset-y', currentOffsetY + 'mm');
            const dispX = document.getElementById('dispOffsetX');
            const dispY = document.getElementById('dispOffsetY');
            if (dispX) dispX.textContent = currentOffsetX + ' mm';
            if (dispY) dispY.textContent = currentOffsetY + ' mm';
        }

        function adjustOffset(axis, delta) {
            if (axis === 'x') currentOffsetX += delta;
            if (axis === 'y') currentOffsetY += delta;
            updateCssOffsets();
        }

        function resetOffset() {
            currentOffsetX = 0;
            currentOffsetY = 0;
            updateCssOffsets();
        }

        function quickSetSumber(val) {
            document.querySelectorAll('.print-sumber-target').forEach(el => {
                el.innerText = val;
            });
        }

        function togglePreviewTimpa() {
            document.body.classList.toggle('screen-preview-timpa');
            const isPreview = document.body.classList.contains('screen-preview-timpa');
            const btn = document.getElementById('btnPreviewTimpa');
            const txt = document.getElementById('txtBtnPreview');
            if (isPreview) {
                btn.classList.add('bg-amber-100', 'border-amber-400', 'text-amber-900');
                txt.textContent = 'Kembali Normal';
            } else {
                btn.classList.remove('bg-amber-100', 'border-amber-400', 'text-amber-900');
                txt.textContent = 'Preview Timpa';
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

        function savePrintData() {
            const data = {};
            document.querySelectorAll('.editable-field').forEach(el => {
                if (el.id) {
                    data[el.id] = el.innerHTML;
                }
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));

            const btnText = document.getElementById('save-btn-text');
            if (btnText) {
                btnText.textContent = 'Tersimpan!';
                setTimeout(() => {
                    btnText.textContent = 'Simpan Perubahan';
                }, 1500);
            }
        }

        // Restore custom edits on load
        window.addEventListener('DOMContentLoaded', () => {
            updateCssOffsets();
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    for (const [id, val] of Object.entries(data)) {
                        const el = document.getElementById(id);
                        if (el) el.innerHTML = val;
                    }
                } catch(e) {}
            }
        });
    </script>
</body>
</html>
