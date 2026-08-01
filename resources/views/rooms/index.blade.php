@extends('layouts.app')

@section('header_title', 'Master Data Lokasi / Ruangan')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showModal: false, editMode: false, currentId: null, form: {name: '', type: 'Ruangan', penanggung_jawab: '', latitude: '', longitude: '', description: ''} }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Lokasi & Ruangan</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola data Pustu, Ponkesdes, dan Ruangan Puskesmas sebagai referensi lokasi aset.</p>
        </div>
        
        <button @click="showModal = true; editMode = false; form = {name: '', type: 'Ruangan', penanggung_jawab: '', latitude: '', longitude: '', description: ''}" class="text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg shadow-sm shadow-indigo-600/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Lokasi Baru
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 flex items-center gap-3 border border-emerald-100">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Nama Lokasi / Ruangan</th>
                        <th class="p-4 font-semibold">Tipe</th>
                        <th class="p-4 font-semibold">Penanggung Jawab</th>
                        <th class="p-4 font-semibold text-center">Jumlah Aset</th>
                        <th class="p-4 font-semibold text-center">Peta (GIS)</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4">
                            <div class="font-bold text-slate-800">{{ $room->name }}</div>
                            @if($room->description)
                            <div class="text-xs text-slate-500 mt-1">{{ $room->description }}</div>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                {{ $room->type === 'Puskesmas' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $room->type === 'Pustu' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                {{ $room->type === 'Ponkesdes' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $room->type === 'Polindes' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $room->type === 'Ruangan' ? 'bg-slate-100 text-slate-700' : '' }}">
                                {{ $room->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $room->penanggung_jawab ?: '-' }}</td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] h-6 px-2 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                {{ $room->assets_count }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if($room->latitude && $room->longitude)
                                <span class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md text-xs font-medium border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Tersetting
                                </span>
                            @else
                                <span class="text-xs text-slate-400 font-medium">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="showModal = true; editMode = true; currentId = {{ $room->id }}; form = {name: '{{ addslashes($room->name) }}', type: '{{ $room->type }}', penanggung_jawab: '{{ addslashes($room->penanggung_jawab) }}', latitude: '{{ $room->latitude }}', longitude: '{{ $room->longitude }}', description: '{{ addslashes($room->description) }}'}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus lokasi ini? Data aset yang berada di lokasi ini tidak akan dihapus.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 text-sm">Belum ada data lokasi/ruangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900 opacity-60 backdrop-blur-sm"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form :action="editMode ? '/rooms/' + currentId : '{{ route('rooms.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="bg-white px-6 pt-6 pb-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight" x-text="editMode ? 'Edit Lokasi' : 'Tambah Lokasi Baru'"></h3>
                            <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lokasi/Ruangan <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="form.name" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required placeholder="Cth: Ruang UGD, Pustu Tugu...">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Lokasi <span class="text-rose-500">*</span></label>
                                <select name="type" x-model="form.type" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="Ruangan">Ruangan Dalam Puskesmas</option>
                                    <option value="Pustu">Pustu (Puskesmas Pembantu)</option>
                                    <option value="Ponkesdes">Ponkesdes</option>
                                    <option value="Polindes">Polindes</option>
                                    <option value="Puskesmas">Puskesmas Induk</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" x-model="form.penanggung_jawab" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Nama petugas penanggung jawab">
                            </div>
                            
                            <div x-show="form.type !== 'Ruangan'" class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 space-y-3">
                                <p class="text-xs font-semibold text-indigo-800 flex items-center gap-1.5 mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Koordinat Peta (Hanya untuk Pustu/Ponkesdes/Polindes)
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Latitude</label>
                                        <input type="text" name="latitude" x-model="form.latitude" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="-7.xxxx">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Longitude</label>
                                        <input type="text" name="longitude" x-model="form.longitude" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="112.xxxx">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Keterangan Tambahan</label>
                                <textarea name="description" x-model="form.description" rows="2" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors flex items-center gap-2 shadow-sm shadow-indigo-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="editMode ? 'Simpan Perubahan' : 'Tambah Lokasi'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
