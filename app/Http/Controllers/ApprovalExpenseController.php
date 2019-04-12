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
use App\Cart as Carts;
use App\Item;
class ApprovalExpenseController extends Controller
{
    public function getData()
    {
        $expenses = Cart::instance('expense')->content();

        if (Cart::count() > 0 ) {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = Cart::count();
            $result['recordsFiltered'] = Cart::count();
			
            foreach ($expenses as $expense) {

                $result['data'][] = [
                                    'budget_no'         => $expense->options->budget_no.'<input type="hidden" class="checklist">',
                                    'project_name'      => $expense->name,
                                    'price_remaining'   => $expense->price,
                                    'pr_specs'          => $expense->qty,
                                    'plan_gr'           => $expense->options->plan_gr,
                                    'option' => ' 
                                        <button class="btn btn-danger btn-xs btn-bordered" onclick="onDelete(\''.$expense->rowId.'\')" data-toggle="tooltip" title="Hapus"><i class="mdi mdi-close"></i></button>'
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

        $expenses           = Expense::find($request->budget_no);
        $sap_gl_account     = SapGlAccount::find($request->sap_gl_account_id);
        $sap_assets         = SapAsset::find($request->sap_asset_id);
        $sap_costs          = SapCostCenter::find($request->sap_cost_id); 
        $sap_uoms           = SapUom::find($request->sap_uom_id);
		$item 				= Item::find($request->remarks);

        Cart::instance('expense')->add([

                    'id' => $request->budget_no,
                    'name' => $request->project_name,
                    'price' => $request->price_remaining,
                    'qty' => $request->qty_item,
                    'options' => [
                        'budget_no'             => $expenses->budget_no,
                        'sap_account_code'      => $request->sap_gl_account_id,
						'sap_account_text'		=> $request->gl_fname,
                        'budget_description'    => $request->budget_description,
                        'qty_remaining'         => $request->qty_remaining,
                        'qty_actual'            => $request->qty_actual,
                        'remarks'               => $item->item_description,
						'item_id'				=> $item->id,
                        'sap_cos_center_id'     => $request->sap_cos_center_id,
                        'sap_uom_id'            => $sap_uoms->uom_sname,
                        'price_actual'          => $request->price_actual,
                        'budget_remaining_log'  => $request->budget_remaining_log,
						'currency'				=> $request->currency,
                        'price_to_download'     => $request->price_to_download,
                        'plan_gr'               => $request->plan_gr,
						'pr_specs'				=> $request->pr_specs,
						'is_chemical'			=> $request->asset_category,
						
                    ]
                ]);
		Carts::where('item_id',$item->id)->where('user_id',auth()->user()->id)->delete();

        $res = [
					'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been inserted'
                ];

        return redirect()
                        ->route('approval-expense.index')
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
        $sap_gl_group = SapGlAccount::where('gl_gcode',$id)->first();
        return response()->json($sap_gl_group);
    }

    public function SubmitApproval(Request $request)
    {
        
        
            $res = '';


            DB::transaction(function() use ($request, &$res){
                // Save data in Tabel Bom
                $user = \Auth::user();
                

                foreach (Cart::instance('expense')->content() as $details) {
                    $approval_no = ApprovalMaster::getNewApprovalNumber('EX', $user->department->department_code);  
                
                    $capex                         = new ApprovalMaster;
                    $capex->approval_number        = $approval_no;
                    $capex->budget_type            = 'ex';
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
                    // $approval->actual_qty            = $details->qty;
					$approval->actual_qty            = $details->options->qty_actual;
                    $approval->actual_price_user     = $details->price;
                    $approval->sap_account_code      = $details->options->sap_account_code;
					$approval->sap_account_text		 = $details->options->sap_account_text;
                    // $approval->qty_remaining         = $details->options->qty_remaining;
                    $approval->sap_is_chemical        = $details->options->is_chemical;
                    $approval->remarks               = $details->options->remarks;
					$approval->item_id 				 = $details->options->item_id;
					$approval->pr_specs 			 = $details->options->pr_specs;
                    $approval->sap_cc_code     		 = $details->options->sap_cost_center_id;
                    $approval->pr_uom            	 = $details->options->sap_uom_id;
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
				   Cart::instance('expense')->destroy();
				}
                

              
               
            });
         return redirect()
                            ->route('approval-expense.ListApproval')
                            ->with($res);
    }
	public function ListApprovalUnvalidated()
	{
		return view('pages.approval.expense.list-approval');
	}
    /**
     * Display the specified resource.
     *
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */
    public function ListApproval()
    {

        return view('pages.approval.expense.index-admin');
    }
    public function getApprovalExpense($status){
        $type 	= 'ex';

        $user = \Auth::user();
		$approval_expense = ApprovalMaster::with('departments')
                                ->where('budget_type', 'like', 'ex%');
		if(\Entrust::hasRole('user')) {
			$approval_expense->where('created_by',$user->id);
		}

        if (\Entrust::hasRole('department_head')) {
            $approval_expense->whereIn('department', [$user->department->department_code]);
        }

        if (\Entrust::hasRole('gm')) {
            $approval_expense->where('division', $user->division->division_code);
        }

        if (\Entrust::hasRole('director')) {
            $approval_expense->where('dir', $user->dir);
        }
		if($status == 'need_approval'){
			$approval_expense->where('status','0');
		}
		
        $approval_expense = $approval_expense->get();
        return DataTables::of($approval_expense)
        ->rawColumns(['action'])

        ->addColumn("action", function ($approval_expense) use ($type, $status){ 
            if($status!='need_approval'){

                if(\Entrust::hasRole('user')) {
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/ex/'.$approval_expense->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

                        <a href="#" onclick="printApproval(&#39;'.$approval_expense->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>

                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$approval_expense->id.')"><i class="mdi mdi-close"></i></button>
                        <form action="'.route('approval_expense.delete', $approval_expense->id).'" method="POST" id="form-delete-'.$approval_expense->id .'" style="display:none">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                        </form>';
                }elseif(\Entrust::hasRole('budget')) {
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/ex/'.$approval_expense->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approval_expense->approval_number.'&#39;);return false;"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
                }else{
                    return "<div id='$approval_expense->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/ex/'.$approval_expense->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                }
            }else{
                // return "else";
				//<a  href='#' onclick='javascript:validateApproval(&#39;$approval_expense->approval_number&#39;);return false;' class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a>
                return "<div id='$approval_expense->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/ex/'.$approval_expense->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a href='#' class='btn btn-danger' onclick='cancelApproval(&#39;".$approval_expense->approval_number."&#39;);return false;'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
            }
        })

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

        ->toJson();
    }

    public function DetailApproval($approval_number)
	{
		$approver   = $this->can_approve($approval_number);
		$master 	= ApprovalMaster::getSelf($approval_number);
		return view('pages.approval.expense.view',compact('master','approver'));
	}
	public function AjaxDetailApproval($approval_number)
	{
		 $approval_master = ApprovalMaster::select('*','approval_masters.status as am_status','approval_details.id as id_ad','approval_details.sap_cc_code as ad_sap_cc_code')->join('approval_details','approval_masters.id','=','approval_details.approval_master_id')
						->join('expenses','expenses.budget_no','=','approval_details.budget_no')
						->where('approval_number',$approval_number);
		 return DataTables::of($approval_master)
				->editColumn("asset_no", function ($approval) {
					return $approval->asset_no.'<input class="approval_data" type="hidden" value="'.$approval->id_ad.'">';
				})
				->editColumn("status", function ($approval) {
					if ($approval->am_status == '0') {
						return "User Created";
					}elseif ($approval->am_status == '1') {
						return "Validasi Budget";
					}elseif ($approval->am_status == '2') {
						return "Approved by Dept. Head";
					}elseif ($approval->am_status == '3') {
						return "Approved by GM";
					}elseif ($approval->am_status == '4') {
						return "Approved by Director";
					}elseif ($approval->am_status == '-1') {
						return "Canceled on Quotation Validation";
					}elseif ($approval->am_status == '-2') {
						return "Canceled Dept. Head Approval";
					}else{
						return "Canceled on Group Manager Approval";
					}
					
				})->toJson();
	}
    public function delete($id)
    {
        DB::transaction(function() use ($id){
            $approval_expense = ApprovalMaster::find($id);
            $approval_expense->details()->delete();
            $approval_expense->delete();
        });
        $res = [
                    'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been removed'
                ];

        return redirect()
                    ->route('approval-expense.ListApproval')
                    ->with($res);
    }
	public function getDelete(Request $request)
	{
		Cart::instance('expense')->remove($request->rowid);
		
		 $res = [
                    'title' => 'Success',
                    'type' => 'success',
                    'message' => 'Data has been removed'
                ];
		return json_encode($res);
	}
}
