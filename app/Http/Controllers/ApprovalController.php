<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApprovalMaster;
use DataTables;
use DB;
use Storage;
use App\Capex;
use App\Capex_archive;      
use App\Helper;
use App\User;
use App\Department;
use Carbon\Carbon;          
use App\Period;             
use App\Expense;            
use App\Expense_archive;    
use App\Approval_master;    
use App\ApprovalDetail;

class ApprovalController extends Controller
{
    public function index($type)
    {
        switch ($type) {
            case 'cx':
                $active = 'capex';
                $view = 'pages.approval.capex.index-admin';
                break;

            case 'ex':
                $active = 'expense';
                $view = 'pages.approval.expense.index-admin';
                break;

            case 'ub':
                $active = 'unbudget';
                $view = 'pages.approval.unbudget.index-admin';
                break;

            default:
                // set error flash
                \Session::flash('flash_type', 'alert-danger');
                \Session::flash('flash_message', 'Budget type ['.$type.'] doesn\'t exist.');

                // redirect back to user create form
                return redirect('home');
                break;
        }

        if (\Entrust::hasRole('budget')) {    // v3.5 by Ferry, 20151113, add budget
            $view .= '_admin';
        }

        if (\Entrust::hasRole('user')) {
            $view .= '_user';
        }

        return view($view, compact('active'));
    }
    public function createApproval()
    {
    	return view('pages.approval.capex.create-approval');
    }
    public function create()
    {
        $sap_asset      = SapAsset::get();
        $sap_cost       = SapCost::get(); 
        $sap_gl_group   = SapGlGroup::get();

    	return view('pages.approval.capex.create', compact(['sap_asset','sap_cost','sap_gl_group']));
    }
    public function store(Request $request)
    {
        $approval_capex                     = new ApprovallDetail;
        $approval_capex->budget_no          = $request->budget_no;
        $approval_capex->project_name       = $request->project_name;
        $approval_capex->sap_gl_group       = $request->sap_gl_group;
        $approval_capex->sap_gl_account     = $request->sap_gl_account;
        $approval_capex->budget_description = $request->budget_description;
        $approval_capex->asset_categ        = $request->asset_categ;
        $approval_capex->sap_cost_center    = $request->sap_cost_center;
        $approval_capex->remarks            = $request->remarks;
        $approval_capex->pr_specs           = $request->pr_specs;
        $approval_capex->actual_qty         = $request->actual_qty;
        $approval_capex->sap_uom            = $request->sap_uom;
        $approval_capex->budget_remining_log = $request->budget_remining_log;
        $approval_capex->budget_plan        = $request->budget_plan;
        $approval_capex->currency           = $request->currency;
        $approval_capex->plan_gr            = $request->plan_gr;


    }

    public function approvalExpense()
    {
        return view('pages.approval.expense.list-approval');
    }
    public function createApprovalExpense()
    {
        return view('pages.approval.expense.create-approval');
    }
    public function createExpense()
    {
        return view('pages.approval.expense.create');
    }
    public function storeExpense()
    {

    }

    public function approvalUnbudget()
    {
        return view('pages.approval.unbudget.list-approval');
    }
    public function createApprovalUnbudget()
    {
        return view('pages.approval.unbudget.create-approval');
    }
    public function createUnbudget()
    {
        return view('pages.approval.unbudget.create');
    }
    public function storeUnbudget()
    {
    
    }

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

            $approvals = ApprovalMaster::select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
        ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
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
            $approvals = ApprovalMaster::select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
        ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
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

        return DataTables::of($approvals) // dev-4.2.1 by Fahrul, 20171107
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

    public function getCIPAdminList() {

        // $title1 = 'Convert CIP From Immediate Use';
        // $title2 = 'Extend CIP Settlement Date';

        // $budget_no = $this->getCIPAdminListConvert();
        // $budget_no_cip = $this->getCIPAdminListConvert('cip');

        // Get first element as init --> budget_no
        // $budget_name = '';
        
        // reset($budget_no);
        // $budget_key = key($budget_no);

        // if (! empty($budget_key)) {
        //     $arrData = Capex::getByBudgetNo($budget_key);
        //     $budget_name = $arrData[0]->equipment_name;
        // }

        // // Get first element as init --> budget_no_cip
        // $budget_settlement = '';

        // reset($budget_no_cip);
        // $budget_key = key($budget_no_cip);

        // if (! empty($budget_key)) {
        //     $arrData = ApprovalDetail::getByBudgetNo($budget_key);
        //     $budget_settlement = $arrData->settlement_date;
        // }

        return view("pages.capex.cip-admin");
                        // compact('title1', 'title2', 'budget_no', 'budget_name', 'budget_no_cip', 'budget_settlement'));
    }

    public function getCIPAdminListConvert($mode='one-time') {
        // Cek otentikasi
        $user = \Auth::user();

        $approvals = ApprovalDetail::query();

        if (\Entrust::hasRole('budget')) {
            // $approvals->whereIn('division', array_keys(config('global.division')));

            if ($mode == 'one-time') {
                $approvals = $approvals->whereNull('cip_no');
            }
            elseif ($mode == 'cip') {
                $approvals = $approvals->whereNotNull('cip_no');
            }

            $approvals = $approvals->orderBy('budget_no')
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->where('budget_type', 'cx')
                                    ->select('budget_no')
                                    ->distinct()->get();

            return $this->getCIPFormatted('combolist', $approvals);
        }
        else {
            return $data[''] = '';
        }
    }

    public function getCIPSettlementList() {

        $title = 'List of Outstanding CIP';
        $budget_no = $this->getCIPSettlementAjaxList();
        $cip = $this->getCIPSettlementAjaxList('tablelist', 'open', key($budget_no));

        // Get first element as init
        // Get asset_no for init
        $budget_key = key($budget_no);

        if (! empty($budget_key)) {

            reset($budget_no);
            $table_ajax = url('cip/settlement/ajaxlist/tablelist/open').'/'.$budget_key;

            $cip_key = key($cip);
            $asset_no = (! empty($cip_key)) ? $cip['data'][0][1] : null;
        }
        else {
            $asset_no = '';
            $table_ajax = url('cip/settlement/ajaxlist/tablelist/open/none');
        }

        return view("capex.cip", compact('title', 'table_ajax', 'budget_no', 'asset_no'));
    }
    
    public function getCip()
    {
        return view('pages.capex.cip');
    }
}
