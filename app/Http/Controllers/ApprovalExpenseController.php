<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\SapModel\SapAsset;
use App\SapModel\SapGlAccount;           
use App\SapModel\SapCostCenter;           
use App\SapModel\SapUom;
use App\ApprovalDetail;
use App\ApprovalMaster;
use DB;
use DataTables;
use Cart;

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
                                    'budget_no'         => $expense->options->budget_no,
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

        $expenses           = Expense::find($request->expense_id);
        $sap_gl_account     = SapGlAccount::find($request->sap_gl_account_id);
        $sap_assets         = SapAsset::find($request->sap_asset_id);
        $sap_costs          = SapCostCenter::find($request->sap_cost_id); 
        $sap_uoms           = SapUom::find($request->sap_uom_id);

        $budget = Expense::find($request->budget_no);

        Cart::instance('expense')->add([

                    'id' => $request->budget_no,
                    'name' => $request->project_name,
                    'price' => $request->price_remaining,
                    'qty' => $request->pr_specs,
                    'options' => [
                        'budget_no'             => $budget->budget_no,
                        'sap_gl_account_id'     => $request->sap_gl_account_id,
                        'budget_description'    => $request->budget_description,
                        'qty_remaining'         => $request->qty_remaining,
                        'qty_actual'            => $request->qty_actual,
                        'remarks'               => $request->remarks,
                        'sap_cos_center_id'     => $request->sap_cos_center_id,
                        'sap_uom_id'            => $request->sap_uom_id,
                        'price_actual'          => $request->price_actual,
                        'budget_remaining_log'  => $request->budget_remaining_log,
                        'price_to_download'     => $request->price_to_download,
                        'plan_gr'               => $request->plan_gr,
                    ]
                ]);


        $res = [
                    'type' => 'success',
                    'title' => 'Success',
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
                    'type' => 'success',
                    'title' => 'Success',
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

    public function SubmitApproval(Request $request)
    {
        
        
            $res = '';


            DB::transaction(function() use ($request, &$res){
                // Save data in Tabel Bom
                $user = \Auth::user();
                

                foreach (Cart::instance('expense')->content() as $details) {
                    $approval_no = ApprovalMaster::getNewApprovalNumber('EX', $user->department_id);  
                
                    $capex                         = new ApprovalMaster;
                    $capex->approval_number        = $approval_no;
                    $capex->budget_type            = 'ex';
                    $capex->dir                    = $user->direction;
                    $capex->division_id            = $user->division_id;
                    $capex->department_id          = $user->department_id;
                    $capex->total                  = $details->price;
                    $capex->status                 = 0;
                    $capex->created_by             = $user->id;
                    $capex->save();
                    $approval                        = new ApprovalDetail;
                    $approval->budget_no             = $details->options->budget_no;
                    $approval->project_name          = $details->name;
                    $approval->actual_qty            = $details->qty;
                    $approval->actual_price_user     = $details->price;
                    $approval->sap_gl_account_id     = $details->options->sap_gl_account_id;
                    $approval->qty_remaining         = $details->options->qty_remaining;
                    $approval->qty_actual            = $details->options->qty_actual;
                    $approval->remarks               = $details->options->remarks;
                    $approval->sap_cost_center_id     = $details->options->sap_cost_center_id;
                    $approval->sap_uom_id            = $details->options->sap_uom_id;
                    // $approval->price_remaining       = $details->options->price_actual;
                    $approval->budget_remaining_log  = $details->options->budget_remaining_log;
                    $approval->price_to_download     = $details->options->price_to_download;
                    $approval->actual_gr             = $details->options->plan_gr;
                    $approval->fyear                 = '2018';
                    $approval->budget_reserved       = 2018;
                    $capex->details()->save($approval);
                }

                $res = [
                            'title' => 'Sukses',
                            'type' => 'success',
                            'message' => 'Data berhasil disimpan!'
                        ];

                Cart::instance('expense')->destroy();
                return redirect()
                            ->route('approval-expense.ListApproval')
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

        return view('pages.approval.expense.index-admin');
    }
    public function getApprovalExpense(){
        $type = '';
        $status ='';

        $user = \Auth::user();

        $approval_expense = ApprovalMaster::with('departments')
                                ->where('budget_type', 'like', 'ex%')
                                ->get();
        return DataTables::of($approval_expense)
        ->rawColumns(['action'])

        ->addColumn("action", function ($approval_expense) use ($type, $status){ 
            if($status!='need_approval'){
                // return "<div id='$approval_expense->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$type"."/$approvals->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                if(\Entrust::hasRole('user')) {
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.$type.'/'.$approval_expense->approval_number.'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

                        <a href="#" onclick="printApproval(&#39;'.$approval_expense->approval_number.'&#39;)" class="btn btn-primary" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>

                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$approval_expense->id.')"><i class="mdi mdi-close"></i></button>
                        <form action="'.route('approval_expense.delete', $approval_expense->id).'" method="POST" id="form-delete-'.$approval_expense->id .'" style="display:none">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                        </form>';
                }elseif(\Entrust::hasRole('budget')) {
                    return '<div class="btn-group btn-group-xs" role="group" aria-label="Extra-small button group"><a href="'.$type.'/'.$approval_expense->approval_number.'" class="btn btn-info"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><a href="#" class="btn btn-danger" onclick="cancelApproval(&#39;'.$approval_expense->approval_number.'&#39;)"><span class="glyphicon glyphicon-remove"aria-hidden="true"></span></a></div>';
                }else{
                    return "<div id='$approval_expense->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$type"."/$approval_expense->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a></div>";
                }
            }else{
                // return "else";
                return "<div id='$approval_expense->approval_number' class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a href='$approval_expense->approval_number' class='btn btn-info'><span class='glyphicon glyphicon-eye-open' aria-hiden='true'></span></a><a  href='javascript:validateApproval(&#39;$approval_expense->approval_number&#39;);' class='btn btn-success'><span class='glyphicon glyphicon-ok' aria-hiden='true'></span></a><a href='$approval_expense->approval_number' class='btn btn-danger'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
            }
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

        ->addColumn("overbudget_info", function ($approvals) {
            return $approvals->status < 0 ? 'Canceled' : ($approvals->isOverExist() ? 'Overbudget exist' : 'All underbudget');
        }) 

        ->toJson();
    }

    
    public function delete($id)
    {
        DB::transaction(function() use ($id){
            $approval_expense = ApprovalMaster::find($id);
            $approval_expense->details()->delete();
            $approval_expense->delete();
        });
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('approval-expense.ListApproval')
                    ->with($res);
    }
}