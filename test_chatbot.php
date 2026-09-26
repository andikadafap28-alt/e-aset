<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = new \App\Services\ChatbotService();
    // Simulate user asking for format
    $reply = $service->processMessage('123456', 'Format dpp', 'telegram');
    echo "SUCCESS:\n" . $reply;
} catch (\Throwable $e) {
    echo "ERROR:\n" . $e->getMessage();
}
