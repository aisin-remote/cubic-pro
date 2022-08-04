<?php

namespace App\Imports;

use App\SalesRb;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SalesImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
     * Batch Size
     *
     * A batch size of 1000 will not be the most optimal situation for your import.
     * Play around with this number to find the sweet spot
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
    * @param array $row
    */
    public function model(array $row)
    {
        $acc_code      = isset($row['acc_code']) ? $row['acc_code'] : null;
        $acc_name      = isset($row['acc_name']) ? $row['acc_name'] : null;
        $group         = isset($row['group']) ? $row['group'] : null;
        $code          = isset($row['code']) ? $row['code'] : null;
        $apr           = isset($row['apr']) ? $row['apr'] : null;
        $may           = isset($row['may']) ? $row['may'] : null;
        $jun           = isset($row['jun']) ? $row['jun'] : null;
        $jul           = isset($row['jul']) ? $row['jul'] : null;
        $aug           = isset($row['aug']) ? $row['aug'] : null;
        $sept          = isset($row['sept']) ? $row['sept'] : null;
        $oct           = isset($row['oct']) ? $row['oct'] : null;
        $nov           = isset($row['nov']) ? $row['nov'] : null;
        $dec           = isset($row['dec']) ? $row['dec'] : null;
        $jan           = isset($row['jan']) ? $row['jan'] : null;
        $feb           = isset($row['feb']) ? $row['feb'] : null;
        $mar           = isset($row['mar']) ? $row['mar'] : null;
        $fy_2022_1st   = isset($row['fy_2022_1st']) ? $row['fy_2022_1st'] : null;
        $fy_2022_2nd   = isset($row['fy_2022_2nd']) ? $row['fy_2022_2nd'] : null;
        $fy_2022_total = isset($row['fy_2022_total']) ? $row['fy_2022_total']: null;

        $cek = SalesRb::where([
                'acc_name' => $acc_name,
                'group' => $group,
                'code' => $code
            ])->first();

        if ($cek) {
            $salesrb = SalesRb::where([
                'acc_name' => $acc_name,
                'group' => $group,
                'code' => $code
            ])->update([
                'april'     => $apr,
                'mei'       => $may,
                'juni'      => $jun,
                'juli'      => $jul,
                'agustus'   => $aug,
                'september' => $sept,
                'oktober'   => $oct,
                'november'  => $nov,
                'december'  => $dec,
                'januari'   => $jan,
                'februari'  => $feb,
                'maret'     => $mar,
                'fy_first'  => $fy_2022_1st,
                'fy_second' => $fy_2022_2nd,
                'fy_total'  => $fy_2022_total
            ]);
        } else {
            $salesrb            = new SalesRb;
            $salesrb->acc_code  = $acc_code;
            $salesrb->acc_name  = $acc_name;
            $salesrb->group     = $group;
            $salesrb->code      = $code;
            $salesrb->april     = $apr;
            $salesrb->mei       = $may;
            $salesrb->juni      = $jun;
            $salesrb->juli      = $jul;
            $salesrb->agustus   = $aug;
            $salesrb->september = $sept;
            $salesrb->oktober   = $oct;
            $salesrb->november  = $nov;
            $salesrb->december  = $dec;
            $salesrb->januari   = $jan;
            $salesrb->februari  = $feb;
            $salesrb->maret     = $mar;
            $salesrb->fy_first  = $fy_2022_1st;
            $salesrb->fy_second = $fy_2022_2nd;
            $salesrb->fy_total  = $fy_2022_total;
            $salesrb->save();
        }
    }
}
