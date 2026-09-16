<?php

namespace App\Http\Controllers;

use App\Models\Dpp;
use App\Models\DppAttachment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DppController extends Controller
{
    public function index()
    {
        $dpps = Dpp::orderBy('created_at', 'desc')->get();
        return view('aset.dpp.index', compact('dpps'));
    }

    public function create()
    {
        if (auth()->user()?->role === 'arsiparis') abort(403);
        return view('aset.dpp.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()?->role === 'arsiparis') abort(403);
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'kode_rup' => 'required|string|max:255',
            'kode_rup_manual' => 'nullable|string|max:255',
            'nama_paket' => 'nullable|string|max:255',
            'spesifikasi_teknis' => 'nullable|string',
            'jumlah' => 'nullable|string',
            'harga_satuan' => 'nullable|numeric',
            'pagu_anggaran' => 'nullable|numeric',
            'tanggal_dpp' => 'required|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        if ($validated['kode_rup'] === 'Manual') {
            $validated['kode_rup'] = $request->input('kode_rup_manual');
        }
        unset($validated['kode_rup_manual']);

        Dpp::create($validated);

        return redirect()->route('aset.dpp.index')->with('success', 'DPP berhasil dibuat.');
    }

    public function show($id)
    {
        $dpp = Dpp::with('attachments')->findOrFail($id);
        return view('aset.dpp.show', compact('dpp'));
    }

    public function generatePdf($id)
    {
        $dpp = Dpp::findOrFail($id);

        $pdf = Pdf::loadView('aset.dpp.pdf', compact('dpp'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('DPP_' . $dpp->nomor_surat . '.pdf');
    }

    public function uploadAttachment(Request $request, $id)
    {
        if (auth()->user()?->role === 'arsiparis') abort(403);
        $request->validate([
            'jenis_file' => 'required|string',
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $dpp = Dpp::findOrFail($id);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('dpp_attachments/' . $dpp->id, $filename, 'public');

        $dpp->attachments()->create([
            'jenis_file' => $request->jenis_file,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);

        return back()->with('success', 'File berhasil diunggah.');
    }

    public function deleteAttachment($attachmentId)
    {
        if (auth()->user()?->role === 'arsiparis') abort(403);
        $attachment = DppAttachment::findOrFail($attachmentId);
        
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }

    public function destroy($id)
    {
        if (auth()->user()?->role === 'arsiparis') abort(403);
        $dpp = Dpp::findOrFail($id);
        
        // Delete all physical files for attachments
        foreach ($dpp->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }
        
        $dpp->delete();

        return redirect()->route('aset.dpp.index')->with('success', 'DPP berhasil dihapus.');
    }
}
