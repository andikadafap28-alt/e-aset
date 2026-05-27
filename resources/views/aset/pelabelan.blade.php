@extends('layouts.app')

@section('header_title', 'Pelabelan Aset')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Pelabelan Aset</h2>
        <p class="text-slate-500 text-sm mt-1">Pilih aset yang ingin dicetak label QR Code-nya</p>
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 border-b border-red-100 text-sm">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 border-b border-red-100 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('aset.pelabelan.print') }}" method="POST" target="_blank" id="form-cetak">
        @csrf
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="check-all" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 cursor-pointer">
                <label for="check-all" class="text-sm font-medium text-slate-700 cursor-pointer">Pilih Semua</label>
            </div>
            <button type="submit" id="btn-cetak" disabled class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Terpilih (<span id="count-selected">0</span>)
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">#</th>
                        <th class="p-4 font-semibold">Kode Aset</th>
                        <th class="p-4 font-semibold">Nama Aset</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="asset-checkbox rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="p-4 font-medium text-slate-800">{{ $asset->asset_code }}</td>
                            <td class="p-4 text-slate-600">{{ $asset->name }}</td>
                            <td class="p-4 text-slate-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $asset->category }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600">{{ $asset->location }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                Belum ada data aset untuk dilabeli.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        const btnCetak = document.getElementById('btn-cetak');
        const countSpan = document.getElementById('count-selected');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
            countSpan.textContent = checkedCount;
            
            if (checkedCount > 0) {
                btnCetak.removeAttribute('disabled');
            } else {
                btnCetak.setAttribute('disabled', 'disabled');
            }
            
            // Update check-all state based on individual checkboxes
            if (checkboxes.length > 0) {
                checkAll.checked = checkedCount === checkboxes.length;
            }
        }

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateButtonState();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });
    });
</script>
@endsection
