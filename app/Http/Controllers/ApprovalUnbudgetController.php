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
use Carbon\Carbon;

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
                                        'budget_no'         => $unbudget->options->token.'<input type="hidden" class="checklist">',
                                        'project_name'      => $unbudget->name,
                                        'price_remaining'   => number_format($unbudget->options->actual_price_user),
                                        'qty_actual'        => $unbudget->options->actual_qty,
                                        'plan_gr'           => $unbudget->options->actual_gr,
                                        'type'              => $unbudget->options->type,
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
        $sap_assets         = SapAsset::where('asset_type',$request->sap_asset_id)->where('asset_code', $request->sap_code_id)->first();
        $sap_gl_acc         = SapGlAccount::where('gl_gname', $request->sap_gl_account_id)->where('gl_aname', $request->gl_fname)->first();
        $sap_costs          = SapCostCenter::find($request->sap_cost_center_id); 
		$sap_uoms           = SapUom::find($request->sap_uom_id);
        // $item 				= Item::find($request->remarks);
        $item 				= Item::firstOrNew(['item_description' => $request->remarks]);
        $item->item_description = $request->remarks;
        $item->item_category_id = '0';
        $item->item_code = 'XXX';
        $item->item_price = str_replace(',','',$request->price_actual);
        $item->uom_id = $sap_uoms->id;
        $item->supplier_id = '0';
        $item->save();
        
        
        
        $cartData 			= [

									'id' => "ub",//$budget->budget_no
									'name' => $request->project_name,
									'price' => str_replace(',','',$request->price_actual),
									'qty' => 1,
									'options' => [
                                        'token'             => $request->_token,
                                        'budget_no'         => "-",
                                        'asset_code'        => $request->sap_code_id,
                                        'sap_is_chemical'   => $request->asset_category,
                                        'sap_cc_code'       => $sap_costs->cc_code,
                                        'sap_cc_fname'      => $sap_costs->cc_fname,
                                        'actual_qty'        => !empty($request->qty_actual) ? $request->qty_actual : 1,
                                        'actual_price_user' => str_replace(',','',$request->price_actual),
                                        'currency'          => !empty($request->currency) ? $request->currency : 'IDR' ,
                                        'actual_gr'         => $request->plan_gr,
                                        'remarks'           => $item->item_description,
                                        'item_id'           => $request->remarks,
                                        'pr_specs'          => $request->pr_specs,
                                        'pr_uom'            => $sap_uoms->uom_sname,
									]
								];
								
		if($request->type == "1"){
            $cartData['options']['budget_type'] = "uc";
            $cartData['options']['sap_asset_class']     = $sap_assets->asset_class;                               
			$cartData['options']['sap_account_code']    = $sap_assets->asset_account;
            $cartData['options']['sap_account_text']    = $sap_assets->asset_acctext;
            $cartData['options']['type']                = "Unbudget CAPEX";
		}else{
			$cartData['options']['budget_type'] = "ue";
			$cartData['options']['sap_account_code'] 	= $sap_gl_acc->gl_acode;
			$cartData['options']['sap_account_text'] 	= $sap_gl_acc->gl_aname;
			$cartData['options']['type']                = "Unbudget EXPENSE";
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
        $sap_gl_group = SapGlAccount::select('gl_aname as id', 'gl_aname as text')->where('gl_gname',$id)->where('dep_key',auth()->user()->department->department_code)->get();
       
        $result = [];
        foreach ($sap_gl_group as $group) {
            $result[]=['id' => $group->text, 'text' => $group->text];
        }

        return response()->json($result);
    }
    public function getAsset($id)
    {
        $sap_asset = SapAsset::select('asset_code as id', 'sap_assets.asset_name as text')->where('asset_type', $id)->get();
       
        $result = [];
        foreach ($sap_asset as $asset) {
            $result[] = ['id' => $asset->id, 'text' => $asset->text];
        }

        return response()->json($result);
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
                                ->whereIn('budget_type',['ub', 'uc','ue']);
                                                      
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
		$approver   = $this->can_approve($approval_number);
		$master 	= ApprovalMaster::getSelf($approval_number);
		return view('pages.approval.unbudget.view',compact('master','approver'));
    }
    
	public function AjaxDetailApproval($approval_number)
	{
        $approval_master = ApprovalMaster::select('*','approval_details.id as id_ad','approval_details.sap_cc_code as ad_sap_cc_code')
                        ->join('approval_details','approval_masters.id','=','approval_details.approval_master_id')
						->where('approval_number',$approval_number); 
		
		 return DataTables::of($approval_master)
				->editColumn("asset_no", function ($approval) {
					return $approval->asset_no.'<input class="approval_data" type="hidden" value="'.$approval->id_ad.'">';
                })
                ->editColumn('budget_remaining_log', function($approval){
                    return number_format($approval->budget_remaining_log);
                })
                ->editColumn('budget_reserved', function($approval){
                    return number_format($approval->budget_reserved);
                })
                ->editColumn('actual_price_user', function($approval){
                    return number_format($approval->actual_price_user);
                })
                ->editColumn('price_to_download', function($approval) {
                    return number_format($approval->price_to_download);
                })
                ->addColumn("status", function ($approval) {
                    return $approval->budget_type = 'uc' ? 'Unbudget Capex' : 'Undbudget Expense';
                }) 
                ->addColumn("actual_gr", function ($approval) {
                    return Carbon::parse($approval->actual_gr)->format('d M Y');
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
                
                $user = \Auth::user();
                $budget_type = Cart::instance('unbudget')->content()->first()->options->budget_type;

                $approval_no = ApprovalMaster::getNewApprovalNumber(strtoupper($budget_type), $user->department->department_code); 
                
                    $capex                         = new ApprovalMaster;
                    $capex->fyear 				   =  date('Y');
                    $capex->approval_number        = $approval_no;
                    $capex->budget_type            = $budget_type;
                    $capex->dir                    = $user->direction;
                    $capex->division               = $user->division->division_code;
                    $capex->department             = $user->department->department_code;
                    $capex->total                  = str_replace(',', '', Cart::instance('unbudget')->subtotal($formatted = false));
                    $capex->status                 = 0;
                    $capex->created_by             = $user->id;
					
                    $capex->save();
                    $i = 1;
                foreach (Cart::instance('unbudget')->content() as $details) {
                    $approval                           = new ApprovalDetail;
                    $approval->fyear                    = date('Y');
                    $approval->budget_no                = $details->options->budget_no;  
                    $approval->sap_track_no             = ApprovalMaster::getNewSapTrackingNo(2,$user->department_id,$approval_no,$i);
                    
                    if($details->options->budget_type == "uc"){
                        $approval->asset_no             = $details->options->asset_code.'JE'.str_pad($i, 3, '0', STR_PAD_LEFT);
                        $approval->sap_asset_class      = $details->options->sap_asset_class;
                        $approval->sap_account_code     = $details->options->sap_account_code;
                        $approval->sap_account_text     = $details->options->sap_account_text;
					}else{
						$approval->sap_account_code     = $details->options->sap_account_code;
						$approval->sap_account_text		= $details->options->sap_account_text;
                    }
                    $approval->sap_is_chemical          = $details->options->sap_is_chemical;
                    $approval->sap_cc_code     		    = $details->options->sap_cc_code;
                    $approval->sap_cc_fname             = $details->options->sap_cc_fname;
                    $approval->project_name             = $details->name;
                    $approval->actual_qty               = $details->options->actual_qty;
                    $approval->actual_price_user        = $details->options->actual_price_user;
                    $approval->price_to_download        = $details->options->actual_price_user;
                    $approval->currency                 = $details->options->currency;
                    $approval->actual_gr                = date('Y-m-d',strtotime($details->options->actual_gr));
                    $approval->remarks                  = $details->options->remarks;
					$approval->item_id 				    = $details->options->item_id;
                    $approval->pr_specs				    = $details->options->pr_specs;
                    $approval->pr_uom           	    = $details->options->pr_uom;                   

                    $capex->details()->save($approval);
                    $i++;
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
