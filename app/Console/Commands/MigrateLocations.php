<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateLocations extends Command
{
    protected $signature = 'app:migrate-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate location string data from assets to rooms table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locations = \Illuminate\Support\Facades\DB::table('assets')
            ->select('location')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->pluck('location');

        $count = 0;
        foreach ($locations as $loc) {
            $room = \App\Models\Room::firstOrCreate([
                'name' => $loc
            ], [
                'type' => 'Ruangan'
            ]);
            
            \Illuminate\Support\Facades\DB::table('assets')
                ->where('location', $loc)
                ->update(['room_id' => $room->id]);
            
            $count++;
        }
        
        $this->info("Migrated {$count} unique locations to rooms table.");
    }
}
