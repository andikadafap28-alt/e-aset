@extends('layouts.app')
@section('header_title', 'Buat DPP Baru')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl mx-auto" x-data="dppForm()">
    <div class="mb-6 border-b border-slate-100 pb-4">
        <h3 class="text-lg font-bold text-slate-800">Form Pembuatan Dokumen Persiapan Pengadaan (DPP)</h3>
        <p class="text-sm text-slate-500">Isi detail pengadaan untuk membuat dokumen DPP.</p>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 text-sm border border-rose-100">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dpp.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Surat (Bagian XXX)</label>
                <div class="flex items-center">
                    <span class="bg-slate-100 border border-slate-300 border-r-0 text-slate-500 text-sm px-3 py-2 rounded-l-xl">000.3.1/</span>
                    <input type="text" name="nomor_surat" required class="w-full border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="333">
                    <span class="bg-slate-100 border border-slate-300 border-l-0 text-slate-500 text-sm px-3 py-2 rounded-r-xl">/PPBJ/413.102.5.18/2026</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode RUP</label>
                <select name="kode_rup" x-model="kode_rup" @change="onRupChange" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled selected>Pilih Kode RUP</option>
                    <option value="67261766">67261766 - Obat-obatan (JKN)</option>
                    <option value="67261750">67261750 - Bahan-bahan lainnya (JKN)</option>
                    <option value="Manual">Lainnya (Isi Manual)</option>
                </select>
                <template x-if="kode_rup === 'Manual'">
                    <input type="text" name="kode_rup_manual" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mt-2" placeholder="Masukkan Kode RUP Manual">
                </template>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Paket Pekerjaan</label>
            <input type="text" name="nama_paket" x-model="nama_paket" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Belanja Barang dan Jasa (Belanja Bahan Obat-obatan(JKN))">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Jumlah (Spesifikasi)</label>
                <input type="text" name="jumlah" x-model="jumlah" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1 Paket">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                <input type="number" name="harga_satuan" x-model="harga_satuan" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Pagu Anggaran (Rp)</label>
                <input type="number" name="pagu_anggaran" x-model="pagu_anggaran" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Spesifikasi Teknis</label>
            <textarea name="spesifikasi_teknis" x-model="spesifikasi_teknis" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Belanja Obat-obatan"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pembuatan DPP</label>
                <input type="date" name="tanggal_dpp" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tgl Pelaksanaan (Mulai)</label>
                <input type="date" name="tanggal_mulai" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tgl Pelaksanaan (Selesai)</label>
                <input type="date" name="tanggal_selesai" required class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <a href="{{ route('dpp.index') }}" class="px-5 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">Simpan & Lanjutkan</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dppForm', () => ({
            kode_rup: '',
            nama_paket: '',
            spesifikasi_teknis: '',
            jumlah: '',
            harga_satuan: '',
            pagu_anggaran: '',
            rups: {
                '67261766': {
                    nama_paket: 'Belanja Barang dan Jasa (Belanja Bahan Obat-obatan(JKN))',
                    spesifikasi_teknis: 'Belanja Obat-obatan',
                    jumlah: '1 Paket',
                    harga_satuan: 144000000,
                    pagu_anggaran: 144000000
                },
                '67261750': {
                    nama_paket: 'Belanja Barang dan Jasa (Belanja Bahan-bahan lainnya (JKN))',
                    spesifikasi_teknis: 'Belanja Bahan-Bahan Lainnya',
                    jumlah: '1 Paket',
                    harga_satuan: 100000000,
                    pagu_anggaran: 100000000
                }
            },
            onRupChange() {
                if(this.rups[this.kode_rup]) {
                    let data = this.rups[this.kode_rup];
                    this.nama_paket = data.nama_paket;
                    this.spesifikasi_teknis = data.spesifikasi_teknis;
                    this.jumlah = data.jumlah;
                    this.harga_satuan = data.harga_satuan;
                    this.pagu_anggaran = data.pagu_anggaran;
                } else if(this.kode_rup === 'Manual') {
                    this.nama_paket = '';
                    this.spesifikasi_teknis = '';
                    this.jumlah = '';
                    this.harga_satuan = '';
                    this.pagu_anggaran = '';
                }
            }
        }))
    })
</script>
@endsection
