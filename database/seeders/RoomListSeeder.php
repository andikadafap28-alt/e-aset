<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomListSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            'Ruang Pendaftaran',
            'Ruang Farmasi',
            'Gudang Obat',
            'Ruang Gizi',
            'Ruang Poli Gigi',
            'Ruang Pengaduan dan Kesehatan Lingkungan',
            'Ruang Pemeriksaan Mata',
            'Ruang Poli Dewasa',
            'Ruang Poli Lansia',
            'Ruang Poli Anak',
            'Ruang Poli KIA',
            'Ruang Laboratorium',
            'Ruang Tunggu',
            'Ruang Bermain Anak',
            'Ruang Poli Paru',
            'Ruang Radiologi',
            'Ruang UGD',
            'Ruang Perawat Jaga',
            'Ruang Sterilisasi',
            'Ruang PONED',
            'Ruang Dokter',
            'Ruang Manajemen UGD',
            'Ruang Obat UGD',
            'Kamar 1',
            'Kamar 2',
            'Kamar 3',
            'Kamar 4',
            'Kamar 5',
            'Kamar 6',
            'Kamar 7',
            'Kamar 8',
            'Kamar 9',
            'Kamar 10',
            'Ruang Pertemuan 1',
            'Ruang Pertemuan 2',
            'Musholla',
            'Ruang Kepala Puskesmas',
            'Ruang Manajemen 1',
            'Ruang Manajemen 2',
            'Ruang ATK',
            'Ruang Arsip 1',
            'Ruang Arsip 2',
            'Ruang Loket Parkir',
        ];

        foreach ($rooms as $name) {
            Room::firstOrCreate(
                ['name' => $name],
                ['type' => 'Ruangan']
            );
        }
    }
}
