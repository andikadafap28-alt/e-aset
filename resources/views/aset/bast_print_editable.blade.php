<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST - {{ $loan->asset->asset_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none !important; }
            .print-border-0 { border: none !important; }
            .editable-field { border: none !important; padding: 0 !important; background: transparent !important; }
            @page { margin: 2cm; }
        }
        .editable-field {
            display: inline-block;
            min-width: 20px;
            border-bottom: 1px dashed #cbd5e1;
            padding: 0 4px;
            transition: all 0.2s;
            cursor: text;
        }
        .editable-field:hover, .editable-field:focus {
            background-color: #fef08a;
            outline: none;
            border-bottom-color: #eab308;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900 font-serif">

    <!-- Control Panel (No Print) -->
    <div class="no-print fixed top-0 left-0 w-full bg-indigo-600 text-white p-4 shadow-md z-50 flex justify-between items-center">
        <div>
            <h1 class="font-bold text-lg">Mode Edit & Cetak BAST</h1>
            <p class="text-xs text-indigo-200 mt-1">Anda bisa klik teks bergaris bawah putus-putus untuk mengeditnya secara manual sebelum dicetak.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.close()" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-400 rounded-lg text-sm font-medium transition-colors">Tutup</button>
            <button onclick="window.print()" class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Sekarang
            </button>
        </div>
    </div>

    <!-- Kertas A4 -->
    <div class="bg-white mx-auto shadow-xl mt-24 mb-10 relative" style="width: 21cm; min-height: 29.7cm; padding: 2cm;">
        
        <!-- Kop Surat Editable -->
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h2 class="text-xl font-bold uppercase"><span class="editable-field" contenteditable="true">Pemerintah Kabupaten/Kota</span></h2>
            <h1 class="text-2xl font-bold uppercase"><span class="editable-field" contenteditable="true">Dinas Kesehatan</span></h1>
            <h1 class="text-2xl font-bold uppercase"><span class="editable-field" contenteditable="true">Puskesmas Mantup</span></h1>
            <p class="text-sm mt-1"><span class="editable-field" contenteditable="true">Jl. Contoh Alamat No. 123, Telp. (0322) 123456</span></p>
        </div>

        <div class="text-center mb-10">
            <h3 class="text-lg font-bold underline">BERITA ACARA SERAH TERIMA ASET</h3>
            <p class="mt-1">Nomor: <span class="editable-field" contenteditable="true">020/BAST/{{ date('m/Y') }}</span></p>
        </div>

        <div class="mb-6 leading-relaxed text-justify">
            <p>Pada hari ini <span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('l') }}</span> tanggal <span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d') }}</span> bulan <span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('F') }}</span> tahun <span class="editable-field" contenteditable="true">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('Y') }}</span>, kami yang bertanda tangan di bawah ini:</p>
        </div>

        <!-- Pihak 1 -->
        <div class="mb-6 ml-4">
            <table class="w-full">
                <tr>
                    <td class="w-8 align-top">1.</td>
                    <td class="w-32 align-top">Nama</td>
                    <td class="w-4 align-top">:</td>
                    <td><span class="editable-field font-bold" contenteditable="true">Pengurus Barang</span></td>
                </tr>
                <tr>
                    <td class="align-top"></td>
                    <td class="align-top">NIP</td>
                    <td class="align-top">:</td>
                    <td><span class="editable-field" contenteditable="true">19800101 200501 1 001</span></td>
                </tr>
                <tr>
                    <td class="align-top"></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td><span class="editable-field" contenteditable="true">Pengurus Barang Puskesmas</span></td>
                </tr>
            </table>
            <p class="mt-2 text-justify">Selanjutnya disebut sebagai <b>PIHAK PERTAMA</b> (Yang Menyerahkan).</p>
        </div>

        <!-- Pihak 2 -->
        <div class="mb-8 ml-4">
            <table class="w-full">
                <tr>
                    <td class="w-8 align-top">2.</td>
                    <td class="w-32 align-top">Nama</td>
                    <td class="w-4 align-top">:</td>
                    <td><span class="editable-field font-bold" contenteditable="true">{{ $loan->borrower_name }}</span></td>
                </tr>
                <tr>
                    <td class="align-top"></td>
                    <td class="align-top">Kontak / NIP</td>
                    <td class="align-top">:</td>
                    <td><span class="editable-field" contenteditable="true">{{ $loan->borrower_contact ?: '.........................................' }}</span></td>
                </tr>
                <tr>
                    <td class="align-top"></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td><span class="editable-field" contenteditable="true">Jabatan Peminjam</span></td>
                </tr>
            </table>
            <p class="mt-2 text-justify">Selanjutnya disebut sebagai <b>PIHAK KEDUA</b> (Yang Menerima).</p>
        </div>

        <div class="mb-6 leading-relaxed text-justify">
            <p><b>PIHAK PERTAMA</b> telah menyerahkan kepada <b>PIHAK KEDUA</b>, dan <b>PIHAK KEDUA</b> telah menerima dari <b>PIHAK PERTAMA</b> berupa aset/barang inventaris dengan rincian sebagai berikut:</p>
        </div>

        <!-- Tabel Aset -->
        <table class="w-full border-collapse border border-black mb-6 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black p-2 w-10 text-center">No.</th>
                    <th class="border border-black p-2">Kode Aset</th>
                    <th class="border border-black p-2">Nama Barang / Spesifikasi</th>
                    <th class="border border-black p-2 w-16 text-center">Tahun</th>
                    <th class="border border-black p-2 w-20 text-center">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black p-2 text-center">1</td>
                    <td class="border border-black p-2"><span class="editable-field" contenteditable="true">{{ $loan->asset->asset_code }}</span></td>
                    <td class="border border-black p-2">
                        <span class="editable-field font-semibold" contenteditable="true">{{ $loan->asset->name }}</span>
                        @if($loan->asset->merk)
                            <br><span class="editable-field text-xs text-gray-600" contenteditable="true">Merek: {{ $loan->asset->merk }}</span>
                        @endif
                    </td>
                    <td class="border border-black p-2 text-center"><span class="editable-field" contenteditable="true">{{ $loan->asset->year_purchased }}</span></td>
                    <td class="border border-black p-2 text-center"><span class="editable-field" contenteditable="true">{{ $loan->asset->condition }}</span></td>
                </tr>
            </tbody>
        </table>

        <div class="mb-10 leading-relaxed text-justify">
            <p>Barang tersebut diserahkan dalam keadaan <span class="editable-field" contenteditable="true"><b>Baik</b></span> dan untuk selanjutnya menjadi tanggung jawab <b>PIHAK KEDUA</b> dalam penggunaannya sesuai keperluan dinas.</p>
            <p class="mt-2">Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="flex justify-between items-start mt-10 px-8">
            <div class="text-center">
                <p>Yang Menerima,</p>
                <p><b>PIHAK KEDUA</b></p>
                <div class="h-24"></div>
                <p class="font-bold underline"><span class="editable-field" contenteditable="true">{{ $loan->borrower_name }}</span></p>
                <p>NIP. <span class="editable-field" contenteditable="true">...........................</span></p>
            </div>
            
            <div class="text-center">
                <p><span class="editable-field" contenteditable="true">Mantup, {{ $date }}</span></p>
                <p>Yang Menyerahkan,</p>
                <p><b>PIHAK PERTAMA</b></p>
                <div class="h-20"></div>
                <p class="font-bold underline"><span class="editable-field" contenteditable="true">Nama Pengurus Barang</span></p>
                <p>NIP. <span class="editable-field" contenteditable="true">19800101 200501 1 001</span></p>
            </div>
        </div>
        
        <!-- Mengetahui Kepala -->
        <div class="text-center mt-12">
            <p>Mengetahui,</p>
            <p><b>Kepala Puskesmas Mantup</b></p>
            <div class="h-24 flex justify-center items-center">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="h-20 w-20 opacity-80" alt="QR Code">
            </div>
            <p class="font-bold underline"><span class="editable-field" contenteditable="true">dr. Nama Kepala Puskesmas</span></p>
            <p>NIP. <span class="editable-field" contenteditable="true">19700101 200001 1 001</span></p>
        </div>
    </div>
</body>
</html>
