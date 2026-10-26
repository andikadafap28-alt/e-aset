<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\MasterKode108;
use App\Models\Room;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class KibImport implements ToCollection
{
    protected $source;
    protected $fileName;
    protected $importedCount = 0;

    public function __construct($source = 'BMD', $fileName = '')
    {
        $this->source = $source;
        $this->fileName = $fileName;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function collection(Collection $rows)
    {
        // 1. Scan baris awal untuk mendeteksi header jika ada
        $headerMap = [];
        $headerRowIndex = null;

        foreach ($rows->take(15) as $rIdx => $row) {
            $foundKeywords = 0;
            $tempMap = [];
            foreach ($row as $cIdx => $val) {
                $v = strtolower(trim((string)$val));
                if (empty($v)) continue;

                if (str_contains($v, 'kode barang') || str_contains($v, 'kode_108') || str_contains($v, 'kode 108')) {
                    $tempMap['kode_108'] = $cIdx; $foundKeywords++;
                }
                if ($v === 'nibar' || str_contains($v, 'nibar') || str_contains($v, 'kode lama')) {
                    $tempMap['nibar'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'register') || str_contains($v, 'no register') || str_contains($v, 'nibar baru')) {
                    $tempMap['no_register'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'spesifikasi nama')) {
                    $tempMap['name_spec'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'nama barang') || str_contains($v, 'uraian barang') || str_contains($v, 'nama aset')) {
                    $tempMap['name_primary'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'merek') || str_contains($v, 'merk') || str_contains($v, 'tipe')) {
                    $tempMap['merk'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'lokasi') || str_contains($v, 'alamat') || str_contains($v, 'ruangan')) {
                    $tempMap['location'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'satuan perolehan') || str_contains($v, 'harga satuan') || (str_contains($v, 'harga') && !isset($tempMap['purchase_price']))) {
                    $tempMap['purchase_price'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'nilai perolehan') || str_contains($v, 'total harga')) {
                    $tempMap['nilai_perolehan'] = $cIdx; $foundKeywords++;
                }
                if (str_contains($v, 'tanggal') || str_contains($v, 'tahun perolehan') || str_contains($v, 'tahun')) {
                    if (!isset($tempMap['year_purchased'])) {
                        $tempMap['year_purchased'] = $cIdx; $foundKeywords++;
                    }
                }
                if (str_contains($v, 'kondisi')) {
                    $tempMap['condition'] = $cIdx;
                }
                if (str_contains($v, 'keterangan')) {
                    $tempMap['keterangan'] = $cIdx;
                }
                if (str_contains($v, 'spesifikasi lainnya') || str_contains($v, 'bahan')) {
                    $tempMap['bahan'] = $cIdx;
                }
            }

            if ($foundKeywords >= 3) {
                $headerMap = $tempMap;
                $headerRowIndex = $rIdx;
                break;
            }
        }

        // Cache untuk optimasi query massal
        $masterKodeCache = [];
        $maxRegisterCache = [];
        $roomList = Room::all();
        $categoryList = AssetCategory::all();
        $batch = [];

        foreach ($rows as $index => $row) {
            // Lewati baris header jika terdeteksi
            if ($headerRowIndex !== null && $index <= $headerRowIndex) {
                continue;
            }

            // Lewati baris yang sepenuhnya kosong
            $nonEmptyCells = array_filter(iterator_to_array($row), function($c) {
                return trim((string)$c) !== '';
            });
            if (empty($nonEmptyCells)) {
                continue;
            }

            // --- DETEKSI FORMAT KOLOM ---
            // Format 1: Format Resmi BMD SIMBADA (22 Kolom Sesuai File Pengguna)
            // Kolom 0-6 berisi 7 pecahan kode 108 berturut-turut, Kolom 7 berisi Nama Barang (bukan angka)
            $isFormatBmd22 = false;
            if (isset($row[0]) && isset($row[1]) && isset($row[2]) && isset($row[6]) && isset($row[7])
                && is_numeric(trim((string)$row[0])) 
                && is_numeric(trim((string)$row[1])) 
                && is_numeric(trim((string)$row[2]))
                && !is_numeric(trim((string)$row[7]))) {
                $isFormatBmd22 = true;
            }

            // Format 2: Format KIB lama dengan kolom G (indeks 6) kosong, dan kode ke-7 di kolom H (indeks 7)
            $isFormatKibOld = false;
            if (!$isFormatBmd22 && isset($row[0]) && isset($row[1]) && isset($row[7]) && isset($row[8])
                && is_numeric(trim((string)$row[0]))
                && is_numeric(trim((string)$row[7]))
                && !is_numeric(trim((string)$row[8]))) {
                $isFormatKibOld = true;
            }

            $kode108 = null;
            $namaBarang = null;
            $nibar = null;
            $noRegisterRaw = null;
            $nameSpec = null;
            $bahan = null;
            $merk = null;
            $location = null;
            $priceRaw = null;
            $yearRaw = null;
            $condition = 'Baik';
            $keterangan = null;
            $caraPerolehan = null;

            if ($isFormatBmd22) {
                // Kolom 0-6: Pecahan Kode 108
                $parts = [];
                for ($i = 0; $i <= 6; $i++) {
                    $val = trim((string)($row[$i] ?? ''));
                    if ($val === '') continue;
                    if ($i >= 6) {
                        $parts[] = str_pad($val, 3, '0', STR_PAD_LEFT);
                    } elseif ($i >= 3) {
                        $parts[] = str_pad($val, 2, '0', STR_PAD_LEFT);
                    } else {
                        $parts[] = $val;
                    }
                }
                $kode108 = implode('.', $parts);

                $namaBarang = $row[7] ?? null;
                $nibarLama = $row[8] ?? null;
                $nibarBaru = $row[9] ?? null;
                $nameSpec = $row[10] ?? null;
                $bahan = $row[11] ?? null;
                $merk = $row[12] ?? null;
                $location = $row[13] ?? null;
                $priceRaw = !empty($row[16]) ? $row[16] : ($row[17] ?? 0);
                $caraPerolehan = $row[18] ?? null;
                $yearRaw = $row[19] ?? null;
                $keterangan = $row[21] ?? null;

                // NIBAR & Register
                $nibar = !empty($nibarBaru) ? trim((string)$nibarBaru) : trim((string)$nibarLama);
                $noRegisterRaw = !empty($nibarBaru) ? trim((string)$nibarBaru) : trim((string)$nibarLama);

            } elseif ($isFormatKibOld) {
                // Kolom 0-5 lalu 7: Pecahan Kode 108
                $parts = [];
                $indices = [0, 1, 2, 3, 4, 5, 7];
                foreach ($indices as $idx => $col) {
                    $val = trim((string)($row[$col] ?? ''));
                    if ($val === '') continue;
                    if ($col == 7) {
                        $parts[] = str_pad($val, 3, '0', STR_PAD_LEFT);
                    } elseif ($col >= 3) {
                        $parts[] = str_pad($val, 2, '0', STR_PAD_LEFT);
                    } else {
                        $parts[] = $val;
                    }
                }
                $kode108 = implode('.', $parts);

                $namaBarang = $row[8] ?? null;
                $nibar = $row[9] ?? null;
                $noRegisterRaw = $row[10] ?? null;
                $nameSpec = $row[11] ?? null;
                $bahan = $row[12] ?? null;
                $merk = $row[14] ?? null;
                $location = $row[15] ?? null;
                $priceRaw = !empty($row[22]) ? $row[22] : ($row[23] ?? 0);
                $caraPerolehan = $row[24] ?? null;
                $yearRaw = $row[25] ?? null;
                $keterangan = $row[28] ?? null;

            } elseif (!empty($headerMap)) {
                // Gunakan mapping header hasil scan
                if (isset($headerMap['kode_108'])) $kode108 = $row[$headerMap['kode_108']] ?? null;
                if (isset($headerMap['name_primary'])) $namaBarang = $row[$headerMap['name_primary']] ?? null;
                if (isset($headerMap['nibar'])) $nibar = $row[$headerMap['nibar']] ?? null;
                if (isset($headerMap['no_register'])) $noRegisterRaw = $row[$headerMap['no_register']] ?? null;
                if (isset($headerMap['name_spec'])) $nameSpec = $row[$headerMap['name_spec']] ?? null;
                if (isset($headerMap['merk'])) $merk = $row[$headerMap['merk']] ?? null;
                if (isset($headerMap['location'])) $location = $row[$headerMap['location']] ?? null;
                if (isset($headerMap['purchase_price'])) $priceRaw = $row[$headerMap['purchase_price']] ?? 0;
                if (isset($headerMap['year_purchased'])) $yearRaw = $row[$headerMap['year_purchased']] ?? null;
                if (isset($headerMap['condition'])) $condition = $row[$headerMap['condition']] ?? 'Baik';
                if (isset($headerMap['keterangan'])) $keterangan = $row[$headerMap['keterangan']] ?? null;
                if (isset($headerMap['bahan'])) $bahan = $row[$headerMap['bahan']] ?? null;
            } else {
                // Fallback default kolom jika file flat
                $kode108 = $row[0] ?? null;
                $namaBarang = $row[1] ?? null;
                $nibar = $row[2] ?? null;
                $noRegisterRaw = $row[3] ?? null;
                $merk = $row[4] ?? null;
                $location = $row[5] ?? null;
                $priceRaw = $row[6] ?? 0;
                $yearRaw = $row[7] ?? null;
            }

            // Nama barang final (jika nama_primary kosong gunakan name_spec)
            $finalName = trim((string)$namaBarang);
            if (empty($finalName) && !empty($nameSpec)) {
                $finalName = trim((string)$nameSpec);
            }

            // Abaikan jika nama kosong atau baris subtotal / header berulang
            $lowerName = strtolower($finalName);
            if (empty($lowerName) 
                || str_contains($lowerName, 'nama barang') 
                || str_contains($lowerName, 'spesifikasi nama')
                || str_contains($lowerName, 'jumlah')
                || str_contains($lowerName, 'sub total')
                || str_contains($lowerName, 'total')) {
                continue;
            }

            // Bersihkan Kode 108
            $kode108 = trim((string)$kode108);
            if (empty($kode108) && !empty($nibar) && preg_match('/^\d+(\.\d+)+/', $nibar, $mKode)) {
                $kode108 = $mKode[0];
            }

            // Jangan import baris penomoran template (misal 1 . 3)
            if ($kode108 === '1.3' || $kode108 === '1' || $lowerName === 'lap top' && empty($nibar)) {
                if (empty($nibar) && empty($priceRaw)) continue;
            }

            // --- EKSTRAKSI NOMOR REGISTER & MENCEGAH INTEGER OVERFLOW ---
            $noRegister = null;
            if (!empty($noRegisterRaw)) {
                $cleanReg = trim((string)$noRegisterRaw);
                // Jika berbentuk string register bertitik di akhir (cth: 1.3.02...2023.001 atau .001)
                if (preg_match('/\.(\d{1,6})$/', $cleanReg, $mReg)) {
                    $noRegister = (int)$mReg[1];
                } elseif (is_numeric($cleanReg) && strlen($cleanReg) <= 6) {
                    $noRegister = (int)$cleanReg;
                }
            }

            // Jika belum ada nomor register, autogenerate secara sekuensial per kode 108
            if (empty($noRegister)) {
                if (!array_key_exists($kode108, $maxRegisterCache)) {
                    $lastReg = Asset::where('kode_108', $kode108)->max('no_register');
                    $maxRegisterCache[$kode108] = $lastReg ? (int)$lastReg : 0;
                }
                $maxRegisterCache[$kode108]++;
                $noRegister = $maxRegisterCache[$kode108];
            }

            // NIBAR / Asset Code unik
            $nibar = trim((string)$nibar);
            if (empty($nibar)) {
                $nibar = 'AST-' . ($kode108 ? str_replace('.', '', $kode108) . '-' : '') . str_pad($noRegister, 4, '0', STR_PAD_LEFT);
            }

            // --- EKSTRAKSI TAHUN PEROLEHAN SECARA CERDAS ---
            $year = null;
            // 1. Coba deteksi tahun 4 digit dari NIBAR (cth: .2023.001)
            if (!empty($nibar) && preg_match('/\.(20\d{2}|19\d{2})\./', $nibar, $mYear)) {
                $year = (int)$mYear[1];
            }
            // 2. Jika belum ketemu, cek yearRaw
            if (!$year && !empty($yearRaw)) {
                $yStr = trim((string)$yearRaw);
                if (is_numeric($yStr)) {
                    if (strlen($yStr) == 4) {
                        $year = (int)$yStr;
                    } elseif (strlen($yStr) == 8 && (str_starts_with($yStr, '19') || str_starts_with($yStr, '20'))) {
                        // Format YYYYMMDD (cth: 19910206)
                        $year = (int)substr($yStr, 0, 4);
                    } elseif ($yStr > 10000 && $yStr < 100000) {
                        try {
                            $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($yStr);
                            $year = (int)$dateObj->format('Y');
                        } catch (\Exception $e) {
                            $year = (int)substr($yStr, 0, 4);
                        }
                    }
                } elseif (preg_match('/(20\d{2}|19\d{2})/', $yStr, $mYear2)) {
                    $year = (int)$mYear2[1];
                }
            }
            // 3. Fallback jika masih kosong
            if (!$year) {
                $year = (int)date('Y');
            }

            // --- PEMBERSIHAN HARGA RUPIAH (BEBAS BUG SEN ,00) ---
            $price = 0;
            if (!empty($priceRaw)) {
                $pStr = trim((string)$priceRaw);
                // Hilangkan desimal/sen rupiah di akhir (misal ,00 atau ,50)
                if (preg_match('/,(\d{1,2})$/', $pStr, $mCents)) {
                    $pStr = substr($pStr, 0, -strlen($mCents[0]));
                }
                // Hapus semua karakter selain angka
                $cleanedDigits = preg_replace('/[^0-9]/', '', $pStr);
                if ($cleanedDigits !== '') {
                    $price = (float)$cleanedDigits;
                }
            }

            // --- DETEKSI KATEGORI ASET DARI KODE 108 / MASTER KODE ---
            $category = 'Peralatan dan Mesin';
            if (!empty($kode108)) {
                if (!array_key_exists($kode108, $masterKodeCache)) {
                    $mk = MasterKode108::where('kode', $kode108)->first();
                    $masterKodeCache[$kode108] = $mk ? $mk->uraian : null;
                }
                if (!empty($masterKodeCache[$kode108])) {
                    $category = $masterKodeCache[$kode108];
                } else {
                    if (str_starts_with($kode108, '1.3.1') || str_starts_with($kode108, '1.3.01')) $category = 'Tanah';
                    elseif (str_starts_with($kode108, '1.3.2') || str_starts_with($kode108, '1.3.02')) $category = 'Peralatan dan Mesin';
                    elseif (str_starts_with($kode108, '1.3.3') || str_starts_with($kode108, '1.3.03')) $category = 'Gedung dan Bangunan';
                    elseif (str_starts_with($kode108, '1.3.4') || str_starts_with($kode108, '1.3.04')) $category = 'Jalan, Irigasi dan Jaringan';
                    elseif (str_starts_with($kode108, '1.3.5') || str_starts_with($kode108, '1.3.05')) $category = 'Aset Tetap Lainnya';
                    elseif (str_starts_with($kode108, '1.3.6') || str_starts_with($kode108, '1.3.06')) $category = 'Konstruksi dalam Pengerjaan';
                }
            }

            // Cocokkan category_id jika ada relasi
            $matchedCategoryId = null;
            foreach ($categoryList as $cat) {
                if (stripos($finalName, $cat->nama_kategori) !== false || stripos($category, $cat->nama_kategori) !== false) {
                    $matchedCategoryId = $cat->id;
                    break;
                }
            }
            if (!$matchedCategoryId && $categoryList->isNotEmpty()) {
                $matchedCategoryId = $categoryList->first()->id;
            }

            // Bersihkan format Merk (misal: "Merk: HP NOTEBOOK" -> "HP NOTEBOOK")
            $merkClean = trim((string)$merk);
            if (preg_match('/^merk\s*:\s*(.*)$/i', $merkClean, $mMerk)) {
                $merkClean = trim($mMerk[1]);
            }

            // Gabungkan merk ke nama jika belum ada
            if (!empty($merkClean) && stripos($finalName, $merkClean) === false) {
                $finalName .= ' - ' . $merkClean;
            }

            // Lokasi & Pencocokan Room ID
            $loc = trim((string)$location) ?: 'Puskesmas';
            $matchedRoomId = null;
            foreach ($roomList as $room) {
                if (stripos($room->name, $loc) !== false || stripos($loc, $room->name) !== false) {
                    $matchedRoomId = $room->id;
                    break;
                }
            }

            // Format tanggal BAST / Perolehan
            $tanggalBast = "$year-01-01";
            if (!empty($yearRaw)) {
                $yStr = trim((string)$yearRaw);
                if (strlen($yStr) == 8 && is_numeric($yStr) && (str_starts_with($yStr, '19') || str_starts_with($yStr, '20'))) {
                    $yPart = substr($yStr, 0, 4);
                    $mPart = substr($yStr, 4, 2);
                    $dPart = substr($yStr, 6, 2);
                    if (checkdate((int)$mPart, (int)$dPart, (int)$yPart)) {
                        $tanggalBast = "$yPart-$mPart-$dPart";
                    }
                } elseif (strtotime($yStr)) {
                    $tanggalBast = date('Y-m-d', strtotime($yStr));
                }
            }

            $batch[] = [
                'asset_code' => $nibar,
                'name' => substr($finalName, 0, 255),
                'kode_108' => $kode108,
                'no_register' => $noRegister,
                'location' => substr($loc, 0, 255),
                'room_id' => $matchedRoomId,
                'category_id' => $matchedCategoryId,
                'year_purchased' => $year,
                'harga_perolehan' => $price,
                'condition' => in_array($condition, ['Baik', 'Rusak Ringan', 'Rusak Berat']) ? $condition : 'Baik',
                'category' => substr($category, 0, 255),
                'source' => $this->source,
                'merk' => substr($merkClean, 0, 255),
                'bahan' => !empty($bahan) ? substr(trim((string)$bahan), 0, 255) : null,
                'keterangan' => !empty($keterangan) ? trim((string)$keterangan) : ($caraPerolehan ? "Perolehan: $caraPerolehan" : null),
                'tanggal_bast' => $tanggalBast,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        // Eksekusi Simpan Massal (Update or Create berdasarkan asset_code)
        DB::beginTransaction();
        try {
            foreach ($batch as $data) {
                Asset::updateOrCreate(
                    ['asset_code' => $data['asset_code']],
                    $data
                );
            }
            DB::commit();
            $this->importedCount = count($batch);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
