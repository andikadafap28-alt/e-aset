@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Data Pegawai</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-200 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-xl border border-red-200 flex items-center gap-2">
            <span class="material-symbols-outlined">error</span>
            {{ session('error') }}
        </div>
    @endif
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500">upload_file</span>
            <h2 class="text-lg font-semibold text-slate-800">Import Data Pegawai</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-end gap-4">
                @csrf
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Pilih file CSV</label>
                    <input type="file" name="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors cursor-pointer border border-slate-200 rounded-xl" required accept=".csv,.txt">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">upload</span>
                    Import CSV
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">info</span>
                Format CSV: NO, NAMA, NIP, PANGKAT, GOLONGAN, JABATAN. Jangan gunakan spasi pada header CSV.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">NO</th>
                        <th class="px-6 py-4 min-w-[200px]">NAMA</th>
                        <th class="px-6 py-4 whitespace-nowrap">NIP</th>
                        <th class="px-6 py-4 min-w-[150px]">PANGKAT</th>
                        <th class="px-6 py-4 min-w-[200px]">GOLONGAN</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap min-w-[120px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $index => $employee)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 align-middle">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 align-middle font-medium text-slate-800">{{ $employee->name }}</td>
                        <td class="px-6 py-4 align-middle whitespace-nowrap">{{ $employee->nip ?: '-' }}</td>
                        <td class="px-6 py-4 align-middle">
                            @if($employee->pangkat)
                                <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs whitespace-nowrap">{{ $employee->pangkat }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle">
                            @if($employee->golongan)
                                <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold">{{ $employee->golongan }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center mx-auto" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data pegawai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
