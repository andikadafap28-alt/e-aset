<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'status_aktif' => 'boolean',
        'is_in_print_queue' => 'boolean',
    ];

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class, 'asset_id');
    }

    public function calibrations()
    {
        return $this->hasMany(AssetCalibration::class, 'asset_id');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class, 'asset_id');
    }

    public function corrections()
    {
        return $this->hasMany(AssetCorrection::class, 'asset_id');
    }

    /**
     * Menghitung nilai penyusutan per tahun
     */
    public function getAnnualDepreciationAttribute()
    {
        $category = $this->getRelationValue('category');
        if (!$this->harga_perolehan || !$category || !$category->umur_ekonomis) {
            return 0;
        }

        // Residu Rp 1
        $depreciableCost = $this->harga_perolehan - 1;
        if ($depreciableCost <= 0) return 0;

        return $depreciableCost / $category->umur_ekonomis;
    }

    /**
     * Menghitung total akumulasi penyusutan
     */
    public function getAccumulatedDepreciationAttribute()
    {
        $category = $this->getRelationValue('category');
        if (!$this->harga_perolehan || !$category || !$category->umur_ekonomis || !$this->year_purchased) {
            return 0;
        }

        $yearsUsed = date('Y') - $this->year_purchased;
        if ($yearsUsed < 0) $yearsUsed = 0;

        $accumulated = $this->annual_depreciation * $yearsUsed;
        
        $depreciableCost = $this->harga_perolehan - 1;

        if ($accumulated > $depreciableCost) {
            $accumulated = $depreciableCost;
        }

        return $accumulated;
    }

    /**
     * Menghitung nilai buku saat ini (Book Value)
     */
    public function getBookValueAttribute()
    {
        if (!$this->harga_perolehan) {
            return 0;
        }
        return $this->harga_perolehan - $this->accumulated_depreciation;
    }

    /**
     * Mendapatkan Kode 108 lengkap (7 level)
     * Jika kode terpotong 6 level di database, sistem mengambil sub-sub rincian dari NIBAR
     */
    public function getFullKode108Attribute()
    {
        $kode = trim((string)$this->kode_108);
        if (empty($kode)) {
            return '-';
        }

        // Jika kurang dari 7 level (titik kurang dari 6) dan NIBAR memiliki 45 digit
        if (substr_count($kode, '.') < 6 && strlen($this->asset_code) >= 38) {
            if (preg_match('/(20\d{2}|19\d{2})13\d{7}(\d{3})\d{7}/', $this->asset_code, $m)) {
                $kode .= '.' . $m[2];
            }
        }

        return $kode;
    }

    /**
     * Format Kode Lengkap beserta Nomor Register (persis seperti pada stiker fisik)
     * Contoh: 1.3.2.07.01.01.005 - 012
     */
    public function getKodeRegisterLengkapAttribute()
    {
        $kode = $this->full_kode_108;
        if ($kode === '-') {
            return '-';
        }

        if ($this->no_register) {
            return $kode . ' - ' . str_pad($this->no_register, 3, '0', STR_PAD_LEFT);
        }

        return $kode;
    }

    /**
     * Nama aset bersih tanpa akhiran strip/spasi
     */
    public function getCleanNameAttribute()
    {
        $clean = str_replace(' - ya', '', (string)$this->name);
        return rtrim(trim($clean), " \t\n\r\0\x0B-");
    }
}
