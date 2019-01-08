<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Capex;
use App\SapModel\SapAsset;
use App\SapModel\SapGlAccount;           
use App\SapModel\SapCostCenter;           
use App\SapModel\SapUom;
use App\ApprovalDetail;
use App\ApprovalMaster;
use DB;
use App\Department;
use DataTables;


use Cart;         


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
                                        'budget_no' => $capex->options->budget_no,
                                        'asset_category' => $capex->options->asset_category,
                                        'remarks' => $capex->options->remarks,
                                        'budget_remaining_log' => $capex->options->budget_remaining_log,
                                        'sap_uom_id' => $capex->options->sap_uom_id,
                                        'sap_asset_id' => $capex->options->sap_asset_id,
                                        'sap_cost_center_id' => $capex->options->sap_cost_center_id,
                                        'project_name' => $capex->name,
                                        'pr_specs' => $capex->qty,
                                        'price_actual' => $capex->price,
                                        'asset_kind' => $capex->options->asset_kind,
                                        'plan_gr' => $capex->options->plan_gr,
                                        'settlement_date'=> $capex->options->settlement_date,
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

        $capex              = Capex::find($request->capex_id);
        $sap_gl_account     = SapGlAccount::find($request->sap_gl_account_id);
        $sap_assets         = SapAsset::find($request->sap_asset_id);
        $sap_costs          = SapCostCenter::find($request->sap_cost_center_id); 
        $sap_uoms           = SapUom::find($request->sap_uom_id);

        $budget = Capex::find($request->budget_no);

        Cart::instance('capex')->add([

                    'id'    => $request->budget_no,
                    'name'  => $request->project_name,
                    'price' => $request->price_actual,
                    'qty'   => $request->pr_specs,
                    'options' => [
                        'budget_no'             => $budget->budget_no,
                        'budget_description'    => $request->budget_description,
                        'asset_kind'            => $request->asset_kind,
                        'asset_category'        => $request->asset_category,
                        'sap_cost_center_id'    => $request->sap_cost_center_id,
                        'sap_asset_id'          => $request->sap_asset_id,
                        'remarks'               => $request->remarks,
                        'sap_uom_id'            => $request->sap_uom_id,
                        'budget_remaining_log'  => $request->budget_remaining_log,
                        'price_remaining'       => $request->price_remaining,
                        'price_to_download'     => $request->price_to_download,
                        'plan_gr'               => $request->plan_gr,
                        'settlement_date'       => $request->settlement_date,
                        'type' => 'capex'
                    ]
                ]);


        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been inserted'
                ];

        return redirect()
                        ->route('approval-capex.index')
                        ->with($res);
        // dd()
    }

    function show($id)
    {
        Cart::destroy();

    }

    function destroy($id)
    {
        Cart::remove($id);

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
        $capex = Capex::find($id);
        return response()->json($capex);

    }

    public function getAsset($id)
    {
        $sap_asset = SapAsset::find($id);
        return response()->json($sap_asset);
    }

    public function SubmitApproval(Request $request)
    {
        
        $res = '';

        DB::transaction(function() use ($request, &$res){
            // Save data in Tabel Bom
            $user = \Auth::user();
            

            foreach (Cart::instance('capex')->content() as $details) {

                $approval_no = ApprovalMaster::getNewApprovalNumber('CX', $user->department_id);  
            
                $capex                         = new ApprovalMaster;
                $capex->approval_number        = $approval_no;
                $capex->budget_type            = 'cx';
                $capex->dir                    = $user->direction;
                $capex->division_id            = $user->division_id;
                $capex->department_id          = $user->department_id;
                $capex->total                  = $detail->price;
                $capex->status                 = 0;
                $capex->created_by             = $user->id;
                $capex->save();

                $approval                        = new ApprovalDetail;
                $approval->budget_no             = $details->options->budget_no;
                $approval->project_name          = $details->name;
                $approval->actual_qty            = $details->qty;
                $approval->actual_price_user     = $details->price;
                $approval->asset_kind            = $details->options->asset_kind;
                $approval->asset_category        = $details->options->asset_category;
                $approval->sap_cost_center_id    = $details->options->sap_cost_center_id;
                $approval->sap_asset_id          = $details->options->sap_asset_id;
                $approval->remarks               = $details->options->remarks;
                $approval->sap_uom_id            = $details->options->sap_uom_id;
                $approval->budget_remaining_log  = $details->options->budget_remaining_log;
                $approval->price_to_download     = $details->options->price_to_download;
                $approval->actual_gr             = $details->options->plan_gr;
                $approval->settlement_date       = $details->options->settlement_date;
                $approval->fyear                 = '2018';
                $approval->budget_reserved       = 2018;
                $capex->details()->save($approval);
            }

            $res = [
                        'title' => 'Sukses',
                        'type' => 'success',
                        'message' => 'Data berhasil disimpan!'
                    ];

    

            Cart::instance('capex')->destroy();
            return redirect()
                        ->route('approval-capex.ListApproval')
                        ->with($res);
        });
        
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
                    'budget_remaining_log'  => $detail->budget_remaining_log,
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

    public function getApprovalCapex(){
        $type = '';
        $status ='';

        $user = \Auth::user();

        
        $approval_capex = ApprovalMaster::with('departments')
                                ->where('budget_type', 'like', 'cx%')
                                ->get();

        return DataTables::of($approval_capex)
        ->rawColumns(['action'])

        ->addColumn("action", function ($approval_capex) use ($type, $status){ // dev-4.2.1 by Fahrul, 20171116
            if($status!='need_approval'){
                if(\Entrust::hasRole('user')) {
                    return '
                        <div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.route('approval_capex.edit', $approval_capex->id).'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

                        <a href="#" onclick="printApproval(&#39;'.$approval_capex->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>

                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$approval_capex->id.')"><i class="mdi mdi-close"></i></button>
                        <form action="'.route('approval_capex.delete', $approval_capex->id).'" method="POST" id="form-delete-'.$approval_capex->id .'" style="display:none">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                        </form>';
                }elseif(\Entrust::hasRole('budget')) { //Sebenarnya ini ga bakal dieksekusi
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.$type.'/'.$approval_capex->approval_number.'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approval_capex->approval_number.'&#39;)"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
                }else{
                    return "<div id='$approval_capex->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$type"."/$approval_capex->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                }
            }else{
                // return "else";
                return "<div id='$approval_capex->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$approval_capex->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a  href='javascript:validateApproval(&#39;$approval_capex->approval_number&#39;);' class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a><a href='$approval_capex->approval_number' class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
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
            return $approval_capex->status < 0 ? 'Canceled' : ($approval_capex->isOverExist() ? 'Overbudget exist' : 'All underbudget');
        }) 

        ->addColumn('details_url', function($approval_capex) {
            return url('approval-capex/details-data/' . $approval_capex->id);
        })

        ->toJson();
    }

    public function getDetailsData($id)
    {
        $details = ApprovalMaster::find($id)
                ->details()
                ->with([ 'sap_assets', 'sap_uoms','sap_costs'])
                ->get();

        return Datatables::of($details)->make(true);
    }

    public function cancelAjax()
    {
        try {
            \DB::beginTransaction();

            $input = \Input::all();
            $approval = Approval_master::where('approval_number', '=', $input['approval_number'])->firstOrFail();
            if (($approval->budget_type != 'uc') && ($approval->budget_type != 'ue')) {     //v2.14 by Ferry, 20150901, prev 'ub'
                // return budget reserved
                foreach ($approval->details as $detail) {
                    $detail->budget->budget_reserved -= $detail->budget_reserved;
                    $detail->budget->save();

                    $detail->budget_reserved = 0;
                    $detail->save();
                }

                if ($approval->status > 2) {
                    $actual_prices = [];
                    foreach ($approval->details as $detail) {
                        $budget = $detail->budget;
                        $budget->budget_remaining += $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                        $budget->budget_used -= $detail->actual_price_purchasing == 0 ? $detail->actual_price_user : $detail->actual_price_purchasing;

                        if ($approval->budget_type == 'ex') {
            
                            $budget->qty_remaining += $detail->actual_qty;

                            $budget->qty_used -= $detail->actual_qty;
                        }

                        $budget->status = $budget->budget_remaining >= 0 ? 0 : 1;

                        $budget->is_closed = $budget->status == 0 ? 0 : 1;

                        $budget->save();
                    }
                }
            }

            // set approval cancel status
            $approval->cancel();

            // save udpates
            $approval->save();

            // commit transact
            \DB::commit();

            $data['success'] = 'Approval ['.$input['approval_number'].'] canceled.';
        } catch (\Exception $e) {
            // rollback transact
            \DB::rollback();

            // return error message
            $data['error'] = $e->getMessage();
        }

        return $data;
    }
    
}
