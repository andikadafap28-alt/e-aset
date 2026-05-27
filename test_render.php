<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$asset = App\Models\Asset::find(14);
try {
    echo view('aset.show', compact('asset'))->render();
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n" . $e->getFile() . " : " . $e->getLine();
}
