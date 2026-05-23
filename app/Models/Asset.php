<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = ['id'];

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class, 'asset_id');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class, 'asset_id');
    }

    /**
     * Menghitung nilai penyusutan per tahun
     */
    public function getAnnualDepreciationAttribute()
    {
        if (!$this->harga_perolehan || !$this->category || !$this->category->umur_ekonomis) {
            return 0;
        }

        // Residu Rp 1
        $depreciableCost = $this->harga_perolehan - 1;
        if ($depreciableCost <= 0) return 0;

        return $depreciableCost / $this->category->umur_ekonomis;
    }

    /**
     * Menghitung total akumulasi penyusutan
     */
    public function getAccumulatedDepreciationAttribute()
    {
        if (!$this->harga_perolehan || !$this->category || !$this->category->umur_ekonomis || !$this->year_purchased) {
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
}
