@extends('layouts.app')

@section('header_title', 'Detail Barang - ' . $nama_kategori)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="/{{ $kategori_besar }}/items" class="text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ $item->nama_barang }}</h2>
                <p class="text-sm font-medium text-slate-500">Kode: {{ $item->kode_barang ?? '-' }} | Sisa Stok: <span class="font-bold text-emerald-600">{{ $item->stok_sekarang }} {{ $item->satuan }}</span></p>
            </div>
        </div>
        
        @if(in_array($kategori_besar, ['pengadaan', 'bantuan_sarpras']))
        @php
            $terdaftar = \App\Models\Asset::where('pengadaan_id', $item->id)->count();
            $sisa = $item->stok_sekarang - $terdaftar;
        @endphp
        <div>
            @if($sisa > 0)
            <a href="{{ route('aset.create', ['pengadaan_id' => $item->id, 'nama' => $item->nama_barang, 'kategori' => $item->kategori, 'tahun' => $item->tahun_pengadaan, 'jumlah' => $sisa]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Daftarkan Aset (Sisa: {{ $sisa }})
            </a>
            @else
            <span class="bg-slate-100 text-slate-500 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 border border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Semua Aset Telah Didaftarkan
            </span>
            @endif
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-xl mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Kartu Stok (Riwayat Transaksi)</h3>
            <div class="flex items-center gap-2">
                <a href="/{{ $kategori_besar }}/{{ $item->id }}/kartu-stok/pdf" target="_blank" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>
                <button type="button" onclick="document.getElementById('aiScanInput').click()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Scan AI
                </button>
            </div>
            <input type="file" id="aiScanInput" accept="image/*" capture="environment" class="hidden" onchange="handleAiScan(this)">
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                    <th class="py-3 px-5">Tanggal</th>
                    <th class="py-3 px-5">Keterangan</th>
                    <th class="py-3 px-5 text-center">Masuk</th>
                    <th class="py-3 px-5 text-center">Keluar</th>
                    <th class="py-3 px-5 text-center">Sisa</th>
                    <th class="py-3 px-5">Status</th>
                    <th class="py-3 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($item->transactions as $tx)
                <tr class="hover:bg-slate-50/50">
                    <td class="py-3 px-5 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                    <td class="py-3 px-5 text-slate-600 max-w-xs truncate" title="{{ $tx->keterangan }}">
                        {{ $tx->keterangan ?: '-' }}
                        @if($tx->expired_date)
                            <br><span class="text-xs font-semibold text-rose-500">ED: {{ \Carbon\Carbon::parse($tx->expired_date)->translatedFormat('d M Y') }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-center font-bold text-emerald-600">{{ $tx->jenis_transaksi == 'masuk' ? $tx->jumlah : '-' }}</td>
                    <td class="py-3 px-5 text-center font-bold text-rose-600">{{ $tx->jenis_transaksi == 'keluar' ? $tx->jumlah : '-' }}</td>
                    <td class="py-3 px-5 text-center font-bold text-indigo-600">{{ $tx->running_balance }}</td>
                    <td class="py-3 px-5">
                        @if($tx->status_hutang)
                            <span class="text-rose-600 font-medium text-xs">Hutang</span>
                        @else
                            <span class="text-slate-500 text-xs">Lunas/SPJ</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right">
                        <form action="/{{ $kategori_besar }}/transaksi/{{ $tx->id }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Stok akan dikembalikan otomatis.');" class="flex items-center justify-end gap-3">
                            <a href="/{{ $kategori_besar }}/transaksi/{{ $tx->id }}/edit" class="text-indigo-500 hover:text-indigo-700 font-medium text-xs">Edit</a>
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-medium text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-slate-500">Belum ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Dokumen Pengadaan</h3>
            <button type="button" onclick="openProcurementModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload Dokumen
            </button>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                    <th class="py-3 px-5">Tanggal</th>
                    <th class="py-3 px-5">Jenis Dokumen</th>
                    <th class="py-3 px-5">Penyedia</th>
                    <th class="py-3 px-5">Nama File</th>
                    <th class="py-3 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($item->procurementFiles as $doc)
                <tr class="hover:bg-slate-50/50">
                    <td class="py-3 px-5">{{ $doc->tanggal_dokumen }}</td>
                    <td class="py-3 px-5"><span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium">{{ $doc->jenis_dokumen }}</span></td>
                    <td class="py-3 px-5 text-slate-600 font-medium"><span class="uppercase">{{ str_replace('_', ' ', $doc->nama_penyedia ?? '-') }}</span></td>
                    <td class="py-3 px-5 text-slate-600">
                        <span title="{{ $doc->file_name }}">
                            {{ \Illuminate\Support\Str::limit($doc->file_name, 35, '...') }}
                        </span>
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex items-center justify-end gap-3">
                            @if($doc->path_gdrive)
                            <a href="/procurement-file/{{ $doc->id }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs flex items-center gap-1">
                                Lihat
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                            @else
                            <span class="text-slate-400 text-xs">-</span>
                            @endif
                            
                            <form action="{{ route('procurement.destroy-file', $doc->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus dokumen ini secara permanen? File di Google Drive juga akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-medium text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-slate-500">Belum ada dokumen pengadaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Upload Dokumen Pengadaan -->
<div id="uploadProcurementModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Upload Dokumen Pengadaan</h3>
            <button type="button" onclick="closeProcurementModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="procurementForm" action="/{{ $kategori_besar }}/{{ $item->id }}/procurement-files" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Step 1: Upload & Scan -->
            <div id="procurementStep1">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Dokumen</label>
                        <select name="jenis_dokumen" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" onchange="toggleJenisKustom(this.value)">
                            <option value="DPP">DPP</option>
                            <option value="SP">SP</option>
                            <option value="BAST">BAST</option>
                            <option value="Invoice">Invoice</option>
                            <option value="Surat Pernyataan">Surat Pernyataan</option>
                            <option value="Input Sendiri">Input Sendiri</option>
                        </select>
                    </div>
                    <div id="jenisKustomContainer" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Dokumen Kustom</label>
                        <input type="text" name="jenis_dokumen_kustom" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Surat Penawaran">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">File Dokumen (PDF)</label>
                        <input type="file" id="file_dokumen_input" name="file_dokumen" accept=".pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="closeProcurementModal()" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="button" onclick="scanProcurement()" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200">Scan & Lanjutkan</button>
                </div>
            </div>

            <!-- Step 2: Konfirmasi Hasil Scan -->
            <div id="procurementStep2" class="hidden">
                <div class="p-6 space-y-4">
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-3 rounded text-sm text-indigo-700 mb-2">
                        Silakan periksa hasil bacaan otomatis (AI) berikut. Anda dapat mengubahnya jika ada kesalahan.
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Penyedia <span class="text-rose-500">*</span></label>
                        <input type="text" id="input_nama_penyedia" name="nama_penyedia" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: PT Intisumber Hasil Sempurna" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Dokumen</label>
                        <input type="date" id="input_tanggal_dokumen" name="tanggal_dokumen" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                    <button type="button" onclick="backToStep1()" class="text-sm font-medium text-slate-500 hover:text-slate-800">Kembali</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm shadow-emerald-200">Upload Sekarang</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function openProcurementModal() {
        document.getElementById('uploadProcurementModal').classList.remove('hidden');
        document.getElementById('procurementStep1').classList.remove('hidden');
        document.getElementById('procurementStep2').classList.add('hidden');
        document.getElementById('procurementForm').reset();
        document.getElementById('jenisKustomContainer').classList.add('hidden');
    }

    function closeProcurementModal() {
        document.getElementById('uploadProcurementModal').classList.add('hidden');
    }

    function backToStep1() {
        document.getElementById('procurementStep2').classList.add('hidden');
        document.getElementById('procurementStep1').classList.remove('hidden');
    }

    function toggleJenisKustom(val) {
        if(val === 'Input Sendiri') {
            document.getElementById('jenisKustomContainer').classList.remove('hidden');
        } else {
            document.getElementById('jenisKustomContainer').classList.add('hidden');
        }
    }

    function scanProcurement() {
        const fileInput = document.getElementById('file_dokumen_input');
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih file PDF terlebih dahulu', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('file_dokumen', fileInput.files[0]);
        formData.append('jenis_dokumen', document.querySelector('select[name="jenis_dokumen"]').value);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'Membaca PDF...',
            text: 'Mencari tanggal dan nama penyedia',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/{{ $kategori_besar }}/{{ $item->id }}/scan-procurement', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Swal.close();
                document.getElementById('input_tanggal_dokumen').value = res.data.tanggal || '';
                document.getElementById('input_nama_penyedia').value = res.data.nama_penyedia || '';
                
                document.getElementById('procurementStep1').classList.add('hidden');
                document.getElementById('procurementStep2').classList.remove('hidden');
            } else {
                Swal.fire('Gagal Scan', res.message || 'Tidak dapat membaca PDF.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
        });
    }
</script>

<!-- Modal Konfirmasi AI -->
<div id="aiConfirmModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Konfirmasi Hasil Scan AI
            </h3>
            <button type="button" onclick="closeAiModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="/{{ $kategori_besar }}/transaksi/{{ $item->id }}/ai-store" method="POST" id="aiConfirmForm" class="flex flex-col overflow-hidden">
            @csrf
            <div class="p-6 overflow-y-auto bg-white">
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r-lg text-sm">
                    <strong>Penting:</strong> AI mungkin salah membaca angka atau huruf karena kualitas foto/tulisan. Harap <strong>periksa kembali</strong> setiap baris di bawah ini dan edit jika perlu sebelum menyimpan.
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                            <th class="py-2 px-3">Tanggal</th>
                            <th class="py-2 px-3">Jenis (masuk/keluar)</th>
                            <th class="py-2 px-3">Jumlah</th>
                            <th class="py-2 px-3">Keterangan</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="aiTableBody" class="text-sm divide-y divide-slate-100">
                        <!-- Rows injected by JS -->
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button type="button" onclick="closeAiModal()" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200">Simpan Rincian ke Database</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let aiRowIndex = 0;

    function handleAiScan(input) {
        if (!input.files || input.files.length === 0) return;
        
        const file = input.files[0];
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'Menganalisis Gambar...',
            html: 'AI sedang membaca kartu stok Anda. Proses ini butuh waktu beberapa detik.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/{{ $kategori_besar }}/transaksi/{{ $item->id }}/ai-extract', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Swal.close();
                populateAiTable(res.data);
                document.getElementById('aiConfirmModal').classList.remove('hidden');
            } else {
                Swal.fire('Error', res.message || 'Gagal mengekstrak data dari AI.', 'error');
            }
            input.value = ''; // reset
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
            input.value = '';
        });
    }

    function populateAiTable(data) {
        const tbody = document.getElementById('aiTableBody');
        tbody.innerHTML = '';
        aiRowIndex = 0;

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-rose-500">AI tidak menemukan baris transaksi yang valid.</td></tr>';
            return;
        }

        data.forEach(row => {
            addAiRow(row.tanggal || '', row.jenis_transaksi || 'masuk', row.jumlah || 0, row.keterangan || '');
        });
    }

    function addAiRow(tanggal, jenis, jumlah, keterangan) {
        const tbody = document.getElementById('aiTableBody');
        const tr = document.createElement('tr');
        tr.id = `ai-row-${aiRowIndex}`;
        
        const safeJenis = jenis.toLowerCase().includes('keluar') ? 'keluar' : 'masuk';

        tr.innerHTML = `
            <td class="py-2 px-3">
                <input type="date" name="transactions[${aiRowIndex}][tanggal]" value="${tanggal}" class="w-full text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="py-2 px-3">
                <select name="transactions[${aiRowIndex}][jenis_transaksi]" class="w-full text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="masuk" ${safeJenis === 'masuk' ? 'selected' : ''}>Masuk</option>
                    <option value="keluar" ${safeJenis === 'keluar' ? 'selected' : ''}>Keluar</option>
                </select>
            </td>
            <td class="py-2 px-3">
                <input type="number" name="transactions[${aiRowIndex}][jumlah]" value="${jumlah}" min="1" class="w-full text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="py-2 px-3">
                <input type="text" name="transactions[${aiRowIndex}][keterangan]" value="${keterangan}" class="w-full text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </td>
            <td class="py-2 px-3 text-center">
                <button type="button" onclick="document.getElementById('ai-row-${aiRowIndex}').remove()" class="text-rose-500 hover:text-rose-700">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        aiRowIndex++;
    }

    function closeAiModal() {
        document.getElementById('aiConfirmModal').classList.add('hidden');
    }
</script>
@endsection