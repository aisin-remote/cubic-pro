<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Approval_detail;
use App\Capex;
use App\Expense;        // Added by Ferry, July 10th 2015
use App\Department;
use DB;
use Illuminate\Support\Collection;
use App\Period;
use Datatables;

class ApprovalMaster extends Model
{
	protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

	public function get_list()
    {
        // return $status;
    // dev-4.0, Ferry, 20161222, Merging kemungkinan sedikit saya ubah, punya Yudo sementara aku comment

    // get user
        $user = \Auth::user();

    // dev-4.2.1, Fahrul, 20171107, Menampilkan data yang akan ditampilkan di tabel
    // $approvals = self::select('name','approval_number','total','status','overbudget_info','action');

    // v2.12 by Ferry, 20150820, Filter uc / ue
	if (($type == 'ub') &&
            \Entrust::hasRole(['admin', 'gm', 'department_head', 'director', 'user',
                'budget', 'purchasing', 'accounting'])) {

            $approvals = self::query()->select('departments.name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
        ->join('departments', 'approval_masters.department', '=', 'departments.dep_key')
        ->where('budget_type', 'like', 'u%');

    }

    //dev-4.0 by yudo,  untuk view pr convert sap po
    elseif (($type == 'all')  && \Entrust::hasRole('purchasing')) {   // bukan type capex, expense, unbudget. Tapi lemparan URI/URL/Querystring utk print
        if ($status == config('global.status_code.approved.dir'))
        {
            $approvals = self::query()->where('status', '=', $status)
            ->where('is_download', '=', 0);
        }
        else if($status == config('global.status_code.approved.bgt')){
            $approvals = self::query()->where('is_download', '=', 1);
        }
        else
        {
            $approvals = self::query()->where('status', '<=' , $status);
        }

    }

    // dev-4.0, Ferry, 20161222, Merging
    elseif (($type != 'ub') &&
        \Entrust::hasRole(['admin', 'gm', 'department_head', 'director', 'user',
            'budget', 'purchasing', 'accounting'])) {

        //$approvals = self::query()->where('budget_type', $type);
        $approvals = self::query()->select('departments.name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
    ->join('departments', 'approval_masters.department', '=', 'departments.dep_key')
    ->where('budget_type', $type);
	}

	    // if level == user
	if (\Entrust::hasRole('user')) {
	    $approvals->where('department', $user->department);
	}

	    // Added by : Ferry, on July 1st 2015
	    // if level == department_head
	if (\Entrust::hasRole('department_head')) {
	        // $approvals->where('department', $user->department);
	    $approvals->whereIn('department', config('global.department.'.$user->department.'.dep_grp'));
	}
	    // End of Ferry

	    // if level == GM
	if (\Entrust::hasRole('gm')) {
	    $approvals->where('division', $user->division);
	}

	    // if level == director
	if (\Entrust::hasRole('director')) {
	    $approvals->where('dir', $user->dir);
	}

	    // if approval is needed
	if ($status == 'need_approval') {
	        // if admin
	        if(\Entrust::hasRole('budget')) $approvals->needBudgetValidation();  // v3.5 by Ferry, 20151113, prev admin

	        // if dept head
	        if(\Entrust::hasRole('department_head')) $approvals->needDeptHeadApproval();

	        // if group manager
	        if(\Entrust::hasRole('gm')) $approvals->needGMApproval();

	        // if dept head
	        if(\Entrust::hasRole('director')) $approvals->needDirApproval();
	}

    // return $approvals->get();

    return Datatables::of($approvals) // dev-4.2.1 by Fahrul, 20171107
    ->addColumn("overbudget_info", function ($approvals) {
        return $approvals->status < 0 ? 'Canceled' : ($approvals->isOverExist() ? 'Overbudget exist' : 'All underbudget');
    })
    ->addColumn("action", function ($approvals) use($type,$status){ // dev-4.2.1 by Fahrul, 20171116
        if($status!='need_approval'){
            // return "<div id='$approvals->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$type"."/$approvals->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
            if(\Entrust::hasRole('user')) {
                return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.$type.'/'.$approvals->approval_number.'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" onclick="printApproval(&#39;'.$approvals->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="deleteApproval(&#39;'.$approvals->approval_number.'&#39;)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></div>';
            }elseif(\Entrust::hasRole('budget')) { //Sebenarnya ini ga bakal dieksekusi
                return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.$type.'/'.$approvals->approval_number.'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approvals->approval_number.'&#39;)"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
            }else{
                return "<div id='$approvals->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$type"."/$approvals->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
            }
        }else{
            // return "else";
            return "<div id='$approvals->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$approvals->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a  href='javascript:validateApproval(&#39;$approvals->approval_number&#39;);' class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a><a href='$approvals->approval_number' class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
        }
        // return $type;

    })
        ->editColumn("total", function ($approvals) {
            return number_format($approvals->total);
        })
        ->editColumn("status", function ($approvals){ // dev-4.2.1 by Fahrul, 20171116
            if ($approvals->status == '0') {
                return "User Created";
            }elseif ($approvals->status == '1') {
                return "Validasi Budget";
            }elseif ($approvals->status == '2') {
                return "Approved by Dept. Head";
            }elseif ($approvals->status == '3') {
                return "Approved by GM";
            }elseif ($approvals->status == '4') {
                return "Approved by Director";
            }elseif ($approvals->status == '-1') {
                return "Canceled on Quotation Validation";
            }elseif ($approvals->status == '-2') {
                return "Canceled Dept. Head Approval";
            }else{
                return "Canceled on Group Manager Approval";
            }
        })
        ->make(true);
    }

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
        return $this->hasMany(\App\ApprovalDetail::class);
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

    public function approver_user()
    {
        return $this->hasMany('App\ApproverUser', 'approval_master_id');
    }

	public static function get_pending_sum ($budget_type, $group_type, $group_name, $thousands = 1000000, $rounded = 2)
	{
		$user = auth()->user();
		$total = 0.0;
		$arr_budget_type = is_array($budget_type) ? $budget_type : array($budget_type, 'u'.substr($budget_type, 0, 1) );

		$approvals = self::query()->whereIn('budget_type', $arr_budget_type);

        if($user->hasRole('budget')) self::NeedBudgetValidation($approvals);

        if($user->hasRole('department_head')) self::NeedDeptHeadApproval($approvals);

        if($user->hasRole('gm')) self::NeedGMApproval($approvals);

        if($user->hasRole('director')) self::NeedDirApproval($approvals);

        $total = $approvals->whereIn($group_type, is_array($group_name) ? $group_name : array($group_name))->sum('total');
        $total = round(floatval($total)/$thousands, $rounded);

        return $total;
    }
	public function approve()
    {
        if(\Entrust::hasRole('budget')) $this->status = 1;

        if(\Entrust::hasRole('department_head')) $this->status = 2;

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

        if(\Entrust::hasRole('department_head')) $this->status = -2;

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
}
