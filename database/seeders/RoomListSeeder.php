<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomListSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            'Lintas Klaster (Gizi)',
            'Gudang Apotik',
            'R. Pelayanan Obat',
            'Ruang Radiologi',
            'PONED',
            'UGD',
            'Rawat Inap Alkes',
            'R. Sterilisasi',
            'Ruang Pertemuan 1',
            'Ruang Pertemuan 2',
            'Rawat Inap Kamar 1',
            'Rawat Inap Kamar 2',
            'Rawat Inap Kamar 3',
            'Rawat Inap Kamar 4',
            'Rawat Inap Kamar 5',
            'Rawat Inap Kamar 6',
            'Rawat Inap Kamar 7',
            'Rawat Inap Kamar 8',
            'Rawat Inap Kamar 9',
            'Rawat Inap Kamar 10',
            'Ruang Tunggu Rawat Jalan',
            'Klaster 2 Ibu',
            'Lintas Klaster (Gigi)',
            'Klaster 3 Dewasa',
            'Lintas Klaster (Klinik Sanitasi)',
            'Ruang Obat UGD',
            'Klaster 1 (Manajemen 1)',
            'Ruang ATK',
            'Musholla Lantai 2',
            'Ruang Arsip',
            'Klaster 1 (Manajemen 2)',
            'Ruang Kepala Puskesmas',
            'Ruang Pendaftaran',
        ];

        foreach ($rooms as $name) {
            Room::firstOrCreate(
                ['name' => $name],
                ['type' => 'Ruangan']
            );
        }
    }
}
