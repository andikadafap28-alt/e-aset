<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StockOpnamePersediaan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_opname' => 'date',
        'atk' => 'float',
        'bahan_cetak' => 'float',
        'benda_pos' => 'float',
        'bahan_komputer' => 'float',
        'obat_blud' => 'float',
        'bmhp_blud' => 'float',
        'obat_apbd' => 'float',
        'bmhp_apbd' => 'float',
        'obat_apbn' => 'float',
        'bmhp_apbn' => 'float',
        'custom_data' => 'array',
    ];

    public function getTotalObatBludAttribute()
    {
        return (float)($this->obat_blud + $this->bmhp_blud);
    }

    public function getTotalObatApbdAttribute()
    {
        return (float)($this->obat_apbd + $this->bmhp_apbd);
    }

    public function getTotalObatApbnAttribute()
    {
        return (float)($this->obat_apbn + $this->bmhp_apbn);
    }

    public function getTotalNilaiAttribute()
    {
        return (float)(
            $this->atk +
            $this->bahan_cetak +
            $this->benda_pos +
            $this->bahan_komputer +
            $this->total_obat_blud +
            $this->total_obat_apbd +
            $this->total_obat_apbn
        );
    }

    /**
     * Mengembalikan daftar rekening untuk Berita Acara (Halaman 1)
     */
    public function getRekeningSummaryList()
    {
        $all = [
            [
                'key' => 'atk',
                'name' => 'Alat Tulis Kantor',
                'val' => (float)$this->atk
            ],
            [
                'key' => 'bahan_cetak',
                'name' => 'Bahan Cetak',
                'val' => (float)$this->bahan_cetak
            ],
            [
                'key' => 'benda_pos',
                'name' => 'Benda Pos',
                'val' => (float)$this->benda_pos
            ],
            [
                'key' => 'bahan_komputer',
                'name' => 'Bahan Komputer',
                'val' => (float)$this->bahan_komputer
            ],
            [
                'key' => 'obat_blud',
                'name' => 'Obat BLUD',
                'val' => (float)$this->total_obat_blud
            ],
            [
                'key' => 'obat_apbd',
                'name' => 'Obat APBD',
                'val' => (float)$this->total_obat_apbd
            ],
            [
                'key' => 'obat_apbn',
                'name' => 'Obat APBN',
                'val' => (float)$this->total_obat_apbn
            ],
        ];

        // Saring hanya yang memiliki nilai > 0
        $filtered = array_filter($all, function($item) {
            return $item['val'] > 0;
        });

        // Jika tidak ada nilai sama sekali (>0), kembalikan semua agar tabel tetap berstruktur
        if (empty($filtered)) {
            $filtered = $all;
        }

        return array_values($filtered);
    }

    /**
     * Konversi angka ke kata terbilang Bahasa Indonesia lengkap (Ratusan, Ribuan, Jutaan, Milyaran)
     */
    public static function terbilang($angka)
    {
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $angka = abs((float)$angka);

        if ($angka < 12) {
            return $bilangan[(int)$angka];
        } elseif ($angka < 20) {
            return self::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return self::terbilang((int)($angka / 10)) . ' Puluh' . (($angka % 10 != 0) ? ' ' . self::terbilang($angka % 10) : '');
        } elseif ($angka < 200) {
            return 'Seratus' . (($angka - 100 != 0) ? ' ' . self::terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            return self::terbilang((int)($angka / 100)) . ' Ratus' . (($angka % 100 != 0) ? ' ' . self::terbilang($angka % 100) : '');
        } elseif ($angka < 2000) {
            return 'Seribu' . (($angka - 1000 != 0) ? ' ' . self::terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            return self::terbilang((int)($angka / 1000)) . ' Ribu' . (($angka % 1000 != 0) ? ' ' . self::terbilang($angka % 1000) : '');
        } elseif ($angka < 1000000000) {
            return self::terbilang((int)($angka / 1000000)) . ' Juta' . (($angka % 1000000 != 0) ? ' ' . self::terbilang($angka % 1000000) : '');
        } elseif ($angka < 1000000000000) {
            return self::terbilang((int)($angka / 1000000000)) . ' Milyar' . (($angka % 1000000000 != 0) ? ' ' . self::terbilang($angka % 1000000000) : '');
        }
        return (string)$angka;
    }

    public function getTotalNilaiTerbilang()
    {
        $val = round($this->total_nilai);
        if ($val <= 0) return 'Nol Rupiah';
        return trim(self::terbilang($val)) . ' Rupiah';
    }

    /**
     * Format nomor surat agar memiliki space yang cukup lebar untuk penulisan nomor manual menggunakan bolpoin
     */
    public function getFormattedNomorSuratHtml()
    {
        $raw = trim($this->nomor_surat);
        // Pola pemisah slash untuk nomor manual, misal: 000.2.1.2/   /413.102.5.18/2026 atau 000.2.1.2/ /...
        if (preg_match('/^(.*?)\s*\/\s*(\/.*)$/', $raw, $matches)) {
            $prefix = trim($matches[1]);
            $suffix = trim(ltrim($matches[2], '/ '));
            return e($prefix) . ' / <span class="nomor-manual-space" style="display: inline-block; min-width: 85px; text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> / ' . e($suffix);
        }
        return e($raw);
    }

    public function getHariTerbilang()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('dddd');
    }

    public function getTanggalTerbilang()
    {
        $day = (int)Carbon::parse($this->tanggal_opname)->format('d');
        return self::terbilang($day);
    }

    public function getBulanTerbilang()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('MMMM');
    }

    public function getTahunTerbilang()
    {
        $year = (int)Carbon::parse($this->tanggal_opname)->format('Y');
        return self::terbilang($year);
    }

    public function getTanggalFormatted()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('D MMMM Y');
    }
}
