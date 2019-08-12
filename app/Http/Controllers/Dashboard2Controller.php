<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Division;
use App\Capex;
use App\Expense;
use App\ApprovalMaster;
use App\ApprovalDetail;
use Carbon\Carbon;
use App\Period;
use Excel;

class Dashboard2Controller extends Controller
{
    public function view(Request $request)
    {

        if ($request->has('download2')) {
            $this->download2($request);
        } else if ($request->has('download')) {
            $this->download($request);
        } else {
            $departments = Department::all();
            $divisions = Division::all();
            $periods = Period::all();
            $period_date = $periods->where('name', 'fyear_open_from')->first()->value;
            $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
            $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;
    
            return view('pages.dashboard.view', compact(['departments', 'divisions', 'period_date', 'period_date_from', 'period_date_to']));
        }
    }

    public function get(Request $request)
    {
        
        $periods = Period::all();
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;


        $date = !empty($request->interval) ? explode('-', str_replace(' ', '', $request->interval)) : [$period_date_from, $period_date_to];
        
        
        $date_from = Carbon::createFromFormat('d/m/Y', $date[0])->format('Y-m-d');
        $date_to = Carbon::createFromFormat('d/m/Y', $date[1])->format('Y-m-d');
        
        $cx = ApprovalMaster::where('budget_type', 'cx')
                    ->when($request, function($query, $request){
                        if (!empty($request->division)) {
                            $query->where('division', $request->division);
                        }

                        if (!empty($request->department)) {
                            $query->where('department', $request->department);
                        }
                    })
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);
        
        $uc = ApprovalMaster::where('budget_type', 'uc')
                    ->when($request, function($query, $request){
                        if (!empty($request->division)) {
                            $query->where('division', $request->division);
                        }

                        if (!empty($request->department)) {
                            $query->where('department', $request->department);
                        }
                    })
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);

        $ex = ApprovalMaster::where('budget_type', 'ex')
                    ->when($request, function($query, $request){
                        if (!empty($request->division)) {
                            $query->where('division', $request->division);
                        }

                        if (!empty($request->department)) {
                            $query->where('department', $request->department);
                        }
                    })
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);
        
        $ue = ApprovalMaster::where('budget_type', 'ue')
                    ->when($request, function($query, $request){
                        if (!empty($request->division)) {
                            $query->where('division', $request->division);
                        }

                        if (!empty($request->department)) {
                            $query->where('department', $request->department);
                        }
                    })
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);

        $capexes = Capex::whereDate('created_at', '>=', $date_from)
                        ->whereDate('created_at', '<=', $date_to)
                        ->when($request, function($query, $request){
                            if (!empty($request->plan_type)) {
                                if ($request->plan_type === 'rev') {
                                    $query->where('is_revised', 1);
                                } else {
                                    $query->where('is_revised', 0);
                                }
                            } else {
                                $query->where('is_revised', 0);
                            }

                            if (!empty($request->division)) {
                                $query->where('division', $request->division);
                            }

                            if (!empty($request->department)) {
                                $query->where('department', $request->department);
                            }
                        });
        
        $expenses = Expense::whereDate('created_at', '>=', $date_from)
                        ->whereDate('created_at', '<=', $date_to)
                        ->when($request, function($query, $request){
                            if (!empty($request->plan_type)) {
                                if ($request->plan_type === 'rev') {
                                    $query->where('is_revised', 1);
                                } else {
                                    $query->where('is_revised', 0);
                                }
                            } else {
                                $query->where('is_revised', 0);
                            }

                            if (!empty($request->division)) {
                                $query->where('division', $request->division);
                            }

                            if (!empty($request->department)) {
                                $query->where('department', $request->department);
                            }
                        });
        

        
        $capex_per_month = $capexes->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });

        $unbudget_capex_per_month = $uc->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });

        $actual_capex_per_month = $cx->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });


        $expense_per_month = $expenses->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });


        $unbudget_expense_per_month = $ue->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });

        $actual_expense_per_month = $ex->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('m');
        });

        $capexmonthly = [];
        $expensemonthly = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;

        foreach ($capex_per_month as $month => $capex) {
            $capexmonthly['plan'][$month] = round($capex->sum('budget_plan') / 1000000000, 2);
            $capexmonthly['cum_plan'][$month] =  round($capex->sum('budget_plan') / 1000000000, 2) + $i;
            $i += round($capex->sum('budget_plan') / 1000000000, 2);
        }

        
        foreach ($unbudget_capex_per_month as $month => $unbudgetcapex) {
            $capexmonthly['unbudget'][$month] = round($unbudgetcapex->sum('total') / 1000000000, 2);
        }


        foreach ($actual_capex_per_month as $month => $actual_capex) {
            $capexmonthly['actual'][$month] = round($actual_capex->sum('total') / 1000000000, 2);
            $capexmonthly['cum_actual'][$month] =  round($actual_capex->sum('total') / 1000000000, 2) + $j;
            $j += round($actual_capex->sum('total') / 1000000000, 2);
        }

        foreach ($expense_per_month as $month => $expense) {
            $expensemonthly['plan'][$month] = round($expense->sum('budget_plan') / 1000000000, 2);
            $expensemonthly['cum_plan'][$month] =  round($expense->sum('budget_plan') / 1000000000, 2) + $k;
            $k += round($expense->sum('budget_plan') / 1000000000, 2);
        }

        
        foreach ($unbudget_expense_per_month as $month => $unbudgetexpense) {
            $expensemonthly['unbudget'][$month] = round($unbudgetexpense->sum('total') / 1000000000, 2);
        }


        foreach ($actual_expense_per_month as $month => $actual_expense) {
            $expensemonthly['actual'][$month] = round($actual_expense->sum('total') / 1000000000, 2);
            $expensemonthly['cum_actual'][$month] =  round($actual_expense->sum('total') / 1000000000, 2) + $l;
            $l += round($actual_expense->sum('total') / 1000000000, 2);
        }


        $capex_map = !empty($capexmonthly['plan']) ? array_keys($capexmonthly['plan']) : array_keys([]);
        
        foreach ($capex_map as $k)
        {
            if (!isset($capexmonthly['actual'][$k])) $capexmonthly['actual'][$k] = 0;
            if (!isset($capexmonthly['cum_actual'][$k])) $capexmonthly['cum_actual'][$k] = 0;
            if (!isset($capexmonthly['unbudget'][$k])) $capexmonthly['unbudget'][$k] = 0;
        }

        $capexmonthly['actual'] = !empty($capexmonthly['actual']) ? collect($capexmonthly['actual'])->sortKeys()->flatten() : [];
        $capexmonthly['unbudget'] = !empty($capexmonthly['unbudget']) ? collect($capexmonthly['unbudget'])->sortKeys()->flatten() : [];
        $capexmonthly['cum_actual'] = !empty($capexmonthly['cum_actual']) ? collect($capexmonthly['cum_actual'])->sortKeys()->flatten() : [];

        $expense_map = !empty($expensemonthly['plan']) ? array_keys($expensemonthly['plan']) : array_keys([]);
        
        foreach ($expense_map as $k)
        {
            if (!isset($expensemonthly['actual'][$k])) $expensemonthly['actual'][$k] = 0;
            if (!isset($expensemonthly['cum_actual'][$k])) $expensemonthly['cum_actual'][$k] = 0;
            if (!isset($expensemonthly['unbudget'][$k])) $expensemonthly['unbudget'][$k] = 0;
        }

        $expensemonthly['actual'] = !empty($expensemonthly['actual']) ? collect($expensemonthly['actual'])->sortKeys()->flatten() : [];
        $expensemonthly['unbudget'] =  !empty($expensemonthly['unbudget']) ? collect($expensemonthly['unbudget'])->sortKeys()->flatten() : [];
        $expensemonthly['cum_actual'] = !empty($expensemonthly['cum_actual']) ? collect($expensemonthly['cum_actual'])->sortKeys()->flatten() : [];
        
        return response()->json([
            'data' => [
                'capexes' => [
                    'free' => round(($capexes->sum('budget_plan') - ( $uc->sum('total') + $cx->sum('total')) ) / 1000000000, 2),
                    'unbudget' => round($uc->sum('total') / 1000000000, 2),
                    'normal_used' => round($cx->sum('total') / 1000000000, 2)
                ],
                'expenses' => [
                    'free' => round(($expenses->sum('budget_plan') - ( $ex->sum('total') + $ue->sum('total')) ) / 1000000000, 2),
                    'unbudget' => round($ue->sum('total') / 1000000000, 2),
                    'normal_used' => round($ex->sum('total') / 1000000000, 2)
                ],
                'capex_bar' => [
                    'plan' => !empty($capexmonthly['plan']) ? collect($capexmonthly['plan'])->flatten() : [0],
                    'unbudget' => !empty($capexmonthly['unbudget']) ? $capexmonthly['unbudget'] : [0],
                    'actual' => !empty($capexmonthly['actual']) ? $capexmonthly['actual'] : [0],
                    'cum_plan' => !empty($capexmonthly['cum_plan']) ? collect($capexmonthly['cum_plan'])->flatten() : [0],
                    'cum_actual' => !empty($capexmonthly['cum_actual']) ? $capexmonthly['cum_actual'] : [0],
                    'keys' => $capex_per_month->keys()->all()
                ],
                'expense_bar' => [
                    'plan' => !empty($expensemonthly['plan']) ? collect($expensemonthly['plan'])->flatten() : [0],
                    'unbudget' => !empty($expensemonthly['unbudget']) ? $expensemonthly['unbudget'] : [0],
                    'actual' => !empty($expensemonthly['actual']) ? $expensemonthly['actual'] : [0],
                    'cum_plan' => !empty($expensemonthly['cum_plan']) ? collect($expensemonthly['cum_plan'])->flatten() : [0],
                    'cum_actual' => !empty($expensemonthly['cum_actual']) ? $expensemonthly['cum_actual'] : [0],
                    'keys' => $expense_per_month->keys()->all()
                ]
            ]
        ]);

    }

    protected function download2($request)
    {
        $periods = Period::all();
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;
        $date = !empty($request->interval) ? explode('-', str_replace(' ', '', $request->interval)) : [$period_date_from, $period_date_to];
        $date_from = Carbon::createFromFormat('d/m/Y', $date[0])->format('Y-m-d');
        $date_to = Carbon::createFromFormat('d/m/Y', $date[1])->format('Y-m-d');

        $approvals =  ApprovalDetail::with(['approval', 'capex', 'expense'])->when($request, function($query, $request){
                            if (!empty($request->division)) {
                                $query->whereHas('approval', function($where) use ($request){
                                    $where->where('division', $request->division);
                                });
                            }

                            if (!empty($request->department)) {
                                $query->whereHas('approval', function($where) use ($request){
                                    $where->where('department', $request->department);
                                });
                            }
                        })
                        ->whereDate('created_at', '>=', $date_from)
                        ->whereDate('created_at', '<=', $date_to)
                        ->get();


        $approvals = $approvals->map(function($detail) {

            if ($detail->approval->budget_type === 'cx') {
                $budget_type = 'Capex';
            } else if ($detail->budget_type === 'ex') {
                $budget_type = 'Expense';
            } else if ($detail->budget_type === 'uc') {
                $budget_type = 'Unbudget Capex';
            } else {
                $budget_type = 'Unbudget Expense';
            }

            if ($detail->approval->status == '0'){
                $status = "Underbudget";
            }else{
                $status = "Overbudget";
            }
            

            if ($detail->approval->budget_type === 'cx' && !empty($detail->capex) ) {
                $plan_gr = Carbon::parse($detail->capex->plan_gr)->format('d M \'y');
            } else if ($detail->approval->budget_type === 'ex' && !empty($detail->expense)) {
                $plan_gr = Carbon::parse($detail->expense->plan_gr)->format('d M \'y');
            } else {
                $plan_gr = '';
            }

            return [
                'id' => $detail->id,
                'department' => $detail->approval->departments->department_name,
                'type' => $budget_type,
                'approval_number' =>  $detail->approval->approval_number,
                'budget_number' => $detail->budget_no,
                'asset_number' => $detail->asset_no,
                'sap_track_number' => $detail->sap_track_no,
                'budget_description' => '',
                'project_name' => $detail->project_name,
                'actual_qty' => $detail->actual_qty,
                'budget_reserved' => $detail->budget_reserved,
                'actual_price_by_user' => $detail->actual_price_user,
                'actual_price_by_purchasing' => $detail->actual_price_purchasing,
                'status_approval' => $detail->approval->status,
                'status' => $status,
                'plan_gr' => $plan_gr,
                'actual_gr' => Carbon::parse($detail->actual_gr)->format('d M \'y'),
                'po_number' => $detail->po_number,
                'remarks' => $detail->remarks,
                'gl_account' => $detail->sap_account_code,
                'gl_account_name' => $detail->sap_account_text,
                'cost_center' => $detail->sap_cc_code,
                'created_at' => Carbon::parse($detail->created_at)->format('d M \'y')
            ];
        });
        
        Excel::create('CSV2dashboard.'.$date_from.'.'.$date_to, function($excel) use($approvals) {
            $excel->sheet('Sheet 1', function($sheet) use($approvals) {
                $sheet->fromArray($approvals);
            });
        })->export('csv');
    }

    protected function download(Request $request)
    {

        $periods = Period::all();
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;
        $date = !empty($request->interval) ? explode('-', str_replace(' ', '', $request->interval)) : [$period_date_from, $period_date_to];
        $date_from = Carbon::createFromFormat('d/m/Y', $date[0])->format('Y-m-d');
        $date_to = Carbon::createFromFormat('d/m/Y', $date[1])->format('Y-m-d');

        $capex = Capex::select('budget_no', 'equipment_name as description', 'budget_plan', 'budget_used', 'budget_remaining')
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to)
                    ->when($request, function($query, $request){
                        if (!empty($request->plan_type)) {
                            if ($request->plan_type === 'rev') {
                                $query->where('is_revised', 1);
                            } else {
                                $query->where('is_revised', 0);
                            }
                        } else {
                            $query->where('is_revised', 0);
                        }

                        if (!empty($request->division)) {
                            $query->where('division', $request->division);
                        }

                        if (!empty($request->department)) {
                            $query->where('department', $request->department);
                        }
                    });

        $capex_expenses = Expense::select('budget_no', 'description', 'budget_plan', 'budget_used', 'budget_remaining')
            ->whereDate('created_at', '>=', $date_from)
            ->whereDate('created_at', '<=', $date_to)
            ->when($request, function($query, $request){
                if (!empty($request->plan_type)) {
                    if ($request->plan_type === 'rev') {
                        $query->where('is_revised', 1);
                    } else {
                        $query->where('is_revised', 0);
                    }
                } else {
                    $query->where('is_revised', 0);
                }

                if (!empty($request->division)) {
                    $query->where('division', $request->division);
                }

                if (!empty($request->department)) {
                    $query->where('department', $request->department);
                }
            })
            ->union($capex)
            ->get();

        $capex_expenses = $capex_expenses->map(function($cxex) {
            return [
                'budget_no' => $cxex->budget_no,
                'budget_name' => $cxex->description,
                'budget_plan' => $cxex->budget_plan,
                'budget_used' => $cxex->budget_used,
                'budget_remaining' => $cxex->budget_remaining,
            ];
        });

        Excel::create('CSVdashboard.'.$date_from.'.'.$date_to, function($excel) use($capex_expenses) {
            $excel->sheet('Sheet 1', function($sheet) use($capex_expenses) {
                $sheet->fromArray($capex_expenses);
            });
        })->export('csv');

    }
}
