<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AktivitasPemeliharaanSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $data;
    protected $bulanAwal;
    protected $bulanAkhir;

    public function __construct($data, $bulanAwal, $bulanAkhir)
    {
        $this->data = $data;
        $this->bulanAwal = $bulanAwal;
        $this->bulanAkhir = $bulanAkhir;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                'no' => $no++,
                'kode' => $item->asset ? $item->asset->asset_code : '-',
                'nama' => $item->asset ? $item->asset->name : '-',
                'tanggal_pelaksanaan' => $item->tanggal_pelaksanaan,
                'jenis_pemeliharaan' => $item->jenis_pemeliharaan,
                'biaya' => $item->biaya
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['DAFTAR RIWAYAT PEMELIHARAAN / KALIBRASI'],
            ['Periode: ' . $this->bulanAwal . ' s/d ' . $this->bulanAkhir],
            [],
            ['No', 'Kode Aset', 'Nama Aset', 'Tanggal Pelaksanaan', 'Jenis Pemeliharaan', 'Biaya (Rp)']
        ];
    }

    public function title(): string
    {
        return 'Pemeliharaan Aset';
    }
}
