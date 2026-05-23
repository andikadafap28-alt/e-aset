<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Asset::create([
            'asset_code' => 'ALKES-001',
            'name' => 'Tensimeter Digital OneMed',
            'category' => 'Alat Kesehatan',
            'location' => 'Puskesmas Induk - Ruang Pemeriksaan 1',
            'year_purchased' => '2026',
            'last_calibration' => Carbon::now()->subMonths(2),
            'condition' => 'Baik',
            'latitude' => -7.218500,
            'longitude' => 112.339500,
        ]);

        Asset::create([
            'asset_code' => 'ALKES-002',
            'name' => 'Kursi Roda Standar',
            'category' => 'Alat Medis Habis Pakai / Non-Mesin',
            'location' => 'Pustu Desa Mantup',
            'year_purchased' => '2024',
            'condition' => 'Rusak Ringan',
        ]);

        Asset::create([
            'asset_code' => 'ALKES-003',
            'name' => 'Sterilisator Kering',
            'category' => 'Alat Kesehatan',
            'location' => 'Puskesmas Induk - UGD',
            'year_purchased' => '2022',
            'last_calibration' => Carbon::now()->subYears(2),
            'condition' => 'Rusak Berat',
        ]);
    }
}
