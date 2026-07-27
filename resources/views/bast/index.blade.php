@extends('layouts.app')
@section('title', 'Bukti Serah Terima (BAST)')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Bukti Serah Terima (BAST)</h1>
        <a href="{{ route('bast.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat BAST Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>NAMA BARANG</th>
                            <th>PENERIMA</th>
                            <th>KEPERLUAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($handovers as $bast)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($bast->handover_date)->format('d-m-Y') }}</td>
                            <td>{{ $bast->asset->name ?? '-' }} ({{ $bast->asset->asset_code ?? '-' }})</td>
                            <td>{{ $bast->employee ? str_replace(['Dr. ', 'Drg. '], ['dr. ', 'drg. '], ucwords(strtolower($bast->employee->name))) : '-' }}</td>
                            <td>{{ $bast->keperluan }}</td>
                            <td>
                                <a href="{{ route('bast.show', $bast->id) }}" class="btn btn-sm btn-info" target="_blank"><i class="fas fa-print"></i> Cetak</a>
                                <form action="{{ route('bast.destroy', $bast->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus BAST ini?')">
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
