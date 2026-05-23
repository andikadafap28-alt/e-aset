<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementFile extends Model
{
    protected $table = 'procurement_files';

    protected $fillable = [
        'item_id',
        'kategori',
        'jenis_dokumen',
        'nama_penyedia',
        'file_name',
        'drive_file_id',
        'tanggal_dokumen',
        'path_gdrive',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
