<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Carbon\Carbon;
use App\Period;

use App\SapModel\SapAsset;
use App\SapModel\SapCostCenter;
use App\SapModel\SapGlAccount;
use App\SapModel\SapUom;
use App\Capex;
use App\CapexArchive;
use App\Expense;
use App\ExpenseArchive;
use App\Item;

use App\Cart;
use App\Approval;
use App\ApproverUser;
use App\ApprovalDtl;
use App\ApprovalMaster;
use App\ApprovalDetail;

use function PHPSTORM_META\override;

require '../PHPExcel/PHPExcel.php';

class ApprovalController extends Controller
{
    public function index()
    {
        $type = '';
        switch ($type) {
            case 'cx':
                $active = 'capex';
                $view = 'pages.approval.capex.index';
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
		$approval = Approval::get();
		$isExistOverdueCIP = $this->isExistOverdueCIP();
    	return view('pages.approval.capex.create-approval',compact(['approval','isExistOverdueCIP']));
    }
    public function create()
    {
        $sap_assets      = DB::table('sap_assets')->select('id', 'asset_type')->groupBy('asset_type')->get();
        $sap_codes       = DB::table('sap_assets')->select('asset_code')->distinct()->get();
        $sap_costs       = SapCostCenter::get();
        $sap_gl_group    = SapGlAccount::get();
        $sap_uoms        = SapUom::get();
        $capexs          = Capex::where('department', auth()->user()->department->department_code)->get();
        $carts 			 = Cart::select('*')->join('items','items.id','=','carts.item_id')->where('user_id', auth()->user()->id)->get();
        $items           = Item::get();

       if (url()->previous() == route('cart')){
            $itemcart = 'catalog';
        } else {
            $itemcart = 'non-catalog';
        }
    	return view('pages.approval.capex.create', compact(['sap_assets','sap_codes','sap_costs','sap_gl_group', 'sap_uoms', 'capexs', 'carts', 'items', 'itemcart']));
    }
    public function approvalExpense()
    {
        return view('pages.approval.expense.list-approval');
    }
    public function createApprovalExpense()
    {
		$approval = Approval::get();
        return view('pages.approval.expense.create-approval',compact(['approval']));
    }
    public function createExpense()
    {
        $sap_assets      = SapAsset::get();
        $sap_costs       = SapCostCenter::get();
        $sap_gl_account  = DB::table('sap_gl_accounts')->select('gl_gname')->where('dep_key',auth()->user()->department->department_code)->distinct()->get();
        $sap_uoms        = SapUom::get();
        $expenses        = Expense::where('department', auth()->user()->department->department_code)->get();
		$carts 			 = Cart::select('*')->join('items','items.id','=','carts.item_id')->where('user_id', auth()->user()->id)->get();
        $items           = Item::get();

       if (url()->previous() == route('cart')){
            $itemcart = 'catalog';
        } else {
            $itemcart = 'non-catalog';
        }
        return view('pages.approval.expense.create', compact(['sap_assets','sap_costs','sap_gl_account', 'sap_uoms', 'expenses','carts', 'items', 'itemcart']));
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
		$approval = Approval::get();
        return view('pages.approval.unbudget.create-approval',compact(['approval']));
    }
    public function createUnbudget()
    {
        $sap_assets      = DB::table('sap_assets')->select('id', 'asset_type')->groupBy('asset_type')->get();
        $sap_codes       = DB::table('sap_assets')->select('asset_code')->distinct()->get();
        $sap_costs       = SapCostCenter::get();
        $sap_gl_account  = DB::table('sap_gl_accounts')->select('gl_gname')->where('dep_key',auth()->user()->department->department_code)->distinct()->get();
        $sap_uoms        = SapUom::get();
        $expenses        = Expense::where('department', auth()->user()->department->department_code)->get();
		$capexs          = Capex::where('department', auth()->user()->department->department_code)->get();
		$carts 			 = Cart::select('*')->join('items','items.id','=','carts.item_id')->where('user_id', auth()->user()->id)->get();
        $items           = Item::get();

        if (url()->previous() == route('cart')){
            $itemcart = 'catalog';
        } else {
            $itemcart = 'non-catalog';
        }
        return view('pages.approval.unbudget.create',compact(['sap_assets','sap_costs','sap_gl_account', 'sap_uoms', 'expenses', 'capexs','carts', 'items', 'itemcart']));
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
            \Entrust::hasRole(['admin', 'gm', 'department-head', 'director', 'user',
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
            \Entrust::hasRole(['admin', 'gm', 'department-head', 'director', 'user',
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
            // if level == department-head
        if (\Entrust::hasRole('department-head')) {
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
                if(\Entrust::hasRole('department-head')) $approvals->needDeptHeadApproval();

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
		$budget_nos 	= $this->getCIPAdminListConvert();
        $budget_nos_cip = $this->getCIPAdminListConvert('cip');

        return view("pages.capex.cip-admin",compact('budget_nos','budget_nos_cip'));
    }
	public function getCIPAdminListConvert($mode='one-time',$control="data") {

        $user = auth()->user();

        if ($user->hasRole('budget')) {
            $approvals = ApprovalDetail::query();
            // $approvals->whereIn('division', [$user->division->division_code]);
            if ($mode == 'one-time') {
                $approvals = $approvals->whereNull('cip_no');
            }
            elseif ($mode == 'cip') {
                $approvals = $approvals->whereNotNull('cip_no');
            }

            $approvals = $approvals->orderBy('budget_no')
                                    ->whereHas('approval', function($q) {
                                        $q->where('budget_type', 'cx');
                                    })
                                    ->select('budget_no')
                                    ->distinct()->get();

			if($control == "data"){
				return $approvals;
			}else{
				return $this->getCIPFormatted($control, $approvals);
			}
        }else {
            return array();//$data[''] = '';
        }
    }
	public function getCIPFormatted($control='combolist', $approvals)
    {
        if (count($approvals) > 0) {

            if ($control == 'combolist') {

                foreach ($approvals as $v) {
                    $data[$v->budget_no] = str_replace('-', ' - ', $v->budget_no);
                }
            }
            elseif ($control == 'tablelist') {

                foreach ($approvals as $v) {
                    $data['data'][] = [
                        $v->budget_no,
                        $v->asset_no,
                        $v->cip_no,
                        $v->settlement_date,
                        is_null($v->settlement_name) ? '-- Not Yet Assigned --' : $v->settlement_name,
                        $v->actual_gr,
                        is_null($v->settlement_name) ? 'Open' : 'Close'
                    ];
                }
            }
        }
        else {
            if ($control == 'combolist') {
                $data[''] = '';
            }
            elseif ($control == 'tablelist') {
                $data['data'] = [];
            }
        }

        return $data;
    }

    public function getCipSettlementList()
    {
        $budget_nos = $this->getCIPSettlementAjaxList('data');
		// Get first element as init
        $budgetno 	= count($budget_nos) > 0 ? $budget_nos[0]->budget_no:'';
        return view('pages.capex.cip',compact('budget_nos','budgetno'));
    }
	public function getApprovalDetail($budget_no)
	{
		$approval_detail = ApprovalDetail::where('approval_details.budget_no',$budget_no)->join('capexes','approval_details.budget_no','=','capexes.budget_no')->first();
		$approval_detail->settlement_date = date('d-M-Y',strtotime($approval_detail->settlement_date));
		return json_encode($approval_detail);
	}
	public function convertToCIP (Request $request) {
        try {
           DB::transaction(function() use ($request){
				// find the cip
				$approvals = ApprovalDetail::where('budget_no', $request->budget_no)->get();

				$i = 1;
				foreach ($approvals as $approval) {
					$approval->cip_no = $request->budget_no.'-'.str_pad($i, 4, '0', STR_PAD_LEFT);
					$approval->settlement_date = date('Y-m-d',strtotime($request->settlement_date));
					$approval->save();
					$i++;
				}
		   });
            $data['success'] = 'One time budget '.$request->budget_no.' is successfully converted to CIP';
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return $data;
    }
	public function extendResettle(Request $request){
        try {

            DB::transaction(function() use ($request){
				// find the cip
				$approvals = ApprovalDetail::where('budget_no', $request->budget_no)->get();

				$i = 1;
				foreach ($approvals as $approval) {
					$approval->settlement_date = date('Y-m-d',strtotime($request->new_settlement_date));
					$approval->save();
					$i++;
				}
			});

            $data['success'] = 'New settlement date: '.$request->new_settlement_date.' is successfully updated';

        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return $data;

	}
	public function getCIPSettlementAjaxList($control='combolist', $status = 'open', $filter='none') {

        // Cek otentikasi
        $user = \Auth::user();

        $approvals = ApprovalDetail::query();

        // View based on authorize
        if (\Entrust::hasRole('department-head')) {
            $approvals->whereIn('department',[$user->department->department_code]);
        }
        elseif (\Entrust::hasRole('gm')) {
            $approvals->where('division', $user->division->division_code);
        }
        elseif (\Entrust::hasRole('director')) {
            $approvals->where('dir', $user->dir);
        }
        elseif (\Entrust::hasRole('budget')) {
            // $approvals->whereIn('division', [$user->division->division_code]);
        }
        else {  // Common users
            $approvals->where('department', $user->department->department_code);
        }

        if ($control == 'combolist'|| $control == 'data') {

            $approvals = $approvals->whereNotNull('cip_no')
                                    ->whereNull('settlement_name')
                                    ->orderBy('settlement_date')
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->where('budget_type', 'cx')
                                    ->select('budget_no')
                                    ->distinct()->get();
        }
        elseif ($control == 'tablelist') {

            if ($status == 'open') {
                $approvals = ApprovalDetail::where('budget_no', $filter)
                                            ->whereNull('settlement_name')
                                            ->orderBy('settlement_date')
                                            ->get();
            }
            elseif ($status == 'close') {
                $approvals = $approvals->whereNotNull('settlement_name')
                                        ->orderBy('settlement_date')
                                        ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                        ->get();
            }
			 return DataTables::of($approvals)
						->editColumn("settlement_name", function ($approval) {
								return is_null($approval->settlement_name)?'-- Not Yet Assigned --' : $approval->settlement_name;
						 })
						 ->editColumn("actual_gr", function ($approval) {
								return date('d-M-Y',strtotime($approval->actual_gr));
						 })
						->editColumn("status", function ($approval) {
								return  is_null($approval->settlement_name) ? 'Open' : 'Close';
						})->make(true);
        }
		if($control == 'data'){

			return $approvals;
		}else{
			return $this->getCIPFormatted($control, $approvals);
		}
    }
	public function finishCIP(Request $request) {
        try {
           DB::transaction(function() use ($request){

				// find the cip
				$approvals = ApprovalDetail::where('budget_no', $request->budget_no)->get();

				foreach ($approvals as $approval) {
					// Commented, not necessary now
					$approval->settlement_name = $request->settlement_name;
					$approval->save();
				}

				// Close budget capex nya
				$myCapex = Capex::where('budget_no', $request->budget_no)->first();
				$myCapex->is_closed = 1;
				$myCapex->save();
			});
				$data['success'] = 'CIP is successfully finished';
		} catch (\Exception $e) {

				$data['error'] = $e->getMessage();
		}

        return $data;
    }
	public static function sumBudgetGroupPlan($budget_type, $group_type, $group_name,
                                                $thousands = 1000000, $rounded = 2)
    {
        $total = 0.0;
        $total = $budget_type == 'cx' ?
            Capex::whereIn($group_type, is_array($group_name) ? $group_name : array($group_name))
                    ->get([
                            DB::raw('SUM(CASE WHEN is_revised = 0 THEN budget_plan ELSE budget_used END) as total') // hotfix-3.4.11, Andre, 20160420 prev is_revised = 1. Ferry fixing comment of Andre 20160421
                        ])
                    ->first()->total :
            Expense::whereIn($group_type, is_array($group_name) ? $group_name : array($group_name))
                    ->get([
                            DB::raw('SUM(CASE WHEN is_revised = 0 THEN budget_plan ELSE budget_used END) as total') // hotfix-3.4.11, Andre, 20160420 prev is_revised = 1. Ferry fixing comment of Andre 20160421
                        ])
                    ->first()->total;   // v3.4 by Ferry, 20151015, Prev: ->sum('budget_plan')

        $total = round(floatval($total)/$thousands, $rounded);
        return $total;
    }

    public static function sumBudgetGroupActual($budget_type, $group_type, $group_name,
                                                $ym_start = '2016-04-01 00:00:00',
                                                $ym_end = '2017-03-31 23:59:59',
                                                $thousands = 1000000, $rounded = 2)
    {
        $total = 0.0;
        $arr_budget_type = is_array($budget_type) ? $budget_type : array($budget_type, 'u'.substr($budget_type, 0, 1) );

        $total = ApprovalDetail::whereBetween('actual_gr', [$ym_start, $ym_end])
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->whereIn('budget_type', $arr_budget_type)
                                    ->whereIn($group_type, is_array($group_name) ? $group_name : array($group_name))
                                    ->where('status', '>=', 3)
                                    ->get([
                                                DB::raw('SUM(CASE WHEN actual_price_purchasing <= 0 THEN actual_price_user ELSE actual_price_purchasing END) as total')
                                            ])
                                    ->first();

        $total = round(floatval($total->total)/$thousands, $rounded);
        return $total;
    }
	public static function sumBudgetPlan(   $budget_type,
                                            $plan_code,
                                            $group_name = [],
                                            $group_type = 'division',
                                            $thousands = 1000000000)
    {

        // Revised Plan = Plan (Actual based) + Revised (Plan) - Archive Plan
        // Original Plan = Plan - Revised Plan + Archive Plan

        $total = 0.0;
		$period = Period::all();
		if(!empty($period) && count($period)>=6)
		{
			$fyear_open = $period[0]->value;

			// Revised Plan
			if ($plan_code == 'R') {
				$total = $budget_type == 'cx' ?
							Capex::whereIn($group_type, $group_name)
								 ->where('fyear', $fyear_open)
								 ->get([
										DB::raw('SUM(CASE WHEN is_revised = 1 THEN budget_plan ELSE budget_used END) as total')
									])
								->first() :
							Expense::whereIn($group_type, $group_name)
								 ->where('fyear', $fyear_open)
								 ->get([
										DB::raw('SUM(CASE WHEN is_revised = 1 THEN budget_plan ELSE budget_used END) as total')
									])
								->first();
				$total = round(floatval($total->total)/$thousands, 2);
			}
			elseif ($plan_code == 'O') {

			// Original Plan
				$total_master = $budget_type == 'cx' ?
									Capex::whereIn($group_type, $group_name)       // Capex::whereIn('division', $division)
										->where('fyear', $fyear_open)                // bugs fiscal year as parameter
										->where('is_revised', 0)
										->sum('budget_plan') :
									Expense::whereIn($group_type, $group_name)       // Expense::whereIn('division', $division)
										->where('fyear',$fyear_open)                // bugs fiscal year as parameter
										->where('is_revised', 0)
										->sum('budget_plan');

				$total_archive = $budget_type == 'cx' ?
									CapexArchive::whereIn($group_type, $group_name)       // Capex_archives::whereIn('division', $division)
										->where('fyear', $fyear_open)                // fiscal year as parameter
										->sum('budget_plan') :
									ExpenseArchive::whereIn($group_type, $group_name)       //Expense_archives::whereIn('division', $division)
										->where('fyear', $fyear_open)                //bugs fiscal year as parameter
										->sum('budget_plan');

				$total = $total_master + $total_archive;
				$total = round($total/$thousands, 2);
			}
		}


        return $total;
    }

    public static function sumBudgetActual($budget_type, $filter_date, $group_name =[], $group_type = 'division', $thousands = 1000000000, $rounded = 2)
    {
        $total = 0.0;
        $arr_budget_type = is_array($budget_type) ? $budget_type :
                            array($budget_type, 'u'.substr($budget_type, 0, 1) );

        $total = ApprovalDetail::whereBetween('actual_gr', $filter_date)
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->whereIn('budget_type', $arr_budget_type)
                                    ->whereIn($group_type, $group_name)
                                    ->where('status', '>=', 3)  // filter GM Up (>=3)
                                    ->get([
                                                DB::raw('SUM(CASE WHEN actual_price_purchasing <= 0 THEN actual_price_user ELSE actual_price_purchasing END) as total')
                                            ])
                                    ->first();

        $total = round(floatval($total->total)/$thousands, $rounded);
        return $total;

    }
	 public static function sumBudgetPlanMonthly($budget_type,
                                                $filter_date,
                                                $plan_code,
                                                $group_name = [],
                                                $group_type = 'division',
                                                $thousands = 1000000000)
    {
        $subtotal = 0.0;
		$budgets  = [];
        // Revised Plan = Plan (Actual based) + Revised (Plan) - Archive Plan
        // Original Plan = Plan - Revised Plan + Archive PlanS
		$period = Period::all();
		if(!empty($period) && count($period)>=6)
		{
			$fyear_open = $period[0]->name;

			// Revised
			if ($plan_code == 'R') {

				$budgets = $budget_type == 'cx' ?

								Capex::whereBetween('plan_gr', $filter_date)
										->where('fyear',$fyear_open)
										->whereIn($group_type, $group_name)
										->orderBy('plan_gr', 'asc')
										->groupBy('month')->get([
															DB::raw('substr(plan_gr,6,2) as month'),
															DB::raw('SUM(CASE WHEN is_revised = 1 THEN budget_plan ELSE budget_used END) as total')
														]) :
								Expense::whereBetween('plan_gr', $filter_date)
										->where('fyear',$fyear_open)
										->whereIn($group_type, $group_name)
										->orderBy('plan_gr', 'asc')
										->groupBy('month')->get([
															DB::raw('substr(plan_gr,6,2) as month'),
															DB::raw('SUM(CASE WHEN is_revised = 1 THEN budget_plan ELSE budget_used END) as total')
														]);
			}
			elseif ($plan_code == 'O') {
				// Original
				$tbl_budget = $budget_type == 'cx' ? 'capexes' : 'expenses';
				$tbl_archive = $budget_type == 'cx' ? 'capex_archives' : 'expense_archives';
				$queries = DB::select(
							'select substr(plan_gr,6,2) AS month, SUM(`budget_plan`) as total
								from (
										(select * from `'.$tbl_archive.'`
											where `plan_gr` between :filter_date1 and :filter_date2
											and `'.$group_type.'` in (\''.implode("','", $group_name).'\')
										)
											union all
										(select `'.$tbl_budget.'`.*, `'.$tbl_archive.'`.`archived_by`, `'.$tbl_archive.'`.`archived_at`
											from `'.$tbl_budget.'`
											left join `'.$tbl_archive.'`
											on `'.$tbl_budget.'`.`budget_no` = `'.$tbl_archive.'`.`budget_no`
											where `'.$tbl_budget.'`.`'.$group_type.'` in (\''.implode("','", $group_name).'\') and
													`'.$tbl_budget.'`.`is_revised` = :is_revised and `'.$tbl_budget.'`.`plan_gr`
											between :filter_date3 and :filter_date4
												and `'.$tbl_archive.'`.`budget_no` is null
										)
									) t
								group by month
								order by `plan_gr` asc',
							[   'filter_date1'  => $filter_date[0],
								'filter_date2'  => $filter_date[1],
								'filter_date3'  => $filter_date[0],
								'filter_date4'  => $filter_date[1],
								'is_revised'    => 0
							]);
				$budgets = $queries;
			}
		}

		if (count($budgets) <= 0) {
			return [[0.0], [0.0],[]];
		}
		else {
			$subtotals 		= array();
			$subtotalsCopy 	= array();
			$month 			= array();
			foreach ($budgets as $budget) {
				$subtotals[] 		= round(floatval($budget->total)/$thousands, 2);
				$subtotalsCopy[] 	= round(floatval($budget->total)/$thousands, 2);
				$month[] 			= $budget->month;
			}
			// $subtotals 		= array('5','10','15');
			// $subtotalsCopy 	= array('5','10','15');
			// $month 			= array('03','04','05');
			$n = sizeof($subtotalsCopy)-1;
			$cummPlan = array();
			while ($n >= 0) {
				array_unshift($cummPlan, array_sum($subtotalsCopy));

				array_pop($subtotalsCopy);
				$n--;
			}
			return array($subtotals, $cummPlan, $month);
		}
    }

	public static function sumBudgetActualMonthly($budget_type,
                                                    $filter_date,
                                                    $group_name = [],
                                                    $group_type = 'division',
                                                    $thousands = 1000000000)
    {
        $subtotal = 0.0;
        $budgets = ApprovalDetail::whereBetween('actual_gr', $filter_date)
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->where('budget_type', $budget_type)
                                    ->whereIn($group_type, $group_name)
                                    ->where('status', '>=', 3)  //filter GM Up (>=3)
                                    ->orderBy('actual_gr', 'asc')
                                    ->groupBy('month')
                                    ->get([
                                                DB::raw('MONTH(actual_gr) as month'),
                                                DB::raw('SUM(CASE WHEN actual_price_purchasing <= 0 THEN actual_price_user ELSE actual_price_purchasing END) as total')
                                            ]);

        if (count($budgets) <= 0) {
            return [[0.0], [0.0],[]];
        }
        else {
            $m = 4;
			$subtotals 		= array();
			$subtotalsCopy 	= array();
			$month 			= array();
            foreach ($budgets as $budget) {
                // while ($m < intval($budget->month)) {
                    // $subtotals[] = 0.00;
                    // $subtotalsCopy[] = 0.00;
                    // $m++;
                // }
                // if ($m == intval($budget->month)) {
                    $subtotals[] 		= round(floatval($budget->total)/$thousands, 2);
                    $subtotalsCopy[] 	= round(floatval($budget->total)/$thousands, 2);
					$month[]			= $budget->month;
                    $m++;
                // }
                // elseif ($m > intval($budget->month)) {
                    // $m = 1;
                // }
            }
			// $subtotals 		= array('20','30','40');
			// $subtotalsCopy 	= array('20','30','40');
			// $month 			= array('01','03','04');
            $n = sizeof($subtotalsCopy)-1;
            $cummActual = array();
            while ($n >= 0) {
                array_unshift($cummActual, array_sum($subtotalsCopy));
                array_pop($subtotalsCopy);
                $n--;
            }

            return array($subtotals, $cummActual, $month);
        }
    }
	 public static function isExistOverdueCIP() {
        $user = \Auth::user();

        $approvals = ApprovalDetail::where('department', $user->department->department_code)
                                    ->whereNotNull('cip_no')
                                    ->whereNull('settlement_name')
                                    ->where('settlement_date', '<', Carbon::now()->format('Y-m-d'))
                                    ->orderBy('budget_no')
                                    ->join('approval_masters', 'approval_master_id', '=', 'approval_masters.id' )
                                    ->where('budget_type', 'cx')
                                    ->select('budget_no')
                                    ->distinct()->get();
        return (count($approvals) > 0);
    }

	/*
		1. ambil data approvals
		2. ambil data level dari approval_dtls berdasarkan user approve
		3. update approver_user berdasarkan user yang approve
		4. update approval master status dengan level dari user yang approve
		5. kalau expense atau capex dan level user dari approval_dtls yang tertinggi maka :
		   a. capex/expense budget_remaining diisi budget_remaining dikurangi approval_details.actual_price_purchasing atau  kalau 0 maka approval_details.actual_price_user
		   b. capex/expense budget_used diisi budget_used ditambah approval_details.actual_price_purchasing atau  kalau 0 maka approval_details.actual_price_user
		   c. kalau expense qty_remaining diisi qty_remaining dikurang actual_qty, qty_used diisi qty_used ditambah actual_qty
		   d. capex/expense status jadi 1 klo approval_details.budget_remaining_log < 0 dan 0 klo approval_details.budget_remaining_log >= 0
		   e. capex/expense is_closed jadi 1 klo approval_details.budget_remaining_log <=0 dan 0 klo approval_details.budget_remaining_log > 0
	*/
	public function approveAjax(Request $request)
	{
		try{

			 DB::transaction(function() use ($request){
                $user = auth()->user();
                $can_approve   = $this->can_approve($request->approval_number);

				if($can_approve > 0){
                    $approvalMaster          = ApprovalMaster::where('approval_number', $request->approval_number)->first();
                    $approval = Approval::where('department',$approvalMaster->department)->first();
                    $approverLevel = $approval->details()->where('user_id', $user->id)->first();

                    if (!$approverLevel) {
                        throw new \Exception('Cannot make approval, Approver level in approval is undefined');
                    }

                    $status = $approverLevel->level;
                    $needOtherApprove = false;

                    if ($approval->is_must_all == '1') {
                        // cek di approver user apakah ada orang lain yang perlu approve
                        $otherApprovers = $approval->details()
                            ->where('user_id', '!=', $user->id)
                            ->where('level', $approverLevel->level)
                            ->get();

                        if ($otherApprovers->count()) {
                            foreach ($otherApprovers as $otherApprover) {
                                $statusOtherApprover = $otherApprover->approverUsers()
                                    ->where('approval_master_id', $approvalMaster->id)
                                    ->first();

                                if ($statusOtherApprover->is_approve == 0) {
                                    $status = $approvalMaster->status;
                                    $needOtherApprove = true;

                                    break;
                                }
                            }
                        }
                    }

                    $approvalMaster->status = $status;
                    $approvalMaster->save();

                    $approvalMaster->approverUsers()
                        ->where('user_id', $user->id)
                        ->update([
                            'is_approve' => '1',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                    if (!$needOtherApprove) {
                        $user_approve = 4;
                        $type = $approvalMaster->budget_type;

                        if ($type != 'ub' && $type != 'uc' && $type != 'ue' && $approvalMaster->status == $user_approve) {
                            foreach($approvalMaster->details as $detail) {
                                $budget = $type == 'cx' ? $detail->capex : $detail->expense;

                                if(is_null($budget)){
                                    $data['error']	="Master Budget No: " . $detail->budget_no." is Deleted by Finance.\nPlease Contact Finance Department";

                                    return $data;
                                }

                                $budget->budget_remaining 	-= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                                $budget->budget_used 		+= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                                if ($approvalMaster->budget_type == 'ex') {
                                    $budget->qty_used 		+= $detail->actual_qty;
                                    $budget->qty_remaining 	= $budget->qty_plan - $budget->qty_used;
                                }

                                $budget->status 	= $budget->budget_remaining >= 0 ? 0 : 1;

                                $budget->is_closed = $budget->budget_remaining > 0 ? 0 : 1;
                                $budget->save();
                            }

                        }
                    }
				} else {
					 throw new \Exception("You can not approve this data because of this approval must be sequential or you have no priviledge");
				}
            });

			$data['success'] = 'Approval ['.$request->approval_number.'] approved.';

		}catch(\Exception $e){
			 DB::rollback();
			 $data['error']	= $e->getMessage();
		}

		return $data;
	}
	public function cancelApproval(Request $request)
	{
		try{

			$ret =  DB::transaction(function() use ($request){
				 $user = auth()->user();
                 $can_approve   = $this->can_approve($request->approval_number);

				 if($can_approve > 0 || \Entrust::hasRole('budget') || \Entrust::hasRole('admin')){

                    $dept           = ApprovalMaster::where('approval_number', $request->approval_number)->first();
                    $approvals 	    = Approval::where('department',$dept->department)->first();
                    $approverLevel  = ApprovalDtl::where('approval_id',$approvals->id)->where('user_id',$user->id)->first();

					 if(!empty($approverLevel)){
                         $approval_master = ApprovalMaster::where('approval_number',$request->approval_number)->first();
                        if ($approval_master->budget_type == 'cx' || $approval_master->budget_type == 'ex') {
                            foreach ($approval_master->details as $detail) {
                                $budget = $approval_master->budget_type == 'cx' ? Capex::where('budget_no',$detail->budget_no)->first() : Expense::where('budget_no',$detail->budget_no)->first();

                                $budget->budget_reserved -= $detail->budget_reserved;

                                $detail->budget_reserved = 0;
                                $detail->save();

                                if ($approval_master->status > 2) {
                                    $budget->budget_remaining 	+= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                                    $budget->budget_used 		-= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;
                                }
                                if ($approval_master->budget_type == 'ex') {
                                    $budget->qty_remaining 	+= $detail->actual_qty;

                                    $budget->qty_used 		-= $detail->actual_qty;
                                }

                                $budget->status 	= $budget->budget_remaining >= 0 ? 0 : 1;

                                $budget->is_closed 	= $budget->status == 0 ? 0 : 1;

                                $budget->save();
                            }
                        }
                        $approval_master->status = '-'.$approverLevel->level;
                        $approval_master->save();
                        ApproverUser::where('approval_master_id',$approval_master->id)
                            ->where('user_id',$user->id)
                            ->update([
                                'is_approve' => '-1',
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
					 }else{
						 throw new \Exception('Cannot cancel approval, Approver level in approval is undefined');
					 }
				 }else{
					throw new \Exception("You can not approve this data because of this approval must be sequential or you have no priviledge");
				 }
			 });

			$data['success'] = 'Cancel Approval ['.$request->approval_number.'] succcess.';

		}catch(\Exception $e){
			 DB::rollback();
			 $data['error']	= $e->getMessage();
		}

        return $data;

	}

	public function printApproval($approval_number)
    {
		$approval_master = ApprovalMaster::where('approval_number',$approval_number)->first();

        if (is_null($approval = ApprovalMaster::getSelf($approval_number))) {
			$res = [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => 'Approval ['.$approval_number.'] doesn\'t exist.'
                ];

			return redirect()
                    ->route('dashboard')
                    ->with($res);
        }

        if ($approval->status < 3) {
			$res = [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => 'Could not print Approval ['.$approval_number.']: GM Approval required.'
                ];


			if(strtolower($approval->budget_type)=="cx"){
				return redirect()
                    ->route('approval-capex.ListApproval')
                    ->with($res);
			}else if(strtolower($approval->budget_type) == "ex"){
				return redirect()
                    ->route('approval-expense.ListApproval')
                    ->with($res);
			}
        }

        switch ($approval->budget_type) {
            case 'cx':
                $type = 'Capex';
                //$budgets = Capex::query();
                break;

            case 'ex':
                $type = 'Expense';
                //$budgets = Expense::query();
                break;

            default:
                $type = 'Unbudget';
                break;
        }

        $overbudgets[] = '';
        $statistics[] = '';

        if ($type != 'Unbudget'){

            // foreach ($approval->details as $detail) {

                // if ($detail->budgetStatus == 'Overbudget') {
                    // $overbudgets[] = $detail->budget_no;
                // }
            // }

            // $overbudgets = $budgets->whereIn('budget_no', $overbudgets)->get();

            $stat_plan			 	= $this->sumBudgetGroupPlan($approval->budget_type, 'department', $approval->department);
            $stat_approval_total 	= round($approval->total / 1000000, 2);
            $stat_actual 			= $this->sumBudgetGroupActual($approval->budget_type, 'department', $approval->department) - $stat_approval_total;
            $stat_plan 				= $stat_plan == 0.0 ?1:$stat_plan;
			$stat_actual_percentage = round(($stat_actual / $stat_plan) * 100, 2) > 100 ? 100 : round(($stat_actual / $stat_plan) * 100, 2);

            $stat_approval_total 	+= $stat_actual;
            $stat_approval_total_percentage = round(($stat_approval_total / $stat_plan) * 100) > 100 ? 100 : round(($stat_approval_total / $stat_plan) * 100, 2);


            $statistics = array(
                                    'stat_plan' => $stat_plan,
                                    'stat_actual' => $stat_actual,
                                    'stat_actual_percentage' => $stat_actual_percentage,
                                    'stat_approval_total' => $stat_approval_total,
                                    'stat_approval_total_percentage' => $stat_approval_total_percentage
                                );
        }

        $department = $approval->department;

        return view("pages.approval.sheet", compact('approval', 'type', 'department', 'overbudgets', 'statistics'));
    }

	public function printApprovalExcel($approval_number)
    {
        if (is_null($approval = ApprovalMaster::getSelf($approval_number))) {
            $res = [
                 'title' => 'Error',
                 'type' => 'error',
                 'message' => 'Approval ['.$approval_number.'] doesn\'t exist.'
            ];

            return redirect()
                ->route('dashboard')
                ->with($res);
        }
        $dept = $approval->departments;
        $appDtl = $dept->approval->details()
            ->where('level', '4')
            ->first();

        $approverUser = $approval->approverUsers()
            ->where('user_id', $appDtl->user_id)
            ->where('is_approve', '1')
            ->first();

        if (!$approverUser) {
            $res = [
                'title' => 'Error',
                'type' => 'error',
                'message' => 'Could not print Approval ['.$approval_number.']: Director Approval required.'
            ];


            if(strtolower($approval->budget_type)=="cx") {
                return redirect()
                    ->route('approval-capex.ListApproval')
                    ->with($res);
            }else if(strtolower($approval->budget_type) == "ex") {
                return redirect()
                    ->route('approval-expense.ListApproval')
                    ->with($res);
            }
        }

        switch ($approval->budget_type) {
            case 'cx':
                $overbudget = ApprovalMaster::get_budgetInfo("cx","all",$approval_number);
                $overbudget_info = "Capex ".$overbudget."";

                $print = ApprovalDetail::selectRaw('approval_masters.*, approval_details.*, capexes.equipment_name')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->join('capexes','approval_details.budget_no','=','capexes.budget_no')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->orderBy('approval_details.budget_no')
                    ->get();

                break;

            case 'ex':
                $overbudget = ApprovalMaster::get_budgetInfo("ex","all",$approval_number);
                $overbudget_info = "Expense ".$overbudget."";

                $print = ApprovalDetail::selectRaw('approval_masters.*, approval_details.*, expenses.description as equipment_name')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->join('expenses','approval_details.budget_no','=','expenses.budget_no')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->orderBy('approval_details.budget_no')
                    ->get();

                break;

            default:
                $overbudget_info = $approval->budget_type == "uc" ? "Unbudget Capex" : "Unbudget Expense";

                $print = DB::table('approval_details')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->Select('approval_masters.*','approval_details.*')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->orderBy('approval_details.budget_no')
                    ->get();

                break;
        }

        $appVersion= 'App version = 4.3.0/ Printed by = '.\Auth::user()->name.' '.Carbon::now();

        $data  = [];
        $over = 0;
        // foreach ($print as $prints) {
        //     $tes = $prints->actual_price_user > $prints->budget_reserved ? $prints->actual_price_user - $prints->budget_reserved  : 0;
        //     $over += $tes;
        //     $tes = 0;
        // }

        $no_budget = '';
        $budget_available = 0;
        $budget_over = 0;

        foreach($print as $prints) {
            if ($no_budget != $prints->budget_no) {
                $budget_available = $prints->budget_remaining_log;
                $budget_over = $budget_available - $prints->actual_price_user;
            } else {
                $budget_over = $budget_over - $prints->actual_price_user;
            }

            // $over -= $budget_over > 0 ? 0 : $budget_over;

            
            $newDate = date("M-y", strtotime($prints->budget_type == "cx" ? $prints->settlement_date : $prints->actual_gr));
            $data[] = array(
                ($prints->budget_type == "uc" ? '-' : ($prints->budget_type == "ue" ? '-' : $prints->equipment_name)),
                $prints->pr_specs,
                $prints->sap_is_chemical,
                $prints->budget_no,
                $prints->sap_account_text,
                $prints->sap_account_code,
                $prints->actual_qty,
                $prints->pr_uom,
                $prints->sap_cc_code,
                $prints->sap_asset_no,
                $newDate,
                $prints->sap_cc_code,
                $prints->sap_cc_fname,
                $approval_number,
                $appVersion,
                //$prints->actual_price_user > $prints->budget_reserved ? 'OVER' : 'UNDER',
                $budget_over > 0 ? 'UNDER' : 'OVER',
                
                $prints->budget_remaining_log,
                //$prints->budget_reserved,
                $budget_available,
                $prints->actual_price_user,
                $over == 0 ? 'All Under Budget' : 'Over Budget Rp ' . number_format($over, 0, ',', '.'),
                // $prints->actual_price_user - $prints->budget_reserved == 0 ? '0' : $prints->actual_price_user - $prints->budget_reserved
                $budget_over > 0 ? '0' : $budget_over
            );

            if ($no_budget != $prints->budget_no) {
                $no_budget = $prints->budget_no;
                $budget_available -= $prints->actual_price_user;
                // $budget_over -= $prints->actual_price_user;
            } else {
                $budget_available -= $prints->actual_price_user;
                // $budget_over = $budget_over - $prints->actual_price_user;
                // dd($prints->actual_price_user);
            }

        }
        $excel = \PHPExcel_IOFactory::load(storage_path('template/pr_output_baru.xlsm'));
        $excel->setActiveSheetIndex(2);
        $objWorksheet2 	= $excel->getActiveSheet();
        $objWorksheet2->fromArray($data,null,'A1',false,false);

        $writer = new \PHPExcel_Writer_Excel2007($excel);

        // Save the file.
        $writer->save(storage_path().'/app/public/approval.xlsm');
        header('Location:'.url('storage/approval.xlsm'));
        exit;
    }

	public function get_print($status)
    {
        $user = \Auth::user();

        $approvals = ApprovalMaster::query()->select('approval_masters.id','departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
        ->join('departments', 'approval_masters.department', '=', 'departments.department_code');

        if ((\Entrust::hasRole('purchasing') || \Entrust::hasRole('admin')) && $status) {
            if ($status == 4) //all approve
            {
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->where('is_download', '=', 0)
                ->whereDoesntHave('approverUsers', function ($q) {
                    $q->where('is_approve', '!=', '1');
                });
            }
            else if($status == 1){ // already download
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->where('is_download', '=', 1);
            }
            else
            {
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->whereHas('approverUsers', function ($q) {
                    $q->where('is_approve', '!=', '1');
                });
            }

        }

        return DataTables::of($approvals)
        ->editColumn("total", function ($approvals) {
            return number_format($approvals->total);
        })
        ->editColumn("status", function ($approvals){
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
        ->addColumn("overbudget_info", function ($approvals) {
            return $approvals->status < 0 ? 'Canceled' : ($approvals->isOverExist() ? 'Overbudget exist' : 'All underbudget');
        })
        ->addColumn("action", function ($approvals) use($status) {
            if ($status == '0') {
                $approverUser = $approvals->approverUsers()->where('is_approve', '!=', '1')->first();

                if (!$approverUser && $approvals->status > 0) {
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="#" onclick="printApproval(&#39;'.$approvals->approval_number.'&#39;);return false;" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span></a></div>';
                }
            } elseif (($status == '1' || $status == '4') && $approvals->status > 0) {
                return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="#" onclick="printApproval(&#39;'.$approvals->approval_number.'&#39;);return false;" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span></a></div>';
            }

            return '';
        })
        ->make(true);
	}

	/*statistic*/
	public function buildJSONApprovalStatus($budget_type)
	{
		// $budget_type="cx";
        $user = auth()->user();

        if ($user->hasRole('department-head')) {
            $group_type = 'department';
            $group_name = $user->department->department_code;
        }
        elseif ($user->hasRole('gm')) {
            $group_type ='division';
            $group_name = $user->division->division_code;
        }
        elseif ($user->hasRole('director')) {
        	$group_type ='dir';
            $group_name = $user->dir;
        }
        elseif ($user->hasRole('admin') || $user->hasRole('budget')) {	// v3.5 by Ferry, 20151113, add budget role
            $group_type ='division';
            $group_name = $user->division->division_code;
        }
        else {
        	$group_type = 'department';
            $group_name = $user->department->department_code;
        }

		$budget_type_ori = $budget_type;
        if (($budget_type == 'uc') || ($budget_type == 'ue')) {
        	$budget_type = substr($budget_type, 1, 1). 'x';
        }

        $totPlan = ApprovalController::sumBudgetGroupPlan($budget_type, $group_type, $group_name);
        $totUsed = ApprovalController::sumBudgetGroupActual(array($budget_type), $group_type, $group_name);
        $totUnbudget = ApprovalController::sumBudgetGroupActual(array('u'.substr($budget_type, 0, 1)), $group_type, $group_name);
		$totDummy = ($totPlan - ($totUsed + $totUnbudget)) <= 0 ? 0 : ($totPlan - ($totUsed + $totUnbudget));


        if (($budget_type_ori == 'uc') || ($budget_type_ori == 'ue')) {
        	$totOutlook = ApprovalMaster::get_pending_sum($budget_type_ori, $group_type, $group_name);
        }
        else {
        	$totOutlook = ApprovalMaster::get_pending_sum($budget_type, $group_type, $group_name);
        }

        $attrStack = (($totUsed + $totUnbudget) <= $totPlan) ? "percent" : "normal";
        $attrTick = (($totUsed + $totUnbudget) <= $totPlan) ? 20 : null;
        $attrPlanTitle = (($totUsed + $totUnbudget) <= $totPlan) ? "Plan" : "Plan (Overbudget)";
        $attrPlanColor = (($totUsed + $totUnbudget) <= $totPlan) ? 0 : 5;

		$arrJSON = array(
							["totPlan"		=> array($totPlan)],
							["totUsed"		=> array($totUsed)],
							["totUnbudget"	=> array($totUnbudget)],
							["totDummy"		=> array($totDummy)],
							["totOutlook"	=> array($totOutlook)],
							["attrStack"	=> $attrStack,
							 "attrTick"		=> $attrTick,
							 "attrPlanTitle"	=> $attrPlanTitle,
							 "attrPlanColor"	=> $attrPlanColor]
			    		);

		return $arrJSON;
	}
}
