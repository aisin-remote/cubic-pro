<?php

namespace App\Imports;
use DB;
use App\CapexRb;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CapexImport implements ToModel, WithHeadingRow, WithBatchInserts
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
        $dept                       = isset($row["dept"]) ? $row["dept"] : null; // dept
        $no                         = isset($row["no"]) ? $row["no"] : null; // no
        $budget_no                  = isset($row["budget_no"]) ? $row["budget_no"] : null; // budget_no
        $line_or_dept               = isset($row["line_or_dept"]) ? $row["line_or_dept"] : null; // line_or_dept
        $profit_center              = isset($row["profit_center"]) ? $row["profit_center"] : null; // profit_center
        $profit_center_code         = isset($row["profit_center_code"]) ? $row["profit_center_code"] : null; // profit_center_code
        $cost_center                = isset($row["cost_center"]) ? $row["cost_center"] : null; // cost_center
        $type                       = isset($row["type"]) ? $row["type"] : null; // type
        $project_name               = isset($row["project_name"]) ? $row["project_name"] : null; // project_name
        $import_domestic            = isset($row["import_domestic"]) ? $row["import_domestic"] : null; // import_domestic
        $items_name                 = isset($row["items_name"]) ? $row["items_name"] : null; // items_name
        $equipment                  = isset($row["equipment"]) ? $row["equipment"] : null; // equipment
        $qty                        = isset($row["qty"]) ? $row["qty"] : null; // qty
        $curency                    = isset($row["curency"]) ? $row["curency"] : null; // curency
        $original_price_full_amount = isset($row["original_price_full_amount"]) ? $row["original_price_full_amount"] : null; // original_price_full_amount
        $exchange_rate              = isset($row["exchange_rate"]) ? $row["exchange_rate"] : null; // exchange_rate
        $price                      = isset($row["price"]) ? $row["price"] : null; // price
        $sop                        = isset($row["sop"]) ? $row["sop"] : null; // sop
        $first_down_payment_term    = isset($row["first_down_payment_term"]) ? $row["first_down_payment_term"] : null; // first_down_payment_term
        $first_down_payment_amount  = isset($row["first_down_payment_amount"]) ? $row["first_down_payment_amount"] : null; // first_down_payment_amount
        $final_payment_term         = isset($row["final_payment_term"]) ? $row["final_payment_term"] : null; // final_payment_term
        $final_payment_amount       = isset($row["final_payment_amount"]) ? $row["final_payment_amount"] : null; // final_payment_amount
        $owner_asset                = isset($row["owner_asset"]) ? $row["owner_asset"] : null; // owner_asset
        $apr                        = isset($row["apr"]) ? $row["apr"] : null; // apr
        $may                        = isset($row["may"]) ? $row["may"] : null; // may
        $jun                        = isset($row["jun"]) ? $row["jun"] : null; // jun
        $jul                        = isset($row["jul"]) ? $row["jul"] : null; // jul
        $aug                        = isset($row["aug"]) ? $row["aug"] : null; // aug
        $sep                        = isset($row["sep"]) ? $row["sep"] : null; // sep
        $oct                        = isset($row["oct"]) ? $row["oct"] : null; // oct
        $nov                        = isset($row["nov"]) ? $row["nov"] : null; // nov
        $dec                        = isset($row["dec"]) ? $row["dec"] : null; // dec
        $jan                        = isset($row["jan"]) ? $row["jan"] : null; // jan
        $feb                        = isset($row["feb"]) ? $row["feb"] : null; // feb
        $mar                        = isset($row["mar"]) ? $row["mar"] : null; // mar

        if (!empty($budget_no)) {
            DB::beginTransaction();
            
            try {
                $delete = CapexRb::where([
                    'dept' => $dept,
                    'budget_no' => $budget_no,
                    'line' => $line_or_dept
                ])->delete();
                if (!$delete) {
                    DB::rollBack();
                }

                $capexrb                         = new CapexRb;
                $capexrb->dept                   = $dept;
                $capexrb->budget_no              = $budget_no;
                $capexrb->line                   = $line_or_dept;
                $capexrb->profit_center          = $profit_center;
                $capexrb->profit_center_code     = $profit_center_code;
                $capexrb->cost_center            = $cost_center;
                $capexrb->type                   = $type;
                $capexrb->project_name           = $project_name;
                $capexrb->import_domestic        = $import_domestic;
                $capexrb->items_name             = $items_name;
                $capexrb->equipment              = $equipment;
                $capexrb->qty                    = $qty;
                $capexrb->curency                = $curency;
                $capexrb->original_price         = $original_price_full_amount;
                $capexrb->exchange_rate          = $exchange_rate;
                $capexrb->price                  = $price;
                $capexrb->sop                    = $sop;
                $capexrb->first_dopayment_term   = $first_down_payment_term;
                $capexrb->first_dopayment_amount = $first_down_payment_amount;
                $capexrb->final_payment_term     = $final_payment_term;
                $capexrb->final_payment_amount   = $final_payment_amount;
                $capexrb->owner_asset            = $owner_asset;
                $capexrb->april                  = $apr;
                $capexrb->mei                    = $may;
                $capexrb->juni                   = $jun;
                $capexrb->juli                   = $jul;
                $capexrb->agustus                = $aug;
                $capexrb->september              = $sep;
                $capexrb->oktober                = $oct;
                $capexrb->november               = $nov;
                $capexrb->december               = $dec;
                $capexrb->januari                = $jan;
                $capexrb->februari               = $feb;
                $capexrb->maret                  = $mar;
                if (  $capexrb->save()) {
                    DB::commit();
                }

            } catch (Exception $ex) {
                DB::rollBack();
            }
           
            
                
           
        }
    }
}
