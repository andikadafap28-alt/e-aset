<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class MasterTransaksiSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $bulan;
    protected $jenis;
    protected $items;

    public function __construct($bulan, $jenis, $items)
    {
        $this->bulan = $bulan;
        $this->jenis = $jenis;
        $this->items = $items;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->items as $item) {
            $transactions = $item->transactions()->orderBy('tanggal_transaksi', 'asc')->get();
            
            $saldoAwal = 0;
            $masuk = 0;
            $keluar = 0;

            if ($this->jenis == 'internal') {
                foreach ($transactions as $trx) {
                    if (!$trx->tanggal_transaksi) continue;
                    $bulanTrx = Carbon::parse($trx->tanggal_transaksi)->format('Y-m');
                    
                    if ($bulanTrx < $this->bulan) {
                        if ($trx->jenis_transaksi == 'masuk') $saldoAwal += $trx->jumlah;
                        else $saldoAwal -= $trx->jumlah;
                    } elseif ($bulanTrx == $this->bulan) {
                        if ($trx->jenis_transaksi == 'masuk') $masuk += $trx->jumlah;
                        else $keluar += $trx->jumlah;
                    }
                }
            } else {
                // Logika Dinas (Virtual Ledger / Capping Pemakaian Tertunda)
                $stokLunas = 0;
                $pemakaianTertunda = 0;
                $events = [];

                foreach ($transactions as $trx) {
                    if ($trx->jenis_transaksi == 'masuk') {
                        if (!$trx->status_hutang && $trx->tanggal_spj) {
                            $events[] = [
                                'type' => 'masuk',
                                'date' => $trx->tanggal_spj,
                                'jumlah' => $trx->jumlah
                            ];
                        }
                    } else {
                        if ($trx->tanggal_transaksi) {
                            $events[] = [
                                'type' => 'keluar',
                                'date' => $trx->tanggal_transaksi,
                                'jumlah' => $trx->jumlah
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
                        
                        if ($eventBulan < $this->bulan) {
                            $saldoAwal += $event['jumlah'];
                        } elseif ($eventBulan == $this->bulan) {
                            $masuk += $event['jumlah'];
                        }

                        // Bayar pemakaian tertunda
                        if ($pemakaianTertunda > 0 && $stokLunas > 0) {
                            $processed = min($pemakaianTertunda, $stokLunas);
                            $pemakaianTertunda -= $processed;
                            $stokLunas -= $processed;

                            if ($eventBulan < $this->bulan) {
                                $saldoAwal -= $processed;
                            } elseif ($eventBulan == $this->bulan) {
                                $keluar += $processed;
                            }
                        }
                    } else {
                        // Keluar
                        $pemakaianTertunda += $event['jumlah'];
                        
                        if ($stokLunas > 0) {
                            $processed = min($pemakaianTertunda, $stokLunas);
                            $pemakaianTertunda -= $processed;
                            $stokLunas -= $processed;

                            if ($eventBulan < $this->bulan) {
                                $saldoAwal -= $processed;
                            } elseif ($eventBulan == $this->bulan) {
                                $keluar += $processed;
                            }
                        }
                    }
                }
            }

            $saldoAkhir = $saldoAwal + $masuk - $keluar;

            $data[] = [
                'Nama Barang' => $item->nama_barang ?? '-',
                'Satuan'      => $item->satuan ?? '-',
                'Saldo Awal'  => $saldoAwal,
                'Masuk'       => $masuk,
                'Keluar'      => $keluar,
                'Saldo Akhir' => $saldoAkhir
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Satuan',
            'Saldo Awal',
            'Masuk',
            'Keluar',
            'Saldo Akhir'
        ];
    }

    public function title(): string
    {
        return 'Rekap ' . ucfirst($this->jenis);
    }
}
