<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'penanggung_jawab',
        'latitude',
        'longitude',
        'description',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function kirItems()
    {
        return $this->hasMany(KirItem::class)->orderBy('name');
    }
}
