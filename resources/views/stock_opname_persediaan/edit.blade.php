@extends('layouts.app')
@section('title', 'Edit Berita Acara Stock Opname Persediaan')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('ba-stock-opname.index') }}" class="hover:text-blue-600 transition-colors">Berita Acara Stok Opname</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Edit Data</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Berita Acara Stock Opname Persediaan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui nilai hasil stock opname untuk Berita Acara tanggal {{ \Carbon\Carbon::parse($record->tanggal_opname)->locale('id')->isoFormat('D MMMM Y') }}.</p>
        </div>
        <a href="{{ route('ba-stock-opname.index') }}" class="px-4 py-2 border border-slate-300 hover:bg-slate-100 text-slate-700 font-medium text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
            <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                <span class="material-symbols-outlined text-[18px]">error</span>
                Mohon periksa kesalahan input berikut:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ba-stock-opname.update', $record->id) }}" method="POST" id="formStockOpname" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- CARD 1: INFORMASI BERITA ACARA -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">1. Tanggal Pelaksanaan &amp; Nomor Berita Acara</h2>
                    <p class="text-xs text-slate-500">Pilih tanggal dilakukannya stock opname untuk ditampilkan secara dinamis pada surat.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Stock Opname <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_opname" id="tanggal_opname" value="{{ old('tanggal_opname', $record->tanggal_opname ? $record->tanggal_opname->format('Y-m-d') : '') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-slate-800 font-medium">
                    <p class="text-[11px] text-slate-400 mt-1">Hari, tanggal terbilang, dan bulan akan dibuat otomatis di dokumen.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $record->nomor_surat) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all font-mono text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Format nomor surat resmi Puskesmas Mantup.</p>
                </div>
            </div>
        </div>

        <!-- CARD 2: INPUT NILAI REKENING UMUM / NON-MEDIS -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">2. Nilai Persediaan Umum / Non-Medis (ATK, Cetak, Pos, Komputer)</h2>
                    <p class="text-xs text-slate-500">Masukkan nilai fisik hasil stok opname dalam satuan Rupiah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- ATK -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">1. Alat Tulis Kantor (ATK)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                        <input type="number" step="0.01" name="atk" id="input_atk" value="{{ old('atk', $record->atk) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Bahan Cetak -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">2. Bahan Cetak</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                        <input type="number" step="0.01" name="bahan_cetak" id="input_bahan_cetak" value="{{ old('bahan_cetak', $record->bahan_cetak) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Benda Pos -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">3. Benda Pos</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                        <input type="number" step="0.01" name="benda_pos" id="input_benda_pos" value="{{ old('benda_pos', $record->benda_pos) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Bahan Komputer -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">4. Bahan Komputer</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                        <input type="number" step="0.01" name="bahan_komputer" id="input_bahan_komputer" value="{{ old('bahan_komputer', $record->bahan_komputer) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: INPUT NILAI OBAT & BMHP -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">medication</span>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">3. Nilai Persediaan Obat &amp; BMHP (BLUD, APBD, &amp; APBN)</h2>
                    <p class="text-xs text-slate-500">Masing-masing terdiri dari 2 inputan (Obat dan BMHP), yang di halaman pertama Berita Acara otomatis dijumlahkan dan hanya tertulis <strong>"Obat BLUD"</strong>, <strong>"Obat APBD"</strong>, dan <strong>"Obat APBN"</strong>.</p>
                </div>
            </div>

            <!-- BLUD -->
            <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">5</span>
                        <h3 class="font-bold text-blue-900 text-sm">Persediaan Obat &amp; BMHP BLUD</h3>
                    </div>
                    <div class="text-xs font-bold text-blue-800 bg-blue-100/80 px-3 py-1 rounded-lg flex items-center gap-1.5">
                        <span>Total Obat BLUD:</span>
                        <span id="disp_total_blud" class="font-mono text-sm text-blue-950">Rp. 0,00</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Obat-Obatan BLUD</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="obat_blud" id="input_obat_blud" value="{{ old('obat_blud', $record->obat_blud) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Bahan Medis Habis Pakai (BMHP) BLUD</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="bmhp_blud" id="input_bmhp_blud" value="{{ old('bmhp_blud', $record->bmhp_blud) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- APBD -->
            <div class="p-4 bg-emerald-50/60 rounded-xl border border-emerald-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">6</span>
                        <h3 class="font-bold text-emerald-900 text-sm">Persediaan Obat &amp; BMHP APBD</h3>
                    </div>
                    <div class="text-xs font-bold text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-lg flex items-center gap-1.5">
                        <span>Total Obat APBD:</span>
                        <span id="disp_total_apbd" class="font-mono text-sm text-emerald-950">Rp. 0,00</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Obat-Obatan APBD</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="obat_apbd" id="input_obat_apbd" value="{{ old('obat_apbd', $record->obat_apbd) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Bahan Medis Habis Pakai (BMHP) APBD</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="bmhp_apbd" id="input_bmhp_apbd" value="{{ old('bmhp_apbd', $record->bmhp_apbd) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- APBN -->
            <div class="p-4 bg-purple-50/60 rounded-xl border border-purple-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex items-center justify-center">7</span>
                        <h3 class="font-bold text-purple-900 text-sm">Persediaan Obat &amp; BMHP APBN</h3>
                    </div>
                    <div class="text-xs font-bold text-purple-800 bg-purple-100/80 px-3 py-1 rounded-lg flex items-center gap-1.5">
                        <span>Total Obat APBN:</span>
                        <span id="disp_total_apbn" class="font-mono text-sm text-purple-950">Rp. 0,00</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Obat-Obatan APBN</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="obat_apbn" id="input_obat_apbn" value="{{ old('obat_apbn', $record->obat_apbn) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Bahan Medis Habis Pakai (BMHP) APBN</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp.</span>
                            <input type="number" step="0.01" name="bmhp_apbn" id="input_bmhp_apbn" value="{{ old('bmhp_apbn', $record->bmhp_apbn) }}" placeholder="0.00" oninput="calcTotals()" class="w-full pl-10 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL KESELURUHAN (LIVE DISPLAY) -->
            <div class="p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-md">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Keseluruhan Nilai Persediaan (JUMLAH)</span>
                    <p class="text-xs text-slate-300 mt-0.5">Penjumlahan otomatis seluruh rekening di atas.</p>
                </div>
                <div class="text-right">
                    <span id="disp_grand_total" class="text-2xl md:text-3xl font-bold font-mono text-emerald-400">Rp. 0,00</span>
                </div>
            </div>
        </div>

        <!-- CARD 4: PENANDATANGAN DOKUMEN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">draw</span>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">4. Pejabat Penandatangan Berita Acara</h2>
                    <p class="text-xs text-slate-500">Disesuaikan untuk Kepala Puskesmas &amp; Pengurus Barang Pembantu.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pihak 1 (Yang Memeriksa) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-blue-600">verified_user</span>
                        Pihak 1: Yang Memeriksa (Kepala Puskesmas)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pejabat</label>
                        <input type="text" name="nama_pemeriksa" value="{{ old('nama_pemeriksa', $record->nama_pemeriksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">NIP</label>
                        <input type="text" name="nip_pemeriksa" value="{{ old('nip_pemeriksa', $record->nip_pemeriksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jabatan</label>
                        <input type="text" name="jabatan_pemeriksa" value="{{ old('jabatan_pemeriksa', $record->jabatan_pemeriksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800">
                    </div>
                </div>

                <!-- Pihak 2 (Yang Diperiksa) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">badge</span>
                        Pihak 2: Yang Diperiksa (Pengurus Barang)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pejabat</label>
                        <input type="text" name="nama_diperiksa" value="{{ old('nama_diperiksa', $record->nama_diperiksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">NIP</label>
                        <input type="text" name="nip_diperiksa" value="{{ old('nip_diperiksa', $record->nip_diperiksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jabatan</label>
                        <input type="text" name="jabatan_diperiksa" value="{{ old('jabatan_diperiksa', $record->jabatan_diperiksa) }}" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('ba-stock-opname.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-100 transition-colors font-medium text-sm">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-md shadow-blue-600/20 font-bold text-sm flex items-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Perbarui &amp; Lihat Dokumen Cetak
            </button>
        </div>
    </form>
</div>

<script>
    function formatRupiah(number) {
        return 'Rp. ' + new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }

    function parseVal(id) {
        const el = document.getElementById(id);
        if (!el || !el.value) return 0;
        const val = parseFloat(el.value);
        return isNaN(val) ? 0 : val;
    }

    function calcTotals() {
        const atk = parseVal('input_atk');
        const cetak = parseVal('input_bahan_cetak');
        const pos = parseVal('input_benda_pos');
        const komp = parseVal('input_bahan_komputer');

        // BLUD
        const obatBlud = parseVal('input_obat_blud');
        const bmhpBlud = parseVal('input_bmhp_blud');
        const totalBlud = obatBlud + bmhpBlud;
        document.getElementById('disp_total_blud').innerText = formatRupiah(totalBlud);

        // APBD
        const obatApbd = parseVal('input_obat_apbd');
        const bmhpApbd = parseVal('input_bmhp_apbd');
        const totalApbd = obatApbd + bmhpApbd;
        document.getElementById('disp_total_apbd').innerText = formatRupiah(totalApbd);

        // APBN
        const obatApbn = parseVal('input_obat_apbn');
        const bmhpApbn = parseVal('input_bmhp_apbn');
        const totalApbn = obatApbn + bmhpApbn;
        document.getElementById('disp_total_apbn').innerText = formatRupiah(totalApbn);

        // Grand Total
        const grandTotal = atk + cetak + pos + komp + totalBlud + totalApbd + totalApbn;
        document.getElementById('disp_grand_total').innerText = formatRupiah(grandTotal);
    }

    document.addEventListener('DOMContentLoaded', () => {
        calcTotals();
    });
</script>
@endsection
