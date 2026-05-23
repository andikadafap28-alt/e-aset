@extends('layouts.app')

@section('header_title', isset($asset) ? 'Edit Data Aset' : 'Tambah Data Aset')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">{{ isset($asset) ? 'Edit Data Aset' : 'Tambah Aset Baru' }}</h2>
        <p class="text-slate-500 text-sm mt-1">Lengkapi informasi aset di bawah ini</p>
    </div>
    <a href="{{ route('aset.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
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

<div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
    <form action="{{ isset($asset) ? route('aset.update', $asset->id) : route('aset.store') }}" method="POST">
        @csrf
        @if(isset($asset))
            @method('PUT')
        @endif
        
        @if(!isset($asset) && !empty($pengadaanData['pengadaan_id']))
            <input type="hidden" name="pengadaan_id" value="{{ $pengadaanData['pengadaan_id'] }}">
        @endif

        @if(!isset($asset))
        <!-- Kode 108 -->
        <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 mb-6 space-y-4">
            <h3 class="font-bold text-slate-700 border-b pb-2">Penomoran Otomatis (Kode 108) <span class="text-xs text-slate-400 font-normal">(Opsional)</span></h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Level 1 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 1 (Akun)</label>
                    <select id="level_1" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Akun --</option>
                    </select>
                </div>

                <!-- Level 2 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 2 (Kelompok)</label>
                    <select id="level_2" disabled class="w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Kelompok --</option>
                    </select>
                </div>

                <!-- Level 3 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 3 (Jenis)</label>
                    <select id="level_3" disabled class="w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Jenis --</option>
                    </select>
                </div>

                <!-- Level 4 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 4 (Objek)</label>
                    <select id="level_4" disabled class="w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Objek --</option>
                    </select>
                </div>

                <!-- Level 5 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 5 (Rincian Objek)</label>
                    <select id="level_5" disabled class="w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Rincian Objek --</option>
                    </select>
                </div>

                <!-- Level 6 -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level 6 (Sub Rincian Objek / Kode Barang Final)</label>
                    <select name="kode_108" id="level_6" disabled class="w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Sub Rincian Objek --</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">NUP/Register akan di-generate otomatis oleh sistem (berurutan) setelah formulir disimpan.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Asset Code -->
            <div>
                <label for="asset_code" class="block text-sm font-medium text-slate-700 mb-1">Kode Aset</label>
                <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code', $asset->asset_code ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: ALKES-001" {{ isset($asset) ? 'required' : '' }}>
                @if(!isset($asset))
                <p class="text-xs text-slate-500 mt-1">Isi manual jika tidak menggunakan Kode 108. Jika Kode 108 dipilih, kolom ini akan diabaikan.</p>
                @endif
            </div>

            @if(!isset($asset))
            <!-- Quantity -->
            <div>
                <label for="jumlah" class="block text-sm font-medium text-slate-700 mb-1">Jumlah Aset (Banyaknya)</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $pengadaanData['jumlah'] ?? 1) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required min="1" max="{{ !empty($pengadaanData['jumlah']) ? $pengadaanData['jumlah'] : 1000 }}">
                <p class="text-xs text-slate-500 mt-1">Jika &gt; 1, kode otomatis ditambah nomor (contoh: ALKES-001-1, dst).</p>
            </div>
            @endif

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Aset</label>
                <input type="text" name="name" id="name" value="{{ old('name', $asset->name ?? ($pengadaanData['name'] ?? '')) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: USG Mindray" required>
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">Kategori (Umur Ekonomis)</label>
                <select name="category_id" id="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $asset->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }} ({{ $cat->umur_ekonomis }} Tahun)
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Digunakan untuk menghitung nilai penyusutan aset.</p>
            </div>

            <!-- Harga Perolehan -->
            <div>
                <label for="harga_perolehan" class="block text-sm font-medium text-slate-700 mb-1">Harga Perolehan Aset (Rp)</label>
                <input type="number" name="harga_perolehan" id="harga_perolehan" value="{{ old('harga_perolehan', $asset->harga_perolehan ?? ($pengadaanData['harga_satuan'] ?? '')) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: 15000000" {{ !isset($asset) && !empty($pengadaanData['pengadaan_id']) ? '' : 'required' }}>
                @if(!isset($asset) && !empty($pengadaanData['pengadaan_id']))
                <p class="text-xs text-slate-500 mt-1">Bisa dikosongkan. Sistem akan otomatis mengambil harga dari data pengadaan jika kosong.</p>
                @endif
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label>
                <input type="text" name="location" id="location" value="{{ old('location', $asset->location ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Ruang UGD / Pustu A" required>
            </div>

            <!-- Penanggung Jawab -->
            <div>
                <label for="penanggung_jawab" class="block text-sm font-medium text-slate-700 mb-1">Penanggung Jawab Ruangan <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                <input type="text" name="penanggung_jawab" id="penanggung_jawab" value="{{ old('penanggung_jawab', $asset->penanggung_jawab ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Dr. Budi / Kepala Ruangan">
            </div>

            <!-- Year Purchased -->
            <div>
                <label for="year_purchased" class="block text-sm font-medium text-slate-700 mb-1">Tahun Pengadaan</label>
                <input type="number" name="year_purchased" id="year_purchased" value="{{ old('year_purchased', $asset->year_purchased ?? ($pengadaanData['year_purchased'] ?? date('Y'))) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required min="1900" max="2100">
            </div>

            <!-- Last Calibration -->
            <div>
                <label for="last_calibration" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Kalibrasi Terakhir <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                <input type="date" name="last_calibration" id="last_calibration" value="{{ old('last_calibration', $asset->last_calibration ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Condition -->
            <div>
                <label for="condition" class="block text-sm font-medium text-slate-700 mb-1">Kondisi</label>
                <select name="condition" id="condition" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="Baik" {{ old('condition', $asset->condition ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ old('condition', $asset->condition ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ old('condition', $asset->condition ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <!-- Document Link -->
            <div>
                <label for="document_link" class="block text-sm font-medium text-slate-700 mb-1">Link Dokumen Pengadaan <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                <input type="url" name="document_link" id="document_link" value="{{ old('document_link', $asset->document_link ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://drive.google.com/...">
            </div>
        </div>

        <div class="border-t border-slate-200 pt-6 mt-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Titik Lokasi (Koordinat)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label for="latitude" class="block text-sm font-medium text-slate-700 mb-1">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $asset->latitude ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" readonly>
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-medium text-slate-700 mb-1">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $asset->longitude ?? '') }}" class="w-full rounded-lg border-slate-300 shadow-sm bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" readonly>
                </div>
            </div>
            
            <p class="text-xs text-slate-500 mb-2">Klik pada peta untuk menentukan/mengubah koordinat lokasi aset.</p>
            <div id="map" style="height: 300px; border-radius: 10px; z-index: 1;"></div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('aset.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                {{ isset($asset) ? 'Simpan Perubahan' : 'Tambahkan Aset' }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Default lokasi (Puskesmas Mantup / Lamongan)
        var defaultLat = {{ isset($asset) && $asset->latitude ? $asset->latitude : -7.2185 }};
        var defaultLng = {{ isset($asset) && $asset->longitude ? $asset->longitude : 112.3395 }};
        
        var map = L.map('map').setView([defaultLat, defaultLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng]).addTo(map);

        // Update koordinat saat peta diklik
        map.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(8);
            var lng = e.latlng.lng.toFixed(8);
            
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });

        // Initialize input values if they are empty
        if (!document.getElementById('latitude').value) {
            document.getElementById('latitude').value = defaultLat;
            document.getElementById('longitude').value = defaultLng;
        }
        
        // Dropdown Logic Kode 108
        @if(!isset($asset))
        const levels = [1, 2, 3, 4, 5, 6];
        
        function resetDropdownsFrom(startLevel) {
            for (let i = startLevel; i <= 6; i++) {
                const el = document.getElementById('level_' + i);
                el.innerHTML = `<option value="">-- Pilih Level ${i} --</option>`;
                el.disabled = true;
                el.classList.add('bg-slate-100');
            }
        }

        function loadDropdown(level, parentCode = '') {
            const url = `/api/kode-108?level=${level}&parent=${parentCode}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const el = document.getElementById('level_' + level);
                    el.innerHTML = `<option value="">-- Pilih --</option>`;
                    
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.kode;
                        option.textContent = `${item.kode} - ${item.uraian}`;
                        el.appendChild(option);
                    });

                    el.disabled = false;
                    el.classList.remove('bg-slate-100');
                })
                .catch(error => console.error('Error fetching Kode 108:', error));
        }

        // Initialize Level 1
        loadDropdown(1);

        // Event Listeners for cascading changes
        levels.forEach((level) => {
            if (level < 6) {
                document.getElementById('level_' + level).addEventListener('change', function() {
                    const selectedVal = this.value;
                    resetDropdownsFrom(level + 1);
                    if (selectedVal) {
                        loadDropdown(level + 1, selectedVal);
                    }
                    
                    // Toggle required on asset_code manual
                    toggleAssetCodeRequired();
                });
            }
        });
        
        document.getElementById('level_6').addEventListener('change', toggleAssetCodeRequired);
        
        function toggleAssetCodeRequired() {
            const level6 = document.getElementById('level_6').value;
            const assetCodeInput = document.getElementById('asset_code');
            if(level6) {
                assetCodeInput.removeAttribute('required');
                assetCodeInput.classList.add('bg-slate-100', 'text-slate-500');
                assetCodeInput.value = '';
                assetCodeInput.placeholder = 'Akan di-generate otomatis...';
                assetCodeInput.readOnly = true;
            } else {
                assetCodeInput.setAttribute('required', 'required');
                assetCodeInput.classList.remove('bg-slate-100', 'text-slate-500');
                assetCodeInput.placeholder = 'Contoh: ALKES-001';
                assetCodeInput.readOnly = false;
            }
        }
        @endif
    });
</script>
@endsection
