<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jadwalkan pengecekan notifikasi harian pada pukul 08:00
Schedule::command('app:check-low-stock')->dailyAt('08:00');
Schedule::command('app:check-loan-due')->dailyAt('08:00');
Schedule::command('app:check-maintenance-due')->dailyAt('08:00');

// Mencegah Supabase menjadi paused dengan melakukan ping setiap 4 jam
Schedule::command('supabase:keep-alive')->everyFourHours();
