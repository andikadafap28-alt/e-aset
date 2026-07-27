<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHandover extends Model
{
    protected $guarded = ['id'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
