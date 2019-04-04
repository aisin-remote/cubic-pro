<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\SapModel\SapAsset;
use App\SapModel\SapGlAccount;           
use App\SapModel\SapCostCenter;           
use App\SapModel\SapUom;
use App\ApprovalMaster;
use App\ApprovalDetail;
use App\Approval;
use App\ApprovalDtl;
use App\ApproverUser;
use DB;
use DataTables;
use Cart;
use App\Item;
use App\Cart as Carts;
class ApprovalUnbudgetController extends Controller
{
    public function getData()
    {
        $unbudgets = Cart::instance('unbudget')->content();

        if (Cart::count() > 0) {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = Cart::count();
            $result['recordsFiltered'] = Cart::count();

            foreach ($unbudgets as $unbudget) {

                $result['data'][] = [
                                        'budget_no'         => $unbudget->options->budget_no.'<input type="hidden" class="checklist">',
                                        'project_name'      => $unbudget->name,
                                        'price_remaining'   => $unbudget->price,
                                        'qty_actual'        => $unbudget->qty,
                                        'plan_gr'           => $unbudget->options->plan_gr,
                                        'option' => ' 
                                            <button class="btn btn-danger btn-xs btn-bordered" onclick="onDelete(\''.$unbudget->rowId.'\')" data-toggle="tooltip" title="Hapus"><i class="mdi mdi-close"></i></button>'
                                    ];
            }

        } else {
            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $result['data'] = [];
        }

        return $result;
    }

    public function store(Request $request)
    {
		$sap_uoms           = SapUom::find($request->sap_uom_id);
		$item 				= Item::find($request->remarks);
		$cartData 			= [

									'id' => "ub",//$budget->budget_no
									'name' => $request->project_name,
									'price' => $request->price_actual,
									'qty' => $request->qty_item,
									'options' => [
										'budget_no' => "",//$budget->budget_no,									
										'sap_account_code' => $request->sap_gl_account_id,
										'sap_account_text'	=> $request->gl_fname,
										// 'budget_description'    => $request->budget_description,
										'qty_remaining' => "",//$request->qty_remaining,	
										'qty_actual' => $request->qty_actual,
										'remarks' => $item->item_description,
										'item_id' => $item->id,
										'sap_cost_center_id' => $request->sap_cos_center,//$request->sap_cos_center_id,
										'sap_uom_id' => $sap_uoms->uom_sname,
										'price_actual' => $request->price_actual,
										'budget_remaining_log' => "",//$request->budget_remaining_log,
										'price_to_download' => $request->price_to_download,
										'plan_gr' => $request->plan_gr,
										'pr_specs'	=> $request->pr_specs,
										'currency'=> $request->currency,
										'is_chemical' => $request->asset_category,
									]
								];
								
		if($request->type == "1"){
			$cartData['id'] = "uc";
			$cartData['options']['sap_asset'] 		= $request->sap_asset_id;
			$cartData['options']['asset_code'] 		= $request->asset_code;
		}else{
			$cartData['id'] = "ue";
			$cartData['options']['sap_gl_account'] 	= $request->sap_gl_account_id;
			$cartData['options']['gl_fname'] 		= $request->gl_fname;
			
		}
		
        Cart::instance('unbudget')->add($cartData);
		Carts::where('item_id',$item->id)->where('user_id',auth()->user()->id)->delete();

        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been inserted'
                ];
		
        return redirect()
                        ->route('approval-unbudget.index')
                        ->with($res);
    }

    function show($id)
    {
        dd(Cart::count());

    }

    function destroy($id)
    {
        Cart::remove($id);
		 $res = [
					'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been removed'
                ];
        return response()
                ->json($res);

    }

    public function getOne($id)
    {
        $expense = Expense::find($id);
        return response()->json($expense);

    }

    public function getGlGroup($id)
    {
        $sap_gl_group = SapGlAccount::find($id);
        return response()->json($sap_gl_group);
    }
    public function getAsset($id)
    {
        $sap_asset = SapAsset::find($id);
        return response()->json($sap_asset);
    }
	public function ListApprovalUnvalidated()
	{
		return view('pages.approval.unbudget.list-approval');
	}
	 public function ListApproval()
    {

        return view('pages.approval.unbudget.index-admin');
    }
	public function getApprovalUnbudget($status){
		
        $type = 'ub';
        $user = auth()->user();
        $approval_ub = ApprovalMaster::with('departments')
								->whereIn('budget_type',['ub,uc','ue']);
		if(\Entrust::hasRole('user')) {
			$approval_ub->where('created_by',$user->id);
		}

        if (\Entrust::hasRole('department_head')) {
            $approval_ub->whereIn('department', [$user->department->department_code]);
        }

        if (\Entrust::hasRole('gm')) {
            $approval_ub->where('division', $user->division->division_code);
        }

        if (\Entrust::hasRole('director')) {
            $approval_ub->where('dir', $user->dir);
        }
		if($status == 'need_approval'){
			$approval_ub->where('status','0');
		}
		
        $approval_ub = $approval_ub->get();
		
        return DataTables::of($approval_ub)
        ->rawColumns(['action'])
	
        ->addColumn("action", function ($approvalub) use ($type, $status){
            if($status!='need_approval'){
				
                if(\Entrust::hasRole('user')) {
                    return '
                        <div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/ub/'.$approvalub->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

                        <a href="#" onclick="printApproval(&#39;'.$approvalub->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>

                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete(\''.$approvalub->id.'\')"><i class="mdi mdi-close"></i></button>
                        <form action="'.route('approval_unbudget.delete', $approvalub->id).'" method="POST" id="form-delete-'.$approvalub->id .'" style="display:none">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                        </form>';
                }elseif(\Entrust::hasRole('budget')) { //Sebenarnya ini ga bakal dieksekusi
					return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/ub/'.$approvalub->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approvalub->approval_number.'&#39;);return false;"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
                }else{
                    return "<div id='$approvalub->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/ub/'.$approvalub->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                }
            }else{
                // return "else";
                // return "<div id='$approvalub->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/ub/'.$approvalub->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a  href='#' onclick='javascript:validateApproval(&#39;$approvalub->approval_number&#39;);return false;'class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a><a href=\"#\" onclick=\"cancelApproval('$approvalub->approval_number');return false;\" class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
				return "<div id='$approvalub->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/ub/'.$approvalub->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a href='#' class='btn btn-danger' onclick='cancelApproval(&#39;".$approvalub->approval_number."&#39;);return false;'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
            }
        })

        ->editColumn("total", function ($approvalub) {
                return number_format($approvalub->total);
            })
        ->editColumn("status", function ($approvalub){ 
            if ($approvalub->status == '0') {
                return "User Created";
            }elseif ($approvalub->status == '1') {
                return "Validasi Budget";
            }elseif ($approvalub->status == '2') {
                return "Approved by Dept. Head";
            }elseif ($approvalub->status == '3') {
                return "Approved by GM";
            }elseif ($approvalub->status == '4') {
                return "Approved by Director";
            }elseif ($approvalub->status == '-1') {
                return "Canceled on Quotation Validation";
            }elseif ($approvalub->status == '-2') {
                return "Canceled Dept. Head Approval";
            }else{
                return "Canceled on Group Manager Approval";
            }
        })

        ->addColumn("overbudget_info", function ($approvalub) {
            return $approvalub->status < 0 ? 'Canceled' : ($approvalub->isOverExist() ? 'Overbudget exist' : 'All underbudget');
        }) 

        ->addColumn('details_url', function($approvalub) {
            return url('approval-capex/details-data/' . $approvalub->id);
        })

        ->toJson();
    }
	public function DetailApproval($approval_number)
	{
		$master = ApprovalMaster::getSelf($approval_number);
		return view('pages.approval.unbudget.view',compact('master'));
	}
	public function AjaxDetailApproval($approval_number)
	{
		$approval_master = ApprovalMaster::select('*','approval_details.id as id_ad','approval_details.sap_cc_code as ad_sap_cc_code')->join('approval_details','approval_masters.id','=','approval_details.approval_master_id')
						->where('approval_number',$approval_number); 
		
		 return DataTables::of($approval_master)
				->editColumn("asset_no", function ($approval) {
					return $approval->asset_no.'<input class="approval_data" type="hidden" value="'.$approval->id_ad.'">';
				})
				->editColumn("status", function ($approval){ 
					//status approval
					if ($approval->status == '0') {
						return "User Created";
					}elseif ($approval->status == '1') {
						return "Validasi Budget";
					}elseif ($approval->status == '2') {
						return "Approved by Dept. Head";
					}elseif ($approval->status == '3') {
						return "Approved by GM";
					}elseif ($approval->status == '4') {
						return "Approved by Director";
					}elseif ($approval->status == '-1') {
						return "Canceled on Quotation Validation";
					}elseif ($approval->status == '-2') {
						return "Canceled Dept. Head Approval";
					}else{
						return "Canceled on Group Manager Approval";
					}
				})->toJson();
	}
	public function delete($id)
    {
        DB::transaction(function() use ($id){
            $approval_unbudget = ApprovalMaster::find($id);
            $approval_unbudget->details()->delete();
            $approval_unbudget->delete();
        });
		 $res = [
					'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been removed!'
                ];
        return redirect()
                    ->route('approval-unbudget.ListApproval')
                    ->with($res);
    }
	public function SubmitApproval(Request $request)
    {      
            $res = '';


            DB::transaction(function() use ($request, &$res){
                // Save data in Tabel Bom
                $user = \Auth::user();
                
                foreach (Cart::instance('unbudget')->content() as $details) {
                    $approval_no = ApprovalMaster::getNewApprovalNumber(strtoupper($details->id), $user->department_id);  
                
                    $capex                         = new ApprovalMaster;
                    $capex->approval_number        = $approval_no;
                    $capex->budget_type            = $details->id;
                    $capex->dir                    = $user->direction;
                    $capex->division               = $user->division->division_code;
                    $capex->department             = $user->department->department_code;
                    $capex->total                  = $details->price;
                    $capex->status                 = 0;
                    $capex->created_by             = $user->id;
					$capex->fyear 				   =  date('Y');
                    $capex->save();
                    $approval                        = new ApprovalDetail;
                    $approval->budget_no             = $details->options->budget_no;
                    $approval->project_name          = $details->name;
                    $approval->actual_qty            = $details->qty;
                    $approval->actual_price_user     = $details->price;
					if($details->id == "uc"){
						
					}else{
						$approval->sap_account_code      = $details->options->sap_gl_account;
						$approval->sap_account_text		 = $details->options->gl_fname;
					}
                    // $approval->qty_remaining         = $details->options->qty_remaining;
                    // $approval->qty_actual            = $details->options->qty_actual;
                     $approval->remarks               = $details->options->remarks;
					$approval->item_id 				 = $details->options->item_id;
                    $approval->sap_cc_code     		 = $details->options->sap_cost_center_id;
                    $approval->pr_uom           	 = $details->options->sap_uom_id;
					$approval->pr_specs				 = $details->options->pr_specs;
					$approval->sap_is_chemical        = $details->options->is_chemical;
                    // $approval->price_remaining       = $details->options->price_actual;
                    $approval->budget_remaining_log  = $details->options->budget_remaining_log;
                    $approval->price_to_download     = $details->options->price_to_download==""?0:$details->options->price_to_download;
                    $approval->actual_gr             = date('Y-m-d',strtotime($details->options->plan_gr));
                    $approval->fyear                 = date('Y');
                    $approval->budget_reserved       = $details->options->budget_remaining_log;
                    $capex->details()->save($approval);
                }
				// Simpan approver user
				$approval_master = ApprovalMaster::where('created_by',$user->id)->where('status',0)->get();
				$approvals = Approval::where('department',$user->department->department_code)->first();
				if(empty($approvals)){
					$res = [
							'title' => 'Error',
							'type' => 'error',
							'message' => 'There is no approval for your department'
							];
				}else{
					foreach($approval_master as $am){
						
						$approval_dtl 	 = ApprovalDtl::where('approval_id',$approvals->id)->get();
						
						foreach($approval_dtl as $app_dtl){
							$approver_user = new ApproverUser();
							$approver_user->approval_master_id  = $am->id;
							$approver_user->user_id  			= $app_dtl->user_id;
							$approver_user->save();
						}
					}
					$res = [
							'title' => 'Success',
							'type' => 'success',
							'message' => 'Data has been inserted'
						];
					Cart::instance('unbudget')->destroy();
				}

                
               
            });
         return redirect()
                            ->route('approval-unbudget.ListApproval')
                            ->with($res);
    }
	public function getDelete(Request $request)
	{
		Cart::instance('unbudget')->remove($request->rowid);
		 $res = [
                    'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been removed'
                ];
		return json_encode($res);
	}
}
