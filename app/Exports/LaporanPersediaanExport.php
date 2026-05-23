<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class LaporanPersediaanExport implements WithMultipleSheets
{
    use Exportable;

    protected $bulan;
    protected $jenis;
    protected $items;

    public function __construct($bulan, $jenis, $items)
    {
        $this->bulan = $bulan;
        $this->jenis = $jenis;
        $this->items = $items;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Tambahkan MasterTransaksiSheet
        $sheets[] = new MasterTransaksiSheet($this->bulan, $this->jenis, $this->items);

        // 2. Tambahkan ItemHistorySheet untuk item yang memiliki riwayat/aktivitas virtual di bulan tersebut
        foreach ($this->items as $item) {
            $sheet = new ItemHistorySheet($item, $this->bulan, $this->jenis);
            
            // Jika ada baris data (transaksi fisik atau virtual) di bulan tersebut, masukkan sheet-nya
            if ($sheet->collection()->count() > 0) {
                $sheets[] = $sheet;
            }
        }

        return $sheets;
    }
}