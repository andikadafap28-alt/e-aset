<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Exports\LaporanPersediaanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    /**
     * Helper to get formatted name for view display
     */
    private function getKategoriName($kategori_besar) {
        $map = [
            'atk' => 'ATK',
            'kertas_cover' => 'Kertas dan Cover',
            'bahan_cetak' => 'Bahan Cetak',
            'benda_pos' => 'Benda Pos',
            'bahan_komputer' => 'Bahan Komputer',
            'obat' => 'Obat',
            'bahan_lainnya' => 'Bahan Lainnya',
            'natura_pakan_lainnya' => 'Natura & Pakan Lainnya',
            'pengadaan' => 'Pengadaan',
            'bantuan_sarpras' => 'Bantuan Sarpras',
            'vaksin' => 'Vaksin',
            'obat_apbd' => 'Obat APBD'
        ];
        return $map[$kategori_besar] ?? 'Persediaan';
    }

    public function index($kategori_besar)
    {
        $items = Item::where('kategori_besar', $kategori_besar)->get();
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.index', compact('items', 'kategori_besar', 'nama_kategori'));
    }

    private function parseHarga($harga)
    {
        if (!$harga) return null;
        $harga = str_replace(['Rp', ' ', '_'], '', $harga);
        if (strpos($harga, ',') !== false) {
            $harga = str_replace('.', '', $harga);
            $harga = str_replace(',', '.', $harga);
        }
        return $harga;
    }

    public function masterList($kategori_besar)
    {
        $items = Item::where('kategori_besar', $kategori_besar)->get();
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.master_list', compact('items', 'kategori_besar', 'nama_kategori'));
    }

    public function editMaster($kategori_besar, $id)
    {
        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.edit_master', compact('item', 'kategori_besar', 'nama_kategori'));
    }

    public function updateMaster(Request $request, $kategori_besar, $id)
    {
        if ($request->has('harga_satuan')) {
            $request->merge(['harga_satuan' => $this->parseHarga($request->harga_satuan)]);
        }

        $request->validate([
            'nama_barang' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'harga_satuan' => 'required|numeric'
        ]);

        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);
        
        $data = $request->only(['nama_barang', 'kategori', 'harga_satuan', 'tahun_pengadaan']);
        $data['satuan'] = $request->satuan === 'Lainnya' ? $request->satuan_lainnya : $request->satuan;
        
        $oldHarga = $item->harga_satuan;
        $newHarga = $data['harga_satuan'];
        $hargaChanged = $oldHarga != $newHarga;

        $item->update($data);

        if ($hargaChanged && in_array($kategori_besar, ['bantuan_sarpras', 'pengadaan'])) {
            \App\Models\InventoryTransaction::where('item_id', $id)->update(['harga_satuan' => $newHarga]);
            \App\Models\Asset::where('pengadaan_id', $id)->update(['harga_perolehan' => $newHarga]);
            return redirect("/{$kategori_besar}/master")->with('success', 'Master Barang berhasil diperbarui! Harga pada riwayat transaksi dan aset terkait juga telah otomatis disesuaikan.');
        }

        return redirect("/{$kategori_besar}/master")->with('success', 'Master Barang berhasil diperbarui!');
    }

    public function createTransaction($kategori_besar)
    {
        $items = Item::where('kategori_besar', $kategori_besar)->get();
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.transaction', compact('items', 'kategori_besar', 'nama_kategori'));
    }

    public function storeTransaction(Request $request, $kategori_besar)
    {
        if ($request->has('harga_satuan')) {
            $request->merge(['harga_satuan' => $this->parseHarga($request->harga_satuan)]);
        }

        $request->validate([
            'item_id' => 'required',
            'nama_barang_manual' => 'required_if:item_id,new', 
            'jenis_transaksi' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_transaksi' => 'required|date',
            'harga_satuan' => 'required|numeric',
            'expired_date' => 'nullable|date'
        ]);

        $hargaInput = $request->harga_satuan;
        $itemId = $request->item_id;

        if ($itemId == 'new') {
            $kodeFinal = null;
            $nextRegister = null;

            $satuan = $request->satuan === 'Lainnya' ? $request->satuan_lainnya : ($request->satuan ?? 'Pcs');
            $item = Item::firstOrCreate(
                ['nama_barang' => $request->nama_barang_manual, 'harga_satuan' => $hargaInput, 'kategori_besar' => $kategori_besar],
                [
                    'kategori' => $request->kategori ?? 'Umum', 
                    'satuan' => $satuan, 
                    'stok_sekarang' => 0,
                    'kode_barang' => $kodeFinal
                ]
            );
        } else {
            $itemLama = Item::where('kategori_besar', $kategori_besar)->findOrFail($itemId);
            if ($itemLama->harga_satuan != $hargaInput) {
                $item = Item::firstOrCreate(
                    ['nama_barang' => $itemLama->nama_barang, 'harga_satuan' => $hargaInput, 'kategori_besar' => $kategori_besar],
                    ['kategori' => $itemLama->kategori, 'satuan' => $itemLama->satuan, 'stok_sekarang' => 0]
                );
            } else {
                $item = $itemLama;
            }
        }

        if ($request->jenis_transaksi == 'keluar') {
            if ($item->stok_sekarang < $request->jumlah) {
                return back()
                    ->withErrors(['jumlah' => 'Stok tidak mencukupi! Sisa stok saat ini: ' . $item->stok_sekarang . ' ' . $item->satuan])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $item, $hargaInput) {
            $isHutang = $request->has('status_hutang');
            InventoryTransaction::create([
                'item_id' => $item->id,
                'jenis_transaksi' => $request->jenis_transaksi,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $hargaInput,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'tanggal_spj' => $isHutang ? null : $request->tanggal_transaksi,
                'status_hutang' => $isHutang,
                'keterangan' => $request->keterangan,
                'expired_date' => $request->jenis_transaksi == 'masuk' ? $request->expired_date : null
            ]);

            if ($request->jenis_transaksi == 'masuk') {
                $item->increment('stok_sekarang', $request->jumlah);
            } else {
                $item->decrement('stok_sekarang', $request->jumlah);
            }
        });

        return redirect("/{$kategori_besar}/items");
    }

    public function show($kategori_besar, $id)
    {
        $item = Item::with(['transactions' => function($q) {
            $q->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc');
        }, 'procurementFiles'])->where('kategori_besar', $kategori_besar)->findOrFail($id);
        
        $balance = 0;
        foreach($item->transactions as $tx) {
            if($tx->jenis_transaksi == 'masuk') {
                $balance += $tx->jumlah;
            } else {
                $balance -= $tx->jumlah;
            }
            $tx->running_balance = $balance;
        }
        
        // Reverse for display (newest first)
        $item->setRelation('transactions', $item->transactions->reverse());

        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.show', compact('item', 'kategori_besar', 'nama_kategori'));
    }

    public function printKartuStok($kategori_besar, $id)
    {
        $item = Item::with(['transactions' => function($q) {
            $q->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc');
        }])->where('kategori_besar', $kategori_besar)->findOrFail($id);
        
        $balance = 0;
        foreach($item->transactions as $tx) {
            if($tx->jenis_transaksi == 'masuk') {
                $balance += $tx->jumlah;
            } else {
                $balance -= $tx->jumlah;
            }
            $tx->running_balance = $balance;
        }
        
        $nama_kategori = $this->getKategoriName($kategori_besar);
        
        $pdf = Pdf::loadView('inventory.kartu_stok_pdf', compact('item', 'kategori_besar', 'nama_kategori'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Kartu_Stok_' . Str::slug($item->nama_barang) . '.pdf');
    }

    public function extractAi(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'image' => 'required|image|max:10240' // max 10MB
        ]);

        try {
            $aiService = new \App\Services\AiVisionService();
            $data = $aiService->extractStockCard($request->file('image'));
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeAiTransactions(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.tanggal' => 'required|date',
            'transactions.*.jenis_transaksi' => 'required|in:masuk,keluar',
            'transactions.*.jumlah' => 'required|integer|min:1',
            'transactions.*.keterangan' => 'nullable|string'
        ]);

        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);

        DB::transaction(function () use ($request, $item) {
            $transactionsData = [];
            $now = now();
            $stokPerubahan = 0;

            foreach ($request->transactions as $tx) {
                $transactionsData[] = [
                    'item_id' => $item->id,
                    'jenis_transaksi' => $tx['jenis_transaksi'],
                    'jumlah' => $tx['jumlah'],
                    'harga_satuan' => $item->harga_satuan,
                    'tanggal_transaksi' => $tx['tanggal'],
                    'status_hutang' => false,
                    'keterangan' => '[AI Vision] ' . ($tx['keterangan'] ?? ''),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($tx['jenis_transaksi'] == 'masuk') {
                    $stokPerubahan += $tx['jumlah'];
                } else {
                    $stokPerubahan -= $tx['jumlah'];
                }
            }

            if (!empty($transactionsData)) {
                InventoryTransaction::insert($transactionsData);
                $item->stok_sekarang += $stokPerubahan;
                $item->save();
            }
        });

        return redirect("/{$kategori_besar}/{$item->id}/detail")->with('success', 'Berhasil menyimpan transaksi dari Kartu Stok (AI)!');
    }

    public function destroy($kategori_besar, $id)
    {
        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);
        $item->delete(); 
        return redirect("/{$kategori_besar}/master");
    }

    public function destroyTransaction($kategori_besar, $id)
    {
        $transaksi = InventoryTransaction::findOrFail($id);
        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($transaksi->item_id);

        if ($transaksi->jenis_transaksi == 'masuk' && ($item->stok_sekarang - $transaksi->jumlah < 0)) {
            return back()->withErrors(['msg' => 'Gagal menghapus riwayat masuk karena akan menyebabkan sisa stok menjadi minus!']);
        }

        DB::transaction(function () use ($transaksi, $item) {
            if ($transaksi->jenis_transaksi == 'masuk') {
                $item->decrement('stok_sekarang', $transaksi->jumlah);
            } else {
                $item->increment('stok_sekarang', $transaksi->jumlah);
            }
            $transaksi->delete();
        });
        
        return redirect("/{$kategori_besar}/{$item->id}/detail");
    }

    public function exportPage($kategori_besar)
    {
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.export', compact('kategori_besar', 'nama_kategori'));
    }

    public function downloadExcel(Request $request, $kategori_besar)
    {
        $request->validate([
            'bulan' => 'required', 
            'jenis_laporan' => 'required|in:internal,dinas'
        ]);

        $items = Item::with('transactions')->where('kategori_besar', $kategori_besar)->get();
        $namaFile = 'Laporan_' . ucfirst($kategori_besar) . '_' . ucfirst($request->jenis_laporan) . '_' . $request->bulan . '.xlsx';

        return Excel::download(new LaporanPersediaanExport($request->bulan, $request->jenis_laporan, $items), $namaFile);
    }

    public function opnamePage($kategori_besar)
    {
        $items = Item::where('kategori_besar', $kategori_besar)->get();
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.opname', compact('items', 'kategori_besar', 'nama_kategori'));
    }

    public function storeOpname(Request $request, $kategori_besar)
    {
        $request->validate([
            'opname' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $kategori_besar) {
            foreach ($request->opname as $itemId => $data) {
                $item = Item::where('kategori_besar', $kategori_besar)->find($itemId);
                if (!$item || !isset($data['stok_fisik'])) continue;

                $stokFisik = (int) $data['stok_fisik'];
                $selisih = abs($stokFisik - $item->stok_sekarang);

                if ($stokFisik > $item->stok_sekarang) {
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'jenis_transaksi' => 'masuk',
                        'jumlah' => $selisih,
                        'harga_satuan' => $item->harga_satuan,
                        'tanggal_transaksi' => now(),
                        'status_hutang' => false,
                        'keterangan' => '[OPNAME] ' . ($data['keterangan'] ?? 'Penyesuaian stok lebih')
                    ]);
                } elseif ($stokFisik < $item->stok_sekarang) {
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'jenis_transaksi' => 'keluar',
                        'jumlah' => $selisih,
                        'harga_satuan' => $item->harga_satuan,
                        'tanggal_transaksi' => now(),
                        'status_hutang' => false,
                        'keterangan' => '[OPNAME] ' . ($data['keterangan'] ?? 'Penyesuaian stok kurang')
                    ]);
                }

                $item->stok_sekarang = $stokFisik;
                $item->save();
            }
        });

        return redirect("/{$kategori_besar}/items")->with('success', 'Stock Opname berhasil disimpan!');
    }

    public function hutangPage($kategori_besar)
    {
        $transactions = InventoryTransaction::with('item')
            ->whereHas('item', function($q) use ($kategori_besar) {
                $q->where('kategori_besar', $kategori_besar);
            })
            ->where('status_hutang', true)
            ->where('jenis_transaksi', 'masuk')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
            
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.hutang', compact('transactions', 'kategori_besar', 'nama_kategori'));
    }

    public function updateSpj(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'tanggal_spj' => 'required|date'
        ]);

        $transaksi = InventoryTransaction::whereHas('item', function($q) use ($kategori_besar) {
            $q->where('kategori_besar', $kategori_besar);
        })->findOrFail($id);
        
        $transaksi->status_hutang = false;
        $transaksi->tanggal_spj = $request->tanggal_spj;
        $transaksi->save();

        return redirect("/{$kategori_besar}/hutang")->with('success', 'Status Hutang berhasil diperbarui ke SPJ!');
    }

    public function editTransaction($kategori_besar, $id)
    {
        $transaksi = InventoryTransaction::with('item')->whereHas('item', function($q) use ($kategori_besar) {
            $q->where('kategori_besar', $kategori_besar);
        })->findOrFail($id);
        
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.edit_transaksi', compact('transaksi', 'kategori_besar', 'nama_kategori'));
    }

    public function updateTransaction(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string',
            'tanggal_spj' => 'nullable|date',
        ]);

        $transaksi = InventoryTransaction::whereHas('item', function($q) use ($kategori_besar) {
            $q->where('kategori_besar', $kategori_besar);
        })->findOrFail($id);
        
        $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
        $transaksi->keterangan = $request->keterangan;
        
        if ($transaksi->jenis_transaksi == 'masuk') {
            if ($request->has('status_hutang')) {
                $transaksi->status_hutang = true;
                $transaksi->tanggal_spj = null;
            } else {
                $transaksi->status_hutang = false;
                $transaksi->tanggal_spj = $request->tanggal_spj ?: $request->tanggal_transaksi;
            }
        }

        $transaksi->save();

        return redirect("/{$kategori_besar}/{$transaksi->item_id}/detail")->with('success', 'Detail transaksi berhasil diperbarui!');
    }

    public function importLogistik(Request $request, $kategori_besar)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            'bulan_import' => 'required|date_format:Y-m'
        ]);

        try {
            DB::transaction(function () use ($request, $kategori_besar) {
                Excel::import(new \App\Imports\LogistikImport($kategori_besar, $request->bulan_import), $request->file('file_excel'));
            });
            return redirect("/{$kategori_besar}/items")->with('success', 'Data barang logistik berhasil disinkronisasi!');
        } catch (\Exception $e) {
            return redirect("/{$kategori_besar}/items")->withErrors(['msg' => 'Gagal mensinkronisasi data: ' . $e->getMessage()]);
        }
    }

    public function scanProcurementFile(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'file_dokumen' => 'required|mimes:pdf|max:10240'
        ]);

        $file = $request->file('file_dokumen');
        $jenisDokumen = $request->input('jenis_dokumen');
        $tanggalDokumen = '';
        $namaPenyedia = '';

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $parsedText = $pdf->getText();
            
            $translateMonth = function($dateString) {
                $bulanId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $bulanEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return str_ireplace($bulanId, $bulanEn, $dateString);
            };

            // Extract Date Berdasarkan Jenis Dokumen (INAPROC)
            if ($jenisDokumen === 'BAST') {
                if (preg_match('/Tanggal BAST Dibuat[\s\S]*?:\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                    $cleanDate = $translateMonth($matches[1]);
                    $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                }
            } elseif ($jenisDokumen === 'SP') {
                if (preg_match('/Tanggal Surat Pesanan[\s\S]*?:\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                    $cleanDate = $translateMonth($matches[1]);
                    $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                }
            } elseif ($jenisDokumen === 'Surat Pernyataan') {
                // Cari tanggal di bagian tanda tangan bawah (contoh: lamongan, 23 April 2026)
                if (preg_match('/(?:lamongan|Lamongan),\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                    $cleanDate = $translateMonth($matches[1]);
                    $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                } 
                // Fallback: Cari di paragraf pembuka (contoh: tanggal 23 bulan April tahun 2026)
                elseif (preg_match('/tanggal\s+([0-9]{1,2})\s+bulan\s+([a-zA-Z]+)\s+tahun\s+([0-9]{4})/i', $parsedText, $matches)) {
                    $rawDate = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3];
                    $cleanDate = $translateMonth($rawDate);
                    $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                }
            } elseif ($jenisDokumen === 'DPP') {
                if (preg_match('/([0-9]{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember|Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des)[a-z]*\s+[0-9]{4})/i', $parsedText, $matches)) {
                    $cleanDate = $translateMonth($matches[1]);
                    $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                }
            }

            // Fallback Date Extraction
            if (!$tanggalDokumen) {
                $patternDate = '/(?:tanggal\s+)?(\d{1,2})[\/\-\s]+([a-zA-Z]+|\d{1,2})[\/\-\s]+(\d{4})/i';
                if (preg_match($patternDate, $parsedText, $matches)) {
                    $day = $matches[1];
                    $monthStr = strtolower($matches[2]);
                    $year = $matches[3];
                    $months = ['januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08', 'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
                               'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12'];
                    $month = is_numeric($monthStr) ? str_pad($monthStr, 2, '0', STR_PAD_LEFT) : ($months[$monthStr] ?? '01');
                    $tanggalDokumen = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                }
            }

            // Extract Provider Name (INAPROC Format)
            if ($jenisDokumen === 'DPP') {
                // Skip pencarian nama penyedia
            } elseif ($jenisDokumen === 'Surat Pernyataan') {
                // Karena label dan isi terpisah, mesin akan membaca "a. Nama penyedia", lalu "b. Nomor", baru kemudian ": PT. INTISUMBER..."
                if (preg_match('/a\.\s*Nama penyedia[\s\S]*?b\.\s*Nomor[\s\S]*?:\s*([^\n]+)/i', $parsedText, $matches)) {
                    $namaPenyedia = trim($matches[1]);
                }
            } else {
                if (preg_match('/Penyedia(?:\s*\(PIHAK PERTAMA\))?\s*([^\n]+)\s*Nama Penanggung Jawab/i', $parsedText, $matches)) {
                    $namaPenyedia = trim($matches[1]); 
                } elseif (preg_match('/(?:PT|CV|Firma)\s+[A-Za-z\s]+(?=\n|Nama)/i', $parsedText, $matches)) {
                    $namaPenyedia = trim($matches[0]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tanggal' => $tanggalDokumen,
                    'nama_penyedia' => $namaPenyedia
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadProcurementFile(Request $request, $kategori_besar, $id)
    {
        $request->validate([
            'file_dokumen' => 'required|mimes:pdf|max:10240',
            'jenis_dokumen' => 'required|string',
            'jenis_dokumen_kustom' => 'nullable|string|required_if:jenis_dokumen,Input Sendiri',
            'nama_penyedia' => 'required|string',
            'tanggal_dokumen' => 'nullable|date'
        ]);

        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);
        $file = $request->file('file_dokumen');
        $jenisDokumen = $request->jenis_dokumen === 'Input Sendiri' ? $request->jenis_dokumen_kustom : $request->jenis_dokumen;
        $namaPenyedia = $request->nama_penyedia;
        
        $tanggalDokumen = $request->tanggal_dokumen;

        // OCR PDF Date Extraction if empty
        if (!$tanggalDokumen) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $parsedText = $pdf->getText();
                
                $translateMonth = function($dateString) {
                    $bulanId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    $bulanEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    return str_ireplace($bulanId, $bulanEn, $dateString);
                };

                // Extract Date Berdasarkan Jenis Dokumen (INAPROC)
                if ($jenisDokumen === 'BAST') {
                    if (preg_match('/Tanggal BAST Dibuat[\s\S]*?:\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                        $cleanDate = $translateMonth($matches[1]);
                        $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                } elseif ($jenisDokumen === 'SP') {
                    if (preg_match('/Tanggal Surat Pesanan[\s\S]*?:\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                        $cleanDate = $translateMonth($matches[1]);
                        $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                } elseif ($jenisDokumen === 'Surat Pernyataan') {
                    // Cari tanggal di bagian tanda tangan bawah (contoh: lamongan, 23 April 2026)
                    if (preg_match('/(?:lamongan|Lamongan),\s*([0-9]{1,2}\s+[a-zA-Z]+\s+[0-9]{4})/i', $parsedText, $matches)) {
                        $cleanDate = $translateMonth($matches[1]);
                        $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    } 
                    // Fallback: Cari di paragraf pembuka (contoh: tanggal 23 bulan April tahun 2026)
                    elseif (preg_match('/tanggal\s+([0-9]{1,2})\s+bulan\s+([a-zA-Z]+)\s+tahun\s+([0-9]{4})/i', $parsedText, $matches)) {
                        $rawDate = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3];
                        $cleanDate = $translateMonth($rawDate);
                        $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                } elseif ($jenisDokumen === 'DPP') {
                    if (preg_match('/([0-9]{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember|Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des)[a-z]*\s+[0-9]{4})/i', $parsedText, $matches)) {
                        $cleanDate = $translateMonth($matches[1]);
                        $tanggalDokumen = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                }

                // Fallback Date Extraction
                if (!$tanggalDokumen) {
                    $patternDate = '/(?:tanggal\s+)?(\d{1,2})[\/\-\s]+([a-zA-Z]+|\d{1,2})[\/\-\s]+(\d{4})/i';
                    if (preg_match($patternDate, $parsedText, $matches)) {
                        $day = $matches[1];
                        $monthStr = strtolower($matches[2]);
                        $year = $matches[3];
                        $months = ['januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08', 'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
                                   'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12'];
                        $month = is_numeric($monthStr) ? str_pad($monthStr, 2, '0', STR_PAD_LEFT) : ($months[$monthStr] ?? '01');
                        $tanggalDokumen = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    } else {
                        $tanggalDokumen = date('Y-m-d'); // fallback to today
                    }
                }
            } catch (\Exception $e) {
                $tanggalDokumen = date('Y-m-d'); // fallback
            }
        }

        // Format nama file: [TANGGAL]_[KATEGORI]_[JENIS_DOKUMEN]_[NAMA_BARANG]_[PENYEDIA]_[UNIQUEID].pdf
        $tanggal = $tanggalDokumen;
        $jenisDokumen = $request->input('jenis_dokumen') === 'Input Sendiri' ? $request->input('jenis_dokumen_kustom') : $request->input('jenis_dokumen');
        $namaBarang = $item->nama_barang;
        // Paksa lowercase dan slug untuk membersihkan input
        $namaPenyedia = Str::slug(Str::lower($request->input('nama_penyedia')), '_');
        $uniqueId = Str::random(5);

        // Susun nama file awal (tanpa ekstensi)
        $rawFileName = "{$tanggal}_{$jenisDokumen}_{$namaBarang}_{$namaPenyedia}_{$uniqueId}";

        // Bersihkan slug sekali lagi secara keseluruhan dan jadikan UPPERCASE, lalu tambah .pdf
        $filename = Str::upper(Str::slug($rawFileName, '_')) . '.PDF';

        try {
            $driveService = new \App\Services\GoogleDriveService();
            $driveData = $driveService->uploadProcurementFile($file, $kategori_besar, $filename);

            \App\Models\ProcurementFile::create([
                'item_id' => $item->id,
                'kategori' => $kategori_besar,
                'jenis_dokumen' => $jenisDokumen,
                'nama_penyedia' => $namaPenyedia,
                'file_name' => $driveData['file_name'],
                'drive_file_id' => $driveData['drive_file_id'],
                'tanggal_dokumen' => $tanggalDokumen,
                'path_gdrive' => $driveData['path_gdrive']
            ]);

            return redirect("/{$kategori_besar}/{$item->id}/detail")->with('success', 'Dokumen pengadaan berhasil diunggah ke Google Drive!');
        } catch (\Exception $e) {
            return redirect("/{$kategori_besar}/{$item->id}/detail")->withErrors(['msg' => 'Gagal mengunggah ke Google Drive: ' . $e->getMessage()]);
        }
    }

    public function viewProcurementFile($id)
    {
        $file = \App\Models\ProcurementFile::findOrFail($id);
        
        try {
            // Karena path_gdrive dan drive_file_id berisi path file (berdasarkan GoogleDriveAdapter v3)
            // kita bisa melakukan redirect ke temporary URL atau stream download
            $url = \Illuminate\Support\Facades\Storage::disk('google')->url($file->path_gdrive);
            return redirect($url);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal mengambil file dari Google Drive: ' . $e->getMessage()]);
        }
    }

    public function destroyProcurementFile($id)
    {
        $file = \App\Models\ProcurementFile::findOrFail($id);
        
        // Hapus file fisik dari Google Drive menggunakan path/id yang tersimpan
        if (\Illuminate\Support\Facades\Storage::disk('google')->exists($file->path_gdrive)) {
            \Illuminate\Support\Facades\Storage::disk('google')->delete($file->path_gdrive);
        }

        // Hapus record dari database
        $file->delete();

        return back()->with('success', 'Dokumen berhasil dihapus dari sistem dan Google Drive.');
    }

    public function printLabel($kategori_besar, $id)
    {
        $item = Item::where('kategori_besar', $kategori_besar)->findOrFail($id);
        return view('inventory.label', compact('item', 'kategori_besar'));
    }

    public function importKode108(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file_excel');
        
        // Membaca array dari excel tanpa membuat class import terpisah
        $dataArray = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array) {}
        }, $file);

        if (isset($dataArray[0])) {
            $insertData = [];
            $now = now();
            foreach ($dataArray[0] as $index => $row) {
                // Lewati baris header
                if ($index === 0) {
                    continue;
                }

                // Auto-detect format: Jika kolom 0 adalah angka/urutan dan kolom 1 adalah kode (ada titiknya), geser index
                $kodeIdx = 0;
                $uraianIdx = 1;

                if (isset($row[0], $row[1], $row[2])) {
                    $col0 = trim($row[0]);
                    $col1 = trim($row[1]);
                    // Jika col0 hanya angka (tidak ada titik) dan col1 mengandung titik ATAU col0 adalah string pendek seperti nomor
                    if (is_numeric($col0) && strpos($col1, '.') !== false) {
                        $kodeIdx = 1;
                        $uraianIdx = 2;
                    }
                }

                if (isset($row[$kodeIdx]) && isset($row[$uraianIdx]) && !empty(trim($row[$kodeIdx]))) {
                    $insertData[] = [
                        'kode' => trim($row[$kodeIdx]),
                        'uraian' => trim($row[$uraianIdx]),
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            // Insert using chunked upsert for massive performance gain
            $chunks = array_chunk($insertData, 500);
            foreach ($chunks as $chunk) {
                \App\Models\MasterKode108::upsert(
                    $chunk,
                    ['kode'],
                    ['uraian', 'updated_at']
                );
            }
        }

        return back()->with('success', 'Master Kode 108 berhasil diimport!');
    }

    public function getKode108(Request $request)
    {
        $level = $request->query('level');
        $parent = $request->query('parent');

        $lengthMap = [
            1 => 3,   // x.x
            2 => 5,   // x.x.x
            3 => 8,   // x.x.x.xx
            4 => 11,  // x.x.x.xx.xx
            5 => 14,  // x.x.x.xx.xx.xx
            6 => 18   // x.x.x.xx.xx.xx.xxx
        ];

        $query = \App\Models\MasterKode108::query();

        if (isset($lengthMap[$level])) {
            $length = $lengthMap[$level];
            $query->whereRaw('LENGTH(kode) = ?', [$length]);
        }

        if ($parent) {
            $query->where('kode', 'like', $parent . '.%');
        }

        $data = $query->orderBy('kode', 'asc')->get();
        return response()->json($data);
    }

    public function createMaster($kategori_besar)
    {
        $nama_kategori = $this->getKategoriName($kategori_besar);
        return view('inventory.create', compact('kategori_besar', 'nama_kategori'));
    }

    public function storeMaster(Request $request, $kategori_besar)
    {
        if ($request->has('harga_satuan')) {
            $request->merge(['harga_satuan' => $this->parseHarga($request->harga_satuan)]);
        }

        $request->validate([
            'nama_barang' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'stok_sekarang' => 'required|numeric',
        ]);

        $kodeFinal = $request->kode_barang; // Default input manual

        $satuan = $request->satuan === 'Lainnya' ? $request->satuan_lainnya : $request->satuan;
        
        Item::create([
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'satuan' => $satuan,
            'harga_satuan' => $request->harga_satuan ?? 0,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'stok_sekarang' => $request->stok_sekarang,
            'kategori_besar' => $kategori_besar,
            'kode_barang' => $kodeFinal
        ]);

        return redirect("/{$kategori_besar}/master")->with('success', 'Master Barang baru berhasil ditambahkan!');
    }

    public function scannerPage()
    {
        return view('inventory.scanner');
    }

    public function scanResult($id)
    {
        $item = Item::findOrFail($id);
        
        // Redirect to detail page
        return redirect("/{$item->kategori_besar}/{$item->id}/detail")->with('success', 'Berhasil memindai barang: ' . $item->nama_barang);
    }
}