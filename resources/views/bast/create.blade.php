@extends('layouts.app')
@section('title', 'Buat BAST Baru')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Buat BAST Baru</h1>
        <a href="{{ route('bast.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-medium text-sm rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl border border-red-200">
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500">assignment</span>
            <h2 class="text-lg font-semibold text-slate-800">Form Serah Terima Barang</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('bast.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penyerahan <span class="text-red-500">*</span></label>
                        <input type="date" name="handover_date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Pegawai (Penerima) <span class="text-red-500">*</span></label>
                        <select name="employee_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], ucwords(strtolower($employee->name), " .-")) }} - {{ $employee->jabatan ?: 'Tanpa Jabatan' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Barang (Aset) <span class="text-red-500">*</span></label>
                        <select name="asset_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->asset_code }} | {{ $asset->name }} | {{ $asset->category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Keperluan <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Contoh: Pelayanan Ponkesdes/Pustu Desa Rumpuk" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan (Lokasi)</label>
                        <input type="text" name="keterangan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Contoh: Ponkesdes Rumpuk">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Sumber Dana</label>
                    <input type="text" name="sumber_dana" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Contoh: JKN, BOK, dll">
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan & Lihat BAST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
