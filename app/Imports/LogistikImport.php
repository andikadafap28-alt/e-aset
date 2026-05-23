<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogistikImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $kategori_besar;
    protected $bulan_import;

    public function __construct($kategori_besar, $bulan_import)
    {
        $this->kategori_besar = $kategori_besar;
        $this->bulan_import = $bulan_import;
    }

    public function collection(Collection $rows)
    {
        // 1. Tentukan Tanggal (Tanggal 1 untuk Saldo Awal, Akhir Bulan untuk Mutasi)
        $tanggalAwal = Carbon::createFromFormat('Y-m', $this->bulan_import)->startOfMonth();
        $tanggalAkhir = Carbon::createFromFormat('Y-m', $this->bulan_import)->endOfMonth();

        DB::transaction(function () use ($rows, $tanggalAwal, $tanggalAkhir) {
            $transactions = [];
            $now = now();

            foreach ($rows as $row) {
                $namaBarang = $row['nama_barang'] ?? $row['nama_obat_atau_barang'] ?? null;
                
                if (empty($namaBarang)) {
                    continue;
                }

                // Ekstraksi Harga dengan Safe Math
                $hargaStr = isset($row['harga_satuan']) ? preg_replace('/[^\d,.]/', '', $row['harga_satuan']) : '0';
                $hargaStr = str_replace(',', '.', $hargaStr); 
                $harga_mentah = (float) $hargaStr;
                $harga = round($harga_mentah, 2);

                // Kuantitas stok dipastikan menjadi bilangan bulat (integer) murni
                $stok_awal = isset($row['stok_awal']) ? (int) round((float) $row['stok_awal']) : 0;
                $penerimaan = isset($row['penerimaan']) ? (int) round((float) $row['penerimaan']) : 0;
                $pemakaian = isset($row['pemakaian']) ? (int) round((float) $row['pemakaian']) : 0;
                
                // Gunakan kolom stok_akhir, jika tidak ada cari fallback ke sisa_stok
                $stok_akhir = isset($row['stok_akhir']) ? (int) round((float) $row['stok_akhir']) : (isset($row['sisa_stok']) ? (int) round((float) $row['sisa_stok']) : 0);

                // Cari item berdasarkan nama_barang dan kategori_besar
                $item = Item::firstOrCreate(
                    [
                        'kategori_besar' => $this->kategori_besar,
                        'nama_barang' => $namaBarang
                    ],
                    [
                        'kode_barang' => $row['kode_barang'] ?? null,
                        'kategori' => $row['kategori'] ?? 'Umum',
                        'satuan' => $row['satuan'] ?? 'Pcs',
                        'harga_satuan' => $harga,
                        'stok_sekarang' => 0
                    ]
                );

                // Segera update data item
                $item->stok_sekarang = $stok_akhir;
                if ($harga > 0) {
                    $item->harga_satuan = $harga;
                }
                if (!empty($row['satuan'])) {
                    $item->satuan = $row['satuan'];
                }
                $item->save();

                $harga_transaksi = $harga > 0 ? $harga : $item->harga_satuan;

                // Logika Saldo Awal (Gunakan Tanggal 1 bulan tersebut)
                if ($stok_awal > 0) {
                    $transactions[] = [
                        'item_id' => $item->id,
                        'jenis_transaksi' => 'masuk',
                        'jumlah' => $stok_awal,
                        'harga_satuan' => $harga_transaksi,
                        'tanggal_transaksi' => clone $tanggalAwal,
                        'status_hutang' => false,
                        'keterangan' => 'Saldo Awal (Import Laporan)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Logika Penerimaan Bulan Ini (Gunakan Akhir Bulan tersebut)
                if ($penerimaan > 0) {
                    $transactions[] = [
                        'item_id' => $item->id,
                        'jenis_transaksi' => 'masuk',
                        'jumlah' => $penerimaan,
                        'harga_satuan' => $harga_transaksi,
                        'tanggal_transaksi' => clone $tanggalAkhir,
                        'status_hutang' => false,
                        'keterangan' => 'Penerimaan Bulan Ini (Import)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Logika Pemakaian Bulan Ini (Gunakan Akhir Bulan tersebut)
                if ($pemakaian > 0) {
                    $transactions[] = [
                        'item_id' => $item->id,
                        'jenis_transaksi' => 'keluar',
                        'jumlah' => $pemakaian,
                        'harga_satuan' => $harga_transaksi,
                        'tanggal_transaksi' => clone $tanggalAkhir,
                        'status_hutang' => false,
                        'keterangan' => 'Pemakaian Bulan Ini (Import)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($transactions)) {
                InventoryTransaction::insert($transactions);
            }
        });
    }
}
