<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $c = app(\App\Http\Controllers\DashboardController::class);
    $req = Illuminate\Http\Request::create('/dashboard', 'GET');
    $res = $c->index($req);
    echo $res->render();
    echo "\nOK\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}
