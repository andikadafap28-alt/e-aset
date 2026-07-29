<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateKibExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('aset.exports.template_kib');
    }
}
