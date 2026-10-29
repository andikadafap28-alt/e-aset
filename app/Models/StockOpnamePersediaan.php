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
     * Konversi angka ke kata terbilang Bahasa Indonesia
     */
    public static function angkaKeKata($angka)
    {
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $angka = (int)$angka;

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return self::angkaKeKata($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return self::angkaKeKata(intval($angka / 10)) . ' Puluh' . (($angka % 10 != 0) ? ' ' . self::angkaKeKata($angka % 10) : '');
        } elseif ($angka < 200) {
            return 'Seratus' . (($angka - 100 != 0) ? ' ' . self::angkaKeKata($angka - 100) : '');
        } elseif ($angka < 1000) {
            return self::angkaKeKata(intval($angka / 100)) . ' Ratus' . (($angka % 100 != 0) ? ' ' . self::angkaKeKata($angka % 100) : '');
        } elseif ($angka < 2000) {
            return 'Seribu' . (($angka - 1000 != 0) ? ' ' . self::angkaKeKata($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            return self::angkaKeKata(intval($angka / 1000)) . ' Ribu' . (($angka % 1000 != 0) ? ' ' . self::angkaKeKata($angka % 1000) : '');
        }
        return (string)$angka;
    }

    public function getHariTerbilang()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('dddd');
    }

    public function getTanggalTerbilang()
    {
        $day = (int)Carbon::parse($this->tanggal_opname)->format('d');
        return self::angkaKeKata($day);
    }

    public function getBulanTerbilang()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('MMMM');
    }

    public function getTahunTerbilang()
    {
        $year = (int)Carbon::parse($this->tanggal_opname)->format('Y');
        return self::angkaKeKata($year);
    }

    public function getTanggalFormatted()
    {
        return Carbon::parse($this->tanggal_opname)->locale('id')->isoFormat('D MMMM Y');
    }
}
