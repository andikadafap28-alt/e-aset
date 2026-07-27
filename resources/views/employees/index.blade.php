@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pegawai</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Import Data Pegawai</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="form-inline">
                @csrf
                <div class="form-group mr-2 mb-2">
                    <input type="file" name="file" class="form-control-file" required accept=".csv,.txt">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Import CSV</button>
            </form>
            <small class="text-muted d-block">Format CSV: NO, NAMA, NIP/NRPTT, PANGKAT, GOLONGAN, JABATAN. Jangan gunakan spasi pada header CSV.</small>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>NIP/NRPTT</th>
                            <th>PANGKAT</th>
                            <th>GOLONGAN</th>
                            <th>JABATAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $index => $employee)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->nip }}</td>
                            <td>{{ $employee->pangkat }}</td>
                            <td>{{ $employee->golongan }}</td>
                            <td>{{ $employee->jabatan }}</td>
                            <td>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
