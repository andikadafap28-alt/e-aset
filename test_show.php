<?php
try {
    $asset = \App\Models\Asset::findOrFail(43);
    echo view('aset.show', compact('asset'))->render();
    echo "OK\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
}
