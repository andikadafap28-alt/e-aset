@extends('layouts.app')

@section('header_title', 'Lembar Pemeliharaan Ruangan')

@section('content')
<div class="space-y-6" x-data="{
    selected: [],
    allIds: {{ json_encode($rooms->pluck('id')->toArray()) }},
    docDate: '{{ $defaultDate ?? '2026-01-02' }}',
    targetRows: 24,
    toggleAll() {
        if (this.selected.length === this.allIds.length) {
            this.selected = [];
        } else {
            this.selected = [...this.allIds];
        }
    },
    printSelected() {
        if (this.selected.length === 0) {
            alert('Pilih minimal satu ruangan untuk dicetak.');
            return;
        }
        window.open('{{ route('rooms.print_batch_pemeliharaan_ruangan') }}?ids=' + this.selected.join(',') + '&date=' + this.docDate + '&rows=' + this.targetRows, '_blank');
    },
    printAll() {
        window.open('{{ route('rooms.print_batch_pemeliharaan_ruangan') }}?date=' + this.docDate + '&rows=' + this.targetRows, '_blank');
    },
    printSingle(roomId) {
        window.open('{{ url('/rooms') }}/' + roomId + '/pemeliharaan-ruangan?date=' + this.docDate + '&rows=' + this.targetRows, '_blank');
    }
}">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-10 -translate-y-10 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold tracking-wide backdrop-blur-md mb-2">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Standar Kertas A4 (21 cm x 29.7 cm) &bull; Puskesmas Mantup
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Lembar Pemeliharaan Ruangan</h1>
                <p class="text-blue-100/90 text-sm mt-1 max-w-2xl">
                    Cetak lembar kontrol fisik sarana, prasarana, dan alat untuk ditempel di setiap ruangan. Tabel dipress rapi dan dilengkapi kolom tanda tangan Kepala Puskesmas serta Pengurus Barang Pembantu.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="printAll()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-900/30 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Semua Ruangan ({{ $rooms->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Informasi Penting & Pengaturan Tanggal -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-xs md:text-sm text-slate-700">
                <span class="font-bold text-slate-900">Pejabat Penandatangan:</span> <strong>dr. Muhamad Sunaryadi (NIP. 19690313 200212 1 007)</strong> &bull; Kepala Puskesmas Mantup & dan <strong>Andika Dafa Penta P. (NIP. 19990728 202504 1 003)</strong> &bull; Pengurus Barang Pembantu.
            </div>
        </div>

        <!-- Setting Tanggal & Baris Default -->
        <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-lg border border-slate-200 flex-wrap">
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">Tanggal Lembar:</label>
                <input type="date" x-model="docDate" class="text-xs font-bold px-2 py-1 bg-white border border-slate-300 rounded shadow-sm focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">Kepadatan Tabel:</label>
                <select x-model="targetRows" class="text-xs font-bold px-2 py-1 bg-white border border-slate-300 rounded shadow-sm focus:ring-1 focus:ring-blue-500">
                    <option value="18">18 Baris (Longgar)</option>
                    <option value="20">20 Baris (Standar)</option>
                    <option value="22">22 Baris (Proporsional)</option>
                    <option value="24">24 Baris (Rekomendasi A4)</option>
                    <option value="26">26 Baris (Padat A4)</option>
                </select>
                <div class="flex items-center gap-1">
                    <input type="number" min="1" max="45" x-model.number="targetRows" class="w-14 text-xs font-bold px-2 py-1 bg-white border border-slate-300 rounded shadow-sm focus:ring-1 focus:ring-blue-500 text-center" title="Atau ketik angka manual">
                    <span class="text-xs text-slate-500 font-medium">baris</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Batch Action Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Toolbar -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" @change="toggleAll()" :checked="selected.length === allIds.length && allIds.length > 0" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <span class="text-sm font-semibold text-slate-700">Pilih Semua Ruangan</span>
                </label>
                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-200 text-slate-700 font-bold" x-text="selected.length + ' dipilih'"></span>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Batch Print Button -->
                <button type="button" @click="printSelected()" :disabled="selected.length === 0" :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-200 text-slate-500' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm cursor-pointer'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Ruangan Terpilih
                </button>

                <!-- Search Form -->
                <form action="{{ route('rooms.lembar_pemeliharaan') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama ruangan..." class="w-56 sm:w-64 pl-9 pr-4 py-1.5 text-xs md:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>
            </div>
        </div>

        <!-- Room Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
            @forelse($rooms as $r)
            <div class="group relative bg-white rounded-xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all p-4 flex flex-col justify-between">
                <div class="flex items-start gap-3">
                    <input type="checkbox" value="{{ $r->id }}" x-model="selected" class="w-4 h-4 mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-800 text-sm truncate group-hover:text-blue-600 transition-colors" title="{{ $r->name }}">
                                {{ $r->name }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            PJ: <span class="text-slate-700 font-medium">{{ $r->penanggung_jawab ?: '-' }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                {{ $r->assets_count }} Aset
                            </span>
                            <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                A4 (21 x 29.7 cm)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="printSingle({{ $r->id }})" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Lembar A4
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="font-medium text-sm">Tidak ada ruangan yang cocok dengan pencarian.</p>
                <a href="{{ route('rooms.lembar_pemeliharaan') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Reset Pencarian</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
