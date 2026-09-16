@extends('layouts.app')
@section('header_title', 'Detail DPP')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Detail DPP -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Dokumen Persiapan Pengadaan (DPP)</h3>
                    <p class="text-sm font-medium text-slate-500 mt-1">Nomor: 000.3.1/{{ $dpp->nomor_surat }}/PPBJ/413.102.5.18/2026</p>
                </div>
                <a href="{{ route('dpp.pdf', $dpp->id) }}" target="_blank" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">print</span> Cetak PDF
                </a>
            </div>

            <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-sm">
                <div>
                    <p class="text-slate-500 mb-1">Tanggal Pembuatan</p>
                    <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($dpp->tanggal_dpp)->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-1">Kode RUP</p>
                    <p class="font-semibold text-slate-800">{{ $dpp->kode_rup }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-slate-500 mb-1">Nama Paket Pekerjaan</p>
                    <p class="font-semibold text-slate-800">{{ $dpp->nama_paket ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-slate-500 mb-1">Spesifikasi Teknis</p>
                    <p class="font-semibold text-slate-800">{{ $dpp->spesifikasi_teknis ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-1">Jumlah</p>
                    <p class="font-semibold text-slate-800">{{ $dpp->jumlah ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-1">Pelaksanaan Pekerjaan</p>
                    <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($dpp->tanggal_mulai)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($dpp->tanggal_selesai)->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-1">Harga Satuan</p>
                    <p class="font-semibold text-slate-800">Rp. {{ number_format($dpp->harga_satuan, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-1">Pagu Anggaran</p>
                    <p class="font-semibold text-slate-800">Rp. {{ number_format($dpp->pagu_anggaran, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Lampiran Dokumen -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Lampiran Dokumen</h3>
            <p class="text-xs text-slate-500 mb-6">Unggah dokumen terkait seperti Surat Pesanan, BAST, dll agar tertata rapi.</p>

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-600 p-3 rounded-lg mb-4 text-xs font-medium border border-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 text-rose-600 p-3 rounded-lg mb-4 text-xs border border-rose-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dpp.attachments.upload', $dpp->id) }}" method="POST" enctype="multipart/form-data" class="mb-6 space-y-4">
                @csrf
                <div>
                    <select name="jenis_file" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>Pilih Jenis Dokumen</option>
                        <option value="Surat Pesanan">Surat Pesanan</option>
                        <option value="BAST">BAST</option>
                        <option value="Invoice">Invoice / Faktur</option>
                        <option value="Kuitansi">Kuitansi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <input type="file" name="file" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                    Upload Dokumen
                </button>
            </form>

            <div class="space-y-3">
                @forelse($dpp->attachments as $attachment)
                    <div class="flex items-center justify-between p-3 border border-slate-100 bg-slate-50 rounded-xl">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-xs font-bold text-slate-700">{{ $attachment->jenis_file }}</p>
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate block" title="{{ $attachment->file_name }}">
                                {{ $attachment->file_name }}
                            </a>
                        </div>
                        <form action="{{ route('dpp.attachments.destroy', $attachment->id) }}" method="POST" onsubmit="return confirm('Hapus lampiran ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-slate-400 hover:text-red-600 p-1 transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-4 text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                        Belum ada lampiran dokumen.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
