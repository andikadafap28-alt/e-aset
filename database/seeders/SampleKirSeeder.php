<?php

namespace Database\Seeders;

use App\Models\KirItem;
use App\Models\Room;
use Illuminate\Database\Seeder;

class SampleKirSeeder extends Seeder
{
    public function run(): void
    {
        $room = Room::firstOrCreate(
            ['name' => 'Ruang Kepala Puskesmas'],
            ['type' => 'Ruangan', 'penanggung_jawab' => 'dr. Muhamad Sunaryadi']
        );

        if ($room->kirItems()->count() === 0) {
            $items = [
                ['name' => 'AC', 'merk' => 'Panasonic', 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'TV LED', 'merk' => 'Toshiba', 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Antena TV', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Sofa set + Meja', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Kursi putar', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Lemari kaca', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Kursi', 'merk' => 'Polaris', 'bahan' => 'Besi', 'jumlah' => 4, 'kondisi_baik' => 4, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Vas bunga kecil', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Vas bunga sedang', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 2, 'kondisi_baik' => 2, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Jam Dinding', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'UPS', 'merk' => null, 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'PC AIO', 'merk' => 'Dell', 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Mouse', 'merk' => 'Lenovo', 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Keyboard', 'merk' => 'Dell', 'bahan' => 'Campuran', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Meja kerja', 'merk' => null, 'bahan' => 'Kayu', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
                ['name' => 'Tempat sampah', 'merk' => null, 'bahan' => 'Plastik', 'jumlah' => 1, 'kondisi_baik' => 1, 'kondisi_kurang_baik' => 0, 'kondisi_rusak_berat' => 0],
            ];

            foreach ($items as $item) {
                KirItem::create(array_merge($item, ['room_id' => $room->id]));
            }
        }
    }
}
