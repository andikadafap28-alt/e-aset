@extends('layouts.app')

@section('header_title', 'Peminjaman Aset')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Sistem Peminjaman Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola dan lacak peminjaman barang tetap/inventaris</p>
    </div>
    <button onclick="document.getElementById('modalTambahPeminjaman').classList.remove('hidden')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg shadow-sm font-medium transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Catat Peminjaman Baru
    </button>
</div>

<!-- Modal Tambah Peminjaman -->
<div id="modalTambahPeminjaman" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('aset.peminjaman.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Formulir Peminjaman Aset</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih aset dan isi detail peminjam.</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Aset</label>
                            <select name="asset_id" required class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">-- Pilih Aset --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->asset_code }} - {{ $asset->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Peminjam</label>
                            <input type="text" name="borrower_name" required class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Cth: Dr. Andi / Ruang Mawar">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Peminjam (Opsional)</label>
                            <input type="text" name="borrower_contact" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Nomor WA / Ekstensi">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pinjam</label>
                                <input type="date" name="loan_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Kembali</label>
                                <input type="date" name="expected_return_date" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Keperluan peminjaman..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" onclick="document.getElementById('modalTambahPeminjaman').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($loans as $loan)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $loan->asset->name ?? 'Aset Dihapus' }}</div>
                    <div class="text-sm text-gray-500">{{ $loan->asset->asset_code ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $loan->borrower_name }}</div>
                    <div class="text-sm text-gray-500">{{ $loan->borrower_contact }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $loan->loan_date->format('d M Y') }}
                    @if($loan->expected_return_date)
                        <br><span class="text-xs text-teal-500">Batas: {{ $loan->expected_return_date->format('d M Y') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($loan->approval_status == 'pending')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>
                    @elseif($loan->approval_status == 'rejected')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                    @else
                        @if($loan->status == 'dipinjam')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Dipinjam</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span><br>
                            <span class="text-xs text-gray-500">{{ $loan->actual_return_date ? $loan->actual_return_date->format('d M Y') : '' }}</span>
                        @endif
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    @if($loan->approval_status == 'pending' && auth()->user()?->role == 'kepala')
                        <form action="{{ route('aset.peminjaman.approve', $loan->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Setujui</button>
                        </form>
                        <form action="{{ route('aset.peminjaman.reject', $loan->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900">Tolak</button>
                        </form>
                    @endif

                    @if($loan->approval_status == 'approved')
                        <a href="{{ route('aset.peminjaman.bast', $loan->id) }}" class="text-emerald-600 hover:text-emerald-900 mr-2" title="Cetak BAST">Cetak BAST</a>
                    @endif

                    @if($loan->approval_status == 'approved' && $loan->status == 'dipinjam')
                        <button onclick="document.getElementById('modalKembali-{{ $loan->id }}').classList.remove('hidden')" class="text-teal-600 hover:text-indigo-900">Kembalikan</button>
                        
                        <!-- Modal Kembalikan -->
                        <div id="modalKembali-{{ $loan->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto text-left" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('aset.peminjaman.return', $loan->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengembalian Aset</h3>
                                            <p class="text-sm text-gray-500 mb-4">Konfirmasi pengembalian aset <strong>{{ $loan->asset->name ?? '-' }}</strong> dari <strong>{{ $loan->borrower_name }}</strong>.</p>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Aktual</label>
                                                <input type="date" name="actual_return_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-teal-500 focus:border-teal-500">
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 sm:ml-3 sm:w-auto sm:text-sm">Konfirmasi Pengembalian</button>
                                            <button type="button" onclick="document.getElementById('modalKembali-{{ $loan->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada data peminjaman aset.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

