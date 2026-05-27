@extends('layouts.app')

@section('header_title', 'Export Laporan - ' . $nama_kategori)

@section('content')
<div class="max-w-md mx-auto">
    <a href="/{{ $kategori_besar }}/items" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar {{ $nama_kategori }}
    </a>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Unduh Laporan</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih periode dan jenis laporan yang akan diekstrak ke dalam format Excel.</p>
        </div>

        <form action="/{{ $kategori_besar }}/export/download" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">Periode Bulan <span class="text-rose-500">*</span></label>
                <input type="month" name="bulan" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">Peruntukan Laporan <span class="text-rose-500">*</span></label>
                
                <div class="space-y-3 mt-2">
                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                        <input type="radio" name="jenis_laporan" value="internal" required class="mt-0.5 w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <div>
                            <span class="block font-semibold text-slate-800 text-sm">Laporan Internal Puskesmas</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Mencatat seluruh mutasi barang fisik, termasuk barang hutang.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                        <input type="radio" name="jenis_laporan" value="dinas" class="mt-0.5 w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <div>
                            <span class="block font-semibold text-slate-800 text-sm">Laporan Dinas (SPJ)</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Hanya menampilkan transaksi yang sudah lunas. Barang hutang akan diabaikan.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="w-full px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-medium shadow-sm flex items-center justify-center gap-2 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Proses & Ekstrak Excel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection