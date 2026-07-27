<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255',
            'pangkat' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);
        
        $data = $request->all();
        $name = ucwords(strtolower($data['name']));
        $data['name'] = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $name);

        Employee::create($data);
        return back()->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);
        $file = $request->file('file');
        
        try {
            $handle = fopen($file->path(), 'r');
            $header = true;
            $delimiter = ',';
            
            // Cek delimiter yang digunakan (koma atau titik koma)
            $firstLine = fgets($handle);
            if (strpos($firstLine, ';') !== false) {
                $delimiter = ';';
            }
            rewind($handle);

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if ($header) {
                    $header = false;
                    continue;
                }
                if (count($data) >= 2) {
                    $nameRaw = trim($data[1] ?? '');
                    $nameFormatted = ucwords(strtolower($nameRaw));
                    $nameFormatted = str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], $nameFormatted);

                    // Struktur CSV: NO, NAMA, NIP, PANGKAT, GOLONGAN, JABATAN
                    Employee::create([
                        'name' => $nameFormatted,
                        'nip' => trim($data[2] ?? ''),
                        'pangkat' => trim($data[3] ?? ''),
                        'golongan' => trim($data[4] ?? ''),
                        'jabatan' => trim($data[5] ?? ''),
                    ]);
                }
            }
            fclose($handle);
            return back()->with('success', 'Data Pegawai berhasil di-import.');
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat import data. Pastikan format CSV sudah benar.');
        }
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
