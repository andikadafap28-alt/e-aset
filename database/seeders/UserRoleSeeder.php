<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['name' => 'adminmantup'],
            [
                'email' => 'admin@pkmmantup.local',
                'password' => Hash::make('pkmmantup1'),
                'role' => 'admin',
            ]
        );

        // Manajemen
        User::updateOrCreate(
            ['name' => 'manajemenmantup'],
            [
                'email' => 'manajemen@pkmmantup.local',
                'password' => Hash::make('mantup135'),
                'role' => 'kepala',
            ]
        );

        // User (Desa Tugu)
        User::updateOrCreate(
            ['name' => 'usertugu'],
            [
                'email' => 'usertugu@pkmmantup.local',
                'password' => Hash::make('tugu135'),
                'role' => 'user',
            ]
        );
    }
}
