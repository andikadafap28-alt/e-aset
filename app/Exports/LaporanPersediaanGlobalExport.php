<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Item;

class LaporanPersediaanGlobalExport implements WithMultipleSheets
{
    use Exportable;

    protected $bulanAwal;
    protected $bulanAkhir;
    protected $jenis;

    public function __construct($bulanAwal, $bulanAkhir, $jenis)
    {
        $this->bulanAwal = $bulanAwal;
        $this->bulanAkhir = $bulanAkhir;
        $this->jenis = $jenis;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Dapatkan semua kategori besar unik
        $kategoris = Item::select('kategori_besar')->distinct()->pluck('kategori_besar');

        foreach ($kategoris as $kategori) {
            $items = Item::where('kategori_besar', $kategori)->with('transactions')->get();
            
            // Format nama kategori agar bersih
            $kategoriName = str_replace(['_', '-'], ' ', $kategori);
            $kategoriName = ucwords($kategoriName);

            $sheets[] = new GlobalTransaksiSheet($this->bulanAwal, $this->bulanAkhir, $this->jenis, $items, $kategoriName);
        }

        return $sheets;
    }
}
