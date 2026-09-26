<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('name', 'adminmantup')->first();
echo "Password: " . $user->password . "\n";
echo "Attempt: " . (\Illuminate\Support\Facades\Auth::attempt(['name' => 'adminmantup', 'password' => 'adminmantup123']) ? 'Success' : 'Failed') . "\n";
