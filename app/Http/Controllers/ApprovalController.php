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
use App\SapModel\SapAsset;           
use App\SapModel\SapCostCenter;           
use App\SapModel\SapGlAccount;           
use App\SapModel\SapUom;           
use App\Expense_archive;    
use App\Approval_master;    
use App\ApprovalDetail;
use App\Cart;
use App\Approval;
use Cart as Carts;
use App\ApproverUser;
use Excel;
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
    	return view('pages.approval.capex.create-approval',compact(['approval']));
    }
    public function create()
    {
        $sap_assets      = SapAsset::get();
        $sap_costs       = SapCostCenter::get(); 
        $sap_gl_group    = SapGlAccount::get();
        $sap_uoms        = SapUom::get();
        $capexs          = Capex::where('department', auth()->user()->department->department_code)->get();
		$carts 			 = Cart::where('user_id', auth()->user()->id)->get();
    	return view('pages.approval.capex.create', compact(['sap_assets','sap_costs','sap_gl_group', 'sap_uoms', 'capexs', 'carts']));
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
        $sap_gl_account  = SapGlAccount::get();
        $sap_uoms        = SapUom::get();
        $expenses        = Expense::where('department', auth()->user()->department->department_code)->get();
		$carts 			 = Cart::where('user_id', auth()->user()->id)->get();
        return view('pages.approval.expense.create', compact(['sap_assets','sap_costs','sap_gl_account', 'sap_uoms', 'expenses','carts']));
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
        $sap_assets      = SapAsset::get();
        $sap_costs       = SapCostCenter::get(); 
        $sap_gl_account  = SapGlAccount::get();
        $sap_uoms        = SapUom::get();
        $expenses        = Expense::where('department', auth()->user()->department->department_code)->get();
		$capexs          = Capex::where('department', auth()->user()->department->department_code)->get();
		$carts 			 = Cart::where('user_id', auth()->user()->id)->get();
        return view('pages.approval.unbudget.create',compact(['sap_assets','sap_costs','sap_gl_account', 'sap_uoms', 'expenses', 'capexs','carts']));
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
		$budget_nos 	= $this->getCIPAdminListConvert();
		$budget_nos_cip = $this->getCIPAdminListConvert('cip'); 
		
        return view("pages.capex.cip-admin",compact('budget_nos','budget_nos_cip'));
    }
	public function getCIPAdminListConvert($mode='one-time',$control="data") {
        
        $user = auth()->user();
		
        if ($user->hasRole('budget')) {
			$approvals = ApprovalDetail::query();
            $approvals->whereIn('division', [$user->division->division_code]);
            if ($mode == 'one-time') {
                $approvals = $approvals->whereNull('cip_no');
            }
            elseif ($mode == 'cip') {
                $approvals = $approvals->whereNotNull('cip_no');
            }
			
            $approvals = $approvals->orderBy('budget_no')
                                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id' )
                                    ->where('budget_type', 'cx')
                                    ->select('budget_no')
                                    ->distinct()->get();
			
			if($control == "data"){
				return $approvals;
			}else{
				return $this->getCIPFormatted($control, $approvals);
			}
        }else {
            return $data[''] = '';
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
					$approval->settlement_date = $request->settlement_date;
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
					$approval->settlement_date = $request->new_settlement_date;
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
        if (\Entrust::hasRole('department_head')) {
            $approvals->whereIn('department',[$user->department->department_code]);
        }
        elseif (\Entrust::hasRole('gm')) {
            $approvals->where('division', $user->division->division_code);
        }
        elseif (\Entrust::hasRole('director')) {
            $approvals->where('dir', $user->dir);
        }
        elseif (\Entrust::hasRole('budget')) {
            $approvals->whereIn('division', [$user->division->division_code]);
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
	
	public function approveAjax(Request $request)
	{
		$data = array('success'=>'Approval ['.$request->approval_number.'] approved.');
		
		try{
			
		 DB::transaction(function() use ($request){
			$user = auth()->user(); 
            $approval = ApprovalMaster::getSelf($request->approval_number);
			// if(Helpers::can($approval))
			// {
				$approver_user = ApproverUser::where('approval_master_id',$approval->id)->where('user_id',$user->id)->update(array('is_approve'=>'1'));
				
			// }
            $approval->approve();
			
            if ($approval->budget_type != 'ub' && $approval->budget_type != 'uc' && $approval->budget_type != 'ue' && $approval->status == 3) {
				
                $actual_prices = [];

                foreach ($approval->details as $detail) {
                    
                    if(is_null($detail)){
                        $data['error']	="Master Budget No: ".$detail->budget_no." is Deleted by Finance.\nPlease Contact Finance Department";
                        return $data;
                    }
                    
                    $detail->budget_remaining -= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                    $detail->budget_used += $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                    if ($approval->budget_type == 'ex') {
                        
                        $detail->qty_remaining -= $detail->actual_qty;

                        $detail->qty_used += $detail->actual_qty;
                    }

                    $detail->status = $detail->budget_remaining >= 0 ? 0 : 1;
                     
                    $detail->is_closed = $detail->budget_remaining > 0 ? 0 : 1;
                    $detail->save();
                }
            }
            $approval->save();
			
		 });
		 
		}catch(\Exception $e){
			$data['error'] = $e->getMessage();
		}
		
		return $data;
	}
	public function cancelApproval(Request $request)
	{
		
		$data = array('success'=>'Approval ['.$request->approval_number.'] approved.');
		
		try{
			
			 DB::transaction(function() use ($request){
				 
				$approval = ApprovalMaster::getSelf($request->approval_number);
				
				if (($approval->budget_type != 'uc') && ($approval->budget_type != 'ue')) {
					
					foreach ($approval->details as $detail) {
						$detail->budget_reserved = 0;
						$detail->save();
					}

					if ($approval->status > 2) {
						$actual_prices = [];
						foreach ($approval->details as $detail) {

							$detail->budget_remaining_log += $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

							// $detail->budget_used -= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

							if ($approval->budget_type == 'ex') {
								
								$detail->qty_remaining += $detail->actual_qty;

								// $detail->qty_used -= $detail->actual_qty;
							}

							// $detail->status 	= $detail->budget_remaining_log >= 0 ? 0 : 1;

							// $detail->is_closed 	= 1;//$detail->status == 0 ? 0 : 1;

							$detail->save();
						}
					}
				}
				$approval->cancel();
				$approval->save();
				
			 });
			 
		}catch(\Exception $e){
			$data['error'] = $e->getMessage();
		}
		
		return $data;
	}
	public function printApproval($approval_number)
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

        $user = auth()->user();
      
        $sap_cc_query = DB::table('sap_cost_centers')
                    ->Select('cc_code','cc_fname')
                      ->Where('cc_code' ,'=', $user->sap_cc_code)
                      ->get();

        foreach ($sap_cc_query as $sap_cc_query) {
            $cc_code  = $sap_cc_query->cc_code;
            $cc_fname = $sap_cc_query->cc_fname;
        }
		
		
        switch ($approval->budget_type) {
            case 'cx':
                $type = 'Capex';
                $overbudget = ApprovalMaster::get_budgetInfo("cx","all",$approval_number);
                $overbudget_info = "Capex ".$overbudget."";
				
                $print = ApprovalDetail::selectRaw('approval_masters.*, approval_details.*, capexes.equipment_name')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->join('capexes','approval_details.budget_no','=','capexes.budget_no')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->get();
            
                break;

            case 'ex':
                $type = 'Expense';
                $overbudget = ApprovalMaster::get_budgetInfo("ex","all",$approval_number);
                $overbudget_info = "Expense ".$overbudget."";

                $print = ApprovalDetail::selectRaw('approval_masters.*, approval_details.*, expenses.description as equipment_name')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->join('expenses','approval_details.budget_no','=','expenses.budget_no')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->get();

                break;

            default:

                $type = 'Unbudget';
                $overbudget_info = $approval->budget_type == "uc" ? "Unbudget Capex" : "Unbudget Expense";
  
                $print = DB::table('approval_details')
                    ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                    ->Select('approval_masters.*','approval_details.*')
                    ->where('approval_masters.approval_number',$approval_number)
                    ->get();

                break;
        }
		
        $appVersion= 'App version : 4.3.0, Printed by : '.\Auth::user()->name.' '.Carbon::now().'';

        $data = array();

            foreach($print as $prints) {
                 $newDate = date("M-y", strtotime($prints->budget_type == "cx" ? $prints->settlement_date : $prints->actual_gr));
                 $data[] = array(
                    $prints->budget_type == "uc" ? '-' : $prints->budget_type == "ue" ? '-' : $prints->equipment_name, 
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
                    $overbudget_info
                );
            }
            
       
        // \Excel::load('/storage/template/pr_output.xlsm', function($excel) use ($data){
            // $excel->sheet('Data', function($sheet) use ($data) {
                // $sheet->fromArray($data, null, 'A1', false, false);
            // });
			
        // })->setFilename($approval_number)
          // ->download('xlsm');
		

		// return $data;  
		
		 return Excel::create($approval_number, function($excel) use ($print,$approval_number,$appVersion,$overbudget_info){
             $excel->sheet('mysheet', function($sheet) use ($print,$approval_number,$appVersion,$overbudget_info){
				$i = 1;
				foreach($print as $p){
					$sheet->cell('A'.$i, function($cell) use ($p) {$cell->setValue($p->budget_type == "uc" ? '-' : $p->budget_type == "ue" ? '-' : $p->equipment_name);});
					$sheet->cell('B'.$i, function($cell) use ($p) {$cell->setValue($p->pr_specs);});
					$sheet->cell('C'.$i, function($cell) use ($p) {$cell->setValue($p->sap_is_chemical);});
					$sheet->cell('D'.$i, function($cell) use ($p) {$cell->setValue($p->budget_no);});
					$sheet->cell('E'.$i, function($cell) use ($p) {$cell->setValue($p->sap_account_text);});
					$sheet->cell('F'.$i, function($cell) use ($p) {$cell->setValue($p->sap_account_code);});
					$sheet->cell('G'.$i, function($cell) use ($p) {$cell->setValue($p->actual_qty);});
					$sheet->cell('H'.$i, function($cell) use ($p) {$cell->setValue($p->pr_uom);});
					$sheet->cell('I'.$i, function($cell) use ($p) {$cell->setValue($p->sap_cc_code);});
					$sheet->cell('J'.$i, function($cell) use ($p) {$cell->setValue($p->sap_asset_no);});
					$sheet->cell('K'.$i, function($cell) use ($p) {$cell->setValue(date("M-y", strtotime($p->budget_type == "cx" ? $p->settlement_date : $p->actual_gr)));});
					$sheet->cell('L'.$i, function($cell) use ($p) {$cell->setValue($p->sap_cc_code);});
					$sheet->cell('M'.$i, function($cell) use ($p) {$cell->setValue($p->sap_cc_fname);});
					$sheet->cell('N'.$i, function($cell) use ($approval_number) {$cell->setValue($approval_number);});
					$sheet->cell('O'.$i, function($cell) use ($appVersion) {$cell->setValue($appVersion);});
					$sheet->cell('P'.$i, function($cell) use ($overbudget_info) {$cell->setValue($overbudget_info);});
					$i++;
				}
             });
			
        })->download('csv');
		
    }
	
	public function get_print($status)
    {
        $user = \Auth::user();

        $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
        ->join('departments', 'approval_masters.department', '=', 'departments.department_code');

        if ( \Entrust::hasRole('purchasing') && $status) { 
            if ($status == 4) //dir 
            {
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->where('status', '=', $status)
                ->where('is_download', '=', 0);
            }
            else if($status == 1){ // bgt
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->where('is_download', '=', 1);
            }              
            else
            {
                $approvals = ApprovalMaster::query()->select('departments.department_name','approval_masters.approval_number','approval_masters.total','approval_masters.status','budget_type')
                ->join('departments', 'approval_masters.department', '=', 'departments.department_code')
                ->where('status', '<=' , $status);              
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
        ->addColumn("action", function ($approvals) {
            return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="#" onclick="printApproval(&#39;'.$approvals->approval_number.'&#39;);return false;" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span></a></div>';
        })
        ->make(true);  
	}
}
