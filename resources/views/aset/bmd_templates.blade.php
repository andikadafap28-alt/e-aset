@extends('layouts.app')

@section('header_title', 'Download Template BMD')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="p-2 bg-sky-100 text-sky-700 rounded-xl">
                <span class="material-symbols-outlined text-2xl">download_for_offline</span>
            </span>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Download Template Aset BMD (KIB)</h2>
                <p class="text-slate-500 text-sm mt-0.5">Unduh template spreadsheet resmi yang 100% siap diisi dan kompatibel dengan sistem impor</p>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('aset.bmd.upload') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors">
            <span class="material-symbols-outlined text-lg">upload_file</span>
            <span>Upload Aset BMD</span>
        </a>
        <a href="{{ route('aset.bmd.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-semibold transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            <span>Daftar Aset BMD</span>
        </a>
    </div>
</div>

<!-- PILIHAN TEMPLATE EXCEL -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">grid_view</span>
            Pilih Template Klasifikasi KIB
        </h3>
        <span class="text-xs text-slate-500">Klik tombol unduh untuk mengunduh berkas .xlsx</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- KIB B (UTAMA / SESUAI FILE PENGGUNA) -->
        <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-md shadow-blue-500/5 p-6 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -top-3 -right-3 w-20 h-20 bg-blue-50 rounded-full flex items-end justify-start p-3 text-blue-300 pointer-events-none">
                <span class="material-symbols-outlined text-4xl">laptop_mac</span>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Format Sesuai File Anda</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Rekomendasi</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mt-1">KIB B: Peralatan dan Mesin</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Format 22 kolom identik dengan ekspor SIMBADA Anda. Cocok untuk Komputer, Laptop, Printer, Alat Medis/Kesehatan, Kendaraan Operasional, Mesin, dan Perlengkapan Kantor.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Akun:</span>
                        <span class="font-mono font-semibold text-slate-800">1.3.2 (Peralatan &amp; Mesin)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jumlah Kolom:</span>
                        <span class="font-semibold text-slate-800">22 Kolom (A s/d V)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Contoh Data:</span>
                        <span class="font-semibold text-blue-600">Lap Top HP, PC Lenovo, Omron</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('aset.bmd.template', ['kib' => 'B']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-600/20 transition-all hover:scale-[1.01]">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span>Download Template KIB B (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- KIB A (Tanah) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Aset Tetap 1.3.1</span>
                <h4 class="text-lg font-bold text-slate-900 mt-2">KIB A: Tanah</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Untuk mencatat bidang tanah puskesmas induk, tanah pustu, poskesdes, sertifikat hak pakai, status penguasaan, dan luas tanah.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Akun:</span>
                        <span class="font-mono font-semibold text-slate-800">1.3.1 (Tanah)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Satuan Utama:</span>
                        <span class="font-semibold text-slate-800">M2 / Bidang</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('aset.bmd.template', ['kib' => 'A']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span>Download Template KIB A (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- KIB C (Gedung & Bangunan) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Aset Tetap 1.3.3</span>
                <h4 class="text-lg font-bold text-slate-900 mt-2">KIB C: Gedung &amp; Bangunan</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Untuk mencatat gedung rawat inap, instalasi gawat darurat, gedung kantor administrasi, rumah dinas tenaga medis, gudang obat, dll.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Akun:</span>
                        <span class="font-mono font-semibold text-slate-800">1.3.3 (Gedung &amp; Bangunan)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Satuan Utama:</span>
                        <span class="font-semibold text-slate-800">Unit / Gedung</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('aset.bmd.template', ['kib' => 'C']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span>Download Template KIB C (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- KIB D (Jalan, Irigasi & Jaringan) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <span class="px-2.5 py-0.5 bg-purple-100 text-purple-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Aset Tetap 1.3.4</span>
                <h4 class="text-lg font-bold text-slate-900 mt-2">KIB D: Jalan, Irigasi &amp; Jaringan</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Untuk instalasi IPAL medis, saluran drainase limbah klinis, jaringan listrik/genset, paving block halaman parkir, dan jaringan pipa air.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Akun:</span>
                        <span class="font-mono font-semibold text-slate-800">1.3.4 (Jalan &amp; Jaringan)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Satuan Utama:</span>
                        <span class="font-semibold text-slate-800">Unit / Meter / Titik</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('aset.bmd.template', ['kib' => 'D']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span>Download Template KIB D (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- KIB E (Aset Tetap Lainnya) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div>
                <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Aset Tetap 1.3.5</span>
                <h4 class="text-lg font-bold text-slate-900 mt-2">KIB E: Aset Tetap Lainnya</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Untuk buku referensi perpustakaan, pedoman klinis, modul SOP akreditasi cetak, lukisan, hiasan bernilai seni, tanaman penghijauan khusus.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Akun:</span>
                        <span class="font-mono font-semibold text-slate-800">1.3.5 (Aset Lainnya)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Satuan Utama:</span>
                        <span class="font-semibold text-slate-800">Eksemplar / Buah</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('aset.bmd.template', ['kib' => 'E']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span>Download Template KIB E (.xlsx)</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- PREVIEW TAMPILAN SPREADSHEET BMD -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8 overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">table_chart</span>
                Preview Format Kolom Spreadsheet (Sesuai File BMD Anda)
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Berikut gambaran struktur tabel saat file Excel diisi dan dibuka di spreadsheet:</p>
        </div>
        <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-semibold border border-emerald-200">
            22 Kolom Terisi Lengkap
        </span>
    </div>

    <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-xs text-left whitespace-nowrap">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">A (1)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">B (2)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">C (3)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">D (4)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">E (5)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">F (6)</th>
                    <th class="p-2.5 border-r border-slate-700 font-mono text-center">G (7)</th>
                    <th class="p-2.5 border-r border-slate-700 font-bold">H: Nama Barang</th>
                    <th class="p-2.5 border-r border-slate-700 font-bold">I: NIBAR Lama</th>
                    <th class="p-2.5 border-r border-slate-700 font-bold">J: NIBAR Baru / Reg</th>
                    <th class="p-2.5 border-r border-slate-700">K: Spesifikasi Nama</th>
                    <th class="p-2.5 border-r border-slate-700">L: Bahan</th>
                    <th class="p-2.5 border-r border-slate-700">M: Merek/Tipe</th>
                    <th class="p-2.5 border-r border-slate-700">N: Lokasi</th>
                    <th class="p-2.5 border-r border-slate-700 text-right">O: Jml</th>
                    <th class="p-2.5 border-r border-slate-700 text-center">P: Sat</th>
                    <th class="p-2.5 border-r border-slate-700 text-right font-bold">Q: Harga Satuan</th>
                    <th class="p-2.5 border-r border-slate-700 text-right">R: Nilai Perolehan</th>
                    <th class="p-2.5 border-r border-slate-700">S: Cara Perolehan</th>
                    <th class="p-2.5 border-r border-slate-700 text-center">T: Thn</th>
                    <th class="p-2.5 border-r border-slate-700">U: SKPD</th>
                    <th class="p-2.5">V: Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white font-sans text-slate-700">
                <!-- Baris Contoh Sesuai Screenshot User -->
                <tr class="bg-blue-50/50 hover:bg-blue-50 transition-colors font-medium">
                    <td class="p-2 text-center font-mono">1</td>
                    <td class="p-2 text-center font-mono">3</td>
                    <td class="p-2 text-center font-mono">2</td>
                    <td class="p-2 text-center font-mono">10</td>
                    <td class="p-2 text-center font-mono">01</td>
                    <td class="p-2 text-center font-mono">02</td>
                    <td class="p-2 text-center font-mono text-blue-600 font-bold">007</td>
                    <td class="p-2 font-bold text-slate-900">Lap Top</td>
                    <td class="p-2 font-mono text-[11px] text-slate-500">1.3.02.10.01.002.000007.19910...</td>
                    <td class="p-2 font-mono text-[11px] text-blue-700 font-semibold">1.3.02.10.01.002.000007.2023.001</td>
                    <td class="p-2">Lap Top</td>
                    <td class="p-2 text-slate-400">-</td>
                    <td class="p-2">Merk: HP NOTEBOOK 14-CF2019TU</td>
                    <td class="p-2 font-medium text-slate-800">Desa/Mantup</td>
                    <td class="p-2 text-right">1.00</td>
                    <td class="p-2 text-center">Buah</td>
                    <td class="p-2 text-right font-bold text-emerald-700">6.808.080,00</td>
                    <td class="p-2 text-right">6.808.080,00</td>
                    <td class="p-2">Pengadaan APBD</td>
                    <td class="p-2 text-center font-mono">19910206</td>
                    <td class="p-2">PUSKESMAS MANTUP</td>
                    <td class="p-2 text-slate-500">(DPA/B-APBD)...</td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-2 text-center font-mono">1</td>
                    <td class="p-2 text-center font-mono">3</td>
                    <td class="p-2 text-center font-mono">2</td>
                    <td class="p-2 text-center font-mono">10</td>
                    <td class="p-2 text-center font-mono">01</td>
                    <td class="p-2 text-center font-mono">02</td>
                    <td class="p-2 text-center font-mono font-bold">001</td>
                    <td class="p-2 font-bold text-slate-900">Personal Computer (PC)</td>
                    <td class="p-2 font-mono text-[11px] text-slate-500">1.3.02.10.01.002.000001.2022.001</td>
                    <td class="p-2 font-mono text-[11px] text-blue-700">1.3.02.10.01.002.000001.2022.001</td>
                    <td class="p-2">PC All In One</td>
                    <td class="p-2">Besi / Plastik</td>
                    <td class="p-2">Merk: Lenovo V50a Core i5</td>
                    <td class="p-2">Ruang Pendaftaran</td>
                    <td class="p-2 text-right">1.00</td>
                    <td class="p-2 text-center">Unit</td>
                    <td class="p-2 text-right font-bold text-emerald-700">11.250.000,00</td>
                    <td class="p-2 text-right">11.250.000,00</td>
                    <td class="p-2">Pengadaan APBD</td>
                    <td class="p-2 text-center font-mono">2022</td>
                    <td class="p-2">PUSKESMAS MANTUP</td>
                    <td class="p-2 text-slate-500">Loket Pendaftaran</td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-2 text-center font-mono">1</td>
                    <td class="p-2 text-center font-mono">3</td>
                    <td class="p-2 text-center font-mono">2</td>
                    <td class="p-2 text-center font-mono">07</td>
                    <td class="p-2 text-center font-mono">01</td>
                    <td class="p-2 text-center font-mono">01</td>
                    <td class="p-2 text-center font-mono font-bold">002</td>
                    <td class="p-2 font-bold text-slate-900">Tensimeter Digital</td>
                    <td class="p-2 font-mono text-[11px] text-slate-500">1.3.02.07.01.001.000002.2023.001</td>
                    <td class="p-2 font-mono text-[11px] text-blue-700">1.3.02.07.01.001.000002.2023.001</td>
                    <td class="p-2">Tensimeter Digital</td>
                    <td class="p-2">Plastik / Manset</td>
                    <td class="p-2">Merk: Omron HEM-7120</td>
                    <td class="p-2">Ruang Poli Dewasa</td>
                    <td class="p-2 text-right">1.00</td>
                    <td class="p-2 text-center">Buah</td>
                    <td class="p-2 text-right font-bold text-emerald-700">850.000,00</td>
                    <td class="p-2 text-right">850.000,00</td>
                    <td class="p-2">Pengadaan APBD</td>
                    <td class="p-2 text-center font-mono">2023</td>
                    <td class="p-2">PUSKESMAS MANTUP</td>
                    <td class="p-2 text-slate-500">Poli Rawat Jalan</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TABEL PANDUAN KOLOM LENGKAP -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <div class="border-b border-slate-100 pb-3 mb-4">
        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-600">list_alt</span>
            Panduan Rincian Kolom Excel (Kolom A s/d V)
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">Penjelasan fungsi setiap kolom, status wajib/opsional, dan bagaimana sistem memprosesnya:</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                    <th class="py-2.5 px-3">Kolom</th>
                    <th class="py-2.5 px-3">Nama Kolom</th>
                    <th class="py-2.5 px-3">Contoh di File Anda</th>
                    <th class="py-2.5 px-3 text-center">Sifat</th>
                    <th class="py-2.5 px-3">Penjelasan Sistem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-blue-600">A s/d G</td>
                    <td class="py-2.5 px-3 font-semibold">Pecahan Kode 108 (7 Level)</td>
                    <td class="py-2.5 px-3 font-mono text-[11px]">1 | 3 | 2 | 10 | 01 | 02 | 007</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Wajib</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Sistem otomatis menggabungkan ke kode 108 utuh (<code class="font-mono text-[11px] text-slate-700">1.3.2.10.01.02.007</code>) dan menentukan kategori aset (Peralatan &amp; Mesin).</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">H</td>
                    <td class="py-2.5 px-3 font-semibold">Nama Barang</td>
                    <td class="py-2.5 px-3 font-medium">Lap Top</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Wajib</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Nama aset utama yang tampil di daftar aset, label barcode, dan laporan.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">I</td>
                    <td class="py-2.5 px-3 font-semibold">NIBAR Lama</td>
                    <td class="py-2.5 px-3 font-mono text-[11px]">1.3.02.10.01.002.000007.19910...</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Disimpan sebagai cadangan kode aset jika NIBAR baru tidak tersedia.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">J</td>
                    <td class="py-2.5 px-3 font-semibold">Nomor Register / NIBAR Baru</td>
                    <td class="py-2.5 px-3 font-mono text-[11px]">1.3.02.10.01.002.000007.2023.001</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Wajib</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Menjadi identitas barcode unik (<code class="font-mono text-[11px]">asset_code</code>). Sistem mengekstrak angka belakang (<code class="font-mono text-[11px]">001</code> &rarr; 1) sebagai nomor register sehingga aman dari integer overflow.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">K</td>
                    <td class="py-2.5 px-3 font-semibold">Spesifikasi Nama Barang</td>
                    <td class="py-2.5 px-3">Lap Top</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Digunakan sebagai nama barang jika Kolom H kosong.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">L</td>
                    <td class="py-2.5 px-3 font-semibold">Spesifikasi Lainnya / Bahan</td>
                    <td class="py-2.5 px-3 text-slate-400">(Kosong) atau Besi / Plastik</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Disimpan ke kolom bahan aset untuk kebutuhan cetak KIR dan kartu inventaris.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">M</td>
                    <td class="py-2.5 px-3 font-semibold">Merek / Tipe</td>
                    <td class="py-2.5 px-3">Merk: HP NOTEBOOK 14-CF2019TU</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Awalan "Merk:" dibersihkan otomatis dan digabungkan ke nama aset agar mudah dicari saat pelabelan QR Code.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">N</td>
                    <td class="py-2.5 px-3 font-semibold">Lokasi / Ruangan</td>
                    <td class="py-2.5 px-3">Desa/Mantup / Ruang Tata Usaha</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Sistem otomatis mencocokkan dengan nama Ruangan yang terdaftar di Master Lokasi Ruangan.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">Q &amp; R</td>
                    <td class="py-2.5 px-3 font-semibold">Harga Satuan &amp; Nilai Perolehan</td>
                    <td class="py-2.5 px-3 font-bold text-emerald-700">6.808.080,00</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Wajib</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Format rupiah dengan desimal sen (<code class="font-mono text-[11px]">,00</code>) diproses dengan aman menjadi nilai desimal numerik murni (<code class="font-mono text-[11px]">6808080</code>).</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">S</td>
                    <td class="py-2.5 px-3 font-semibold">Cara Perolehan</td>
                    <td class="py-2.5 px-3">Pengadaan APBD</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Sumber dana aset (APBD, Bantuan Sarpras, Hibah, APBN).</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">T</td>
                    <td class="py-2.5 px-3 font-semibold">Tanggal / Tahun Perolehan</td>
                    <td class="py-2.5 px-3 font-mono text-[11px]">19910206 atau 2023</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Tahun perolehan diekstrak otomatis untuk perhitungan umur ekonomis dan akumulasi penyusutan.</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-800">V</td>
                    <td class="py-2.5 px-3 font-semibold">Keterangan / Dokumen</td>
                    <td class="py-2.5 px-3">(DPA/B-APBD)...</td>
                    <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Opsional</span></td>
                    <td class="py-2.5 px-3 text-slate-500">Disimpan ke catatan / keterangan aset.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-between items-center pt-4 border-t border-slate-100">
        <p class="text-xs text-slate-500">Sudah memiliki file BMD yang siap diunggah?</p>
        <a href="{{ route('aset.bmd.upload') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
            <span class="material-symbols-outlined text-base">cloud_upload</span>
            <span>Upload Berkas BMD Sekarang</span>
        </a>
    </div>
</div>
@endsection
