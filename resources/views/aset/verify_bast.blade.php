<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi BAST Otentik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white max-w-md w-full rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        
        <div class="bg-emerald-600 p-6 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Dokumen Valid</h1>
            <p class="text-emerald-100 mt-1 text-sm">Berita Acara Serah Terima Sah & Tercatat di Sistem RAKSA</p>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                
                <div class="border-b border-slate-100 pb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui Oleh</p>
                    <p class="font-bold text-slate-900 mt-1">{{ $loan->approver ? $loan->approver->name : 'Kepala Puskesmas' }}</p>
                    <p class="text-sm text-slate-500">Tanda Tangan Elektronik Asli</p>
                </div>

                <div class="border-b border-slate-100 pb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aset Dipinjam</p>
                    <p class="font-bold text-slate-900 mt-1">{{ $loan->asset->name }} ({{ $loan->asset->asset_code }})</p>
                </div>

                <div class="border-b border-slate-100 pb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Identitas Peminjam</p>
                    <p class="font-bold text-slate-900 mt-1">{{ $loan->borrower_name }}</p>
                    <p class="text-sm text-slate-500">{{ $loan->borrower_contact ?? '-' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Pinjam</p>
                        <p class="font-bold text-slate-900 mt-1">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo</p>
                        <p class="font-bold text-rose-600 mt-1">{{ $loan->expected_return_date ? \Carbon\Carbon::parse($loan->expected_return_date)->translatedFormat('d M Y') : '-' }}</p>
                    </div>
                </div>

            </div>
            
            <div class="mt-8 text-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs font-medium text-slate-500">Dikeluarkan pada:</p>
                <p class="text-sm font-bold text-slate-800">{{ $loan->updated_at->translatedFormat('d F Y - H:i:s') }}</p>
                <p class="text-xs text-slate-400 mt-2">Puskesmas Mantup - RAKSA System</p>
            </div>
        </div>
    </div>

</body>
</html>
