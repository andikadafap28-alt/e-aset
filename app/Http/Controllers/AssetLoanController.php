<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetLoan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetLoanController extends Controller
{
    public function index()
    {
        $loans = AssetLoan::with('asset')->orderBy('created_at', 'desc')->get();
        $assets = Asset::where('status_aktif', true)->get(['id', 'name', 'asset_code']);
        return view('aset.peminjaman', compact('loans', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'borrower_name' => 'required|string|max:255',
            'loan_date' => 'required|date',
            'expected_return_date' => 'nullable|date',
        ]);

        AssetLoan::create([
            'asset_id' => $request->asset_id,
            'borrower_name' => $request->borrower_name,
            'borrower_position' => $request->borrower_position,
            'borrower_contact' => $request->borrower_contact,
            'loan_date' => $request->loan_date,
            'expected_return_date' => $request->expected_return_date,
            'status' => 'dipinjam',
            'approval_status' => 'pending',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Peminjaman berhasil dicatat dan menunggu persetujuan Kepala Puskesmas.');
    }

    public function returnLoan(Request $request, $id)
    {
        $loan = AssetLoan::findOrFail($id);
        
        $request->validate([
            'actual_return_date' => 'required|date',
        ]);

        $loan->update([
            'status' => 'dikembalikan',
            'actual_return_date' => $request->actual_return_date,
        ]);

        return back()->with('success', 'Aset berhasil dikembalikan.');
    }

    public function approveLoan($id)
    {
        if (auth()->user()?->role !== 'kepala') {
            return back()->with('error', 'Hanya Kepala Puskesmas yang dapat menyetujui peminjaman.');
        }

        $loan = AssetLoan::findOrFail($id);
        $loan->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function rejectLoan($id)
    {
        if (auth()->user()?->role !== 'kepala') {
            return back()->with('error', 'Hanya Kepala Puskesmas yang dapat menolak peminjaman.');
        }

        $loan = AssetLoan::findOrFail($id);
        $loan->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Peminjaman telah ditolak.');
    }

    public function downloadBast($id)
    {
        $loan = AssetLoan::with(['asset', 'approver'])->findOrFail($id);

        if ($loan->approval_status !== 'approved') {
            return back()->with('error', 'BAST hanya dapat dicetak untuk peminjaman yang telah disetujui.');
        }

        // Generate QR Code containing the verification URL
        $verifyUrl = route('verify.bast', $loan->id);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $data = [
            'loan' => $loan,
            'qrCode' => $qrCode,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y')
        ];

        $pdf = Pdf::loadView('aset.bast_pdf', $data);
        return $pdf->download('BAST_Peminjaman_' . $loan->id . '_' . date('Ymd') . '.pdf');
    }

    public function verifyBast($id)
    {
        $loan = AssetLoan::with(['asset', 'approver'])->findOrFail($id);
        
        if ($loan->approval_status !== 'approved') {
            abort(404, 'Dokumen tidak valid atau belum disetujui.');
        }

        return view('aset.verify_bast', compact('loan'));
    }

    public function printBast($id)
    {
        $loan = AssetLoan::with(['asset', 'approver'])->findOrFail($id);
        
        $date = \Carbon\Carbon::now()->translatedFormat('d F Y');
        
        // Find NIP of borrower if exists in Employee table
        $employee = \App\Models\Employee::where('name', $loan->borrower_name)->first();
        $borrowerNip = $employee ? $employee->nip : '';
        
        // Format NIP (e.g., 199907282025041003 -> 19990728 202504 1 003)
        $borrowerNipRaw = preg_replace('/[^0-9]/', '', $borrowerNip);
        if (strlen($borrowerNipRaw) == 18) {
            $borrowerNip = substr($borrowerNipRaw, 0, 8) . ' ' . substr($borrowerNipRaw, 8, 6) . ' ' . substr($borrowerNipRaw, 14, 1) . ' ' . substr($borrowerNipRaw, 15, 3);
        }
        
        return view('aset.bast_print_editable', compact('loan', 'date', 'borrowerNip'));
    }

    public function destroy($id)
    {
        $loan = AssetLoan::findOrFail($id);
        $loan->delete();
        return back()->with('success', 'Riwayat BAST berhasil dihapus.');
    }
}
