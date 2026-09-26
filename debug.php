<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Force debug mode
putenv('APP_DEBUG=true');
config(['app.debug' => true]);

try {
    auth()->loginUsingId(4);
    $request = Illuminate\Http\Request::create('/dashboard', 'GET');
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 500) {
        echo "Error: \n";
        echo strip_tags($response->getContent());
    } else {
        echo "Success, status: " . $response->getStatusCode() . "\n";
    }
} catch (\Throwable $e) {
    echo "Exception Caught: \n";
    echo $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
