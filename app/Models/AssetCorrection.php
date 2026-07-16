<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCorrection extends Model
{
    protected $fillable = [
        'asset_id',
        'jenis_koreksi',
        'nilai_lama',
        'nilai_baru',
        'kondisi_lama',
        'kondisi_baru',
        'keterangan'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
