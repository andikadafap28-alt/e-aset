@extends('layouts.app')
@section('title', 'Buat BAST Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Bukti Serah Terima (BAST) Baru</h1>
        <a href="{{ route('bast.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('bast.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Tanggal Penyerahan</label>
                    <div class="col-sm-4">
                        <input type="date" name="handover_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Pilih Pegawai (Penerima)</label>
                    <div class="col-sm-9">
                        <select name="employee_id" class="form-control select2" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Keperluan</label>
                    <div class="col-sm-9">
                        <input type="text" name="keperluan" class="form-control" placeholder="Contoh: Pelayanan Ponkesdes/Pustu Desa Rumpuk" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Pilih Barang (Aset)</label>
                    <div class="col-sm-9">
                        <select name="asset_id" class="form-control select2" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->asset_code }} | {{ $asset->name }} | {{ $asset->category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Keterangan (Lokasi)</label>
                    <div class="col-sm-9">
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Ponkesdes Rumpuk">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Sumber Dana</label>
                    <div class="col-sm-9">
                        <input type="text" name="sumber_dana" class="form-control" placeholder="Contoh: JKN, BOK, dll">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Simpan & Lihat BAST</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
