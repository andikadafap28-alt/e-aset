<table>
    <thead>
        <tr style="background-color: #1e293b; color: #ffffff;">
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 1 (Akun)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 2 (Kelompok)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 3 (Jenis)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 4 (Objek)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 5 (Rincian)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 6 (Sub Rincian)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Kode 7 (Sub-sub)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Nama Barang</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">NIBAR (Kode Lama)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Nomor Register / NIBAR Baru</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Spesifikasi Nama Barang</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Spesifikasi Lainnya / Bahan</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Merek / Tipe</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Lokasi / Ruangan</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: right;">Jumlah</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Satuan</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: right;">Harga Satuan Perolehan (Rp)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: right;">Nilai Perolehan (Rp)</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Cara Perolehan</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: center;">Tanggal / Tahun</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Unit Kerja / SKPD</th>
            <th style="font-weight: bold; background-color: #1e293b; color: #ffffff;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
@if($kib == 'A')
        <tr>
            <td>1</td>
            <td>3</td>
            <td>1</td>
            <td>01</td>
            <td>01</td>
            <td>01</td>
            <td>001</td>
            <td>Tanah Bangunan Gedung Puskesmas</td>
            <td style="text-align: left;">1.3.01.01.01.001.000001.2010.001</td>
            <td style="text-align: left;">1.3.01.01.01.001.000001.2010.001</td>
            <td>Tanah Bangunan Kesehatan</td>
            <td>Sertifikat Hak Pakai No. 12</td>
            <td>-</td>
            <td>Puskesmas Mantup</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Bidang</td>
            <td style="text-align: right;">450000000</td>
            <td style="text-align: right;">450000000</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2010</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Luas 1.500 m2</td>
        </tr>
@elseif($kib == 'C')
        <tr>
            <td>1</td>
            <td>3</td>
            <td>3</td>
            <td>01</td>
            <td>01</td>
            <td>01</td>
            <td>001</td>
            <td>Gedung Kantor Puskesmas Induk</td>
            <td style="text-align: left;">1.3.03.01.01.001.000001.2015.001</td>
            <td style="text-align: left;">1.3.03.01.01.001.000001.2015.001</td>
            <td>Bangunan Gedung Permanen</td>
            <td>Beton Bertulang</td>
            <td>2 Lantai</td>
            <td>Puskesmas Mantup</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Unit</td>
            <td style="text-align: right;">850000000</td>
            <td style="text-align: right;">850000000</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2015</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Kondisi Baik</td>
        </tr>
@elseif($kib == 'D')
        <tr>
            <td>1</td>
            <td>3</td>
            <td>4</td>
            <td>01</td>
            <td>01</td>
            <td>01</td>
            <td>001</td>
            <td>Instalasi Pengolahan Air Limbah (IPAL)</td>
            <td style="text-align: left;">1.3.04.01.01.001.000001.2020.001</td>
            <td style="text-align: left;">1.3.04.01.01.001.000001.2020.001</td>
            <td>Jaringan IPAL Medis</td>
            <td>Pipa PVC & Bak Beton</td>
            <td>Kapasitas 10 m3/hari</td>
            <td>Area IPAL Puskesmas</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Unit</td>
            <td style="text-align: right;">125000000</td>
            <td style="text-align: right;">125000000</td>
            <td>Pengadaan DAK</td>
            <td style="text-align: center;">2020</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Fungsi Normal</td>
        </tr>
@elseif($kib == 'E')
        <tr>
            <td>1</td>
            <td>3</td>
            <td>5</td>
            <td>01</td>
            <td>01</td>
            <td>01</td>
            <td>001</td>
            <td>Buku Pedoman Pelayanan Klinis</td>
            <td style="text-align: left;">1.3.05.01.01.001.000001.2021.001</td>
            <td style="text-align: left;">1.3.05.01.01.001.000001.2021.001</td>
            <td>Buku Perpustakaan / Referensi</td>
            <td>Kertas Hardcover</td>
            <td>Kemenkes RI</td>
            <td>Ruang Tata Usaha</td>
            <td style="text-align: right;">10</td>
            <td style="text-align: center;">Eksemplar</td>
            <td style="text-align: right;">150000</td>
            <td style="text-align: right;">1500000</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2021</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Koleksi Perpustakaan</td>
        </tr>
@else
        {{-- Default KIB B (Peralatan dan Mesin) - Format Resmi Sesuai File BMD Pengguna --}}
        <tr>
            <td>1</td>
            <td>3</td>
            <td>2</td>
            <td>10</td>
            <td>01</td>
            <td>02</td>
            <td>007</td>
            <td>Lap Top</td>
            <td style="text-align: left;">1.3.02.10.01.002.000007.19910206</td>
            <td style="text-align: left;">1.3.02.10.01.002.000007.2023.001</td>
            <td>Lap Top</td>
            <td>Logam / Plastik</td>
            <td>Merk: HP NOTEBOOK 14-CF2019TU</td>
            <td>Desa/Mantup</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Buah</td>
            <td style="text-align: right;">6808080</td>
            <td style="text-align: right;">6808080</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2023</td>
            <td>PUSKESMAS MANTUP</td>
            <td>(DPA/B-APBD) Pengadaan Komputer TU</td>
        </tr>
        <tr>
            <td>1</td>
            <td>3</td>
            <td>2</td>
            <td>10</td>
            <td>01</td>
            <td>02</td>
            <td>001</td>
            <td>Personal Computer (PC)</td>
            <td style="text-align: left;">1.3.02.10.01.002.000001.2022.001</td>
            <td style="text-align: left;">1.3.02.10.01.002.000001.2022.001</td>
            <td>PC All In One</td>
            <td>Besi / Plastik</td>
            <td>Merk: Lenovo V50a Core i5</td>
            <td>Ruang Pendaftaran</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Unit</td>
            <td style="text-align: right;">11250000</td>
            <td style="text-align: right;">11250000</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2022</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Komputer Loket Pendaftaran</td>
        </tr>
        <tr>
            <td>1</td>
            <td>3</td>
            <td>2</td>
            <td>07</td>
            <td>01</td>
            <td>01</td>
            <td>002</td>
            <td>Tensimeter Digital</td>
            <td style="text-align: left;">1.3.02.07.01.001.000002.2023.001</td>
            <td style="text-align: left;">1.3.02.07.01.001.000002.2023.001</td>
            <td>Alat Kesehatan Pemeriksaan</td>
            <td>Plastik / Manset</td>
            <td>Merk: Omron HEM-7120</td>
            <td>Ruang Poli Dewasa</td>
            <td style="text-align: right;">1</td>
            <td style="text-align: center;">Buah</td>
            <td style="text-align: right;">850000</td>
            <td style="text-align: right;">850000</td>
            <td>Pengadaan APBD</td>
            <td style="text-align: center;">2023</td>
            <td>PUSKESMAS MANTUP</td>
            <td>Pelayanan Poli Dewasa</td>
        </tr>
@endif
    </tbody>
</table>
