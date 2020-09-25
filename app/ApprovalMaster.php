<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Department;
use App\Period;

class ApprovalMaster extends Model
{
	protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

    public static function getLastCapex($type, $dept)
    {
        $last_capex = self::query()
        ->where('approval_number', 'like', '%-'.$dept.'-%')
        ->where('budget_type', '=', $type)
        ->orderBy('id', 'desc');

        return $last_capex->first();
    }

    public static function extractApprovalNumber($approval_number)
    {
        return explode('-', $approval_number);
    }

    public static function getNewApprovalNumber($type, $dept)
    {
		$period = Period::all();
		if(!empty($period) && count($period) >= 6){
			$year = $period[0]->value;
		}else{
			$year = "xx";
		}
        $year = substr($year, -2);
        $iteration = 0;

        if (!is_null($last = self::getLastCapex($type, $dept))) {
            list(,,$last_year,$last_iteration) = self::extractApprovalNumber($last->approval_number);

            if ($last_year == $year) {
                $iteration = $last_iteration;
            }
        }

        $iteration++;
        $iteration = str_pad($iteration, 6, 0, STR_PAD_LEFT);

        return $type.'-'.$dept.'-'.$year.'-'.$iteration;
    }

    public static function getNewSapTrackingNo($type, $dept, $approval, $i)
    {
        $sap_key = Department::find($dept);
        $period = Period::all();

        if(!empty($period) && count($period) >= 6){
			$year = $period[0]->value;
		}else{
			$year = "xx";
		}
        $year = substr($year, -2);

        return $type.
                $sap_key->sap_key.
                $year.
                substr($approval, -4).
                str_pad($i, 2, '0', STR_PAD_LEFT);
    }

    public function details()
    {
        return $this->hasMany('App\ApprovalDetail', 'approval_master_id');
    }
    public function isOverExist()
    {
        if ($this->budget_type != 'ub') {
            foreach ($this->details as $detail) {
                if ($detail->budget_reserved > $detail->budget_remaining_log ) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function NeedDirApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 3) : $query->where('status', '>', 3);
    }

    public static function NeedGMApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 2) : $query->where('status', '>', 2);
    }

    public static function NeedDeptHeadApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 1) : $query->where('status', '>', 1);
    }

    public static function NeedBudgetValidation($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 0) : $query->where('status', '>', 0);
    }

    public function departments()
    {
        return $this->belongsTo('App\Department', 'department', 'department_code');
    }

    public function divisions()
    {
        return $this->belongsTo('App\Division', 'division', 'division_code');
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

    public function gr_confirm()
    {
    	return $this->hasOne('App\GrConfirm', 'approval_id');

    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

	public static function get_pending_sum ($budget_type, $group_type, $group_name, $thousands = 1000000, $rounded = 2)
	{
		$user = auth()->user();
		$total = 0.0;
		$arr_budget_type = is_array($budget_type) ? $budget_type : array($budget_type, 'u'.substr($budget_type, 0, 1) );

		$approvals = self::query()->whereIn('budget_type', $arr_budget_type);

        if($user->hasRole('budget')) self::NeedBudgetValidation($approvals);

        if($user->hasRole('department-head')) self::NeedDeptHeadApproval($approvals);

        if($user->hasRole('gm')) self::NeedGMApproval($approvals);

        if($user->hasRole('director')) self::NeedDirApproval($approvals);

        $total = $approvals->whereIn($group_type, is_array($group_name) ? $group_name : array($group_name))->sum('total');
        $total = round(floatval($total)/$thousands, $rounded);

        return $total;
    }
	public function approve()
    {
        if(\Entrust::hasRole('budget')) $this->status = 1;

        if(\Entrust::hasRole('department-head')) $this->status = 2;

        if(\Entrust::hasRole('gm')) $this->status = 3;

        if(\Entrust::hasRole('director')) $this->status = 4;

        return $this->status;
    }
	public function cancel()
	{
		if ($this->status < 0) {
			throw new \Exception("This approval already canceled.", 1);
		}

        if(\Entrust::hasRole('budget')) $this->status = -1;

        if(\Entrust::hasRole('department-head')) $this->status = -2;

        if(\Entrust::hasRole('gm')) $this->status = -3;

        if(\Entrust::hasRole('director')) $this->status = -4;

        return $this->status;
    }
	public static function getSelf($approval_number)
    {
        return self::where('approval_number', $approval_number)->first();
    }
	public static function getDetails($approval_number)
    {
        return self::with('details')->where('approval_number', '=', $approval_number)->first();
    }
	public static function getApprovalDetailsApi($approval_number)
    {
        $data['data'] = [];

        if (!is_null($master = self::getDetails($approval_number))) {
            $i = 1;
            foreach ($master->details as $value) {
                $data['data'][] = [
                    str_pad($i, 2, '0', STR_PAD_LEFT),
                    $value->budget_no,
                    $value->asset_no."<input type='hidden' value='".$value->id."'>",
                    $value->sap_track_no,
                    $value->sap_asset_no,
                    $value->sap_account_code,
                    $value->sap_cc_code,
                    "",        //budget description
                    $value->remarks,
                    $value->project_name,
                    $value->budget_remaining_log,
                    $value->budget_reserved,
                    $value->actual_price_user, // actual_price_purchasing
                    $value->price_to_download,
                    $value->currency,
                    $value->pr_specs, // qty remaining
                    "", // budget status
                    $value->actual_gr,
                    $value->sap_vendor_code,
                    $value->po_number,
                    $value->sap_track_no,
                    $value->sap_tax_code,
                    ];
                    $i++;
                }
            }

            return $data;
	}
	public static function get_budgetInfo($type, $status, $id)
    {
		$overbudget_info ="-";

        if ($type == 'ub') {
            $approvals = self::query()->where('budget_type', 'like', 'u%');
        } else {
            $approvals = self::query()->where('budget_type', '=', $type)->where('approval_number',"=",$id);
        }

        $user = auth()->user();

        if (\Entrust::hasRole('user')) {
            $approvals->where('department', $user->department->department_code);
        }

        if (count($approvals = $approvals->get()) > 0) {
            foreach ($approvals as $v) {

                $overbudget_info = $v->status < 0 ? 'Canceled' : ($v->isOverExist() ? 'Overbudget exist' : 'Underbudget');

            }
        }

        return $overbudget_info;
    }

    public function getIsOverAttribute()
    {
        $details = $this->details;

        foreach($details as $detail) {
            if ($detail->is_over) {
                return true;
            }
        }

        return false;
    }

    public function approverUsers()
    {
        return $this->hasMany('App\ApproverUser', 'approval_master_id');
    }
}
