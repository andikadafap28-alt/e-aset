@extends('layouts.app')

@section('header_title', 'Dashboard Eksekutif (GIS & Analitik)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Eksekutif & Pemetaan</h2>
        <p class="text-slate-500 text-sm mt-1">Pantau sebaran lokasi Puskesmas Pembantu/Polindes dan analitik kerusakan aset</p>
    </div>

    <!-- Peta GIS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Peta Sebaran Faskes (GIS)</h3>
                    <p class="text-xs text-slate-500">Klik titik lokasi untuk melihat rincian aset</p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-medium text-slate-600">Puskesmas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span class="text-xs font-medium text-slate-600">Pustu / Polindes</span>
                </div>
            </div>
        </div>
        
        <div id="map" style="height: 500px; z-index: 1;"></div>
    </div>

    <!-- Analitik Kerusakan -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Top 10 Aset Sering Rusak / Rusak Berat</h3>
                <p class="text-xs text-slate-500">Daftar aset dengan kondisi rusak yang perlu prioritas perbaikan/penghapusan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-3 font-semibold rounded-l-lg">Kode / NIBAR</th>
                        <th class="p-3 font-semibold">Nama Alat</th>
                        <th class="p-3 font-semibold">Lokasi Ruangan</th>
                        <th class="p-3 font-semibold">Kondisi</th>
                        <th class="p-3 font-semibold rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topDamage as $asset)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-3">
                            <span class="font-semibold text-slate-700 text-sm">{{ $asset->asset_code }}</span>
                        </td>
                        <td class="p-3">
                            <div class="text-sm font-medium text-slate-800">{{ $asset->name }}</div>
                            <div class="text-xs text-slate-500">{{ $asset->category->nama_kategori ?? $asset->category }}</div>
                        </td>
                        <td class="p-3">
                            <div class="text-sm text-slate-600 font-medium">
                                {{ $asset->room->name ?? $asset->location }}
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold
                                {{ $asset->condition == 'Rusak Berat' ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('aset.show', $asset->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                Detail Aset
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-emerald-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-medium text-slate-700">Luar Biasa!</p>
                                <p>Tidak ada aset yang dilaporkan rusak saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Map Centered on Puskesmas Mantup (default)
        var map = L.map('map').setView([-7.2185, 112.3395], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Define icons
        var puskesmasIcon = L.divIcon({
            html: `<div style="background-color: #10b981; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);"></div>`,
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        var pustuIcon = L.divIcon({
            html: `<div style="background-color: #6366f1; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);"></div>`,
            className: '',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        var locationsData = {!! json_encode($locations) !!};
        
        var bounds = [];

        locationsData.forEach(function(loc) {
            var icon = (loc.type === 'Puskesmas') ? puskesmasIcon : pustuIcon;
            var marker = L.marker([parseFloat(loc.latitude), parseFloat(loc.longitude)], {icon: icon}).addTo(map);
            bounds.push([parseFloat(loc.latitude), parseFloat(loc.longitude)]);
            
            var popupContent = `
                <div class="p-1 min-w-[200px]">
                    <h4 class="font-bold text-slate-800 text-sm mb-1">${loc.name}</h4>
                    <p class="text-xs text-slate-500 mb-3">${loc.type} ${loc.penanggung_jawab ? '<br>PJ: ' + loc.penanggung_jawab : ''}</p>
                    
                    <div class="flex gap-2">
                        <div class="flex-1 bg-emerald-50 rounded p-2 text-center border border-emerald-100">
                            <p class="text-[10px] font-semibold text-emerald-600 uppercase">Kondisi Baik</p>
                            <p class="text-lg font-bold text-emerald-700">${loc.aset_baik_count}</p>
                        </div>
                        <div class="flex-1 bg-rose-50 rounded p-2 text-center border border-rose-100">
                            <p class="text-[10px] font-semibold text-rose-600 uppercase">Rusak</p>
                            <p class="text-lg font-bold text-rose-700">${loc.aset_rusak_count}</p>
                        </div>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, {padding: [50, 50]});
        }
    });
</script>
@endsection
