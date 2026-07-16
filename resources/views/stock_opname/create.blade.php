@extends('layouts.app')

@section('header_title', 'Mulai Stock Opname')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('stock-opname.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Sesi Baru Stock Opname</h2>
            <p class="text-slate-500 text-sm mt-1">Pilih lokasi atau ruangan yang akan diaudit.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('stock-opname.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan / Lokasi <span class="text-rose-500">*</span></label>
                    <select name="location" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Ruangan --</option>
                        <option value="all" class="font-bold">Semua Ruangan (Global)</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">Pilih "Semua Ruangan" jika Anda akan melakukan audit keliling ke seluruh fasilitas puskesmas sekaligus.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Audit rutin kuartal 1"></textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Simpan & Mulai Scan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
