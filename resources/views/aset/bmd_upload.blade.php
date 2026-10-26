@extends('layouts.app')

@section('header_title', 'Upload Aset BMD')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                <span class="material-symbols-outlined text-2xl">upload_file</span>
            </span>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Upload Aset BMD (KIB)</h2>
                <p class="text-slate-500 text-sm mt-0.5">Impor massal data Kartu Inventaris Barang (BMD / SIMBADA) langsung ke sistem</p>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('aset.bmd.templates') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 rounded-xl text-sm font-semibold transition-colors">
            <span class="material-symbols-outlined text-lg">download</span>
            <span>Download Template BMD</span>
        </a>
        <a href="{{ route('aset.bmd.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-sm font-semibold transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            <span>Daftar Aset BMD</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
    <span class="material-symbols-outlined text-emerald-600 mt-0.5">check_circle</span>
    <div class="flex-1">
        <p class="font-semibold text-sm">Berhasil!</p>
        <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
    <span class="material-symbols-outlined text-rose-600 mt-0.5">error</span>
    <div class="flex-1">
        <p class="font-semibold text-sm">Gagal Mengunggah File:</p>
        <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- Quick Stats Summary -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Aset BMD</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($totalBmd) }}</p>
    </div>
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-blue-600 uppercase tracking-wider">KIB B (Peralatan)</p>
        <p class="text-xl font-bold text-blue-700 mt-1">{{ number_format($totalKibB) }}</p>
    </div>
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">KIB A (Tanah)</p>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ number_format($totalKibA) }}</p>
    </div>
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-amber-600 uppercase tracking-wider">KIB C (Gedung)</p>
        <p class="text-xl font-bold text-amber-700 mt-1">{{ number_format($totalKibC) }}</p>
    </div>
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-purple-600 uppercase tracking-wider">KIB D (Jalan)</p>
        <p class="text-xl font-bold text-purple-700 mt-1">{{ number_format($totalKibD) }}</p>
    </div>
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">KIB E (Lainnya)</p>
        <p class="text-xl font-bold text-slate-700 mt-1">{{ number_format($totalKibE) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- FORM UPLOAD -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6" x-data="{ fileName: '', isDragging: false }">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">publish</span>
                    Unggah Berkas Excel BMD
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pilih file spreadsheet (.xlsx, .xls, .csv) hasil ekspor SIMBADA / SIPD BMD</p>
            </div>

            <form action="{{ route('aset.bmd.import') }}" method="POST" enctype="multipart/form-data" @submit="if(!fileName) { alert('Silakan pilih file terlebih dahulu!'); return false; }">
                @csrf
                <input type="hidden" name="source" value="BMD">

                <!-- Drag & Drop Zone -->
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Berkas Excel KIB / BMD <span class="text-rose-500">*</span></label>
                    <div 
                        @dragover.prevent="isDragging = true" 
                        @dragleave.prevent="isDragging = false" 
                        @drop.prevent="isDragging = false; if($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0].name; }"
                        class="border-2 border-dashed rounded-2xl p-8 text-center transition-all duration-200 cursor-pointer relative"
                        :class="isDragging ? 'border-emerald-500 bg-emerald-50/50' : (fileName ? 'border-blue-400 bg-blue-50/20' : 'border-slate-300 hover:border-blue-400 bg-slate-50/50')"
                        @click="$refs.fileInput.click()"
                    >
                        <input 
                            x-ref="fileInput" 
                            type="file" 
                            name="file_excel" 
                            required 
                            accept=".xlsx, .xls, .csv" 
                            class="hidden"
                            @change="if($event.target.files.length) fileName = $event.target.files[0].name;"
                        >

                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-3 transition-colors" :class="fileName ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400'">
                                <span class="material-symbols-outlined text-3xl" x-text="fileName ? 'description' : 'cloud_upload'"></span>
                            </div>

                            <template x-if="!fileName">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Klik untuk memilih berkas atau geser (drag & drop) ke sini</p>
                                    <p class="text-xs text-slate-400 mt-1">Format didukung: Microsoft Excel (.xlsx, .xls) atau .csv (Maksimal 20MB)</p>
                                </div>
                            </template>

                            <template x-if="fileName">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                        <span class="material-symbols-outlined text-sm">task_alt</span>
                                        File Siap: <span x-text="fileName"></span>
                                    </span>
                                    <p class="text-[11px] text-slate-400">Klik lagi jika ingin mengganti file</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Petunjuk Cepat Format -->
                <div class="p-4 bg-amber-50/80 rounded-xl border border-amber-200 text-amber-900 mb-6 space-y-2">
                    <div class="flex items-center gap-2 font-semibold text-xs text-amber-800">
                        <span class="material-symbols-outlined text-base">info</span>
                        Sistem Otomatis Mengenali Format SIMBADA Anda:
                    </div>
                    <ul class="text-[11px] text-amber-800/90 space-y-1 list-disc list-inside">
                        <li><b>Kode 108 Terpecah (Kolom A s/d G):</b> Kode Akun, Kelompok, Jenis, Objek, Rincian, Sub Rincian, Sub-sub otomatis digabung menjadi kode 108 utuh (contoh: <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">1.3.2.10.01.02.007</code>).</li>
                        <li><b>Nomor Register Aman:</b> Nomor register panjang dari NIBAR (seperti <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">.2023.001</code>) otomatis diambil nomor urutnya (<code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">1</code>) sehingga tidak error integer overflow.</li>
                        <li><b>Pembersihan Nilai Rupiah:</b> Format angka dengan desimal sen seperti <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">6.808.080,00</code> otomatis diparsing menjadi <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">6808080</code> tanpa penggelembungan nilai.</li>
                        <li><b>Pencocokan Ruangan &amp; Kategori:</b> Lokasi yang cocok dengan data Ruangan akan langsung otomatis terhubung.</li>
                    </ul>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('aset.bmd.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Batal</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.01]">
                        <span class="material-symbols-outlined text-lg">cloud_upload</span>
                        <span>Mulai Impor Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PANDUAN KOLOM BMD -->
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">view_column</span>
                    Struktur Kolom File BMD Anda
                </h3>
                <span class="text-[11px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">22 Kolom Standar</span>
            </div>

            <p class="text-xs text-slate-500 mb-4">Berikut urutan kolom dari kiri ke kanan yang dibaca oleh sistem sesuai berkas ekspor SIMBADA / BMD Anda:</p>

            <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1">
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[11px]">A-G</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Kode 108 (Pecahan 7 Kolom)</p>
                        <p class="text-[11px] text-slate-500 truncate">1 | 3 | 2 | 10 | 01 | 02 | 007</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Wajib</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">H</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Nama Barang</p>
                        <p class="text-[11px] text-slate-500 truncate">Contoh: Lap Top, Personal Computer, Meja</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Wajib</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">I</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">NIBAR Lama / Kode Barang</p>
                        <p class="text-[11px] text-slate-500 truncate">1.3.02.10.01.002.000007.19910...</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">J</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Nomor Register / NIBAR Baru</p>
                        <p class="text-[11px] text-slate-500 truncate">1.3.02.10.01.002.000007.2023.001</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Wajib</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">K</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Spesifikasi Nama Barang</p>
                        <p class="text-[11px] text-slate-500 truncate">Lap Top / Cadangan jika nama utama kosong</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">L</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Spesifikasi Lainnya / Bahan</p>
                        <p class="text-[11px] text-slate-500 truncate">Besi / Kayu / Logam / Plastik</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">M</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Merek / Tipe</p>
                        <p class="text-[11px] text-slate-500 truncate">Merk: HP NOTEBOOK 14-CF2019TU</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">N</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Lokasi / Ruangan</p>
                        <p class="text-[11px] text-slate-500 truncate">Desa/Mantup / Ruang Tata Usaha</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">Q-R</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Harga Satuan &amp; Nilai Perolehan</p>
                        <p class="text-[11px] text-slate-500 truncate">6.808.080,00</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Wajib</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">S</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Cara Perolehan</p>
                        <p class="text-[11px] text-slate-500 truncate">Pengadaan APBD / Bantuan Sarpras</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">T</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Tanggal / Tahun Perolehan</p>
                        <p class="text-[11px] text-slate-500 truncate">2023 atau 19910206 (YYYYMMDD)</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[11px]">V</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">Keterangan / Dokumen</p>
                        <p class="text-[11px] text-slate-500 truncate">(DPA/B-APBD)...</p>
                    </div>
                    <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('aset.bmd.templates') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                    <span>Lihat Rincian Lengkap &amp; Contoh Template</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
