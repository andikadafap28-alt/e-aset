<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'item_id', 
        'jenis_transaksi', 
        'jumlah', 
        'harga_satuan', 
        'keterangan', 
        'tanggal_transaksi', 
        'tanggal_spj',
        'status_hutang'
    ];

    // Memberitahu Laravel bahwa transaksi ini milik satu barang (Item)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}