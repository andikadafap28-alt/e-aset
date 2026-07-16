@extends('layouts.app')

@section('header_title', 'Audit Trail & Log Aktivitas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <a href="{{ route('laporan.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Audit Trail (Log Aktivitas)
            </h2>
            <p class="text-slate-500 text-sm mt-1 ml-8">Lacak setiap perubahan (Buat, Ubah, Hapus) pada data aset dan inventaris sistem.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Waktu</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Pengguna</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Modul</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">IP Address</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 text-sm font-medium text-slate-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="py-3 px-4 text-sm font-bold text-slate-800">{{ $log->user ? $log->user->name : 'System/Guest' }}</td>
                        <td class="py-3 px-4 text-sm font-semibold">
                            @if($log->action == 'created')
                                <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Dibuat</span>
                            @elseif($log->action == 'updated')
                                <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded-md">Diubah</span>
                            @elseif($log->action == 'deleted')
                                <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded-md">Dihapus</span>
                            @else
                                <span class="text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-700">
                            {{ str_replace('App\\Models\\', '', $log->model_type) }} <br>
                            <span class="text-slate-400">ID: {{ $log->model_id }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono text-slate-500">{{ $log->ip_address }}</td>
                        <td class="py-3 px-4 text-xs text-slate-600 w-1/3">
                            @if($log->action == 'updated')
                                <div class="max-h-24 overflow-y-auto">
                                    <strong>Data Lama:</strong> <br>
                                    <pre class="bg-slate-100 p-1 rounded text-[10px] mt-1">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                    <strong class="mt-2 block">Data Baru:</strong>
                                    <pre class="bg-slate-100 p-1 rounded text-[10px] mt-1">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @elseif($log->action == 'deleted')
                                <div class="max-h-16 overflow-y-auto">
                                    <strong>Data Dihapus:</strong> <br>
                                    <pre class="bg-slate-100 p-1 rounded text-[10px] mt-1">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @else
                                <span class="text-slate-400 italic">Lihat ID {{ $log->model_id }} di Modul</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada catatan aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
