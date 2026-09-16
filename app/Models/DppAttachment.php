<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DppAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dpp()
    {
        return $this->belongsTo(Dpp::class);
    }
}
