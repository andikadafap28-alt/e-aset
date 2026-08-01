<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        // Data GIS: Ruangan yang memiliki latitude & longitude (seperti Pustu/Polindes)
        $locations = \App\Models\Room::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->withCount(['assets as aset_baik_count' => function ($query) {
                $query->where('condition', 'Baik');
            }])
            ->withCount(['assets as aset_rusak_count' => function ($query) {
                $query->whereIn('condition', ['Rusak Ringan', 'Rusak Berat']);
            }])
            ->get();

        // Top 10 Aset Sering Rusak (Rusak Berat/Ringan atau masuk pemeliharaan)
        // Jika belum ada pemeliharaan, kita urutkan dari kondisi Rusak
        $topDamage = \App\Models\Asset::whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])
            ->with('category', 'room')
            ->orderBy('condition', 'asc') // Rusak Berat dulu
            ->take(10)
            ->get();

        return view('dashboard.eksekutif', compact('locations', 'topDamage'));
    }
}
