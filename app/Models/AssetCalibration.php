<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCalibration extends Model
{
    protected $guarded = ['id'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
