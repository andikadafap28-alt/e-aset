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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransaksiLogistikImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $kategori_besar;

    public function __construct($kategori_besar)
    {
        $this->kategori_besar = $kategori_besar;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $now = now();
            // Cache item untuk menghindari query berulang
            $itemCache = [];
            // Cache perhitungan stok per item yang akan diupdate di akhir
            $stokPerubahan = [];

            foreach ($rows as $row) {
                // Mapping Header: "Nama Barang" -> nama_barang
                $namaBarang = $row['nama_barang'] ?? null;
                if (empty($namaBarang)) continue;

                $tanggalTransaksi = $this->parseDate($row['tanggal_transaksi_yyyy_mm_dd'] ?? $row['tanggal_transaksi'] ?? null);
                if (!$tanggalTransaksi) continue; // Wajib ada tanggal transaksi

                $tanggalSpj = $this->parseDate($row['tanggal_spj_yyyy_mm_dd_opsional'] ?? $row['tanggal_spj'] ?? null);
                
                $jenisTransaksi = strtolower($row['jenis_transaksi_masuk_keluar'] ?? $row['jenis_transaksi'] ?? 'masuk');
                if (!in_array($jenisTransaksi, ['masuk', 'keluar'])) $jenisTransaksi = 'masuk';

                $jumlah = isset($row['jumlah']) ? (int) round((float) $row['jumlah']) : 0;
                if ($jumlah <= 0) continue;

                $hargaStr = isset($row['harga_satuan_angka']) ? preg_replace('/[^\d,.]/', '', $row['harga_satuan_angka']) : (isset($row['harga_satuan']) ? preg_replace('/[^\d,.]/', '', $row['harga_satuan']) : '0');
                $hargaStr = str_replace(',', '.', $hargaStr); 
                $harga = round((float) $hargaStr, 2);

                $keterangan = $row['keterangan_contoh_pembelian_ugd_dll'] ?? $row['keterangan'] ?? '';

                $itemKey = strtolower(trim($namaBarang));
                if (!isset($itemCache[$itemKey])) {
                    $item = Item::firstOrCreate(
                        [
                            'kategori_besar' => $this->kategori_besar,
                            'nama_barang' => trim($namaBarang)
                        ],
                        [
                            'kode_barang' => $row['kode_barang_opsional'] ?? $row['kode_barang'] ?? null,
                            'kategori' => $row['kategori_contoh_umum'] ?? $row['kategori'] ?? 'Umum',
                            'satuan' => $row['satuan_contoh_pcs'] ?? $row['satuan'] ?? 'Pcs',
                            'harga_satuan' => $harga,
                            'stok_sekarang' => 0
                        ]
                    );
                    $itemCache[$itemKey] = $item;
                    $stokPerubahan[$item->id] = 0;
                }

                $item = $itemCache[$itemKey];
                // Update harga satuan dari master jika harga_satuan di excel diisi > 0
                if ($harga > 0 && $item->harga_satuan != $harga) {
                    $item->harga_satuan = $harga;
                    $item->save();
                }

                $harga_transaksi = $harga > 0 ? $harga : $item->harga_satuan;
                $isHutang = empty($tanggalSpj) && $jenisTransaksi == 'masuk';

                InventoryTransaction::create([
                    'item_id' => $item->id,
                    'jenis_transaksi' => $jenisTransaksi,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga_transaksi,
                    'tanggal_transaksi' => $tanggalTransaksi,
                    'tanggal_spj' => $isHutang ? null : ($tanggalSpj ?: $tanggalTransaksi),
                    'status_hutang' => $isHutang,
                    'keterangan' => $keterangan,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                if ($jenisTransaksi == 'masuk') {
                    $stokPerubahan[$item->id] += $jumlah;
                } else {
                    $stokPerubahan[$item->id] -= $jumlah;
                }
            }

            // Update stok akhir masing-masing barang
            foreach ($stokPerubahan as $itemId => $perubahan) {
                if ($perubahan != 0) {
                    $item = Item::find($itemId);
                    $item->stok_sekarang += $perubahan;
                    $item->save();
                }
            }
        });
    }
}
