<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST - {{ $loan->asset->name }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .document-a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
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
        
        @media print {
            body {
                background-color: white;
            }
            .document-a4 {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                border: none;
            }
            .print-hidden {
                display: none !important;
            }
            .editable-field {
                border-bottom: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 py-8">
    
    <!-- Controls (Not printed) -->
    <div class="max-w-[21cm] mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 print-hidden">
        <div>
            <h1 class="font-bold text-slate-800">Cetak BAST / Peminjaman</h1>
            <p class="text-xs text-slate-500">Edit teks dengan mengkliknya sebelum mencetak.</p>
        </div>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Dokumen
        </button>
    </div>

    <!-- A4 Paper -->
    <div class="document-a4 text-[13px]">
        
        <!-- KOP SURAT -->
        <div class="border-b-[3px] border-black pb-2 mb-6 text-center relative flex justify-between items-center">
            <div class="w-24">
                <img src="{{ asset('img/logo-lamongan.png') }}" alt="Logo Lamongan" class="w-20 mx-auto">
            </div>
            <div class="flex-1">
                <div class="font-bold text-base leading-snug">PEMERINTAH KABUPATEN LAMONGAN</div>
                <div class="font-bold text-lg leading-snug">DINAS KESEHATAN</div>
                <div class="font-bold text-xl leading-snug">PUSKESMAS MANTUP</div>
                <div class="mt-1">Jln. Raya Mantup No. 55 Mantup Lamongan</div>
                <div>Telp. (0322) 4670302 Email : puskesmasmantup@yahoo.co.id</div>
            </div>
            <div class="w-24">
                <img src="{{ asset('img/logo-husada.png') }}" alt="Bakti Husada" class="w-20 mx-auto">
            </div>
        </div>
        
        <!-- TITLE -->
        <div class="text-center mb-6">
            <h2 class="font-bold text-[15px] editable-field" contenteditable="true">BUKTI SERAH TERIMA BARANG</h2>
        </div>

        <!-- CONTENT -->
        <div class="mb-3 text-justify">
            <p>Bersama ini telah kami serahkan kepada :</p>
        </div>

        <div class="mb-5">
            <table class="w-full max-w-xl">
                <tr>
                    <td class="w-32 align-top py-0.5">Nama</td>
                    <td class="w-4 align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" contenteditable="true">{{ $loan->borrower_name }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">NIP</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" contenteditable="true">{{ $borrowerNip ?: '.........................................' }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Jabatan</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" contenteditable="true">{{ $loan->borrower_position ?: '.........................................' }}</span></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Untuk Keperluan</td>
                    <td class="align-top py-0.5">:</td>
                    <td class="py-0.5"><span class="editable-field" contenteditable="true">{{ $loan->notes ? ucwords(strtolower($loan->notes)) : 'Pelayanan Ponkesdes/Pustu .....................' }}</span></td>
                </tr>
            </table>
        </div>

        <div class="mb-3">
            <p>Berupa barang yang tersebut di bawah ini :</p>
        </div>

        <!-- TABLE -->
        <table class="w-full border-collapse border border-black mb-6">
            <thead>
                <tr>
                    <th class="border border-black p-2 font-bold text-center">TANGGAL</th>
                    <th class="border border-black p-2 font-bold text-center">NAMA BARANG</th>
                    <th class="border border-black p-2 font-bold text-center">MERK / TYPE</th>
                    <th class="border border-black p-2 font-bold text-center">BANYAKNYA</th>
                    <th class="border border-black p-2 font-bold text-center">KET.</th>
                    <th class="border border-black p-2 font-bold text-center">SUMBER DANA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black p-2 text-center align-top"><span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}</span></td>
                    <td class="border border-black p-2 align-top"><span class="editable-field" contenteditable="true">{{ $loan->asset->name }}</span></td>
                    <td class="border border-black p-2 align-top"><span class="editable-field" contenteditable="true">{{ $loan->asset->merk ?: '-' }} {{ $loan->asset->tipe ? '/ '.$loan->asset->tipe : '' }}</span></td>
                    <td class="border border-black p-2 text-center align-top"><span class="editable-field" contenteditable="true">1 unit</span></td>
                    <td class="border border-black p-2 align-top"><span class="editable-field" contenteditable="true">{{ $loan->asset->location ?: '-' }}</span></td>
                    <td class="border border-black p-2 align-top"><span class="editable-field" contenteditable="true">{{ $loan->asset->funding_source ?: '-' }}</span></td>
                </tr>
            </tbody>
        </table>

        <!-- FOOTER TEXT -->
        <div class="mb-8 text-justify">
            <p class="editable-field" contenteditable="true">Yang selanjutnya akan diserahkan kepada koordinator yang ada di {{ $loan->notes ? ucwords(strtolower($loan->notes)) : 'Pelayanan Ponkesdes/Pustu ............................' }} dengan daftar nama terlampir.</p>
        </div>

        <!-- SIGNATURES -->
        <div class="flex justify-between mt-8 relative items-start">
            
            <div class="text-center w-[250px] flex flex-col h-[180px]">
                <div>
                    <div>&nbsp;</div>
                    <div class="mb-0">Pengurus Barang Pembantu</div>
                    <div>Puskesmas Mantup</div>
                </div>
                <div class="mt-auto">
                    <div class="font-bold underline editable-field" contenteditable="true">Andika Dafa Penta Pratama</div>
                    <div>NIP. <span class="editable-field" contenteditable="true">19990728 202504 1 003</span></div>
                </div>
            </div>
            
            <div class="text-center w-[250px] flex flex-col h-[180px]">
                <div>
                    <div class="mb-0">Mantup, <span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}</span></div>
                    <div>Penerima Barang</div>
                </div>
                <div class="mt-auto">
                    <div class="font-bold underline editable-field" contenteditable="true">{{ $loan->borrower_name }}</div>
                    <div>NIP. <span class="editable-field" contenteditable="true">{{ $borrowerNip ?: '...........................' }}</span></div>
                </div>
            </div>
        </div>

        <!-- CENTER SIGNATURE -->
        <div class="flex justify-center mt-6">
            <div class="text-center w-[300px]">
                <div class="mb-0">Mengetahui,</div>
                <div class="mb-16">Kepala Puskesmas Mantup</div>
                
                <div class="font-bold underline editable-field" contenteditable="true">dr. Muhamad Sunaryadi</div>
                <div>NIP. <span class="editable-field" contenteditable="true">19690313 200212 1 007</span></div>
            </div>
        </div>
        
    </div>

    @if(request('action') == 'print')
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
    @endif

</body>
</html>
