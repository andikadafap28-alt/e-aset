<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // Mengizinkan kolom ini untuk diisi data
    protected $fillable = [
    'kode_barang', 
    'nama_barang', 
    'kategori', 
    'kategori_besar',
    'satuan', 
    'stok_sekarang',
    'harga_satuan',
    'tahun_pengadaan',
    'kode_108',
    'no_register'
    ];

    // Tambahkan baris ini ke bawah:
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class)->orderBy('tanggal_transaksi', 'desc');
    }

    public function procurementFiles()
    {
        return $this->hasMany(ProcurementFile::class)->orderBy('tanggal_dokumen', 'desc');
    }
}