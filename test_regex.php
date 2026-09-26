<?php
$lines = [
    "Kode RUP: [Isi dengan angka 1 atau 2]",
    "Kode RUP: 1",
    "Kode RUP (Obat/BMHP/Jasa/dll): 1",
    "Kode RUP: : 1",
    "Kode RUP: :",
];

foreach ($lines as $line) {
    if (preg_match('/kode\s*rup\s*:\s*(.+)/i', $line, $matches) || preg_match('/kode\s*rup\s*(.+)/i', $line, $matches)) {
        $inputRup = strtolower(trim($matches[1]));
        $inputRupNumber = preg_replace('/[^0-9]/', '', $inputRup);
        echo "Line: $line\n";
        echo "  - inputRup: '$inputRup'\n";
        echo "  - inputRupNumber: '$inputRupNumber'\n";
        
        if (str_contains($inputRup, 'obat') || $inputRupNumber === '1' || str_contains($inputRup, '67261766')) {
            echo "  -> MATCHES OBAT (1)\n";
        } elseif (str_contains($inputRup, 'bmhp') || str_contains($inputRup, 'bahan') || $inputRupNumber === '2' || str_contains($inputRup, '67261750')) {
            echo "  -> MATCHES BAHAN (2)\n";
        } else {
            echo "  -> FALLBACK to '$inputRup'\n";
        }
    }
}
