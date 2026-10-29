<?php

namespace App\Http\Controllers;

use App\Models\StockOpnamePersediaan;
use Illuminate\Http\Request;

class StockOpnamePersediaanController extends Controller
{
    public function index()
    {
        $records = StockOpnamePersediaan::latest('tanggal_opname')->latest('id')->paginate(15);
        $totalDokumen = StockOpnamePersediaan::count();
        $totalNilaiSemua = StockOpnamePersediaan::all()->sum(function($r) {
            return $r->total_nilai;
        });

        return view('stock_opname_persediaan.index', compact('records', 'totalDokumen', 'totalNilaiSemua'));
    }

    public function create()
    {
        return view('stock_opname_persediaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_opname' => 'required|date',
            'atk' => 'nullable|numeric|min:0',
            'bahan_cetak' => 'nullable|numeric|min:0',
            'benda_pos' => 'nullable|numeric|min:0',
            'bahan_komputer' => 'nullable|numeric|min:0',
            'obat_blud' => 'nullable|numeric|min:0',
            'bmhp_blud' => 'nullable|numeric|min:0',
            'obat_apbd' => 'nullable|numeric|min:0',
            'bmhp_apbd' => 'nullable|numeric|min:0',
            'obat_apbn' => 'nullable|numeric|min:0',
            'bmhp_apbn' => 'nullable|numeric|min:0',
            'nama_pemeriksa' => 'nullable|string',
            'nip_pemeriksa' => 'nullable|string',
            'jabatan_pemeriksa' => 'nullable|string',
            'nama_diperiksa' => 'nullable|string',
            'nip_diperiksa' => 'nullable|string',
            'jabatan_diperiksa' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Default numeric 0 jika null
        $numericFields = ['atk', 'bahan_cetak', 'benda_pos', 'bahan_komputer', 'obat_blud', 'bmhp_blud', 'obat_apbd', 'bmhp_apbd', 'obat_apbn', 'bmhp_apbn'];
        foreach ($numericFields as $field) {
            $validated[$field] = $request->filled($field) ? (float)$request->input($field) : 0;
        }

        $record = StockOpnamePersediaan::create($validated);

        return redirect()->route('ba-stock-opname.show', $record->id)
            ->with('success', 'Berita Acara Stok Opname Persediaan berhasil dibuat!');
    }

    public function show($id)
    {
        $record = StockOpnamePersediaan::findOrFail($id);
        return view('stock_opname_persediaan.print', compact('record'));
    }

    public function edit($id)
    {
        $record = StockOpnamePersediaan::findOrFail($id);
        return view('stock_opname_persediaan.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = StockOpnamePersediaan::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_opname' => 'required|date',
            'atk' => 'nullable|numeric|min:0',
            'bahan_cetak' => 'nullable|numeric|min:0',
            'benda_pos' => 'nullable|numeric|min:0',
            'bahan_komputer' => 'nullable|numeric|min:0',
            'obat_blud' => 'nullable|numeric|min:0',
            'bmhp_blud' => 'nullable|numeric|min:0',
            'obat_apbd' => 'nullable|numeric|min:0',
            'bmhp_apbd' => 'nullable|numeric|min:0',
            'obat_apbn' => 'nullable|numeric|min:0',
            'bmhp_apbn' => 'nullable|numeric|min:0',
            'nama_pemeriksa' => 'nullable|string',
            'nip_pemeriksa' => 'nullable|string',
            'jabatan_pemeriksa' => 'nullable|string',
            'nama_diperiksa' => 'nullable|string',
            'nip_diperiksa' => 'nullable|string',
            'jabatan_diperiksa' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $numericFields = ['atk', 'bahan_cetak', 'benda_pos', 'bahan_komputer', 'obat_blud', 'bmhp_blud', 'obat_apbd', 'bmhp_apbd', 'obat_apbn', 'bmhp_apbn'];
        foreach ($numericFields as $field) {
            $validated[$field] = $request->filled($field) ? (float)$request->input($field) : 0;
        }

        $record->update($validated);

        return redirect()->route('ba-stock-opname.show', $record->id)
            ->with('success', 'Berita Acara Stok Opname Persediaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $record = StockOpnamePersediaan::findOrFail($id);
        $record->delete();

        return redirect()->route('ba-stock-opname.index')
            ->with('success', 'Berita Acara berhasil dihapus.');
    }
}
