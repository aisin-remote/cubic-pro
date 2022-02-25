<?php

namespace App\Exports;

use App\SalesRb;
use Maatwebsite\Excel\Files\NewExcelFile;

class RbExport extends NewExcelFile
{
   public function getFilename()
    {
        return 'AIIA-PNL';
    }
}
