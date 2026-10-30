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
     * Mendukung multi-sheet (setiap sheet = 1 ruangan), deteksi metadata ruangan ("RUANGAN : ..."),
     * deteksi kolom ruangan, atau fallback ke target ruangan yang dipilih.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'room_id' => 'nullable|exists:rooms,id',
            'replace_existing' => 'nullable'
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheets = $spreadsheet->getAllSheets();

            if (empty($sheets)) {
                return back()->with('error', 'File Excel kosong atau format tidak sesuai.');
            }

            $defaultRoom = $request->room_id ? Room::find($request->room_id) : null;
            $replaceExisting = $request->has('replace_existing') && ($request->input('replace_existing') == '1' || $request->input('replace_existing') === true || $request->input('replace_existing') === 'on');

            $processedRooms = [];
            $clearedRooms = [];
            $totalItems = 0;

            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($sheets as $sheet) {
                $rows = $sheet->toArray();
                if (empty($rows) || count($rows) < 2) {
                    continue; // Lewati sheet kosong
                }

                // 1. Deteksi nama ruangan dari metadata atas sheet (baris 0 - 15)
                $metadataRoomName = null;
                $sheetTitle = trim($sheet->getTitle());
                $genericSheetTitles = ['sheet1', 'sheet 1', 'sheet2', 'sheet 2', 'sheet3', 'sheet 3', 'sheet', 'kir', 'template', 'data', 'rekap', 'table', 'halaman'];

                foreach (array_slice($rows, 0, 15) as $metaRow) {
                    foreach ($metaRow as $cellVal) {
                        $cellStr = trim((string)$cellVal);
                        if (empty($cellStr)) continue;

                        // Contoh: "RUANGAN : POLI GIGI" atau "RUANG: KEPALA PUSKESMAS" atau "LOKASI : UGD"
                        if (preg_match('/(?:ruangan|ruang|lokasi)\s*[:\-=]\s*([^\r\n\|;]+)/i', $cellStr, $m)) {
                            $extracted = trim($m[1]);
                            // Pastikan bukan sekedar angka atau terlalu pendek
                            if (!empty($extracted) && !is_numeric($extracted) && strlen($extracted) > 1) {
                                $metadataRoomName = $extracted;
                                break 2;
                            }
                        }
                    }
                }

                // 2. Deteksi header kolom tabel (scan baris 0 - 15)
                $headerRowIndex = -1;
                $colMap = null;

                for ($rIdx = 0; $rIdx < min(15, count($rows)); $rIdx++) {
                    $row = $rows[$rIdx];
                    $tempMap = [];
                    $score = 0;

                    foreach ($row as $cIdx => $cell) {
                        $val = strtolower(trim((string)$cell));
                        if ($val === '') continue;

                        // Cek nama barang (wajib)
                        if (str_contains($val, 'nama barang') || str_contains($val, 'jenis barang') || str_contains($val, 'uraian') || $val === 'nama' || $val === 'barang') {
                            $tempMap['name'] = $cIdx;
                            $score += 3;
                        }
                        // Cek ruang / lokasi (HANYA jika bukan "nomor" atau "no")
                        elseif ((str_contains($val, 'ruang') || str_contains($val, 'lokasi')) && !str_starts_with($val, 'no') && !str_contains($val, 'nomor') && !str_contains($val, 'urut')) {
                            $tempMap['room'] = $cIdx;
                            $score += 2;
                        }
                        // Cek Merk
                        elseif (str_contains($val, 'merek') || str_contains($val, 'merk') || str_contains($val, 'model') || str_contains($val, 'tipe') || $val === 'type') {
                            $tempMap['merk'] = $cIdx;
                            $score += 1;
                        }
                        // Cek Bahan
                        elseif (str_contains($val, 'bahan') || str_contains($val, 'material')) {
                            $tempMap['bahan'] = $cIdx;
                            $score += 1;
                        }
                        // Cek Jumlah
                        elseif (str_contains($val, 'jumlah') || str_contains($val, 'qty') || str_contains($val, 'banyak') || str_contains($val, 'total')) {
                            $tempMap['jumlah'] = $cIdx;
                            $score += 2;
                        }
                        // Cek Kurang Baik
                        elseif (str_contains($val, 'kurang baik') || str_contains($val, 'rusak ringan') || $val === 'kb' || $val === 'rr') {
                            $tempMap['kurang_baik'] = $cIdx;
                            $score += 1;
                        }
                        // Cek Rusak Berat
                        elseif (str_contains($val, 'rusak berat') || $val === 'rb') {
                            $tempMap['rusak_berat'] = $cIdx;
                            $score += 1;
                        }
                        // Cek Baik
                        elseif ($val === 'baik' || str_contains($val, 'keadaan baik') || $val === 'b') {
                            $tempMap['baik'] = $cIdx;
                            $score += 1;
                        }
                        // Cek Keterangan
                        elseif (str_contains($val, 'keterangan') || str_contains($val, 'mutasi') || $val === 'ket') {
                            $tempMap['keterangan'] = $cIdx;
                            $score += 1;
                        }
                    }

                    if (isset($tempMap['name']) && $score >= 4) {
                        $colMap = $tempMap;
                        $headerRowIndex = $rIdx;
                        break;
                    }
                }

                // Fallback default kolom jika tidak terdeteksi via keyword
                if ($colMap === null) {
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
                    $headerRowIndex = 0;
                }

                $dataRows = array_slice($rows, $headerRowIndex + 1);

                foreach ($dataRows as $row) {
                    $namaBarang = isset($colMap['name']) ? trim((string)($row[$colMap['name']] ?? '')) : '';
                    if (empty($namaBarang)) continue;

                    $lowerName = strtolower($namaBarang);
                    // Lewati baris header berulang atau baris tanda tangan / total
                    if (str_contains($lowerName, 'nama barang') || str_contains($lowerName, 'jenis barang') || $lowerName === 'uraian') continue;
                    if (str_starts_with($lowerName, 'total') || str_starts_with($lowerName, 'jumlah') || str_starts_with($lowerName, 'mengetahui') || str_starts_with($lowerName, 'kepala')) continue;

                    // Tentukan nama ruangan untuk baris ini
                    $targetRoomName = '';

                    // 1. Cek dari kolom ruangan (JIKA BUKAN ANGKA dan valid)
                    if (isset($colMap['room'])) {
                        $candidate = trim((string)($row[$colMap['room']] ?? ''));
                        // PENTING: Kolom nomor urut (1, 2, 3...) BUKAN nama ruangan
                        if (!empty($candidate) && !is_numeric($candidate) && strlen($candidate) > 1) {
                            $targetRoomName = $candidate;
                        }
                    }

                    // 2. Jika tidak ada kolom ruangan per-baris, gunakan nama ruangan dari metadata sheet
                    if (empty($targetRoomName) && !empty($metadataRoomName)) {
                        $targetRoomName = $metadataRoomName;
                    }

                    // 3. Jika masih kosong, gunakan nama sheet jika tidak generik
                    if (empty($targetRoomName) && !empty($sheetTitle) && !in_array(strtolower($sheetTitle), $genericSheetTitles) && strlen($sheetTitle) > 1) {
                        $targetRoomName = $sheetTitle;
                    }

                    // 4. Jika masih kosong dan user memilih ruangan target di modal
                    if (empty($targetRoomName) && $defaultRoom) {
                        $targetRoomName = $defaultRoom->name;
                    }

                    // 5. Fallback terakhir
                    if (empty($targetRoomName)) {
                        $targetRoomName = 'Ruang Kepala Puskesmas';
                    }

                    // Pastikan selalu bertipe string untuk mencegah PostgreSQL type mismatch
                    $targetRoomName = trim((string)$targetRoomName);

                    // Cari atau buat Room secara aman di PostgreSQL (string parameter)
                    $room = Room::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($targetRoomName)])->first();
                    if (!$room) {
                        $room = Room::create([
                            'name' => (string)$targetRoomName,
                            'type' => 'Ruangan'
                        ]);
                    }

                    $processedRooms[$room->id] = $room;

                    // Bersihkan data lama jika opsi replace_existing dicentang (hanya sekali per ruangan)
                    if ($replaceExisting && !isset($clearedRooms[$room->id])) {
                        KirItem::where('room_id', $room->id)->delete();
                        $clearedRooms[$room->id] = true;
                    }

                    // Parse atribut barang
                    $merk = isset($colMap['merk']) ? trim((string)($row[$colMap['merk']] ?? '')) : '';
                    if ($merk === '-' || empty($merk)) $merk = null;

                    $bahan = isset($colMap['bahan']) ? trim((string)($row[$colMap['bahan']] ?? '')) : 'Campuran';
                    if (empty($bahan) || $bahan === '-') $bahan = 'Campuran';

                    $qtyRaw = isset($colMap['jumlah']) ? (string)($row[$colMap['jumlah']] ?? '1') : '1';
                    $qtyClean = preg_replace('/[^0-9]/', '', $qtyRaw);
                    $qty = !empty($qtyClean) ? (int)$qtyClean : 1;
                    if ($qty < 1) $qty = 1;

                    $baikRaw = isset($colMap['baik']) ? (string)($row[$colMap['baik']] ?? '') : '';
                    $kbRaw = isset($colMap['kurang_baik']) ? (string)($row[$colMap['kurang_baik']] ?? '') : '';
                    $rbRaw = isset($colMap['rusak_berat']) ? (string)($row[$colMap['rusak_berat']] ?? '') : '';

                    $baikClean = preg_replace('/[^0-9]/', '', $baikRaw);
                    $kbClean = preg_replace('/[^0-9]/', '', $kbRaw);
                    $rbClean = preg_replace('/[^0-9]/', '', $rbRaw);

                    $baik = ($baikClean !== '') ? (int)$baikClean : 0;
                    $kb = ($kbClean !== '') ? (int)$kbClean : 0;
                    $rb = ($rbClean !== '') ? (int)$rbClean : 0;

                    if (($baik + $kb + $rb) === 0) {
                        $baik = $qty;
                    } elseif (($baik + $kb + $rb) < $qty) {
                        $baik += ($qty - ($baik + $kb + $rb));
                    }

                    $ket = isset($colMap['keterangan']) ? trim((string)($row[$colMap['keterangan']] ?? '')) : '';
                    if ($ket === '-' || empty($ket)) $ket = null;

                    KirItem::create([
                        'room_id' => $room->id,
                        'name' => (string)$namaBarang,
                        'merk' => $merk ? (string)$merk : null,
                        'bahan' => (string)$bahan,
                        'jumlah' => $qty,
                        'kondisi_baik' => $baik,
                        'kondisi_kurang_baik' => $kb,
                        'kondisi_rusak_berat' => $rb,
                        'keterangan' => $ket ? (string)$ket : null,
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
