<?php

namespace App\Exports;

use App\bom;
use Maatwebsite\Excel\Concerns\FromCollection;

class BomsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return bom::all();
    }
}
