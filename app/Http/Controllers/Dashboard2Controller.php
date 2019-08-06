<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Division;
use App\Capex;
use App\Expense;
use App\ApprovalMaster;
use Carbon\Carbon;
use App\Period;

class Dashboard2Controller extends Controller
{
    public function view(Request $request, $group_type)
    {
        $departments = Department::all();
        $divisions = Division::all();
        $periods = Period::all();
        $period_date = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;

        return view('pages.dashboard.view', compact(['departments', 'divisions', 'period_date', 'period_date_from', 'period_date_to']));
    }

    public function get(Request $request)
    {
        $periods = Period::all();
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;


        $date = !empty($request->date) ? explode('-', str_replace(' ', '', $request->date)) : [$period_date_from, $period_date_to];
        $date_from = Carbon::parse($date[0])->format('Y-m-d');
        $date_to = Carbon::parse($date[0])->format('Y-m-d');
        
        $cx = ApprovalMaster::where('budget_type', 'cx')
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);
        
        $uc = ApprovalMaster::where('budget_type', 'uc')
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);

        $ex = ApprovalMaster::where('budget_type', 'ex')
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);
        
        $ue = ApprovalMaster::where('budget_type', 'ue')
                    ->whereDate('created_at', '>=', $date_from)
                    ->whereDate('created_at', '<=', $date_to);

        $capexes = Capex::whereDate('created_at', '>=', $date_from)
                        ->whereDate('created_at', '<=', $date_to)
                        ->where('is_revised', 0);
        
        $expenses = Expense::whereDate('created_at', '>=', $date_from)
                        ->whereDate('created_at', '<=', $date_to)
                        ->where('is_revised', 0);
        
        $capex_per_month = $capexes->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });

        $unbudget_capex_per_month = $ue->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });

        $actual_capex_per_month = $ex->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });


        $expense_per_month = $expenses->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });


        $unbudget_expense_per_month = $ue->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });

        $actual_expense_per_month = $ex->orderBy('created_at', 'asc')->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M');
        });

        $capexmonthly = [];
        $expensemonthly = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;

        foreach ($capex_per_month as $month => $capex) {
            $capexmonthly['plan'][] = round($capex->sum('budget_plan') / 1000000000, 2);
            $capexmonthly['cum_plan'][] =  round($capex->sum('budget_plan') / 1000000000, 2) + $i;
            $i += round($capex->sum('budget_plan') / 1000000000, 2);
        }

        
        foreach ($unbudget_capex_per_month as $month => $unbudgetcapex) {
            $capexmonthly['unbudget'][] = round($capex->sum('total') / 1000000000, 2);
        }


        foreach ($actual_capex_per_month as $month => $unbudgetcapex) {
            $capexmonthly['actual'][] = round($capex->sum('total') / 1000000000, 2);
            $capexmonthly['cum_actual'][] =  round($capex->sum('total') / 1000000000, 2) + $j;
            $j += round($capex->sum('total') / 1000000000, 2);
        }

        foreach ($expense_per_month as $month => $expense) {
            $expensemonthly['plan'][] = round($expense->sum('budget_plan') / 1000000000, 2);
            $expensemonthly['cum_plan'][] =  round($expense->sum('budget_plan') / 1000000000, 2) + $k;
            $k += round($expense->sum('budget_plan') / 1000000000, 2);
        }

        
        foreach ($unbudget_expense_per_month as $month => $unbudgetexpense) {
            $expensemonthly['unbudget'][] = round($expense->sum('total') / 1000000000, 2);
        }


        foreach ($actual_expense_per_month as $month => $unbudgetexpense) {
            $expensemonthly['actual'][] = round($expense->sum('total') / 1000000000, 2);
            $expensemonthly['cum_actual'][] =  round($expense->sum('total') / 1000000000, 2) + $l;
            $l += round($expense->sum('total') / 1000000000, 2);
        }
        
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
                    'plan' => !empty($capexmonthly['plan']) ? $capexmonthly['plan'] : [0],
                    'unbudget' => !empty($capexmonthly['unbudget']) ? $capexmonthly['unbudget'] : [0],
                    'actual' => !empty($capexmonthly['actual']) ? $capexmonthly['actual'] : [0],
                    'cum_plan' => !empty($capexmonthly['cum_plan']) ? $capexmonthly['cum_plan'] : [0],
                    'cum_actual' => !empty($capexmonthly['cum_actual']) ? $capexmonthly['cum_actual'] : [0],
                    'keys' => $capex_per_month->keys()->all()
                ],
                'expense_bar' => [
                    'plan' => !empty($expensemonthly['plan']) ? $expensemonthly['plan'] : [0],
                    'unbudget' => !empty($expensemonthly['unbudget']) ? $expensemonthly['unbudget'] : [0],
                    'actual' => !empty($expensemonthly['actual']) ? $expensemonthly['actual'] : [0],
                    'cum_plan' => !empty($expensemonthly['cum_plan']) ? $expensemonthly['cum_plan'] : [0],
                    'cum_actual' => !empty($expensemonthly['cum_actual']) ? $expensemonthly['cum_actual'] : [0],
                    'keys' => $expense_per_month->keys()->all()
                ]
            ]
        ]);

    }
}
