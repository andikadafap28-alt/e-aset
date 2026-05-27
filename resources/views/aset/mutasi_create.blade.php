@extends('layouts.app')

@section('header_title', 'Buat Mutasi Aset')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Buat Mutasi Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Catat perpindahan lokasi atau penanggung jawab aset</p>
    </div>
    <a href="{{ route('aset.mutasi.items') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar Mutasi
    </a>
</div>

@if($errors->any())
<div class="bg-rose-50 text-rose-600 px-4 py-3 rounded-lg text-sm font-medium mb-6 border border-rose-200">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-3xl">
    <form action="{{ route('aset.mutasi.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <!-- Asset Selection -->
            <div>
                <label for="asset_id" class="block text-sm font-medium text-slate-700 mb-1">Aset yang Dimutasi <span class="text-rose-500">*</span></label>
                <select name="asset_id" id="asset_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">-- Pilih Aset --</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" data-lokasi="{{ $asset->location }}" data-pj="{{ $asset->penanggung_jawab }}">
                            {{ $asset->asset_code }} - {{ $asset->name }} (Lokasi saat ini: {{ $asset->location }})
                        </option>
                    @endforeach
                </select>
                <div id="current_info" class="mt-2 text-xs text-slate-500 hidden bg-slate-50 p-3 rounded border border-slate-100">
                    <span class="font-semibold block mb-1">Informasi Saat Ini:</span>
                    Lokasi: <span id="current_location" class="font-medium text-slate-700"></span><br>
                    Penanggung Jawab: <span id="current_pj" class="font-medium text-slate-700"></span>
                </div>
            </div>

            <!-- Tanggal Mutasi -->
            <div>
                <label for="tanggal_mutasi" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mutasi <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" value="{{ old('tanggal_mutasi', date('Y-m-d')) }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <!-- Lokasi Baru -->
                <div>
                    <label for="lokasi_baru" class="block text-sm font-medium text-slate-700 mb-1">Lokasi Tujuan Baru <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi_baru" id="lokasi_baru" value="{{ old('lokasi_baru') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Ruang Rapat Lt 2">
                </div>

                <!-- Penanggung Jawab Baru -->
                <div>
                    <label for="penanggung_jawab_baru" class="block text-sm font-medium text-slate-700 mb-1">Penanggung Jawab Baru <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text" name="penanggung_jawab_baru" id="penanggung_jawab_baru" value="{{ old('penanggung_jawab_baru') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Bpk. Agus">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-slate-700 mb-1">Keterangan / Alasan Mutasi <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                <textarea name="keterangan" id="keterangan" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Perpindahan staf, perombakan ruangan..."></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('aset.mutasi.items') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Mutasi
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetSelect = document.getElementById('asset_id');
        const currentInfo = document.getElementById('current_info');
        const locSpan = document.getElementById('current_location');
        const pjSpan = document.getElementById('current_pj');

        assetSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if(this.value) {
                const loc = selected.getAttribute('data-lokasi');
                const pj = selected.getAttribute('data-pj');
                locSpan.textContent = loc || '-';
                pjSpan.textContent = pj || '-';
                currentInfo.classList.remove('hidden');
            } else {
                currentInfo.classList.add('hidden');
            }
        });
    });
</script>
@endsection
