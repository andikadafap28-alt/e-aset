<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ItemHistorySheet implements FromCollection, WithTitle, WithHeadings
{
    protected $item;
    protected $bulan;
    protected $jenis;

    public function __construct($item, $bulan, $jenis)
    {
        $this->item = $item;
        $this->bulan = $bulan;
        $this->jenis = $jenis;
    }

    public function collection()
    {
        $transactions = $this->item->transactions()->orderBy('tanggal_transaksi', 'asc')->get();
        $data = [];

        if ($this->jenis == 'internal') {
            foreach ($transactions as $trx) {
                if (!$trx->tanggal_transaksi) continue;
                $bulanTrx = Carbon::parse($trx->tanggal_transaksi)->format('Y-m');
                
                if ($bulanTrx == $this->bulan) {
                    $data[] = [
                        'Tanggal'    => Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                        'Jenis'      => ucfirst($trx->jenis_transaksi),
                        'Jumlah'     => $trx->jumlah,
                        'Keterangan' => $trx->keterangan ?? '-'
                    ];
                }
            }
        } else {
            // Logika Dinas (Virtual Ledger)
            $stokLunas = 0;
            $pemakaianTertunda = 0;
            $events = [];

            foreach ($transactions as $trx) {
                if ($trx->jenis_transaksi == 'masuk') {
                    if (!$trx->status_hutang && $trx->tanggal_spj) {
                        $events[] = [
                            'type' => 'masuk',
                            'date' => $trx->tanggal_spj,
                            'jumlah' => $trx->jumlah,
                            'keterangan' => $trx->keterangan
                        ];
                    }
                } else {
                    if ($trx->tanggal_transaksi) {
                        $events[] = [
                            'type' => 'keluar',
                            'date' => $trx->tanggal_transaksi,
                            'jumlah' => $trx->jumlah,
                            'keterangan' => $trx->keterangan
                        ];
                    }
                }
            }

            usort($events, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            foreach ($events as $event) {
                $eventBulan = Carbon::parse($event['date'])->format('Y-m');
                
                if ($event['type'] == 'masuk') {
                    $stokLunas += $event['jumlah'];
                    
                    if ($eventBulan == $this->bulan) {
                        $data[] = [
                            'Tanggal'    => Carbon::parse($event['date'])->format('d/m/Y'),
                            'Jenis'      => 'Masuk',
                            'Jumlah'     => $event['jumlah'],
                            'Keterangan' => '[LUNAS SPJ] ' . ($event['keterangan'] ?? '-')
                        ];
                    }

                    if ($pemakaianTertunda > 0 && $stokLunas > 0) {
                        $processed = min($pemakaianTertunda, $stokLunas);
                        $pemakaianTertunda -= $processed;
                        $stokLunas -= $processed;

                        if ($eventBulan == $this->bulan) {
                            $data[] = [
                                'Tanggal'    => Carbon::parse($event['date'])->format('d/m/Y'),
                                'Jenis'      => 'Keluar',
                                'Jumlah'     => $processed,
                                'Keterangan' => '[PEMAKAIAN TERTUNDA DILAPORKAN]'
                            ];
                        }
                    }
                } else {
                    $pemakaianTertunda += $event['jumlah'];
                    
                    if ($stokLunas > 0) {
                        $processed = min($pemakaianTertunda, $stokLunas);
                        $pemakaianTertunda -= $processed;
                        $stokLunas -= $processed;

                        if ($eventBulan == $this->bulan) {
                            $data[] = [
                                'Tanggal'    => Carbon::parse($event['date'])->format('d/m/Y'),
                                'Jenis'      => 'Keluar',
                                'Jumlah'     => $processed,
                                'Keterangan' => $event['keterangan'] ?? '-'
                            ];
                        }
                    }
                }
            }
        }

        $dataCollection = collect($data);
        
        // Pastikan terurut dari tanggal 1 hingga akhir bulan (kronologis)
        $sortedData = $dataCollection->sortBy(function($item) {
            return Carbon::createFromFormat('d/m/Y', $item['Tanggal'])->timestamp;
        })->values();

        return $sortedData;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Jumlah',
            'Keterangan'
        ];
    }

    public function title(): string
    {
        // Penamaan sheet: nama_harga agar terhindar dari duplikasi
        $harga = $this->item->harga_satuan ?? 0;
        $title = ($this->item->nama_barang ?? 'Item') . '_' . $harga;
        
        // Memastikan panjang string max 31 char
        return substr($title, 0, 31);
    }
}
