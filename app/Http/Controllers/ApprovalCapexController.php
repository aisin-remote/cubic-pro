<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApprovalController;
use App\Capex;
use App\SapModel\SapAsset;
use App\SapModel\SapGlAccount;
use App\SapModel\SapCostCenter;
use App\SapModel\SapNumber;
use App\SapModel\SapUom;
use App\Approval;
use App\ApprovalMaster;
use App\ApprovalDetail;
use App\ApprovalDtl;
use App\ApproverUser;
use DB;
use App\Department;
use DataTables;
use App\Item;

use Cart;
use App\Cart as Carts;
use Carbon\Carbon;

class ApprovalCapexController extends Controller
{
    public function getData()
    {
       $capexs = Cart::instance('capex')->content();

        if (Cart::count() > 0) {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = Cart::count();
            $result['recordsFiltered'] = Cart::count();

            foreach ($capexs as $capex) {

                $result['data'][] = [
                                        'budget_no' => $capex->options->budget_no.'<input type="hidden" class="checklist" />',
                                        'asset_category' => $capex->options->asset_category,
                                        'remarks' => $capex->options->remarks,
                                        'budget_remaining_log' => $capex->options->budget_remaining_log,
                                        'sap_uom_id' => $capex->options->sap_uom_id,
                                        'sap_asset_id' => $capex->options->sap_asset_id,
                                        'sap_cost_center_id' => $capex->options->sap_cost_center_id,
                                        'project_name' => $capex->name,
                                        'pr_specs' => $capex->pr_specs,
										'actual_qty' => $capex->options->actual_qty,
                                        'price_actual' => number_format($capex->price),
                                        'asset_kind' => $capex->options->asset_kind,
                                        'plan_gr' => Carbon::parse($capex->options->plan_gr)->format('d M Y'),
                                        'settlement_date'=> Carbon::parse($capex->options->settlement_date)->format('d M Y'),
                                        'option' => '
                                            <button class="btn btn-danger btn-xs btn-bordered" onclick="onDelete(\''.$capex->rowId.'\')" data-toggle="tooltip" title="Hapus"><i class="mdi mdi-close"></i></button>'
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
        $capex              = Capex::find($request->budget_no);
        $sap_assets         = SapAsset::where('asset_type',$request->sap_asset_id)->where('asset_code', $request->sap_code_id)->first();
        $sap_costs          = SapCostCenter::find($request->sap_cost_center_id);
        $sap_uoms           = SapUom::find($request->sap_uom_id);
        $item 				= Item::firstOrNew(['item_description' => $request->remarks]);
        $item->item_description = $request->remarks;
        $item->item_category_id = '1';
        $item->item_code = 'XXX';
        $item->item_specification = $request->pr_specs;
        $item->item_price = str_replace(',','',$request->price_remaining);
        $item->uom_id = $sap_uoms->id;
        $item->supplier_id = '0';
        $item->save();

	    Cart::instance('capex')->add([

					'id'    => $request->budget_no,
					'name'  => $request->project_name,
					'price' => str_replace(',','',$request->price_actual),
					'qty' 	=> 1,// default satu
					'options' => [
                        'budget_no'             => $capex->budget_no,
                        'budget_description'    => $request->budget_description,
                        'asset_code'            => $request->sap_code_id,
						'asset_kind'            => $request->asset_kind,
						'asset_category'        => $request->asset_category,
						'sap_cost_center_id'    => $sap_costs->cc_code,//$request->sap_cost_center_id,
                        'sap_asset_class'       => $sap_assets->asset_class,
                        'sap_account_code'      => $sap_assets->asset_account,
                        'sap_account_text'      => $sap_assets->asset_name,
                        'sap_cc_code'           => $sap_costs->cc_code,
                        'sap_cc_fname'          => $sap_costs->cc_fname,
						'remarks'               => $item->item_description,//$request->remarks,
						'item_id'				=> $item->id,
						'sap_uom_id'            => $sap_uoms->uom_sname,
						'budget_remaining_log'  => str_replace(',','',$request->budget_remaining_log),
						'price_remaining'       => str_replace(',','',$request->price_remaining),
						'currency'				=> $request->currency,
						'price_to_download'     => str_replace(',','',$request->price_to_download),
						'plan_gr'               => Carbon::parse($request->plan_gr)->format('Y-m-d'),
						'pr_specs'				=> $request->pr_specs,
                        'settlement_date'       => Carbon::parse($request->settlement_date)->format('Y-m-d'),
                        'actual_qty'            => !empty($request->actual_qty) ? $request->actual_qty : 1,
						'type' => 'capex'
					]
                ]);
        // delete item from cart
        Carts::where('item_id',$item->id)->where('user_id',auth()->user()->id)->delete();

        // // update budget reserve capex
        // $budget_reserved = str_replace(',','',$request->price_actual);
        // Capex::where('budget_no', $capex->budget_no)->update(['budget_reserved' => $budget_reserved]);

        // dd($request->price_actual);
        // die;

        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been inserted'
                ];

        return redirect()
                        ->route('approval-capex.index')
                        ->with($res);
    }

    function show($id)
    {
        Cart::destroy();

    }

    function destroy($id)
    {
		Cart::instance('capex')->remove($id);
        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been removed'
                ];

        return response()
                ->json($res);

    }

    public function getOne($id)
    {
        $capex = Capex::findOrFail($id);
        $hasActiveCIP = $capex->isHasCipActive;
        $capex = $capex->toArray();
        $capex['has_active_cip'] = $hasActiveCIP;

        return response()->json($capex);

    }

    public function getAsset($id)
    {
        $sap_asset = SapAsset::select('asset_code as id', 'asset_code as text')->where('asset_type', $id)->get();

        $result = [];
        foreach ($sap_asset as $asset) {
            $result[] = ['id' => $asset->text, 'text' => $asset->text];
        }

        return response()->json($result);
    }

    public function SubmitApproval(Request $request)
    {
        $res = '';

        DB::transaction(function() use ($request, &$res){
            $user = \Auth::user();
            $approval_no = ApprovalMaster::getNewApprovalNumber('CX', $user->department->department_code);

			$remarks ="";
            $capex                         = new ApprovalMaster;
            $capex->approval_number        = $approval_no;
            $capex->budget_type            = 'cx';
            $capex->dir                    = $user->direction;
            $capex->division               = $user->division->division_code;
            $capex->department             = $user->department->department_code;
            $capex->total                  = str_replace(',', '', Cart::instance('capex')->subtotal($formatted = false));
            $capex->status                 = 0;
            $capex->created_by             = $user->id;
            $capex->fyear				   = date('Y');
            $capex->save();
            $i = 1;
            foreach (Cart::instance('capex')->content() as $details) {
                if ($details->options->asset_kind == 'CIP') {
                    $cip_no = ApprovalDetail::getNewCIPNumber($details->options->budget_no);

                    if (substr($cip_no, -4) != '0001') {
                        $asset_no = SapAsset::getAutoAssetCode($details->options->asset_code, 'CIP', $details->options->budget_no);
                    } else {
                        $asset_no = SapAsset::getAutoAssetCode($details->options->asset_code);
                    }

                    $capexStatus = 0;
                } else {
                    $cip_no = null;
                    $capexStatus = 1;
                    $asset_no = SapAsset::getAutoAssetCode($details->options->asset_code);
                }

                $capexBudget = Capex::where('budget_no', $details->options->budget_no)->first();

                if ($capexBudget->budget_reserved + $details->price < $capexBudget->budget_plan) {
                    $capexBudget->budget_reserved += $details->price;
                    $budget_reserved = $details->price;
                } else {
                    $budget_reserved = $capexBudget->budget_plan - $capexBudget->budget_reserved;
                    $capexBudget->budget_reserved = $capexBudget->budget_plan;
                }

                $capexBudget->is_closed = $capexStatus;
                $capexBudget->update();

                $approval                        = new ApprovalDetail;
                $approval->budget_no             = $details->options->budget_no;
                $approval->project_name          = $details->name;
                $approval->actual_qty            = $details->qty;
                $approval->actual_price_user     = $details->price;
                $approval->sap_asset_class       = $details->options->sap_asset_class;
                $approval->sap_account_code      = $details->options->sap_account_code;
                $approval->sap_account_text      = $details->options->sap_account_text;
                $approval->sap_cc_code           = $details->options->sap_cc_code;
                $approval->sap_cc_fname          = $details->options->sap_cc_fname;
                $approval->sap_is_chemical       = $details->options->asset_category;
                $approval->sap_cc_code		     = $details->options->sap_cost_center_id;
                $approval->sap_asset_no          = $details->options->sap_asset_id;
                $approval->remarks               = $details->options->remarks;
				$approval->item_id 				 = $details->options->item_id;
                $approval->pr_uom            	 = $details->options->sap_uom_id;
                $approval->budget_remaining_log  = $details->options->budget_remaining_log;
                $approval->price_to_download     = $details->price;
                $approval->pr_specs 			 = $details->options->pr_specs;
                $approval->cip_no                = $cip_no;
                $approval->actual_gr             = date('Y-m-d',strtotime($details->options->plan_gr));
                $approval->settlement_date       = $cip_no ? date('Y-m-d',strtotime($details->options->settlement_date)) : null;
                $approval->fyear                 = date('Y');
                $approval->budget_reserved       = $budget_reserved;
                $approval->asset_no              = $asset_no;
                $approval->sap_track_no          = ApprovalMaster::getNewSapTrackingNo(1,$user->department_id,$approval_no,$i);
				$capex->details()->save($approval);
                $i++;

                // dev-4.0, Ferry, 20161115, booking sap numbers
                SapNumber::postBookAssetNumber($asset_no);

                // dev-4.0, Ferry, 20161115, Update sap numbers dgn equipment no/asset no last number/current number
                SapNumber::postCurrentAssetNumber($asset_no);
            }
			// Simpan approver user
			$approvals = Approval::where('department',$user->department->department_code)->first();
			if(empty($approvals)){
				$res = [
						'title' => 'Error',
						'type' => 'error',
						'message' => 'There is no approval for your department'
					];
			}else{

                $approval_dtl 	 = ApprovalDtl::where('approval_id',$approvals->id)->get();

                foreach($approval_dtl as $app_dtl){
                    $approver_user = new ApproverUser();
                    $approver_user->approval_master_id  = $capex->id;
                    $approver_user->approval_detail_id  = $app_dtl->id;
                    $approver_user->user_id  = $app_dtl->user_id;
                    $approver_user->save();
                }

				$res = [
						'title' => 'Success',
						'type' => 'success',
						'message' => 'Data has been inserted'
					];
				Cart::instance('capex')->destroy();
			}

        });



         return redirect()
                        ->route('approval-capex.ListApproval')
                        ->with($res);
    }
	public function ListApprovalUnvalidated()
	{
		return view('pages.approval.capex.list-approval');
    }

    public function ListApprovalAcc()
	{
		return view('pages.approval.capex.index_acc');
	}
    /**
     * Display the specified resource.
     *
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */

    public function ListApproval()
    {
        return view('pages.approval.capex.index-admin');
    }

    public function edit(Request $request, $id)
    {
        $capexs             = Capex::get();
        $sap_assets         = SapAsset::get();
        $sap_costs          = SapCostCenter::get();
        $sap_uoms           = SapUom::get();
        $approval_details   = ApprovalDetail::get();
        $approval_masters   = ApprovalMaster::find($id);
        $departments        = Department::get();
        $budget             = Capex::find($request->budget_no);

        foreach ($approval_masters->details as $detail) {

            Cart::instance('capex')->add([

                'id'    => $detail->budget_no,
                'name'  => $detail->project_name,
                'price' => 1,
                'qty'   => 1,
                'options' => [
                    'budget_no'             => $detail->budget_no,
                    'budget_description'    => $detail->budget_description,
                    'asset_kind'            => $detail->asset_kind,
                    'asset_category'        => $detail->asset_category,
                    'sap_cost_center_id'    => $detail->sap_cost_center_id,
                    'sap_asset_id'          => $detail->sap_asset_id,
                    'remarks'               => $detail->remarks,
                    'sap_uom_id'            => $detail->sap_uom_id,
                    'budget_remaining_log'  => number_format($detail->budget_remaining_log),
                    'price_remaining'       => $detail->price_remaining,
                    'price_to_download'     => $detail->price_to_download,
                    'actual_gr'             => $detail->plan_gr,
                    'settlement_date'       => $detail->settlement_date,
                    'type' => 'capex'
                ]
            ]);

        }

        return view('pages.approval.capex.show', compact(['capex', 'sap_gl_account', 'sap_assets', 'sap_costs', 'sap_uoms','approval_masters', 'departments']));
    }

    public function delete($id)
    {
        DB::transaction(function() use ($id){
            $approval_capex = ApprovalMaster::find($id);
            foreach ($approval_capex->details as $value){
                $capex = $value->capex;

                $totalBudget = $capex->approvalDetails->sum('actual_price_user');

                // update budget reserved di expense
                if ($totalBudget > $capex->budget_reserved) {
                    $capex->budget_reserved = $capex->budget_reserved - $value->budget_reserved;
                    $capex->is_closed = 0;
                } else {
                    $capex->budget_reserved = $capex->budget_reserved - $value->budget_reserved;
                    $capex->is_closed = 0;
                }
                $capex->update();
            }

            $approval_capex->details()->delete();
            $approval_capex->delete();

        });

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ];

        return redirect()
                ->route('approval-capex.ListApproval')
                ->with($res);
    }

    public function getApprovalCapex($status){
        $type = 'cx';
        $user = auth()->user();
        $approval_capex = ApprovalMaster::with('departments', 'details')
                            ->where('budget_type', 'like', 'cx%');

        $levels = DB::table('approval_dtls AS ad')->select('ad.status_to_approve', 'a.department')
            ->leftJoin('approvals AS a', 'a.id', '=', 'ad.approval_id')
            ->where('ad.user_id', $user->id)
            ->get();

        if(\Entrust::hasRole('user')) {
            $approval_capex->where('created_by',$user->id);
        } elseif(\Entrust::hasRole(['department-head', 'budget', 'gm', 'director'])) {
            $approval_capex->whereHas('approverUsers',function($query) use($user) {
                $query->where('user_id', $user->id )
                    ->where('is_approve', 0);
            });
        } elseif (\Entrust::hasRole('purchasing')) {
            $approval_capex->whereDoesntHave('approverUsers',function($query) use($user) {
                $query->where('is_approve', 0);
            });
        }

        if ($levels->count()) {
            if($status == 'need_approval'){
                $query = '';

                $levels = $levels->reject(function ($value, $key) {
                    return $value->department == null;
                })->values();

                foreach($levels as $index => $level) {
                    if ($index == 0) {
                        $query .= "(";
                    }

                    $query .= " (`department` = '$level->department' AND `status` = $level->status_to_approve) ";

                    if ($index == $levels->count() - 1) {
                        $query .= ")";
                    } else {
                        $query .= "OR";
                    }
                }

                if ($query != '') {
                    $approval_capex->whereRaw($query);
                }
            }
        }

        $approval_capex = $approval_capex->get();

        return DataTables::of($approval_capex)
            ->rawColumns(['action'])
            ->addColumn("action", function ($approval_capex) use ($type, $status){ // dev-4.2.1 by Fahrul, 20171116
                if($status!='need_approval'){

                    if(\Entrust::hasRole('user')) {
                        return '
                            <div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/cx/'.$approval_capex->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

                            <a href="#" onclick="printApproval(&#39;'.$approval_capex->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>

                            <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$approval_capex->id.')"><i class="mdi mdi-close"></i></button>
                            <form action="'.route('approval_capex.delete', $approval_capex->id).'" method="POST" id="form-delete-'.$approval_capex->id .'" style="display:none">
                                '.csrf_field().'
                                <input type="hidden" name="_method" value="DELETE">
                            </form>';
                    }elseif(\Entrust::hasRole('budget')) { //Sebenarnya ini ga bakal dieksekusi
                        return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.url('approval/cx/'.$approval_capex->approval_number).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approval_capex->approval_number.'&#39;);return false;"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
                    }else{
                        return "<div id='$approval_capex->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/cx/'.$approval_capex->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                    }
                }else{
                    // return "else"; <a  href='#' onclick='javascript:validateApproval(&#39;$approval_capex->approval_number&#39;);return false;'class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a>
                    if(\Entrust::hasRole('user')) {
                        return "<div id='$approval_capex->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/cx/unvalidate/'.$approval_capex->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a href=\"#\" onclick=\"cancelApproval('$approval_capex->approval_number');return false;\" class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
                    } else {
                        return "<div id='$approval_capex->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='".url('approval/cx/unvalidate/'.$approval_capex->approval_number)."' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a href=\"#\" onclick=\"validateApproval('$approval_capex->approval_number');return false;\" class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a><a href=\"#\" onclick=\"cancelApproval('$approval_capex->approval_number');return false;\" class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
                    }
                }
            })

            ->editColumn("total", function ($approval_capex) {
                    return number_format($approval_capex->total);
                })
            ->editColumn("status", function ($approval_capex){
                if ($approval_capex->status == '0') {
                    return "User Created";
                }elseif ($approval_capex->status == '1') {
                    return "Validasi Budget";
                }elseif ($approval_capex->status == '2') {
                    return "Approved by Dept. Head";
                }elseif ($approval_capex->status == '3') {
                    return "Approved by GM";
                }elseif ($approval_capex->status == '4') {
                    return "Approved by Director";
                }elseif ($approval_capex->status == '-1') {
                    return "Canceled on Quotation Validation";
                }elseif ($approval_capex->status == '-2') {
                    return "Canceled Dept. Head Approval";
                }else{
                    return "Canceled on Group Manager Approval";
                }
            })

            ->addColumn("overbudget_info", function ($approval_capex) {
                return $approval_capex->status < 0 ? 'Canceled' : ($approval_capex->is_over ? 'Overbudget exist' : 'All underbudget');
            })

            ->addColumn('details_url', function($approval_capex) {
                return url('approval-capex/details-data/' . $approval_capex->id);
            })

            ->toJson();
    }

    public function DetailApproval($approval_number)
	{
		$approver   = $this->can_approve($approval_number);
        $master 	= ApprovalMaster::getSelf($approval_number);
        $user_app   = ApproverUser::where('approval_master_id',$master->id)->where('user_id',auth()->user()->id)->first();
        $status     = !empty($user_app) ? $user_app->is_approve : 0;

		return view('pages.approval.capex.view',compact('master','approver','status'));
    }

    public function DetailUnvalidateApproval($approval_number)
    {
        $approver   = $this->can_approve($approval_number);
        $master 	= ApprovalMaster::getSelf($approval_number);
        $user_app   = ApproverUser::where('approval_master_id',$master->id)->where('user_id',auth()->user()->id)->first();
        $status     = !empty($user_app) ? $user_app->is_approve : 0;

		return view('pages.approval.capex.unvalidate-view',compact('master','approver','status'));
    }

    public function AjaxDetailApproval($approval_number)
	{
         $approval_master = ApprovalMaster::select('*','approval_details.id as id_ad','approval_details.sap_cc_code as ad_sap_cc_code', DB::RAW('CONCAT_WS(" - ", approval_details.sap_account_code, approval_details.sap_account_text) AS sap_account_code1'))
                        ->join('approval_details','approval_masters.id','=','approval_details.approval_master_id')
						->join('capexes','capexes.budget_no','=','approval_details.budget_no')
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
                ->editColumn('sap_account_code', function($approval){
                    return $approval->sap_account_code."-".$approval->sap_account_text;
                })
                ->editColumn('actual_price_user', function($approval){
                    return number_format($approval->actual_price_user);
                })
                ->editColumn('price_to_download', function($approval) {
                    return number_format($approval->price_to_download);
                })
                ->addColumn("overbudget_info", function ($approval) {
                    // id di appoval = id expense karena efek join
                    if ($approval->status < 0) {
                        return 'Canceled';
                    }

                    $capex = Capex::where('id', $approval->id)->first();
                    $budgetReserved = $capex
                        ->approvalDetails()
                        ->whereHas('approval', function($q) {
                            $q->where('status', '>', 0);
                        })
                        ->select(DB::raw('sum(actual_price_user) as total_reserved'))
                        ->groupBy('budget_no')
                        ->where('id', '<=', $approval->id_ad)
                        ->first();

                    if (!$budgetReserved) {
                        $budgetReserved = $approval->actual_price_user;
                    } else {
                        $budgetReserved = $budgetReserved->total_reserved;
                    }

                    if ($budgetReserved > $capex->budget_plan) {
                        return 'Over Budget';
                    }

                    return 'Under Budget';
                })
                ->addColumn("actual_gr", function ($approval) {
                    return Carbon::parse($approval->actual_gr)->format('d M Y');
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

    public function getDetailsData($id)
    {
        $details = ApprovalMaster::find($id)
                ->details()
                ->with([ 'sap_assets', 'sap_uoms','sap_costs'])
                ->get();

        return Datatables::of($details)->make(true);
    }

	public function xedit(Request $request)
    {
		$status = 0;
		\DB::transaction(function() use ($request, &$capex){
            $approvaDetail = ApprovalDetail::where('id',$request->pk)->first();
            $budgetType = $approvaDetail->approval->budget_type;

            $dataUpdate = [$request->name => $request->value];

            if (($budgetType == 'ex' || $budgetType == 'ue') && $request->name == 'sap_account_code') {
                $accountText = SapGlAccount::where('gl_acode', $request->value)->first()->gl_aname;
                $dataUpdate['sap_account_text'] = $accountText;
            }

			$status =  ApprovalDetail::where('id',$request->pk)->update($dataUpdate);

		});

		return $status;
    }



}
