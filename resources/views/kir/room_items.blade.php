@extends('layouts.app')

@section('header_title', 'Barang Inventaris: ' . $room->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showImportModal: false, 
    showClearModal: false,
    editItem: { id: null, name: '', merk: '', bahan: 'Campuran', jumlah: 1, kondisi_baik: 1, kondisi_kurang_baik: 0, kondisi_rusak_berat: 0, keterangan: '' }
}">

    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('kir.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[16px]">meeting_room</span>
                    KIR Ruangan
                </a>
                <span>/</span>
                <span class="text-slate-900 font-semibold truncate">{{ $room->name }}</span>
            </div>

            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $room->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    {{ $room->type ?? 'Ruangan' }}
                </span>
            </div>

            <p class="text-xs text-slate-500 mt-1">
                Penanggung Jawab: <strong class="text-slate-700">{{ $room->penanggung_jawab ?: 'Belum ditentukan' }}</strong>
                @if($room->description)
                    • <span class="text-slate-400">{{ $room->description }}</span>
                @endif
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <!-- Kembali -->
            <a href="{{ route('kir.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Daftar Ruangan</span>
            </a>

            <!-- Download Template Excel Ruangan Ini -->
            <a href="{{ route('kir.template', ['room_id' => $room->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all shadow-sm" title="Download Excel Format KIR Ruangan Ini untuk Diedit">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Download Template</span>
            </a>

            <!-- Import Excel ke Ruangan Ini -->
            <button type="button" @click="showImportModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all shadow-sm" title="Upload File Excel KIR yang Telah Diisi">
                <span class="material-symbols-outlined text-[16px]">upload</span>
                <span>Import Excel</span>
            </button>

            <!-- Tambah Barang Manual -->
            <button type="button" @click="showAddModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-600/20">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Tambah Barang</span>
            </button>

            <!-- Cetak Dokumen KIR F4 -->
            <a href="{{ route('kir.print', $room->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak KIR (F4)</span>
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

    <!-- Stat Cards Ruangan Ini -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis Barang</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalJenis }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Item tercatat</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fisik</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalFisik }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Unit barang</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 shadow-sm">
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Baik</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalBaik }}</p>
            <p class="text-[11px] text-emerald-600/70 mt-0.5">{{ $totalFisik > 0 ? round(($totalBaik / $totalFisik) * 100) : 0 }}% unit</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/30 shadow-sm">
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Kurang Baik</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalKurangBaik }}</p>
            <p class="text-[11px] text-amber-600/70 mt-0.5">Perlu perbaikan</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-rose-100 bg-rose-50/30 shadow-sm">
            <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Rusak Berat</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $totalRusakBerat }}</p>
            <p class="text-[11px] text-rose-600/70 mt-0.5">Usul afkir</p>
        </div>
    </div>

    <!-- Switch Ruangan Cepat & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('kir.room_items', $room->id) }}" class="flex items-center gap-2 w-full md:w-auto flex-1">
            <div class="relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, merek, atau bahan di ruangan ini..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-900 transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('kir.room_items', $room->id) }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </form>

        <!-- Dropdown Ganti Ruangan Cepat -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <span class="text-xs text-slate-400 font-medium">Pindah ke:</span>
            <select onchange="if(this.value) window.location.href = '{{ url('/kir/ruangan') }}/' + this.value" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-medium">
                @foreach($allRooms as $r)
                    <option value="{{ $r->id }}" {{ $r->id == $room->id ? 'selected' : '' }}>
                        {{ $r->name }} ({{ $r->kirItems()->count() }} barang)
                    </option>
                @endforeach
            </select>

            @if($items->count() > 0)
                <button type="button" @click="showClearModal = true" class="text-xs text-rose-600 hover:text-rose-800 p-2 rounded-xl hover:bg-rose-50 transition-colors" title="Kosongkan semua barang di ruangan ini">
                    <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Barang KIR di Ruangan Ini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Barang &amp; Alat Inventaris Ruangan</h3>
            <span class="text-xs text-slate-400 font-medium">{{ $items->count() }} barang terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="p-3.5 font-semibold w-12 text-center">No</th>
                        <th class="p-3.5 font-semibold">Jenis Barang / Nama Barang</th>
                        <th class="p-3.5 font-semibold">Merek / Model</th>
                        <th class="p-3.5 font-semibold">Bahan</th>
                        <th class="p-3.5 font-semibold text-center w-24">Jumlah</th>
                        <th class="p-3.5 font-semibold text-center w-36">Keadaan Barang</th>
                        <th class="p-3.5 font-semibold">Keterangan</th>
                        <th class="p-3.5 font-semibold text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($items as $idx => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 text-center font-semibold text-slate-400">
                            {{ $idx + 1 }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-900 text-sm block">{{ $item->name }}</span>
                            <span class="text-[10px] text-slate-400">ID: KIR-{{ $item->id }}</span>
                        </td>
                        <td class="p-3.5 text-slate-700">
                            {{ $item->merk ?: '-' }}
                        </td>
                        <td class="p-3.5 text-slate-700">
                            {{ $item->bahan ?: 'Campuran' }}
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="font-bold text-slate-900 text-sm">{{ $item->jumlah }}</span>
                            <span class="text-[10px] text-slate-400 block">unit</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1 text-[10px] font-semibold">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Baik: {{ $item->kondisi_baik }}">
                                    B: {{ $item->kondisi_baik }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" title="Kurang Baik: {{ $item->kondisi_kurang_baik }}">
                                    KB: {{ $item->kondisi_kurang_baik }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800" title="Rusak Berat: {{ $item->kondisi_rusak_berat }}">
                                    RB: {{ $item->kondisi_rusak_berat }}
                                </span>
                            </div>
                        </td>
                        <td class="p-3.5 text-slate-500">
                            {{ $item->keterangan ?: '-' }}
                        </td>
                        <td class="p-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Tombol Edit -->
                                <button type="button" @click="
                                    editItem = {
                                        id: {{ $item->id }},
                                        name: '{{ addslashes($item->name) }}',
                                        merk: '{{ addslashes($item->merk ?? '') }}',
                                        bahan: '{{ addslashes($item->bahan ?? 'Campuran') }}',
                                        jumlah: {{ $item->jumlah }},
                                        kondisi_baik: {{ $item->kondisi_baik }},
                                        kondisi_kurang_baik: {{ $item->kondisi_kurang_baik }},
                                        kondisi_rusak_berat: {{ $item->kondisi_rusak_berat }},
                                        keterangan: '{{ addslashes($item->keterangan ?? '') }}'
                                    };
                                    showEditModal = true;
                                " class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Barang">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('kir.item.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang {{ addslashes($item->name) }} dari ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Barang">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center">
                            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-3xl">inventory_2</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Belum ada barang di ruangan ini</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                Anda dapat mengunduh format template Excel untuk diisi, atau mengimpor file Excel yang telah Anda miliki, atau menambahkan barang secara manual satu per satu.
                            </p>
                            <div class="flex items-center justify-center gap-2 mt-4 flex-wrap">
                                <a href="{{ route('kir.template', ['room_id' => $room->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                    Download Template Excel
                                </a>
                                <button type="button" @click="showImportModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">upload</span>
                                    Import File Excel
                                </button>
                                <button type="button" @click="showAddModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Tambah Manual
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Barang Manual -->
    <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-400">add_circle</span>
                    <h3 class="font-bold text-sm">Tambah Barang ke {{ $room->name }}</h3>
                </div>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('kir.item.store', $room->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama / Jenis Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Cth: Meja Kerja, Kursi Putar, Jam Dinding, PC AIO..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Merek / Model</label>
                            <input type="text" name="merk" placeholder="Cth: Panasonic, Dell, Polaris..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bahan</label>
                            <input type="text" name="bahan" value="Campuran" list="bahanListAdd" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <datalist id="bahanListAdd">
                                <option value="Campuran">
                                <option value="Kayu">
                                <option value="Besi">
                                <option value="Plastik">
                                <option value="Kaca">
                                <option value="Alumunium">
                                <option value="Kain / Busa">
                            </datalist>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jumlah Fisik (Unit) <span class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah" min="1" value="1" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                            <select name="condition_simple" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="Baik" selected>Baik</option>
                                <option value="Kurang Baik">Kurang Baik</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Mutasi dll.</label>
                        <input type="text" name="keterangan" placeholder="Opsional (cth: Pengadaan 2026, Hibah, dll.)" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Barang -->
    <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-400">edit_note</span>
                    <h3 class="font-bold text-sm">Edit Barang KIR</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/kir/item') }}/' + editItem.id" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama / Jenis Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editItem.name" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Merek / Model</label>
                            <input type="text" name="merk" x-model="editItem.merk" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bahan</label>
                            <input type="text" name="bahan" x-model="editItem.bahan" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jumlah Fisik (Unit) <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah" min="1" x-model="editItem.jumlah" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <!-- Rincian Kondisi Fisik -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                        <span class="text-xs font-bold text-slate-700 block">Rincian Kondisi Fisik:</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-700">Baik</label>
                                <input type="number" name="kondisi_baik" min="0" x-model="editItem.kondisi_baik" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-amber-700">Kurang Baik</label>
                                <input type="number" name="kondisi_kurang_baik" min="0" x-model="editItem.kondisi_kurang_baik" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-rose-700">Rusak Berat</label>
                                <input type="number" name="kondisi_rusak_berat" min="0" x-model="editItem.kondisi_rusak_berat" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Mutasi dll.</label>
                        <input type="text" name="keterangan" x-model="editItem.keterangan" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel Khusus Ruangan Ini -->
    <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showImportModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-400">upload_file</span>
                    <h3 class="font-bold text-sm">Upload File Excel ke {{ $room->name }}</h3>
                </div>
                <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('kir.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <div class="p-6 space-y-4">
                    <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-purple-900 text-xs">
                        Target Ruangan: <strong>{{ $room->name }}</strong><br>
                        Data di file Excel akan langsung diisikan ke ruangan ini secara rapi.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih File Excel (.xlsx / .xls / .csv) <span class="text-rose-500">*</span></label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-dashed border-slate-300 rounded-xl p-2 bg-slate-50 cursor-pointer">
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="replace_existing" value="1" checked class="mt-0.5 rounded text-purple-600 focus:ring-purple-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Perbarui data berkala (Timpa data lama ruangan ini)</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Centang ini jika Anda mengupload format terkini saat update 6 bulan sekali agar daftar barang lama digantikan dengan data baru.</p>
                            </div>
                        </label>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Belum punya formatnya?</span>
                        <a href="{{ route('kir.template', ['room_id' => $room->id]) }}" class="text-purple-600 hover:text-purple-800 font-semibold underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Download Template Ruangan Ini
                        </a>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-sm shadow-purple-600/20">Unggah &amp; Generate KIR</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Kosongkan Ruangan -->
    <div x-show="showClearModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showClearModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Kosongkan Barang Ruangan?</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Semua data barang inventaris di <strong>{{ $room->name }}</strong> ({{ $items->count() }} jenis barang) akan dihapus. Anda dapat mengisi ulang kapan saja via import file Excel.
                </p>
                <form action="{{ route('kir.room.clear', $room->id) }}" method="POST" class="mt-6 flex items-center justify-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showClearModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm">
                        Ya, Kosongkan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
