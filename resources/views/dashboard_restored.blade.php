@extends('layouts.app')

@section('header_title', 'Dashboard RAKSA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-8 text-white shadow-lg shadow-indigo-600/20 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">Selamat datang di RAKSA</h1>
            <p class="text-indigo-100 max-w-xl">Respons Akurat Kelola Seluruh Aset. Sistem manajemen logistik terintegrasi untuk Puskesmas Mantup. Pantau pergerakan aset Anda secara real-time.</p>
        </div>
        <!-- Decorative bg -->
        <div class="absolute right-0 top-0 w-64 h-full bg-white opacity-5 transform skew-x-12 -mr-16"></div>
        <div class="absolute right-32 top-0 w-32 h-full bg-white opacity-5 transform skew-x-12"></div>
    </div>

    <!-- Quick Stats (Dynamic Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($kategoriList as $key => $kat)
        <div onclick="updateChart('{{ $key }}')" class="cursor-pointer bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group category-card" id="card-{{ $key }}">
            <!-- Decorative accent line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-{{ $kat['icon'] }}-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 transition-active"></div>
            
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $kat['label'] }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($kat['jenis'], 0, ',', '.') }} <span class="text-sm font-medium text-slate-500">Jenis</span></h3>
                </div>


