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

    public function printKir(\App\Models\Room $room)
    {
        $room->load(['assets' => function($query) {
            $query->orderBy('name');
        }]);
        
        $groupedItems = collect();
        if ($room->assets && $room->assets->count() > 0) {
            $grouped = $room->assets->groupBy(function($item) {
                $name = $item->name ?? '-';
                $merk = $item->merk ?? 'Tanpa Merk';
                return $name . '|' . $merk;
            });
            
            $no = 1;
            foreach($grouped as $key => $group) {
                $first = $group->first();
                
                $baik = $group->where('kondisi', 'baik')->count() + $group->where('kondisi', 'Baik')->count() + $group->where('kondisi', 'B')->count();
                $kurangBaik = $group->where('kondisi', 'kurang baik')->count() + $group->where('kondisi', 'Kurang Baik')->count() + $group->where('kondisi', 'KB')->count();
                $rusakBerat = $group->where('kondisi', 'rusak berat')->count() + $group->where('kondisi', 'Rusak Berat')->count() + $group->where('kondisi', 'RB')->count();
                
                // fallback jika kondisi kosong atau tidak match, anggap baik
                $totalKondisi = $baik + $kurangBaik + $rusakBerat;
                if ($totalKondisi < $group->count()) {
                    $baik += ($group->count() - $totalKondisi);
                }
                
                $groupedItems->push((object)[
                    'no' => $no++,
                    'name' => $first->name,
                    'merk' => $first->merk,
                    'bahan' => $first->bahan ?? '-',
                    'total' => $group->count(),
                    'baik' => $baik,
                    'kurang_baik' => $kurangBaik,
                    'rusak_berat' => $rusakBerat,
                    'keterangan' => $first->keterangan ?? ''
                ]);
            }
        }
        
        return view('rooms.print_kir', compact('room', 'groupedItems'));
    }

    public function printPemeliharaanAlat(\App\Models\Room $room)
    {
        $room->load(['assets.maintenances' => function($query) {
            $query->latest('tanggal_jadwal');
        }]);
        return view('rooms.print_pemeliharaan_alat', compact('room'));
    }

    public function printPemeliharaanRuangan(\App\Models\Room $room)
    {
        return view('rooms.print_pemeliharaan_ruangan', compact('room'));
    }
}
