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
    protected $fileName;

    public function __construct($source = 'BMD', $fileName = '')
    {
        $this->source = $source;
        $this->fileName = $fileName;
    }

    public function collection(Collection $rows)
    {
        $headerFound = false;
        $colMap = []; // Akan menyimpan index kolom

        // Coba scan 15 baris pertama untuk mencari header
        foreach ($rows->take(15) as $rowIndex => $row) {
            foreach ($row as $colIdx => $colVal) {
                $val = strtolower(trim((string)$colVal));
                if (empty($val)) continue;

                if (str_contains($val, 'kode barang') || str_contains($val, 'kode_108')) $colMap['kode_108'] = $colIdx;
                if ($val === 'nibar' || str_contains($val, 'nibar')) $colMap['nibar'] = $colIdx;
                if (str_contains($val, 'register')) $colMap['no_register'] = $colIdx;
                if (str_contains($val, 'spesifikasi nama')) {
                    $colMap['name_spec'] = $colIdx;
                } 
                if (str_contains($val, 'nama barang') || str_contains($val, 'uraian')) {
                    $colMap['name_primary'] = $colIdx;
                }
                if (str_contains($val, 'merek') || str_contains($val, 'merk') || str_contains($val, 'tipe')) $colMap['merk'] = $colIdx;
                if (str_contains($val, 'lokasi') || str_contains($val, 'alamat')) $colMap['location'] = $colIdx;
                if (str_contains($val, 'harga') || str_contains($val, 'satuan perolehan') || str_contains($val, 'nilai')) {
                    if (!isset($colMap['purchase_price'])) $colMap['purchase_price'] = $colIdx;
                }
                if (str_contains($val, 'tanggal') || str_contains($val, 'tahun')) {
                    if (!isset($colMap['year_purchased'])) $colMap['year_purchased'] = $colIdx;
                }
                if (str_contains($val, 'kategori') || str_contains($val, 'kib')) $colMap['category'] = $colIdx;
                if (str_contains($val, 'penyedia') || str_contains($val, 'pihak ketiga')) $colMap['penyedia'] = $colIdx;
            }
        }

        // Jika tidak ketemu 'nibar', kita berasumsi ini bukan file KIB yang valid atau kita gunakan default map
        if (!isset($colMap['nibar'])) {
            $colMap['nibar'] = 7; // Default col 7 if image format KIB B
        }
        if (!isset($colMap['name_primary']) && !isset($colMap['name_spec'])) {
            $colMap['name_primary'] = 8; // Default Nama Barang (kolom I)
            $colMap['name_spec'] = 13; // Default Spesifikasi Nama Barang (kolom N)
        }

        // Caches untuk optimasi performa mass import
        $masterKodeCache = [];
        $maxRegisterCache = [];
        $batch = [];

        foreach ($rows as $index => $row) {
            // Deteksi baris data
            $namaBarang = null;
            if (isset($colMap['name_primary']) && !empty(trim((string)$row[$colMap['name_primary']]))) {
                $namaBarang = $row[$colMap['name_primary']];
            } elseif (isset($colMap['name_spec']) && !empty(trim((string)$row[$colMap['name_spec']]))) {
                $namaBarang = $row[$colMap['name_spec']];
            }
            $nibarVal = isset($colMap['nibar']) ? $row[$colMap['nibar']] : null;
            
            // Skip jika kosong atau itu adalah baris header
            if (empty(trim((string)$namaBarang)) || strtolower(trim((string)$namaBarang)) == 'spesifikasi nama barang' || strtolower(trim((string)$namaBarang)) == 'nama barang') {
                continue;
            }

            // Ekstrak Kode 108
            $kode108 = null;
            // Jika kode 108 terpecah di 6 kolom pertama (format BMD KIB)
            if (isset($row[0]) && isset($row[1]) && isset($row[2]) && is_numeric($row[0]) && is_numeric($row[1])) {
                $parts = [];
                for ($i=0; $i<=5; $i++) {
                    if (isset($row[$i]) && trim((string)$row[$i]) !== '') {
                        $parts[] = str_pad(trim((string)$row[$i]), ($i >= 3 ? 2 : 1), '0', STR_PAD_LEFT);
                    }
                }
                if (count($parts) >= 3) {
                    $kode108 = implode('.', $parts);
                }
            } else if (isset($colMap['kode_108'])) {
                $kode108 = $row[$colMap['kode_108']];
            }

            $nibar = $nibarVal;
            $noRegister = isset($colMap['no_register']) ? $row[$colMap['no_register']] : null;
            
            // Nomor register di DB bertipe integer. Jika data dari KIB berupa NIBAR (27 digit), kita set null
            // agar tidak menyebabkan error SQL Integer Overflow (max 2147483647).
            if (!empty($noRegister) && (strlen((string)$noRegister) > 9 || !is_numeric($noRegister))) {
                $noRegister = null;
            }

            $location = isset($colMap['location']) ? $row[$colMap['location']] : null;
            $priceRaw = isset($colMap['purchase_price']) ? $row[$colMap['purchase_price']] : 0;
            $yearRaw = isset($colMap['year_purchased']) ? $row[$colMap['year_purchased']] : null;
            $year = (!empty($yearRaw) && is_numeric(trim((string)$yearRaw)) && strlen(trim((string)$yearRaw)) == 4) 
                ? (int)trim((string)$yearRaw) 
                : ((!empty($yearRaw) && strtotime(trim((string)$yearRaw))) ? date('Y', strtotime(trim((string)$yearRaw))) : date('Y'));
            $price = (!empty($priceRaw) && is_numeric(trim((string)$priceRaw))) ? (float)trim((string)$priceRaw) : 0;

            // Lewati baris penomoran template (baris 3) di mana kode_108 berisi '1' dan nibar berisi '3'
            if (trim((string)$kode108) === '1' && trim((string)$nibar) === '3') {
                continue;
            }

            $category = 'Peralatan dan Mesin';
            $k108 = (string)$kode108;
            $fname = strtoupper($this->fileName ?? '');
            
            $masterKode = null;
            if (!empty($kode108)) {
                if (!array_key_exists($kode108, $masterKodeCache)) {
                    $masterKodeObj = \App\Models\MasterKode108::where('kode', $kode108)->first();
                    $masterKodeCache[$kode108] = $masterKodeObj ? $masterKodeObj->uraian : null;
                }
                
                if ($masterKodeCache[$kode108]) {
                    $category = $masterKodeCache[$kode108];
                    $masterKode = true;
                }
            }

            if (!$masterKode) {
                if (isset($colMap['category']) && !empty($row[$colMap['category']])) {
                    $category = $row[$colMap['category']];
                } elseif (isset($row[6]) && strtoupper(trim((string)$row[6])) == $row[6] && !empty($row[6]) && strlen(trim((string)$row[6])) > 3) {
                    // Seringkali kolom 6 berisi Nama Grup (cth: ALAT ANGKUTAN)
                    $category = ucwords(strtolower(trim((string)$row[6])));
                } else {
                    // Deteksi KIB otomatis berdasarkan nama file atau awalan kode_108
                    if (str_contains($fname, 'KIB A')) $category = 'Tanah';
                    elseif (str_contains($fname, 'KIB B')) $category = 'Peralatan dan Mesin';
                    elseif (str_contains($fname, 'KIB C')) $category = 'Gedung dan Bangunan';
                    elseif (str_contains($fname, 'KIB D')) $category = 'Jalan, Irigasi dan Jaringan';
                    elseif (str_contains($fname, 'KIB E')) $category = 'Aset Tetap Lainnya';
                    elseif (str_contains($fname, 'KIB F')) $category = 'Konstruksi dalam Pengerjaan';
                    else {
                        if (str_starts_with($k108, '1.3.1')) $category = 'Tanah';
                        elseif (str_starts_with($k108, '1.3.2')) $category = 'Peralatan dan Mesin';
                        elseif (str_starts_with($k108, '1.3.3')) $category = 'Gedung dan Bangunan';
                        elseif (str_starts_with($k108, '1.3.4')) $category = 'Jalan, Irigasi dan Jaringan';
                        elseif (str_starts_with($k108, '1.3.5')) $category = 'Aset Tetap Lainnya';
                        elseif (str_starts_with($k108, '1.3.6')) $category = 'Konstruksi dalam Pengerjaan';
                    }
                }
            }

            $merk = isset($colMap['merk']) ? $row[$colMap['merk']] : null;
            $penyedia = isset($colMap['penyedia']) ? $row[$colMap['penyedia']] : null;

            // Jika tidak ada nibar, no register, dan kode108 asli kosong, kemungkinan ini baris sub-total, lewati
            if (empty($nibar) && empty($noRegister) && empty($kode108)) {
                continue;
            }
            
            // Autogenerate no_register HANYA JIKA ini bukan baris kosong
            if (empty($noRegister)) {
                if (!array_key_exists($kode108, $maxRegisterCache)) {
                    $lastReg = \App\Models\Asset::where('kode_108', $kode108)->max('no_register');
                    $maxRegisterCache[$kode108] = $lastReg ? (int)$lastReg : 0;
                }
                $maxRegisterCache[$kode108]++;
                $noRegister = $maxRegisterCache[$kode108];
            }

            if (empty($nibar)) {
                $nibar = 'AST-' . time() . '-' . rand(100,999) . $index;
            }

            // Bersihkan harga (hilangkan Rp, titik, koma, spasi)
            $price = 0;
            if (!empty($priceRaw)) {
                $priceRaw = preg_replace('/[^0-9]/', '', (string)$priceRaw);
                $price = (float) $priceRaw;
            }

            // Ambil tahun saja
            $year = date('Y');
            if (!empty($yearRaw)) {
                $yearStr = trim((string)$yearRaw);
                if (is_numeric($yearStr) && strlen($yearStr) == 4) {
                    $year = $yearStr;
                } elseif (strtotime($yearStr)) {
                    $year = date('Y', strtotime($yearStr));
                } else {
                    preg_match('/\d{4}/', $yearStr, $matches);
                    if (!empty($matches)) {
                        $year = $matches[0];
                    }
                }
            }

            $finalName = $namaBarang;
            if (!empty($merk) && !str_contains(strtolower($finalName), strtolower($merk))) {
                $finalName .= ' - ' . $merk;
            }

            // Pastikan tidak ada data yang null untuk nama
            if (empty($finalName)) continue;

            $batch[] = [
                'asset_code' => $nibar,
                'name' => substr($finalName, 0, 255),
                'kode_108' => $kode108,
                'no_register' => $noRegister,
                'location' => $location ?: 'Puskesmas',
                'year_purchased' => $year,
                'harga_perolehan' => $price,
                'condition' => 'Baik',
                'category' => substr($category, 0, 255),
                'source' => $this->source,
                'merk' => substr($merk, 0, 255),
                'penyedia' => substr($penyedia, 0, 255),
                'tanggal_bast' => (!empty($yearRaw) && strtotime(trim((string)$yearRaw))) ? date('Y-m-d', strtotime(trim((string)$yearRaw))) : "$year-01-01",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Eksekusi upsert massal per 200 baris untuk efisiensi
        foreach (array_chunk($batch, 200) as $chunk) {
            \App\Models\Asset::upsert(
                $chunk,
                ['asset_code'],
                [
                    'name', 'kode_108', 'no_register', 'location', 
                    'year_purchased', 'harga_perolehan', 'condition', 
                    'category', 'source', 'merk', 'penyedia', 'tanggal_bast', 'updated_at'
                ]
            );
        }
    }
}

