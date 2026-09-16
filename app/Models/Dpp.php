<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dpp extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_dpp' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'harga_satuan' => 'decimal:2',
        'pagu_anggaran' => 'decimal:2',
    ];

    public function attachments()
    {
        return $this->hasMany(DppAttachment::class);
    }
}
