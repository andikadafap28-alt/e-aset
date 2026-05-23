<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@raksa.com'],
            ['name' => 'Admin Logistik', 'password' => Hash::make('password'), 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'kepala@raksa.com'],
            ['name' => 'Kepala Puskesmas', 'password' => Hash::make('password'), 'role' => 'kepala']
        );
        User::updateOrCreate(
            ['email' => 'user@raksa.com'],
            ['name' => 'User Biasa', 'password' => Hash::make('password'), 'role' => 'user']
        );
    }
}
