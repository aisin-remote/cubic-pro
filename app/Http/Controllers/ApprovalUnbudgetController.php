<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovalUnbudgetController extends Controller
{
    public function getData()
    {
        $unbudgets = Cart::content();

        if (Cart::count() > 0) {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = Cart::count();
            $result['recordsFiltered'] = Cart::count();

            foreach ($unbudgets as $unbudget) {

                $result['data'][] = [
                                        'budget_no'         => $unbudget->options->budget_no,
                                        'project_name'      => $unbudget->name,
                                        'price_remaining'   => $unbudget->price,
                                        'pr_specs'          => $unbudget->qty,
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

        $unbudget           = Unbudget::find($request->expense_id);
        $sap_gl_account     = SapGlAccount::find($request->sap_gl_account_id);
        $sap_assets         = SapAsset::find($request->sap_asset_id);
        $sap_costs          = SapCostCenter::find($request->sap_cost_id); 
        $sap_uoms           = SapUom::find($request->sap_uom_id);

        Cart::add([

                    'id' => $request->budget_no,
                    'name' => $request->project_name,
                    'price' => $request->price_remaining,
                    'qty' => $request->pr_specs,
                    'options' => [
                        'budget_no' => $budget->budget_no,
                        'sap_gl_account_id' => $request->sap_gl_account_id,
                        'budget_description' => $request->budget_description,
                        'qty_remaining' => $request->qty_remaining,
                        'qty_actual' => $request->qty_actual,
                        'remarks' => $request->remarks,
                        'sap_cos_center_id' => $request->sap_cos_center_id,
                        'sap_uom_id' => $request->sap_uom_id,
                        'price_actual' => $request->price_actual,
                        'remarks' => $request->remarks,
                        'budget_remaining_log' => $request->budget_remaining_log,
                        'price_to_download' => $request->price_to_download,
                        'plan_gr' => $request->plan_gr,
                    ]
                ]);


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
    public function getAsset($id)
    {
        $sap_asset = SapAsset::find($id);
        return response()->json($sap_asset);
    }
}
