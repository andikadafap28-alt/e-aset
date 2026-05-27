<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAKSA - Puskesmas Mantup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @yield('head')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-indigo-100 flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-50">
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold text-white shadow-sm">
                    R
                </div>
                <div>
                    <h1 class="font-bold text-white tracking-tight leading-tight">RAKSA</h1>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Puskesmas Mantup</p>


