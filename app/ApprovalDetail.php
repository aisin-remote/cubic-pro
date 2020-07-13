<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ApprovalDetail extends Model
{
	protected $fillable = [
        'fyear',                        // hotfix-3.4.7, Ferry, 20160401, Fiscal year
        'budget_no',
        'project_name',
        'cip_no',                       // v3.5 by Ferry, 20151028, CIP choose
        'settlement_date',              // v3.5 by Ferry, 20151028, CIP choose
        'settlement_name',              // v3.5 by Ferry, 20151028, CIP choose
        'asset_no',                     // v2.12 by Ferry, 20150813, New Column
        'sap_track_no',                 // v3.4 by Ferry on 20151008 for SAP tracking no
        'budget_reserved',
        'budget_remaining_log',         // Added 2.6 by Ferry, On 20150713, New Column
        'qty_remaining',
        'actual_qty',
        'actual_price_user',
        'actual_price_purchasing',
        'price_to_download',            //dev-4.1, by yudo, 20170412, add price to download
        'currency',                     //dev-4.1, by yudo, 20170412, add currency
        'actual_gr',
        'po_number',
        'remarks',

        // dev-4.0, Ferry, 20161118, kolom tambahan sap info harus bisa diinput
        'sap_asset_class',
        'sap_account_code',
        'sap_account_text',
        'sap_asset_no',
        'sap_cc_code',
        'sap_cc_fname',
        'sap_vendor_code',
        'sap_is_chemical',
        'pr_specs',
        'pr_uom',
    ];

    public static function getByBudgetNo($budget_no)
    {
        return self::query()->where('budget_no', $budget_no)->first();
    }
	public function items()
	{
		return $this->belongsTo('App\Item', 'item_id', 'id');
	}
	public function sap_gl_accounts()
    {
        return $this->belongsTo('App\SapModel\SapGlAccount', 'sap_account_code', 'gl_gcode');
    }
    public function departments()
    {
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function sap_assets()
    {
        return $this->belongsTo('App\SapModel\SapAsset', 'sap_asset_id', 'id');
    }
    public function sap_costs()
    {
        return $this->belongsTo('App\SapModel\SapCostCenter', 'sap_cost_center_id', 'id');
    }
    public function sap_uoms()
    {
        return $this->belongsTo('App\SapModel\SapUom', 'sap_uom_id', 'id');
    }

    public function approval()
    {
        return $this->belongsTo('App\ApprovalMaster', 'approval_master_id');
    }

    public function capex()
    {
        return $this->belongsTo('App\Capex', 'budget_no', 'budget_no');
    }

    public function expense()
    {
        return $this->belongsTo('App\Expense', 'budget_no', 'budget_no');
    }

    public function gr()
    {
        return $this->hasOne('App\GrConfirmDetail', 'approval_detail_id', 'id');
    }

    public function getIsOverAttribute()
    {
        $budget = $this->expense;
        if (!$budget) {
            $budget = $this->capex;
        }

        if (!$budget) {
            abort(404);
        }

        $budgetReserved = $budget
            ->approvalDetails()
            ->whereHas('approval', function($q) {
                $q->where('status', '>', 0);
            })
            ->select(DB::raw('sum(actual_price_user) as total_reserved'))
            ->groupBy('budget_no')
            ->where('id', '<=', $this->id)
            ->first();

        if (!$budgetReserved) {
            $budgetReserved = $this->actual_price_user;
        } else {
            $budgetReserved = $budgetReserved->total_reserved;
        }

        if ($budgetReserved > $budget->budget_plan) {
            return true;
        }

        return false;
    }

    // get last cip
    public static function getLastCIPNumber($budget_no)
    {
        $last_cip = self::query()
                    ->where('cip_no', 'like', $budget_no.'-%')
                    ->orderBy('cip_no', 'desc');

        return $last_cip->first();
    }

    // get new cip
    public static function getNewCIPNumber($budget_no)
    {
        $i = 0;

        if (!is_null($last = self::getLastCIPNumber($budget_no))) {

            list(,,,,,,$last_cip) = explode('-', $last->cip_no);;
            $i = $last_cip;
        }

        $i++;
        $i = str_pad($i, 4, 0, STR_PAD_LEFT);

        return $budget_no.'-'.$i;
    }

    public function getPlanGrDateAttribute()
    {
        $budget = $this->expense;
        if (!$budget) {
            $budget = $this->capex;
        }

        if (!$budget) {
            abort(404);
        }

        return date('d M Y', strtotime($budget->plan_gr));
    }
}

