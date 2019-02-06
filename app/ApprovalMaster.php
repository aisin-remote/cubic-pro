<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Approval_detail;
use App\Capex;
use App\Expense;        // Added by Ferry, July 10th 2015
use DB;
use Illuminate\Support\Collection; 

use Datatables;

class ApprovalMaster extends Model
{
	protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];
	
	public function get_list($type, $status)
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
        $year = substr(config('period.fyear_open'), -2);    
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
    public function details()
    {
        return $this->hasMany(\App\ApprovalDetail::class);
    }
    public function isOverExist()
    {
        if ($this->budget_type != 'ub') {
            foreach ($this->details as $detail) {
                if ($detail->budgetStatus == 'Overbudget') {
                    return true;
                }
            }
        }

        return false;
    }

    public function NeedDirApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 3) : $query->where('status', '>', 3);
    }

    public function NeedGMApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 2) : $query->where('status', '>', 2);
    }

    public function NeedDeptHeadApproval($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 1) : $query->where('status', '>', 1);
    }

    public function NeedBudgetValidation($query, $andAbove = false)
    {
        return !$andAbove ? $query->where('status', '=', 0) : $query->where('status', '>', 0);
    }

    public function departments()
    {
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }

    public function divisions()
    {
        return $this->belongsTo('App\Division', 'division_id', 'id');
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
    public function cancel()
    {
        if ($this->status < 0) {
            throw new \Exception("This approval already canceled.", 1);
        }

        if(\Entrust::hasRole('budget')) $this->status = -1; 

        // if dept head
        if(\Entrust::hasRole('department_head')) $this->status = -2;

        // if group manager
        if(\Entrust::hasRole('gm')) $this->status = -3;

        // if dept head
        if(\Entrust::hasRole('director')) $this->status = -4;

        return $this->status;
    }

}
