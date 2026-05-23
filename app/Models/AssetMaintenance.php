<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'jenis_pemeliharaan',
        'tanggal_jadwal',
        'tanggal_pelaksanaan',
        'biaya',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_jadwal' => 'date',
        'tanggal_pelaksanaan' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
