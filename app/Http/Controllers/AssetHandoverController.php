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
        $handovers = AssetHandover::with(['asset', 'employee', 'items.asset'])->latest()->get();
        $tensimeterLoans = \App\Models\AssetLoan::with(['asset'])
            ->whereHas('asset', function($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%tensi%']);
            })
            ->latest('loan_date')
            ->latest('id')
            ->get();

        return view('bast.index', compact('handovers', 'tensimeterLoans'));
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
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'handover_date' => 'required|date',
            'keperluan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'sumber_dana' => 'nullable|string',
        ]);

        $handover = AssetHandover::create([
            'employee_id' => $request->employee_id,
            'handover_date' => $request->handover_date,
            'keperluan' => $request->keperluan,
            'keterangan' => $request->keterangan,
            'sumber_dana' => $request->sumber_dana,
            'asset_id' => $request->asset_ids[0] ?? null,
        ]);

        foreach ($request->asset_ids as $assetId) {
            \App\Models\AssetHandoverItem::create([
                'asset_handover_id' => $handover->id,
                'asset_id' => $assetId
            ]);
        }

        return redirect()->route('bast.show', $handover->id)->with('success', 'BAST berhasil dibuat untuk ' . count($request->asset_ids) . ' barang.');
    }

    public function show(AssetHandover $bast)
    {
        $bast->load('asset', 'employee', 'items.asset');
        
        $allItems = $bast->items->sortBy(function($item) {
            return $item->asset->id ?? 0;
        })->values();

        $groupedItems = collect();
        if ($bast->items && $bast->items->count() > 0) {
            $grouped = $bast->items->groupBy(function($item) {
                $name = $item->asset->name ?? '-';
                $keterangan = $item->asset->keterangan ?? ($item->asset->merk ?? '-');
                return $name . '|' . $keterangan;
            });
            
            foreach($grouped as $key => $group) {
                $first = clone $group->first();
                $first->qty = $group->count();
                
                $regs = $group->map(function($i) {
                    return $i->asset && $i->asset->no_register !== null ? (int)$i->asset->no_register : null;
                })->filter()->sort()->values();
                
                if ($regs->count() > 1) {
                    $first->registers = str_pad($regs->first(), 3, '0', STR_PAD_LEFT) . ' s/d ' . str_pad($regs->last(), 3, '0', STR_PAD_LEFT);
                } elseif ($regs->count() == 1) {
                    $first->registers = str_pad($regs->first(), 3, '0', STR_PAD_LEFT);
                } else {
                    $first->registers = '-';
                }
                
                $groupedItems->push($first);
            }
        }
        
        return view('bast.print', compact('bast', 'groupedItems', 'allItems'));
    }
    
    public function destroy(AssetHandover $bast)
    {
        $bast->delete();
        return back()->with('success', 'BAST berhasil dihapus.');
    }
}
