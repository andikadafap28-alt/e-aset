<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleKirSeeder extends Seeder
{
    public function run(): void
    {
        $room = Room::firstOrCreate(
            ['name' => 'Ruang Kepala Puskesmas'],
            ['type' => 'Ruangan', 'penanggung_jawab' => 'dr. Muhamad Sunaryadi']
        );

        // Jika belum ada aset di ruangan ini, masukkan aset sampel sesuai KIR fisik
        if ($room->assets()->count() === 0) {
            $items = [
                ['name' => 'AC', 'merk' => 'Panasonic', 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'TV LED', 'merk' => 'Toshiba', 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Antena TV', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Sofa set + Meja', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Kursi putar', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Lemari kaca', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Kursi', 'merk' => 'Polaris', 'bahan' => 'Besi', 'qty' => 4, 'baik' => 4],
                ['name' => 'Vas bunga kecil', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Vas bunga sedang', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 2, 'baik' => 2],
                ['name' => 'Jam Dinding', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'UPS', 'merk' => null, 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'PC AIO', 'merk' => 'Dell', 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Mouse', 'merk' => 'Lenovo', 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Keyboard', 'merk' => 'Dell', 'bahan' => 'Campuran', 'qty' => 1, 'baik' => 1],
                ['name' => 'Meja kerja', 'merk' => null, 'bahan' => 'Kayu', 'qty' => 1, 'baik' => 1],
                ['name' => 'Tempat sampah', 'merk' => null, 'bahan' => 'Plastik', 'qty' => 1, 'baik' => 1],
            ];

            foreach ($items as $idx => $item) {
                for ($q = 1; $q <= $item['qty']; $q++) {
                    Asset::create([
                        'room_id' => $room->id,
                        'location' => $room->name,
                        'name' => $item['name'],
                        'category' => 'Peralatan dan Mesin',
                        'merk' => $item['merk'],
                        'bahan' => $item['bahan'],
                        'condition' => 'Baik',
                        'keterangan' => null,
                        'source' => 'KIR',
                        'asset_code' => 'KIR-KAPUS-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT) . ($item['qty'] > 1 ? "-$q" : ''),
                        'year_purchased' => 2026,
                    ]);
                }
            }
        }
    }
}
