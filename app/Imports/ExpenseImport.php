<?php

namespace App\Imports;

use App\ExpenseRb;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ExpenseImport implements ToModel, WithHeadingRow, WithBatchInserts
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
        $budget_no                 = isset($row['budget_no']) ? $row['budget_no'] : null;
        $group                     = isset($row['group']) ? $row['group'] : null;
        $line_or_dept              = isset($row['line_or_dept']) ? $row['line_or_dept'] : null;
        $profit_center             = isset($row['profit_center']) ? $row['profit_center'] : null;
        $profit_center_code        = isset($row['profit_center_code']) ? $row['profit_center_code'] : null;
        $cost_center               = isset($row['cost_center']) ? $row['cost_center'] : null;
        $account_code              = isset($row['account_code']) ? $row['account_code'] : null;
        $project_name              = isset($row['project_name']) ? $row['project_name'] : null;
        $equipment_name            = isset($row['equipment_name']) ? $row['equipment_name'] : null;
        $importdomestic            = isset($row['importdomestic']) ? $row['importdomestic'] : null;
        $qty                       = isset($row['qty']) ? $row['qty'] : null;
        $curr                      = isset($row['curr']) ? $row['curr'] : null;
        $price_per_qty             = isset($row['price_per_qty']) ? $row['price_per_qty'] : null;
        $exchange_rate             = isset($row['exchange_rate']) ? $row['exchange_rate'] : null;
        $budget_before_cr          = isset($row['budget_before_cr']) ? $row['budget_before_cr'] : null;
        $cr                        = isset($row['cr']) ? $row['cr'] : null;
        $budget_after_cr           = isset($row['budget_after_cr']) ? $row['budget_after_cr'] : null;
        $po                        = isset($row['po']) ? $row['po'] : null;
        $gr                        = isset($row['gr']) ? $row['gr'] : null;
        $sop                       = isset($row['sop']) ? $row['sop'] : null;
        $first_down_payment_term   = isset($row['first_down_payment_term']) ? $row['first_down_payment_term'] : null;
        $first_down_payment_amount = isset($row['first_down_payment_amount']) ? $row['first_down_payment_amount'] : null;
        $final_payment_term        = isset($row['final_payment_term']) ? $row['final_payment_term'] : null;
        $final_payment_amount      = isset($row['final_payment_amount']) ? $row['final_payment_amount'] : null;
        $apr                       = isset($row['apr_22']) ? $row['apr_22'] : null;
        $may                       = isset($row['may_22']) ? $row['may_22'] : null;
        $jun                       = isset($row['jun_22']) ? $row['jun_22'] : null;
        $jul                       = isset($row['jul_22']) ? $row['jul_22'] : null;
        $aug                       = isset($row['aug_22']) ? $row['aug_22'] : null;
        $sep                       = isset($row['sep_22']) ? $row['sep_22'] : null;
        $oct                       = isset($row['oct_22']) ? $row['oct_22'] : null;
        $nov                       = isset($row['nov_22']) ? $row['nov_22'] : null;
        $dec                       = isset($row['dec_22']) ? $row['dec_22'] : null;
        $jan                       = isset($row['jan_22']) ? $row['jan_22'] : null;
        $feb                       = isset($row['feb_22']) ? $row['feb_22'] : null;
        $mar                       = isset($row['mar_22']) ? $row['mar_22'] : null;
        $checking                  = isset($row['checking']) ? $row['checking'] : null;
        $gl_account                = isset($row['gl_account']) ? $row['gl_account'] : null;
        $gl_account_link           = isset($row['gl_account_link']) ? $row['gl_account_link'] : null;
        $gl_group                  = isset($row['gl_group']) ? $row['gl_group'] : null;
        $code                      = isset($row['code']) ? $row['code'] : null;

        if($budget_no) {
            $cek = ExpenseRb::where([
                'budget_no' => $budget_no,
                'group' => $group,
                'line' => $line_or_dept
            ])->first();

            if ($cek) {
                $expenserb = ExpenseRb::where([
                    'budget_no' => $budget_no,
                    'group' => $group,
                    'line' => $line_or_dept
                ])->update([
                    'profit_center'          => $profit_center,
                    'profit_center_code'     => $profit_center_code,
                    'cost_center'            => $cost_center,
                    'acc_code'               => $account_code,
                    'project_name'           => $project_name,
                    'equipment_name'         => $equipment_name,
                    'import_domestic'        => $importdomestic,
                    'qty'                    => $qty,
                    'cur'                    => $curr,
                    'price_per_qty'          => $price_per_qty,
                    'exchange_rate'          => $exchange_rate,
                    'budget_before'          => $budget_before_cr,
                    'cr'                     => $cr,
                    'budgt_aft_cr'           => $budget_after_cr,
                    'po'                     => $po,
                    'gr'                     => $gr,
                    'sop'                    => $sop,
                    'first_dopayment_term'   => $first_down_payment_term,
                    'first_dopayment_amount' => $first_down_payment_amount,
                    'final_payment_term'     => $final_payment_term,
                    'final_payment_amount'   => $final_payment_amount,
                    'april'                  => $apr,
                    'mei'                    => $may,
                    'juni'                   => $jun,
                    'juli'                   => $jul,
                    'agustus'                => $aug,
                    'september'              => $sep,
                    'oktober'                => $oct,
                    'november'               => $nov,
                    'december'               => $dec,
                    'januari'                => $jan,
                    'februari'               => $feb,
                    'maret'                  => $mar,
                    'checking'               => $checking
                ]);
            } else {
                $expenserb                         = new ExpenseRb;
                $expenserb->budget_no              = $budget_no;
                $expenserb->group                  = $group;
                $expenserb->code                   = $code;
                $expenserb->line                   = $line_or_dept;
                $expenserb->profit_center          = $profit_center;
                $expenserb->profit_center_code     = $profit_center_code;
                $expenserb->cost_center            = $cost_center;
                $expenserb->acc_code               = $account_code;
                $expenserb->project_name           = $project_name;
                $expenserb->equipment_name         = $equipment_name;
                $expenserb->import_domestic        = $importdomestic;
                $expenserb->qty                    = $qty;
                $expenserb->cur                    = $curr;
                $expenserb->price_per_qty          = $price_per_qty;
                $expenserb->exchange_rate          = $exchange_rate;
                $expenserb->budget_before          = $budget_before_cr;
                $expenserb->cr                     = $cr;
                $expenserb->budgt_aft_cr           = $budget_after_cr;
                $expenserb->po                     = $po;
                $expenserb->gr                     = $gr;
                $expenserb->sop                    = $sop;
                $expenserb->first_dopayment_term   = $first_down_payment_term;
                $expenserb->first_dopayment_amount = $first_down_payment_amount;
                $expenserb->final_payment_term     = $final_payment_term;
                $expenserb->final_payment_amount   = $final_payment_amount;
                $expenserb->april                  = $apr;
                $expenserb->mei                    = $may;
                $expenserb->juni                   = $jun;
                $expenserb->juli                   = $jul;
                $expenserb->agustus                = $aug;
                $expenserb->september              = $sep;
                $expenserb->oktober                = $oct;
                $expenserb->november               = $nov;
                $expenserb->december               = $dec;
                $expenserb->januari                = $jan;
                $expenserb->februari               = $feb;
                $expenserb->maret                  = $mar;
                $expenserb->checking               = $checking;
                $expenserb->save();

            }
        }
    }
}
