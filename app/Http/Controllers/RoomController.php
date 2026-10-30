<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = \App\Models\Room::withCount('assets')->latest()->get();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Puskesmas,Pustu,Ponkesdes,Polindes,Ruangan',
            'penanggung_jawab' => 'nullable|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        \App\Models\Room::create($validated);
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(\App\Models\Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, \App\Models\Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Puskesmas,Pustu,Ponkesdes,Polindes,Ruangan',
            'penanggung_jawab' => 'nullable|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(\App\Models\Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            
            if (count($rows) > 1) {
                array_shift($rows);
                
                foreach ($rows as $row) {
                    if (empty($row[0])) continue;
                    
                    \App\Models\Room::create([
                        'name' => $row[0],
                        'type' => !empty($row[1]) && in_array(ucfirst(trim($row[1])), ['Puskesmas','Pustu','Ponkesdes','Polindes','Ruangan']) ? ucfirst(trim($row[1])) : 'Ruangan',
                        'penanggung_jawab' => $row[2] ?? null,
                        'latitude' => $row[3] ?? null,
                        'longitude' => $row[4] ?? null,
                        'description' => $row[5] ?? null,
                    ]);
                }
                return back()->with('success', 'Data lokasi berhasil diimport.');
            }
            return back()->with('error', 'File excel kosong atau format tidak sesuai.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Import_Ruangan.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Lokasi/Ruangan', 'Tipe (Puskesmas/Pustu/Ponkesdes/Polindes/Ruangan)', 'Penanggung Jawab', 'Latitude', 'Longitude', 'Keterangan']);
            fputcsv($file, ['Pustu Tugu', 'Pustu', 'Bidan A', '-7.12345', '112.12345', 'Kondisi baik']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadKirTemplate(Request $request)
    {
        $roomId = $request->query('room_id');
        $room = $roomId ? \App\Models\Room::find($roomId) : null;
        $fileName = $room ? 'Template_KIR_' . \Illuminate\Support\Str::slug($room->name) . '.xlsx' : 'Template_KIR_Format_Resmi.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TemplateKirExport($roomId), $fileName);
    }

        public function importKirExcel(Request $request)
    {
        return app(\App\Http\Controllers\KirController::class)->importExcel($request);
    }

    public function printKir(Request $request, \App\Models\Room $room)
    {
        $room->load(['assets' => function($query) {
            $query->orderBy('name');
        }]);
        
        $groupedItems = $this->getGroupedKirItems($room);
        $allRooms = \App\Models\Room::orderBy('name')->get();
        $docDate = $request->input('date', '2026-01-02');
        $formattedDate = $this->formatIndoDate($docDate);

        return view('rooms.print_kir', compact('room', 'groupedItems', 'allRooms', 'docDate', 'formattedDate'));
    }

    public function printBatchKir(Request $request)
    {
        $ids = $request->input('ids');
        $query = \App\Models\Room::with(['assets' => function($q) {
            $q->orderBy('name');
        }]);

        if ($ids) {
            $idArray = is_array($ids) ? $ids : explode(',', $ids);
            $query->whereIn('id', $idArray);
        }

        $rooms = $query->orderBy('name')->get();

        if ($rooms->isEmpty()) {
            return redirect()->route('rooms.index')->with('error', 'Tidak ada ruangan yang dipilih untuk cetak KIR.');
        }

        $allRooms = \App\Models\Room::orderBy('name')->get();
        $docDate = $request->input('date', '2026-01-02');
        $formattedDate = $this->formatIndoDate($docDate);

        $groupedItemsMap = [];
        foreach ($rooms as $r) {
            $groupedItemsMap[$r->id] = $this->getGroupedKirItems($r);
        }

        return view('rooms.print_kir', [
            'room' => $rooms->first(),
            'rooms' => $rooms,
            'groupedItemsMap' => $groupedItemsMap,
            'allRooms' => $allRooms,
            'docDate' => $docDate,
            'formattedDate' => $formattedDate,
            'isBatch' => true
        ]);
    }

    public function addKirItem(Request $request, \App\Models\Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'bahan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'condition' => 'required|in:Baik,Kurang Baik,Rusak Berat',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jumlah = (int)$validated['jumlah'];
        $lastNo = \App\Models\Asset::where('room_id', $room->id)->count() + 1;

        for ($i = 1; $i <= $jumlah; $i++) {
            $codeSuffix = $jumlah > 1 ? "-$i" : '';
            $assetCode = 'KIR-' . $room->id . '-' . str_pad($lastNo, 3, '0', STR_PAD_LEFT) . $codeSuffix;
            if (\App\Models\Asset::where('asset_code', $assetCode)->exists()) {
                $assetCode .= '-' . \Illuminate\Support\Str::random(3);
            }

            \App\Models\Asset::create([
                'room_id' => $room->id,
                'location' => $room->name,
                'name' => $validated['name'],
                'category' => 'Peralatan dan Mesin',
                'merk' => $validated['merk'] ?: null,
                'bahan' => $validated['bahan'] ?: 'Campuran',
                'condition' => $validated['condition'],
                'keterangan' => $validated['keterangan'] ?: null,
                'source' => 'KIR',
                'asset_code' => $assetCode,
                'year_purchased' => date('Y'),
            ]);
        }

        return redirect()->route('rooms.print_kir', $room->id)
            ->with('success', "Berhasil menambahkan {$jumlah} unit {$validated['name']} ke {$room->name}.");
    }

    public function clearRoomAssets(\App\Models\Room $room)
    {
        \App\Models\Asset::where('room_id', $room->id)->where('source', 'KIR')->delete();
        return redirect()->route('rooms.print_kir', $room->id)->with('success', "Daftar barang KIR di {$room->name} berhasil dikosongkan.");
    }

    protected function getGroupedKirItems(\App\Models\Room $room)
    {
        $groupedItems = collect();
        if ($room->assets && $room->assets->count() > 0) {
            $grouped = $room->assets->groupBy(function($item) {
                $name = trim((string)($item->name ?? '-'));
                $merk = trim((string)($item->merk ?? ''));
                $bahan = trim((string)($item->bahan ?? 'Campuran'));
                return strtolower($name) . '|' . strtolower($merk) . '|' . strtolower($bahan);
            });

            $no = 1;
            foreach ($grouped as $group) {
                $first = $group->first();

                $baik = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['baik', 'b']))->count();
                $kurangBaik = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['kurang baik', 'kb', 'rusak ringan', 'rr']))->count();
                $rusakBerat = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['rusak berat', 'rb']))->count();

                $total = $group->count();
                $totalKondisi = $baik + $kurangBaik + $rusakBerat;
                if ($totalKondisi < $total) {
                    $baik += ($total - $totalKondisi);
                }

                $groupedItems->push((object)[
                    'no' => $no++,
                    'name' => $first->name,
                    'merk' => $first->merk ?: '',
                    'bahan' => $first->bahan ?: 'Campuran',
                    'total' => $total,
                    'baik' => $baik,
                    'kurang_baik' => $kurangBaik,
                    'rusak_berat' => $rusakBerat,
                    'keterangan' => $first->keterangan ?? ''
                ]);
            }
        }
        return $groupedItems;
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

    public function printPemeliharaanAlat(\App\Models\Room $room)
    {
        $room->load(['assets.maintenances' => function($query) {
            $query->latest('tanggal_jadwal');
        }]);
        return view('rooms.print_pemeliharaan_alat', compact('room'));
    }

    public function printPemeliharaanRuangan(Request $request, \App\Models\Room $room)
    {
        $room->load(['assets.maintenances' => function($query) {
            $query->latest('tanggal_jadwal');
        }]);
        $rooms = collect([$room]);
        $docDate = $request->input('date', '2026-01-02');
        $targetRows = (int) $request->input('rows', 24);
        $printMode = $request->input('mode', 'full');

        return view('rooms.print_pemeliharaan_ruangan', compact('rooms', 'docDate', 'targetRows', 'printMode'));
    }

    public function lembarPemeliharaanIndex(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\Room::withCount('assets');
        
        if ($search) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }
        
        $rooms = $query->orderBy('name')->get();
        $defaultDate = '2026-01-02';
        return view('rooms.lembar_pemeliharaan_index', compact('rooms', 'search', 'defaultDate'));
    }

    public function printBatchPemeliharaanRuangan(Request $request)
    {
        $ids = $request->input('ids');
        $query = \App\Models\Room::with(['assets.maintenances' => function($q) {
            $q->latest('tanggal_jadwal');
        }]);

        if ($ids) {
            $idArray = is_array($ids) ? $ids : explode(',', $ids);
            $query->whereIn('id', $idArray);
        }

        $rooms = $query->orderBy('name')->get();

        if ($rooms->isEmpty()) {
            return redirect()->route('rooms.lembar_pemeliharaan')->with('error', 'Tidak ada ruangan yang dipilih.');
        }

        $docDate = $request->input('date', '2026-01-02');
        $targetRows = (int) $request->input('rows', 24);
        $printMode = $request->input('mode', 'full');

        return view('rooms.print_pemeliharaan_ruangan', compact('rooms', 'docDate', 'targetRows', 'printMode'));
    }
}
