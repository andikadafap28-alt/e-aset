@extends('layouts.app')

@section('header_title', 'Kartu Inventaris Ruangan (KIR)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showImportModal: false, targetRoomId: '', targetRoomName: 'Semua Ruangan' }">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                    Inventaris Ruangan (Non-BMD)
                </span>
                <span class="text-xs text-slate-400">• Update Tiap 6 Bulan Sekali</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Kartu Inventaris Ruangan (KIR)</h2>
            <p class="text-sm text-slate-500 mt-1 max-w-3xl">
                Daftar ruangan Puskesmas Mantup untuk inventarisasi barang dan alat perlengkapan ruangan. Barang yang dicatat di sini terpisah dari daftar aset utama (BMD/KIB) sehingga rapi dan tidak mencemari nilai kapitalisasi aset.
            </p>
        </div>

        <!-- Action Buttons Top -->
        <div class="flex items-center flex-wrap gap-2">
            <!-- Rekapitulasi KIR -->
            <a href="{{ route('kir.rekap') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px] text-blue-600">bar_chart</span>
                <span>Rekapan Total Alat</span>
            </a>

            <!-- Download Template Excel -->
            <a href="{{ route('kir.template') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all" title="Download template format resmi Excel">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">download</span>
                <span>Download Template</span>
            </a>

            <!-- Import Excel -->
            <button type="button" @click="showImportModal = true; targetRoomId = ''; targetRoomName = 'Semua Ruangan'" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 shadow-sm shadow-purple-600/20 transition-all">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                <span>Import Excel KIR</span>
            </button>

            <!-- Cetak Batch Semua Ruangan F4 -->
            <a href="{{ route('kir.print_batch') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-600/20 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Semua KIR (F4)</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Ruangan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalRuangan }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $totalRuanganTerisi }} ruangan terisi KIR</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis Barang</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalJenisBarang }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Item inventaris ruangan</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fisik</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalFisikBarang }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Unit barang terdata</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 shadow-sm">
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Kondisi Baik</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalBaik }}</p>
            <p class="text-[11px] text-emerald-600/70 mt-1">{{ $totalFisikBarang > 0 ? round(($totalBaik / $totalFisikBarang) * 100) : 0 }}% total unit</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/30 shadow-sm">
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Kurang Baik</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalKurangBaik }}</p>
            <p class="text-[11px] text-amber-600/70 mt-1">Perlu perbaikan ringan</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-rose-100 bg-rose-50/30 shadow-sm">
            <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Rusak Berat</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $totalRusakBerat }}</p>
            <p class="text-[11px] text-rose-600/70 mt-1">Siap usul afkir/mutasi</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white p-4 rounded-2xl shadow-sm col-span-2 md:col-span-1 flex flex-col justify-between">
            <div class="flex items-center gap-1.5 text-xs text-indigo-300 font-semibold">
                <span class="material-symbols-outlined text-[16px]">print</span>
                Standar KIR
            </div>
            <div class="mt-2">
                <p class="text-base font-bold text-white leading-tight">Ukuran F4</p>
                <p class="text-[11px] text-indigo-200">216 x 330 mm</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('kir.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama ruangan atau penanggung jawab..." class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            <div class="w-full md:w-56">
                <select name="type" class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Tipe Ruangan</option>
                    <option value="Ruangan" {{ $type === 'Ruangan' ? 'selected' : '' }}>Ruangan Puskesmas</option>
                    <option value="Puskesmas" {{ $type === 'Puskesmas' ? 'selected' : '' }}>Gedung Puskesmas</option>
                    <option value="Pustu" {{ $type === 'Pustu' ? 'selected' : '' }}>Pustu</option>
                    <option value="Ponkesdes" {{ $type === 'Ponkesdes' ? 'selected' : '' }}>Ponkesdes</option>
                </select>
            </div>

            <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                Filter
            </button>
            @if($search || $type)
                <a href="{{ route('kir.index') }}" class="w-full md:w-auto px-4 py-2.5 text-slate-500 hover:text-slate-800 text-sm font-semibold rounded-xl transition-all text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Daftar Ruangan KIR -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base">Daftar Ruangan & Inventaris KIR</h3>
            <span class="text-xs text-slate-500 font-medium">Menampilkan {{ $rooms->count() }} ruangan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold w-12 text-center">No</th>
                        <th class="p-4 font-semibold">Nama Ruangan</th>
                        <th class="p-4 font-semibold">Penanggung Jawab</th>
                        <th class="p-4 font-semibold text-center">Jenis Barang</th>
                        <th class="p-4 font-semibold text-center">Total Fisik</th>
                        <th class="p-4 font-semibold text-center">Rincian Kondisi</th>
                        <th class="p-4 font-semibold text-right min-w-[280px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $index => $room)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 text-center text-xs font-semibold text-slate-400">
                            {{ $index + 1 }}
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kir.room_items', $room->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm">
                                    {{ $room->name }}
                                </a>
                                @if($room->kir_items_count > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Terisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500">
                                        Kosong
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                                <span>{{ $room->type ?? 'Ruangan' }}</span>
                                @if($room->description)
                                    <span>•</span>
                                    <span class="truncate max-w-xs">{{ $room->description }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 text-sm text-slate-600">
                            {{ $room->penanggung_jawab ?: '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $room->kir_items_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                                {{ $room->kir_items_count }} jenis
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-900 text-sm">
                                {{ $room->kir_items_sum_jumlah ?? 0 }}
                            </span>
                            <span class="text-xs text-slate-400">unit</span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1.5 text-[11px] font-semibold">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Baik: {{ $room->kir_items_sum_kondisi_baik ?? 0 }}">
                                    B: {{ $room->kir_items_sum_kondisi_baik ?? 0 }}
                                </span>
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800" title="Kurang Baik: {{ $room->kir_items_sum_kondisi_kurang_baik ?? 0 }}">
                                    KB: {{ $room->kir_items_sum_kondisi_kurang_baik ?? 0 }}
                                </span>
                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800" title="Rusak Berat: {{ $room->kir_items_sum_kondisi_rusak_berat ?? 0 }}">
                                    RB: {{ $room->kir_items_sum_kondisi_rusak_berat ?? 0 }}
                                </span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <!-- Tombol Lihat Barang (Utama) -->
                                <a href="{{ route('kir.room_items', $room->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm" title="Lihat semua barang di ruangan {{ $room->name }}">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    <span>Lihat Barang</span>
                                </a>

                                <!-- Tombol Cetak KIR F4 -->
                                <a href="{{ route('kir.print', $room->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors" title="Cetak KIR Kertas F4">
                                    <span class="material-symbols-outlined text-[16px] text-slate-600">print</span>
                                    <span>Cetak</span>
                                </a>

                                <!-- Tombol Download Template Ruangan Ini -->
                                <a href="{{ route('kir.template', ['room_id' => $room->id]) }}" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors" title="Download Format Excel Ruangan Ini">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </a>

                                <!-- Tombol Import Excel Ruangan Ini -->
                                <button type="button" @click="showImportModal = true; targetRoomId = '{{ $room->id }}'; targetRoomName = '{{ addslashes($room->name) }}'" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-600 hover:bg-purple-50 hover:text-purple-700 transition-colors" title="Import File Excel ke Ruangan Ini">
                                    <span class="material-symbols-outlined text-[18px]">upload</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3">
                                <span class="material-symbols-outlined text-3xl">meeting_room</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak ada ruangan ditemukan</h4>
                            <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau filter tipe ruangan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Import Excel KIR -->
    <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showImportModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-purple-400">upload_file</span>
                    <h3 class="font-bold text-base">Import File Excel KIR</h3>
                </div>
                <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-white transition-colors text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('kir.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs leading-relaxed">
                        <strong>💡 Khusus Barang Ruangan (KIR):</strong><br>
                        File yang diunggah akan langsung mengisi daftar inventaris ruangan (KIR) tanpa mencemari daftar Aset Tetap/BMD Puskesmas.
                    </div>

                    <!-- Target Ruangan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Ruangan</label>
                        <select name="room_id" x-model="targetRoomId" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="">-- Otomatis Sesuai Kolom Ruangan di Excel --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->type }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih ruangan target jika file Excel hanya berisi barang untuk 1 ruangan tertentu.</p>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih File Excel (.xlsx / .xls / .csv) <span class="text-rose-500">*</span></label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-dashed border-slate-300 rounded-xl p-2 bg-slate-50 cursor-pointer">
                    </div>

                    <!-- Opsi Timpa Data (Update 6 Bulan Sekali) -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="replace_existing" value="1" checked class="mt-0.5 rounded text-purple-600 focus:ring-purple-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Perbarui Data Berkala (Gantikan data lama di ruangan)</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Centang opsi ini jika Anda melakukan pembaruan berkala tiap 6 bulan sekali agar data inventaris ruangan langsung tersinkronisasi bersih.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Download Template Link -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Belum punya formatnya?</span>
                        <a :href="'{{ url('/kir/template') }}' + (targetRoomId ? '?room_id=' + targetRoomId : '')" class="text-purple-600 hover:text-purple-800 font-semibold underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Download Template Excel
                        </a>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-sm shadow-purple-600/20">
                        Unggah &amp; Proses KIR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
