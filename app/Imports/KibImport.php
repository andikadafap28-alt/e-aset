<?php

namespace App\Imports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Str;

class KibImport implements ToCollection
{
    protected $source;

    public function __construct($source = 'BMD')
    {
        $this->source = $source;
    }

    public function collection(Collection $rows)
    {
        $headerFound = false;
        $colMap = []; // Akan menyimpan index kolom: 'kode_barang' => 0, 'nibar' => 2, dll

        foreach ($rows as $index => $row) {
            // Cek apakah ini baris header
            if (!$headerFound) {
                // Deteksi header jika ada kata "NIBAR" atau "Register" atau "Spesifikasi"
                $rowStr = strtolower(implode(' ', $row->toArray()));
                if (str_contains($rowStr, 'nibar') || str_contains($rowStr, 'kode barang') || str_contains($rowStr, 'register')) {
                    $headerFound = true;
                    // Petakan index kolom
                    foreach ($row as $colIdx => $colVal) {
                        $val = strtolower(trim($colVal));
                        if (str_contains($val, 'kode barang') || str_contains($val, 'kode_108')) $colMap['kode_108'] = $colIdx;
                        if (str_contains($val, 'nibar')) $colMap['nibar'] = $colIdx;
                        if (str_contains($val, 'register')) $colMap['no_register'] = $colIdx;
                        if (str_contains($val, 'spesifikasi nama') || str_contains($val, 'nama barang') || str_contains($val, 'uraian')) $colMap['name'] = $colIdx;
                        if (str_contains($val, 'merek') || str_contains($val, 'merk') || str_contains($val, 'tipe')) $colMap['merk'] = $colIdx;
                        if (str_contains($val, 'lokasi')) $colMap['location'] = $colIdx;
                        if (str_contains($val, 'harga') || str_contains($val, 'satuan perolehan') || str_contains($val, 'nilai')) $colMap['purchase_price'] = $colIdx;
                        if (str_contains($val, 'tahun') || str_contains($val, 'tanggal')) $colMap['year_purchased'] = $colIdx;
                        if (str_contains($val, 'kategori') || str_contains($val, 'kib')) $colMap['category'] = $colIdx;
                    }
                    continue; // Skip baris header ini
                }
                continue; // Terus cari header
            }

            // Jika header sudah ditemukan, ini adalah baris data
            if ($headerFound && isset($colMap['name'])) {
                // Skip baris kosong
                $namaBarang = isset($colMap['name']) ? $row[$colMap['name']] : null;
                if (empty($namaBarang)) continue;

                $kode108 = isset($colMap['kode_108']) ? $row[$colMap['kode_108']] : null;
                $nibar = isset($colMap['nibar']) ? $row[$colMap['nibar']] : null;
                $noRegister = isset($colMap['no_register']) ? $row[$colMap['no_register']] : null;
                $location = isset($colMap['location']) ? $row[$colMap['location']] : null;
                $priceRaw = isset($colMap['purchase_price']) ? $row[$colMap['purchase_price']] : 0;
                $yearRaw = isset($colMap['year_purchased']) ? $row[$colMap['year_purchased']] : null;
                $merk = isset($colMap['merk']) ? $row[$colMap['merk']] : null;
                $category = isset($colMap['category']) ? $row[$colMap['category']] : 'Peralatan dan Mesin';
                
                // Jika tidak ada nibar, generate random string
                if (empty($nibar)) {
                    $nibar = 'AST-' . time() . '-' . rand(100,999);
                }

                // Bersihkan harga (hilangkan Rp, titik, koma)
                $price = 0;
                if (!empty($priceRaw)) {
                    $priceRaw = preg_replace('/[^0-9]/', '', $priceRaw);
                    $price = (float) $priceRaw;
                }

                // Ambil tahun saja jika formatnya tanggal
                $year = date('Y');
                if (!empty($yearRaw)) {
                    if (is_numeric($yearRaw) && strlen($yearRaw) == 4) {
                        $year = $yearRaw;
                    } elseif (strtotime($yearRaw)) {
                        $year = date('Y', strtotime($yearRaw));
                    } else {
                        // Coba cari pola 4 digit
                        preg_match('/\d{4}/', $yearRaw, $matches);
                        if (!empty($matches)) {
                            $year = $matches[0];
                        }
                    }
                }

                $finalName = $namaBarang;
                if (!empty($merk)) {
                    $finalName .= ' - ' . $merk;
                }

                // Masukkan ke DB
                Asset::updateOrCreate(
                    ['asset_code' => $nibar],
                    [
                        'name' => $finalName,
                        'kode_108' => $kode108,
                        'no_register' => $noRegister,
                        'location' => $location ?: 'Puskesmas',
                        'year_purchased' => $year,
                        'purchase_price' => $price,
                        'condition' => 'Baik',
                        'category' => $category, // Kita simpan raw category dulu
                        'source' => $this->source,
                    ]
                );
            }
        }
    }
}

