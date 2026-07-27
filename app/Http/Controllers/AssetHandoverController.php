<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHandover;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetHandoverController extends Controller
{
    public function index()
    {
        $handovers = AssetHandover::with(['asset', 'employee'])->latest()->get();
        return view('bast.index', compact('handovers'));
    }

    public function create()
    {
        $assets = Asset::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();
        return view('bast.create', compact('assets', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'handover_date' => 'required|date',
            'keperluan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'sumber_dana' => 'nullable|string',
        ]);

        $handover = AssetHandover::create($request->all());

        return redirect()->route('bast.show', $handover->id)->with('success', 'BAST berhasil dibuat.');
    }

    public function show(AssetHandover $bast)
    {
        $bast->load('asset', 'employee');
        return view('bast.print', compact('bast'));
    }
    
    public function destroy(AssetHandover $bast)
    {
        $bast->delete();
        return back()->with('success', 'BAST berhasil dihapus.');
    }
}
