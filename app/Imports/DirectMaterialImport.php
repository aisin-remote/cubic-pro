<?php

namespace App\Imports;

use App\SalesRb;
use App\DmaterialRb;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DirectMaterialImport implements ToModel, WithHeadingRow, WithBatchInserts
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
        $acc_code  = isset($row['acc_code']) ? $row['acc_code'] : null;
        $acc_name  = isset($row['acc_name']) ? $row['acc_name'] : null;
        $group     = isset($row['group']) ? $row['group'] : null;
        $april     = isset($row['apr']) ? $row['apr'] : null;
        $mei       = isset($row['may']) ? $row['may'] : null;
        $juni      = isset($row['jun']) ? $row['jun'] : null;
        $juli      = isset($row['jul']) ? $row['jul'] : null;
        $agustus   = isset($row['aug']) ? $row['aug'] : null;
        $september = isset($row['sept']) ? $row['sept'] : null;
        $oktober   = isset($row['oct']) ? $row['oct'] : null;
        $november  = isset($row['nov']) ? $row['nov'] : null;
        $december  = isset($row['dec']) ? $row['dec'] : null;
        $januari   = isset($row['jan']) ? $row['jan'] : null;
        $februari  = isset($row['feb']) ? $row['feb'] : null;
        $maret     = isset($row['mar']) ? $row['mar'] : null;
        $fy_first  = isset($row['fy_2022_1st']) ? $row['fy_2022_1st'] : null;
        $fy_second = isset($row['fy_2022_2nd']) ? $row['fy_2022_2nd'] : null;
        $fy_total  = isset($row['fy_2022_total']) ? $row['fy_2022_total'] : null;

        $cek = DmaterialRb::where('acc_name', $acc_name)->where('group', $group)->first();

        if ($cek) {
            $materialrb = SalesRb::where('acc_name', $acc_name)->where('group', $group)
                ->update([
                    'april'     => $april,
                    'mei'       => $mei,
                    'juni'      => $juni,
                    'juli'      => $juli,
                    'agustus'   => $agustus,
                    'september' => $september,
                    'oktober'   => $oktober,
                    'november'  => $november,
                    'december'  => $december,
                    'januari'   => $januari,
                    'februari'  => $februari,
                    'maret'     => $maret,
                    'fy_first'  => $fy_first,
                    'fy_second' => $fy_second,
                    'fy_total'  => $fy_total,
                ]);
        } else {
            $materialrb = new DmaterialRb;
            $materialrb->acc_code  = $acc_code;
            $materialrb->acc_name  = $acc_name;
            $materialrb->group     = $group;
            $materialrb->april     = $april;
            $materialrb->mei       = $mei;
            $materialrb->juni      = $juni;
            $materialrb->juli      = $juli;
            $materialrb->agustus   = $agustus;
            $materialrb->september = $september;
            $materialrb->oktober   = $oktober;
            $materialrb->november  = $november;
            $materialrb->december  = $december;
            $materialrb->januari   = $januari;
            $materialrb->februari  = $februari;
            $materialrb->maret     = $maret;
            $materialrb->fy_first  = $fy_first;
            $materialrb->fy_second = $fy_second;
            $materialrb->fy_total  = $fy_total;
            $materialrb->save();
        }
    }
}
