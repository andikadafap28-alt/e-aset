<?php

namespace App\Http\Controllers;

use App\Models\KirItem;
use App\Models\Room;
use App\Exports\TemplateKirExport;
use App\Exports\KirRekapExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KirController extends Controller
{
    /**
     * Submenu 1: Daftar Ruangan KIR
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $query = Room::withCount('kirItems')
            ->withSum('kirItems', 'jumlah')
            ->withSum('kirItems', 'kondisi_baik')
            ->withSum('kirItems', 'kondisi_kurang_baik')
            ->withSum('kirItems', 'kondisi_rusak_berat');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'ILIKE', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $rooms = $query->orderBy('name')->get();

        // Statistik Keseluruhan KIR
        $totalRuangan = Room::count();
        $totalRuanganTerisi = Room::has('kirItems')->count();
        $totalJenisBarang = KirItem::count();
        $totalFisikBarang = KirItem::sum('jumlah') ?? 0;
        $totalBaik = KirItem::sum('kondisi_baik') ?? 0;
        $totalKurangBaik = KirItem::sum('kondisi_kurang_baik') ?? 0;
        $totalRusakBerat = KirItem::sum('kondisi_rusak_berat') ?? 0;

        return view('kir.index', compact(
            'rooms',
            'search',
            'type',
            'totalRuangan',
            'totalRuanganTerisi',
            'totalJenisBarang',
            'totalFisikBarang',
            'totalBaik',
            'totalKurangBaik',
            'totalRusakBerat'
        ));
    }

    /**
     * Lihat List Semua Barang di Ruangan Tertentu
     */
    public function roomItems(Request $request, Room $room)
    {
        $search = $request->input('search');
        $query = $room->kirItems();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('merk', 'ILIKE', "%{$search}%")
                  ->orWhere('bahan', 'ILIKE', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->get();
        $allRooms = Room::orderBy('name')->get();

        $totalJenis = $items->count();
        $totalFisik = $items->sum('jumlah');
        $totalBaik = $items->sum('kondisi_baik');
        $totalKurangBaik = $items->sum('kondisi_kurang_baik');
        $totalRusakBerat = $items->sum('kondisi_rusak_berat');

        return view('kir.room_items', compact(
            'room',
            'items',
            'allRooms',
            'search',
            'totalJenis',
            'totalFisik',
            'totalBaik',
            'totalKurangBaik',
            'totalRusakBerat'
        ));
    }

    /**
     * Tambah Barang ke Ruangan Tertentu
     */
    public function storeItem(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'bahan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'kondisi_baik' => 'nullable|integer|min:0',
            'kondisi_kurang_baik' => 'nullable|integer|min:0',
            'kondisi_rusak_berat' => 'nullable|integer|min:0',
            'condition_simple' => 'nullable|in:Baik,Kurang Baik,Rusak Berat',
            'condition' => 'nullable|in:Baik,Kurang Baik,Rusak Berat',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jumlah = (int)$validated['jumlah'];
        $baik = (int)($validated['kondisi_baik'] ?? 0);
        $kb = (int)($validated['kondisi_kurang_baik'] ?? 0);
        $rb = (int)($validated['kondisi_rusak_berat'] ?? 0);

        // Jika menggunakan mode simple kondisi
        $cond = $request->input('condition_simple') ?? $request->input('condition');
        if ($cond) {
            $baik = ($cond === 'Baik') ? $jumlah : 0;
            $kb = ($cond === 'Kurang Baik') ? $jumlah : 0;
            $rb = ($cond === 'Rusak Berat') ? $jumlah : 0;
        } else {
            if (($baik + $kb + $rb) === 0) {
                $baik = $jumlah;
            } elseif (($baik + $kb + $rb) < $jumlah) {
                $baik += ($jumlah - ($baik + $kb + $rb));
            }
        }

        KirItem::create([
            'room_id' => $room->id,
            'name' => $validated['name'],
            'merk' => $validated['merk'] ?: null,
            'bahan' => $validated['bahan'] ?: 'Campuran',
            'jumlah' => $jumlah,
            'kondisi_baik' => $baik,
            'kondisi_kurang_baik' => $kb,
            'kondisi_rusak_berat' => $rb,
            'keterangan' => $validated['keterangan'] ?: null,
        ]);

        return back()->with('success', "Barang {$validated['name']} ({$jumlah} unit) berhasil ditambahkan ke {$room->name}.");
    }

    /**
     * Update Barang Tertentu
     */
    public function updateItem(Request $request, KirItem $kirItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'bahan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'kondisi_baik' => 'nullable|integer|min:0',
            'kondisi_kurang_baik' => 'nullable|integer|min:0',
            'kondisi_rusak_berat' => 'nullable|integer|min:0',
            'condition_simple' => 'nullable|in:Baik,Kurang Baik,Rusak Berat',
            'condition' => 'nullable|in:Baik,Kurang Baik,Rusak Berat',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jumlah = (int)$validated['jumlah'];
        $baik = (int)($validated['kondisi_baik'] ?? 0);
        $kb = (int)($validated['kondisi_kurang_baik'] ?? 0);
        $rb = (int)($validated['kondisi_rusak_berat'] ?? 0);

        $cond = $request->input('condition_simple') ?? $request->input('condition');
        if ($cond) {
            $baik = ($cond === 'Baik') ? $jumlah : 0;
            $kb = ($cond === 'Kurang Baik') ? $jumlah : 0;
            $rb = ($cond === 'Rusak Berat') ? $jumlah : 0;
        } else {
            if (($baik + $kb + $rb) === 0) {
                $baik = $jumlah;
            } elseif (($baik + $kb + $rb) < $jumlah) {
                $baik += ($jumlah - ($baik + $kb + $rb));
            }
        }

        $kirItem->update([
            'name' => $validated['name'],
            'merk' => $validated['merk'] ?: null,
            'bahan' => $validated['bahan'] ?: 'Campuran',
            'jumlah' => $jumlah,
            'kondisi_baik' => $baik,
            'kondisi_kurang_baik' => $kb,
            'kondisi_rusak_berat' => $rb,
            'keterangan' => $validated['keterangan'] ?: null,
        ]);

        return back()->with('success', "Data barang {$validated['name']} berhasil diperbarui.");
    }

    /**
     * Hapus Barang dari Ruangan
     */
    public function destroyItem(KirItem $kirItem)
    {
        $name = $kirItem->name;
        $kirItem->delete();
        return back()->with('success', "Barang {$name} berhasil dihapus dari ruangan.");
    }

    /**
     * Kosongkan Seluruh Barang di Ruangan Tertentu
     */
    public function clearRoom(Room $room)
    {
        $count = $room->kirItems()->count();
        $room->kirItems()->delete();
        return back()->with('success', "Semua barang di ruangan {$room->name} ({$count} jenis barang) berhasil dikosongkan.");
    }

    /**
     * Submenu 2: Rekapitulasi Total Alat/Barang Seluruh KIR
     */
    public function rekap(Request $request)
    {
        $search = $request->input('search');
        $roomId = $request->input('room_id');
        $kondisi = $request->input('kondisi');

        // Ambil semua item KIR dengan relasi ruangan
        $query = KirItem::with('room');

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('merk', 'ILIKE', "%{$search}%")
                  ->orWhere('bahan', 'ILIKE', "%{$search}%");
            });
        }

        $allItems = $query->get();

        // Agregasi / Rekapitulasi per (Nama Barang + Merk + Bahan)
        $grouped = $allItems->groupBy(function($item) {
            $name = trim(strtolower((string)$item->name));
            $merk = trim(strtolower((string)($item->merk ?? '-')));
            $bahan = trim(strtolower((string)($item->bahan ?? 'campuran')));
            return $name . '|' . $merk . '|' . $bahan;
        });

        $rekapItems = collect();
        $no = 1;

        foreach ($grouped as $group) {
            $first = $group->first();
            $totalJumlah = $group->sum('jumlah');
            $totalBaik = $group->sum('kondisi_baik');
            $totalKurangBaik = $group->sum('kondisi_kurang_baik');
            $totalRusakBerat = $group->sum('kondisi_rusak_berat');

            // Ruangan tempat barang ini berada beserta jumlahnya
            $ruanganList = $group->map(function($i) {
                return ($i->room ? $i->room->name : 'Tanpa Ruangan') . ' (' . $i->jumlah . ')';
            })->unique()->values()->all();

            // Filter kondisi jika dipilih
            if ($kondisi === 'baik' && $totalBaik == 0) continue;
            if ($kondisi === 'kurang_baik' && $totalKurangBaik == 0) continue;
            if ($kondisi === 'rusak_berat' && $totalRusakBerat == 0) continue;

            $rekapItems->push((object)[
                'no' => $no++,
                'name' => $first->name,
                'merk' => $first->merk ?: '-',
                'bahan' => $first->bahan ?: 'Campuran',
                'total_jumlah' => $totalJumlah,
                'total_baik' => $totalBaik,
                'total_kurang_baik' => $totalKurangBaik,
                'total_rusak_berat' => $totalRusakBerat,
                'ruangan_list' => $ruanganList,
                'room_count' => count($ruanganList),
            ]);
        }

        // Urutkan berdasarkan nama barang
        $rekapItems = $rekapItems->sortBy('name')->values();

        // Statistik
        $allRooms = Room::orderBy('name')->get();
        $totalJenisBarang = $rekapItems->count();
        $totalFisikBarang = $rekapItems->sum('total_jumlah');
        $totalBaik = $rekapItems->sum('total_baik');
        $totalKurangBaik = $rekapItems->sum('total_kurang_baik');
        $totalRusakBerat = $rekapItems->sum('total_rusak_berat');

        return view('kir.rekap', compact(
            'rekapItems',
            'allRooms',
            'search',
            'roomId',
            'kondisi',
            'totalJenisBarang',
            'totalFisikBarang',
            'totalBaik',
            'totalKurangBaik',
            'totalRusakBerat'
        ));
    }

    /**
     * Export Rekapitulasi KIR ke Excel
     */
    public function exportRekap(Request $request)
    {
        $fileName = 'Rekapitulasi_KIR_Puskesmas_Mantup_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new KirRekapExport($request->all()), $fileName);
    }

    /**
     * Cetak Lembar Rekapitulasi KIR
     */
    public function printRekap(Request $request)
    {
        $allItems = KirItem::with('room')->get();

        $grouped = $allItems->groupBy(function($item) {
            $name = trim(strtolower((string)$item->name));
            $merk = trim(strtolower((string)($item->merk ?? '-')));
            $bahan = trim(strtolower((string)($item->bahan ?? 'campuran')));
            return $name . '|' . $merk . '|' . $bahan;
        });

        $rekapItems = collect();
        $no = 1;

        foreach ($grouped as $group) {
            $first = $group->first();
            $ruanganList = $group->map(function($i) {
                return ($i->room ? $i->room->name : 'Tanpa Ruangan') . ' (' . $i->jumlah . ')';
            })->unique()->values()->all();

            $rekapItems->push((object)[
                'no' => $no++,
                'name' => $first->name,
                'merk' => $first->merk ?: '-',
                'bahan' => $first->bahan ?: 'Campuran',
                'total_jumlah' => $group->sum('jumlah'),
                'total_baik' => $group->sum('kondisi_baik'),
                'total_kurang_baik' => $group->sum('kondisi_kurang_baik'),
                'total_rusak_berat' => $group->sum('kondisi_rusak_berat'),
                'ruangan_str' => implode(', ', $ruanganList),
            ]);
        }

        $rekapItems = $rekapItems->sortBy('name')->values();
        $docDate = $request->input('date', '2026-01-02');
        $formattedDate = $this->formatIndoDate($docDate);

        return view('kir.print_rekap', compact('rekapItems', 'docDate', 'formattedDate'));
    }

    /**
     * Download Template Excel KIR
     */
    public function downloadTemplate(Request $request)
    {
        $roomId = $request->query('room_id');
        $room = $roomId ? Room::find($roomId) : null;
        $fileName = $room ? 'Template_KIR_' . \Illuminate\Support\Str::slug($room->name) . '.xlsx' : 'Template_KIR_Format_Resmi.xlsx';
        return Excel::download(new TemplateKirExport($roomId), $fileName);
    }

    /**
     * Import File Excel KIR ke Database (kir_items)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'room_id' => 'nullable|exists:rooms,id',
            'replace_existing' => 'nullable'
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File Excel kosong atau format tidak sesuai.');
            }

            // Deteksi header kolom
            $headerRowIndex = 0;
            $colMap = [];
            foreach (array_slice($rows, 0, 5) as $rIdx => $row) {
                $foundCount = 0;
                foreach ($row as $cIdx => $cell) {
                    $val = strtolower(trim((string)$cell));
                    if (str_contains($val, 'ruang') || str_contains($val, 'lokasi')) { $colMap['room'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'nama barang') || str_contains($val, 'jenis barang') || $val === 'uraian') { $colMap['name'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'merek') || str_contains($val, 'merk') || str_contains($val, 'model')) { $colMap['merk'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'bahan') || str_contains($val, 'material')) { $colMap['bahan'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'jumlah') || str_contains($val, 'qty') || str_contains($val, 'total')) { $colMap['jumlah'] = $cIdx; $foundCount++; }
                    if ($val === 'baik' || str_contains($val, 'keadaan baik')) { $colMap['baik'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'kurang baik') || str_contains($val, 'rusak ringan') || $val === 'kb') { $colMap['kurang_baik'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'rusak berat') || $val === 'rb') { $colMap['rusak_berat'] = $cIdx; $foundCount++; }
                    if (str_contains($val, 'keterangan') || str_contains($val, 'mutasi')) { $colMap['keterangan'] = $cIdx; $foundCount++; }
                }
                if ($foundCount >= 3) {
                    $headerRowIndex = $rIdx;
                    break;
                }
            }

            // Fallback jika tidak terdeteksi via keyword
            if (!isset($colMap['name'])) {
                $colMap = [
                    'room' => 0,
                    'name' => 2,
                    'merk' => 3,
                    'bahan' => 4,
                    'jumlah' => 5,
                    'baik' => 6,
                    'kurang_baik' => 7,
                    'rusak_berat' => 8,
                    'keterangan' => 9
                ];
            }

            $dataRows = array_slice($rows, $headerRowIndex + 1);
            $defaultRoom = $request->room_id ? Room::find($request->room_id) : null;
            $replaceExisting = $request->has('replace_existing') && ($request->input('replace_existing') == '1' || $request->input('replace_existing') === true || $request->input('replace_existing') === 'on');

            $processedRooms = [];
            $totalItems = 0;

            \Illuminate\Support\Facades\DB::beginTransaction();

            $groupedByRoom = [];
            foreach ($dataRows as $row) {
                $namaBarang = isset($colMap['name']) ? trim((string)($row[$colMap['name']] ?? '')) : '';
                if (empty($namaBarang) || strtolower($namaBarang) === 'jenis barang / nama barang') continue;

                $roomName = isset($colMap['room']) ? trim((string)($row[$colMap['room']] ?? '')) : '';
                if (empty($roomName) && $defaultRoom) {
                    $roomName = $defaultRoom->name;
                } elseif (empty($roomName)) {
                    $roomName = 'Ruang Kepala Puskesmas';
                }

                $groupedByRoom[$roomName][] = $row;
            }

            foreach ($groupedByRoom as $roomName => $items) {
                $room = Room::firstOrCreate(
                    ['name' => $roomName],
                    ['type' => 'Ruangan']
                );

                $processedRooms[$room->id] = $room;

                if ($replaceExisting) {
                    // Hapus data kir_items sebelumnya pada ruangan ini (update berkala 6 bulan sekali)
                    KirItem::where('room_id', $room->id)->delete();
                }

                foreach ($items as $row) {
                    $nama = trim((string)($row[$colMap['name']] ?? ''));
                    $merk = isset($colMap['merk']) ? trim((string)($row[$colMap['merk']] ?? '')) : '';
                    if ($merk === '-' || empty($merk)) $merk = null;

                    $bahan = isset($colMap['bahan']) ? trim((string)($row[$colMap['bahan']] ?? '')) : 'Campuran';
                    if (empty($bahan) || $bahan === '-') $bahan = 'Campuran';

                    $qty = isset($colMap['jumlah']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$colMap['jumlah']] ?? 1)) : 1;
                    if ($qty < 1) $qty = 1;

                    $baik = isset($colMap['baik']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$colMap['baik']] ?? 0)) : 0;
                    $kb = isset($colMap['kurang_baik']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$colMap['kurang_baik']] ?? 0)) : 0;
                    $rb = isset($colMap['rusak_berat']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$colMap['rusak_berat']] ?? 0)) : 0;

                    if (($baik + $kb + $rb) === 0) {
                        $baik = $qty;
                    } elseif (($baik + $kb + $rb) < $qty) {
                        $baik += ($qty - ($baik + $kb + $rb));
                    }

                    $ket = isset($colMap['keterangan']) ? trim((string)($row[$colMap['keterangan']] ?? '')) : '';
                    if ($ket === '-') $ket = null;

                    KirItem::create([
                        'room_id' => $room->id,
                        'name' => $nama,
                        'merk' => $merk,
                        'bahan' => $bahan,
                        'jumlah' => $qty,
                        'kondisi_baik' => $baik,
                        'kondisi_kurang_baik' => $kb,
                        'kondisi_rusak_berat' => $rb,
                        'keterangan' => $ket,
                    ]);
                    $totalItems++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            $roomCount = count($processedRooms);
            $targetRoom = $defaultRoom ?: reset($processedRooms);

            if ($request->room_id && isset($processedRooms[$request->room_id])) {
                return redirect()->route('kir.room_items', $request->room_id)
                    ->with('success', "Berhasil mengimpor {$totalItems} jenis barang KIR ke ruangan {$targetRoom->name}.");
            }

            return redirect()->route('kir.index')
                ->with('success', "Berhasil mengimpor {$totalItems} data barang KIR ke dalam {$roomCount} ruangan.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses file KIR: ' . $e->getMessage());
        }
    }

    /**
     * Cetak KIR Resmi F4 untuk Ruangan Tertentu
     */
    public function printKir(Request $request, Room $room)
    {
        $groupedItems = $room->kirItems()->orderBy('id')->get();
        $allRooms = Room::orderBy('name')->get();
        $docDate = $request->input('date', '2026-01-02');
        $formattedDate = $this->formatIndoDate($docDate);

        // Map items untuk view print agar konsisten
        $mappedItems = $groupedItems->map(function($item, $index) {
            return (object)[
                'no' => $index + 1,
                'name' => $item->name,
                'merk' => $item->merk ?: '',
                'bahan' => $item->bahan ?: 'Campuran',
                'total' => $item->jumlah,
                'baik' => $item->kondisi_baik,
                'kurang_baik' => $item->kondisi_kurang_baik,
                'rusak_berat' => $item->kondisi_rusak_berat,
                'keterangan' => $item->keterangan ?: '',
            ];
        });

        $showTtd = $request->input('ttd', '1') !== '0';
        $modePages = $request->input('pages', null);

        return view('rooms.print_kir', [
            'room' => $room,
            'groupedItems' => $mappedItems,
            'allRooms' => $allRooms,
            'docDate' => $docDate,
            'formattedDate' => $formattedDate,
            'showTtd' => $showTtd,
            'modePages' => $modePages,
        ]);
    }

    /**
     * Cetak Batch KIR untuk Banyak Ruangan Sekaligus
     */
    public function printBatchKir(Request $request)
    {
        $ids = $request->input('ids');
        $query = Room::with('kirItems');

        if ($ids) {
            $idArray = is_array($ids) ? $ids : explode(',', $ids);
            $query->whereIn('id', $idArray);
        }

        $rooms = $query->orderBy('name')->get();

        if ($rooms->isEmpty()) {
            return redirect()->route('kir.index')->with('error', 'Tidak ada ruangan yang dipilih untuk cetak KIR.');
        }

        $allRooms = Room::orderBy('name')->get();
        $docDate = $request->input('date', '2026-01-02');
        $formattedDate = $this->formatIndoDate($docDate);

        $groupedItemsMap = [];
        foreach ($rooms as $r) {
            $groupedItemsMap[$r->id] = $r->kirItems->map(function($item, $index) {
                return (object)[
                    'no' => $index + 1,
                    'name' => $item->name,
                    'merk' => $item->merk ?: '',
                    'bahan' => $item->bahan ?: 'Campuran',
                    'total' => $item->jumlah,
                    'baik' => $item->kondisi_baik,
                    'kurang_baik' => $item->kondisi_kurang_baik,
                    'rusak_berat' => $item->kondisi_rusak_berat,
                    'keterangan' => $item->keterangan ?: '',
                ];
            });
        }

        $showTtd = $request->input('ttd', '1') !== '0';
        $modePages = $request->input('pages', null);

        return view('rooms.print_kir', [
            'room' => $rooms->first(),
            'rooms' => $rooms,
            'groupedItemsMap' => $groupedItemsMap,
            'allRooms' => $allRooms,
            'docDate' => $docDate,
            'formattedDate' => $formattedDate,
            'isBatch' => true,
            'showTtd' => $showTtd,
            'modePages' => $modePages,
        ]);
    }

    protected function formatIndoDate($dateString)
    {
        try {
            $carbon = \Carbon\Carbon::parse($dateString);
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            return sprintf('%02d %s %d', $carbon->day, $months[$carbon->month] ?? '', $carbon->year);
        } catch (\Exception $e) {
            return $dateString;
        }
    }
}
