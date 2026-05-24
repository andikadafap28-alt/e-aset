@extends('layouts.app')

@section('header_title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pengaturan Sistem</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola konfigurasi dan keamanan aplikasi</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm font-medium mb-6 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Keamanan Halaman Publik
            </h3>
            <p class="text-sm text-slate-500 mt-1">Kata sandi ini digunakan untuk membatasi akses masyarakat umum saat memindai QR Code aset. Hanya yang memiliki kata sandi ini yang dapat melihat rincian internal seperti Harga Perolehan dan Dokumen Pengadaan.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="public_asset_password" class="block text-sm font-medium text-slate-700 mb-2">Kata Sandi (Password) Publik</label>
                <div class="relative max-w-md">
                    <input type="text" id="public_asset_password" name="public_asset_password" value="{{ old('public_asset_password', $password) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-10" required minlength="4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                </div>
                @error('public_asset_password')
                    <p class="text-rose-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-500 mt-2">Pastikan memberitahukan kata sandi baru ke seluruh staf terkait jika ada perubahan.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
