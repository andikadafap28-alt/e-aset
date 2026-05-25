<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetMutation;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::with('category')->latest()->get();
        return view('aset.data', compact('assets'));
    }

    public function publicShow($asset_code)
    {
        $asset = Asset::with('category', 'mutations', 'maintenances')->where('asset_code', $asset_code)->firstOrFail();
        $isAuthenticated = session('public_authenticated_' . $asset->id, false);
        return view('aset.public-show', compact('asset', 'isAuthenticated'));
    }

    public function verifyPublicPassword(Request $request, $asset_code)
    {
        $asset = Asset::where('asset_code', $asset_code)->firstOrFail();
        $password = $request->input('password');
        $correctPassword = \App\Models\Setting::where('key', 'public_asset_password')->value('value') ?? 'Mantup135';
        
        if ($password === $correctPassword) {
            session(['public_authenticated_' . $asset->id => true]);
            return back()->with('success', 'Akses detail terbuka.');
        }
        
        return back()->withErrors(['password' => 'Kata sandi salah!']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pengadaanData = [
            'pengadaan_id' => $request->query('pengadaan_id'),
            'name' => $request->query('nama'),
            'category' => $request->query('kategori'),
            'year_purchased' => $request->query('tahun'),
            'jumlah' => $request->query('jumlah', 1)
        ];
        $masterKode108 = \App\Models\MasterKode108::orderBy('kode', 'asc')->get();
        $categories = \App\Models\AssetCategory::orderBy('nama_kategori', 'asc')->get();
        return view('aset.form', compact('pengadaanData', 'masterKode108', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $jumlah = $request->input('jumlah', 1);
        $pengadaanId = $request->input('pengadaan_id');
        $kode108 = $request->input('kode_108');

        $hargaPerolehan = $request->input('harga_perolehan');

        if ($pengadaanId) {
            $item = \App\Models\Item::find($pengadaanId);
            if ($item) {
                if (!$hargaPerolehan) {
                    $hargaPerolehan = $item->harga_satuan;
                }
                $terdaftar = Asset::where('pengadaan_id', $pengadaanId)->count();
                $sisa = $item->stok_sekarang - $terdaftar;
                if ($jumlah > $sisa) {
                    return back()->withErrors(['jumlah' => "Jumlah maksimal yang bisa didaftarkan adalah {$sisa} dari {$item->stok_sekarang} total barang pengadaan."])->withInput();
                }
            }
        }

        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255', // Legacy
            'category_id' => 'required|exists:asset_categories,id',
            'harga_perolehan' => 'nullable|numeric',
            'location' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'year_purchased' => 'required|digits:4',
            'last_calibration' => 'nullable|date',
            'next_calibration' => 'nullable|date',
            'next_service' => 'nullable|date',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'document_link' => 'nullable|url',
        ];

        if (!$kode108) {
            if ($jumlah == 1) {
                $rules['asset_code'] = 'required|unique:assets,asset_code';
            } else {
                $rules['asset_code'] = 'required';
            }
        }

        $validated = $request->validate($rules);
        $validated['pengadaan_id'] = $pengadaanId;
        $validated['harga_perolehan'] = $hargaPerolehan;
        $validated['status_aktif'] = true;
        
        // Fix for legacy 'category' column which is NOT NULL in database
        if (empty($validated['category'])) {
            $catObj = \App\Models\AssetCategory::find($request->category_id);
            $validated['category'] = $catObj ? $catObj->nama_kategori : '-';
        }

        $startRegister = 1;
        if ($kode108) {
            $lastRegister = Asset::where('kode_108', $kode108)->max('no_register');
            $startRegister = $lastRegister ? $lastRegister + 1 : 1;
        }

        if ($jumlah > 1) {
            for ($i = 1; $i <= $jumlah; $i++) {
                $data = $validated;
                $data['is_in_print_queue'] = true;
                
                if ($kode108) {
                    $currentReg = $startRegister + ($i - 1);
                    $data['kode_108'] = $kode108;
                    $data['no_register'] = $currentReg;
                    $data['asset_code'] = $kode108 . ' - ' . str_pad($currentReg, 4, '0', STR_PAD_LEFT);
                } else {
                    $data['asset_code'] = $request->asset_code . '-' . str_pad($i, strlen($jumlah), '0', STR_PAD_LEFT);
                }
                
                if (Asset::where('asset_code', $data['asset_code'])->exists()) {
                    return back()->withErrors(['asset_code' => 'Kode aset ' . $data['asset_code'] . ' sudah digunakan.'])->withInput();
                }
                
                Asset::create($data);
            }
            return redirect()->route('aset.index')->with('success', $jumlah . ' Aset berhasil ditambahkan.');
        } else {
            $data = $validated;
            $data['is_in_print_queue'] = true;
            
            if ($kode108) {
                $data['kode_108'] = $kode108;
                $data['no_register'] = $startRegister;
                $data['asset_code'] = $kode108 . ' - ' . str_pad($startRegister, 4, '0', STR_PAD_LEFT);
            } else {
                $data['asset_code'] = $request->asset_code;
            }

            Asset::create($data);
            return redirect()->route('aset.index')->with('success', 'Aset berhasil ditambahkan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::findOrFail($id);
        return view('aset.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);
        $masterKode108 = \App\Models\MasterKode108::orderBy('kode', 'asc')->get();
        $categories = \App\Models\AssetCategory::orderBy('nama_kategori', 'asc')->get();
        return view('aset.form', compact('asset', 'masterKode108', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'asset_code' => 'required|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255', // Legacy
            'category_id' => 'required|exists:asset_categories,id',
            'harga_perolehan' => 'required|numeric',
            'location' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'year_purchased' => 'required|digits:4',
            'last_calibration' => 'nullable|date',
            'next_calibration' => 'nullable|date',
            'next_service' => 'nullable|date',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'document_link' => 'nullable|url',
        ]);

        // Fix for legacy 'category' column which is NOT NULL in database
        if (empty($validated['category'])) {
            $catObj = \App\Models\AssetCategory::find($request->category_id);
            $validated['category'] = $catObj ? $catObj->nama_kategori : '-';
        }

        $asset->update($validated);

        return redirect()->route('aset.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus.');
    }

    public function pemeliharaan() { return view('aset.pemeliharaan'); }
    public function monitoring()
    {
        $assets = \App\Models\Asset::latest()->get();
        return view('aset.monitoring', compact('assets'));
    }
    public function pelabelan()
    {
        $assets = \App\Models\Asset::latest()->get();
        return view('aset.pelabelan', compact('assets'));
    }

    public function printLabels(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        $assets = \App\Models\Asset::whereIn('id', $request->asset_ids)->get();
        return view('aset.print-labels', compact('assets'));
    }

    public function getPrintQueueData()
    {
        $assets = Asset::where('is_in_print_queue', true)->get();
        return response()->json([
            'count' => $assets->count(),
            'assets' => $assets
        ]);
    }

    public function removeFromPrintQueue($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update(['is_in_print_queue' => false]);
        return response()->json(['success' => true]);
    }

    public function clearPrintQueue()
    {
        Asset::where('is_in_print_queue', true)->update(['is_in_print_queue' => false]);
        return response()->json(['success' => true]);
    }

    public function printQueue()
    {
        $assets = Asset::where('is_in_print_queue', true)->get();
        return view('aset.print-labels', compact('assets'));
    }

    public function mutasi() 
    { 
        $mutations = AssetMutation::with('asset')->latest('tanggal_mutasi')->latest('id')->get();
        return view('aset.mutasi', compact('mutations')); 
    }

    public function createMutasi()
    {
        $assets = Asset::where('status_aktif', true)->get();
        return view('aset.mutasi_create', compact('assets'));
    }

    public function storeMutasi(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tanggal_mutasi' => 'required|date',
            'lokasi_baru' => 'required|string',
            'penanggung_jawab_baru' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        AssetMutation::create([
            'asset_id' => $asset->id,
            'tanggal_mutasi' => $request->tanggal_mutasi,
            'lokasi_lama' => $asset->location,
            'lokasi_baru' => $request->lokasi_baru,
            'penanggung_jawab_lama' => $asset->penanggung_jawab,
            'penanggung_jawab_baru' => $request->penanggung_jawab_baru,
            'keterangan' => $request->keterangan
        ]);

        $asset->update([
            'location' => $request->lokasi_baru,
            'penanggung_jawab' => $request->penanggung_jawab_baru
        ]);

        return redirect()->route('aset.mutasi.items')->with('success', 'Mutasi aset berhasil dicatat.');
    }
}
