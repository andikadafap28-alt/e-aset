<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AktivitasAsetExport implements WithMultipleSheets
{
    use Exportable;

    protected $asetMasuk;
    protected $pemeliharaan;
    protected $penghapusan;
    protected $bulanAwal;
    protected $bulanAkhir;

    public function __construct($asetMasuk, $pemeliharaan, $penghapusan, $bulanAwal, $bulanAkhir)
    {
        $this->asetMasuk = $asetMasuk;
        $this->pemeliharaan = $pemeliharaan;
        $this->penghapusan = $penghapusan;
        $this->bulanAwal = $bulanAwal;
        $this->bulanAkhir = $bulanAkhir;
    }

    public function sheets(): array
    {
        return [
            new AktivitasAsetMasukSheet($this->asetMasuk, $this->bulanAwal, $this->bulanAkhir),
            new AktivitasPemeliharaanSheet($this->pemeliharaan, $this->bulanAwal, $this->bulanAkhir),
            new AktivitasPenghapusanSheet($this->penghapusan, $this->bulanAwal, $this->bulanAkhir),
        ];
    }
}
