<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/dashboard', 'GET');
try {
    $response = $kernel->handle($request);
    if (isset($response->exception) && $response->exception) {
        echo 'EXCEPTION: ' . $response->exception->getMessage();
    } else {
        echo 'NO EXCEPTION 500';
    }
} catch (\Exception $e) {
    echo 'CAUGHT: ' . $e->getMessage();
}
