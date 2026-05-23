<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class ItemImport implements ToCollection, WithHeadingRow
{
    protected $kategori_besar;

    public function __construct($kategori_besar)
    {
        $this->kategori_besar = $kategori_besar;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Pastikan ada data minimal
                if (!isset($row['nama_barang']) || !isset($row['harga_satuan'])) {
                    continue;
                }

                $hargaSatuan = (float) $row['harga_satuan'];
                $stokAwal = isset($row['stok_awal']) ? (int) $row['stok_awal'] : 0;

                // Cari apakah barang dengan nama dan harga yang sama sudah ada di kategori ini
                $item = Item::where('kategori_besar', $this->kategori_besar)
                    ->where('nama_barang', $row['nama_barang'])
                    ->where('harga_satuan', $hargaSatuan)
                    ->first();

                if (!$item) {
                    // Buat barang baru
                    $item = Item::create([
                        'kode_barang' => $row['kode_barang'] ?? null,
                        'nama_barang' => $row['nama_barang'],
                        'kategori' => $row['kategori'] ?? 'Umum',
                        'kategori_besar' => $this->kategori_besar,
                        'satuan' => $row['satuan'] ?? 'Pcs',
                        'harga_satuan' => $hargaSatuan,
                        'stok_sekarang' => $stokAwal
                    ]);

                    // Jika ada stok awal, catat transaksinya
                    if ($stokAwal > 0) {
                        InventoryTransaction::create([
                            'item_id' => $item->id,
                            'jenis_transaksi' => 'masuk',
                            'jumlah' => $stokAwal,
                            'harga_satuan' => $hargaSatuan,
                            'tanggal_transaksi' => now(),
                            'status_hutang' => false,
                            'keterangan' => 'Stok awal dari import Excel'
                        ]);
                    }
                } else {
                    // Jika barang sudah ada, kita tambahkan stok awalnya sebagai transaksi masuk
                    if ($stokAwal > 0) {
                        $item->increment('stok_sekarang', $stokAwal);
                        
                        InventoryTransaction::create([
                            'item_id' => $item->id,
                            'jenis_transaksi' => 'masuk',
                            'jumlah' => $stokAwal,
                            'harga_satuan' => $hargaSatuan,
                            'tanggal_transaksi' => now(),
                            'status_hutang' => false,
                            'keterangan' => 'Penambahan stok dari import Excel'
                        ]);
                    }
                    
                    // Update field lain jika ada (optional)
                    if (isset($row['kode_barang']) && !$item->kode_barang) {
                        $item->kode_barang = $row['kode_barang'];
                        $item->save();
                    }
                }
            }
        });
    }
}
