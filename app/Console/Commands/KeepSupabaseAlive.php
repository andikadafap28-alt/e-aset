<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

    #[Signature('supabase:keep-alive')]
    #[Description('Ping Supabase database to prevent it from pausing')]
    class KeepSupabaseAlive extends Command
    {
        /**
         * Execute the console command.
         */
        public function handle()
        {
            try {
                \Illuminate\Support\Facades\DB::select('SELECT 1');
                $this->info('Supabase pinged successfully.');
            } catch (\Exception $e) {
                $this->error('Failed to ping Supabase: ' . $e->getMessage());
            }
        }
    }
