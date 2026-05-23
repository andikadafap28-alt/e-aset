<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\InventoryTransaction;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $categories = ['ATK', 'Elektronik', 'Peralatan Medis', 'Obat-obatan', 'Umum'];
        $units = ['Pcs', 'Box', 'Rim', 'Lusin', 'Botol', 'Pack'];

        // Buat 100 Item
        for ($i = 0; $i < 100; $i++) {
            $harga = $faker->randomElement([5000, 10000, 15000, 25000, 50000, 100000, 150000]);
            
            $item = Item::create([
                'nama_barang' => ucfirst($faker->words(rand(2, 4), true)),
                'kategori' => $faker->randomElement($categories),
                'satuan' => $faker->randomElement($units),
                'stok_sekarang' => 0, // Akan dikalkulasi dari riwayat
                'harga_satuan' => $harga,
            ]);

            $stok = 0;
            $numTransactions = rand(5, 15); // Setiap item punya 5-15 riwayat
            
            for ($j = 0; $j < $numTransactions; $j++) {
                // Pastikan awal-awal adalah barang masuk agar tidak minus
                if ($j < 2 || $stok == 0) {
                    $jenis = 'masuk';
                } else {
                    $jenis = $faker->randomElement(['masuk', 'keluar', 'keluar']);
                }

                $jumlah = rand(5, 50);

                if ($jenis == 'keluar' && $jumlah > $stok) {
                    $jumlah = $stok; // Pastikan stok tidak minus
                }

                // Jika jumlah 0 setelah penyesuaian, skip
                if ($jumlah == 0) continue;

                // Transaksi dibuat secara acak dalam rentang 3 bulan terakhir
                $tanggal = Carbon::now()->subDays(rand(1, 90));

                InventoryTransaction::create([
                    'item_id' => $item->id,
                    'jenis_transaksi' => $jenis,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'tanggal_transaksi' => $tanggal,
                    'status_hutang' => $faker->boolean(20), // Peluang 20% transaksi ini statusnya hutang
                    'keterangan' => $faker->sentence(3),
                ]);

                // Hitung stok nyata
                if ($jenis == 'masuk') {
                    $stok += $jumlah;
                } else {
                    $stok -= $jumlah;
                }
            }

            // Update stok_sekarang di master item agar sinkron
            $item->update(['stok_sekarang' => $stok]);
        }
    }
}
