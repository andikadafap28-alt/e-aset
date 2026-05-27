<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 11px; }
        .meta-info { margin-bottom: 20px; font-size: 11px; }
        .meta-info table { width: 100%; }
        .meta-info td { vertical-align: top; padding: 2px 0; }
        .section-title { background-color: #f8fafc; padding: 8px 12px; font-size: 13px; font-weight: bold; border: 1px solid #ddd; border-bottom: none; margin-top: 25px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        table.data-table td.center { text-align: center; }
        table.data-table td.right { text-align: right; }
        .footer-note { text-align: right; font-size: 10px; color: #666; margin-top: 30px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Aplikasi Manajemen Aset Puskesmas Mantup</p>
    </div>

    <div class="meta-info">
        <table style="width: 100%">
            <tr>
                <td style="width: 130px;"><strong>Tanggal Cetak</strong></td>
                <td>: {{ $date }}</td>
            </tr>
            <tr>
                <td><strong>Periode Bulan</strong></td>
                <td>: {{ $bulanAwal }} s/d {{ $bulanAkhir }}</td>
            </tr>
        </table>
    </div>

    <!-- 1. ASET MASUK -->
    <div class="section-title">1. Daftar Aset Masuk / Baru</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Aset</th>
                <th style="width: 25%;">Nama Aset</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 10%;">Tgl Masuk</th>
                <th style="width: 10%;">Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asetMasuk as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $item->asset_code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ is_object($item->category) ? $item->category->nama_kategori : ($item->getAttribute('category') ?: '-') }}</td>
                <td>{{ $item->location }}</td>
                <td class="center">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                <td class="right">{{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="center" style="padding: 15px;">Tidak ada aset baru pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. PEMELIHARAAN -->
    <div class="section-title">2. Riwayat Pemeliharaan & Kalibrasi (Selesai)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Aset</th>
                <th style="width: 25%;">Nama Aset</th>
                <th style="width: 15%;">Tgl Pelaksanaan</th>
                <th style="width: 25%;">Jenis Pemeliharaan</th>
                <th style="width: 15%;">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemeliharaan as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $item->asset ? $item->asset->asset_code : '-' }}</td>
                <td>{{ $item->asset ? $item->asset->name : '-' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->format('d/m/Y') }}</td>
                <td>{{ $item->jenis_pemeliharaan }}</td>
                <td class="right">{{ number_format($item->biaya, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="center" style="padding: 15px;">Tidak ada riwayat pemeliharaan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. PENGHAPUSAN -->
    <div class="section-title">3. Riwayat Penghapusan (Disposal)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Aset</th>
                <th style="width: 25%;">Nama Aset</th>
                <th style="width: 15%;">Tgl Dihapus</th>
                <th style="width: 25%;">Alasan</th>
                <th style="width: 15%;">Nilai Jual/Sisa (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penghapusan as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $item->asset ? $item->asset->asset_code : '-' }}</td>
                <td>{{ $item->asset ? $item->asset->name : '-' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_penghapusan)->format('d/m/Y') }}</td>
                <td>{{ $item->alasan }}</td>
                <td class="right">{{ number_format($item->nilai_sisa, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="center" style="padding: 15px;">Tidak ada riwayat penghapusan aset pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        <p>Dokumen ini dihasilkan secara otomatis oleh sistem e-Aset. Akhir dari laporan.</p>
    </div>

</body>
</html>
