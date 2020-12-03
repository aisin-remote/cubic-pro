<?php

namespace App\Http\Controllers;

use App\Approval;
use Illuminate\Http\Request;
use App\Department;
use App\Division;
use App\Capex;
use App\Expense;
use App\ApprovalMaster;
use App\ApprovalDetail;
use App\ApproverUser;
use Carbon\Carbon;
use App\Period;
use Excel;
use Illuminate\Support\Facades\DB;

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

    /**
     * view dashboard department
     */
    public function viewBasedOnRole(Request $request)
    {
        $periods = new Period;
        $period_date = $periods->where('name', 'fyear_open')->first()->value;
        $period_date_from = $periods->where('name', 'fyear_open_from')->first()->value;
        $period_date_to = $periods->where('name', 'fyear_open_to')->first()->value;
        $first_period = $periods->where('name', 'fyear_first')->first()->value;
        $dashboardType = 'department';

        if  (\Entrust::hasRole('gm')) {
            $dashboardType = 'division';
        } elseif (\Entrust::hasRole(['budget', 'director', 'admin'])) {
            return $this->view($request);
        }

        return view('pages.dashboard.based_on_role', compact(['dashboardType', 'period_date', 'period_date_from', 'period_date_to', 'first_period']));
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

    /**
     * Data for pie chart js in dashboard
     */
    public function getPlan(Request $request)
    {
        $type = $request->type;
        $period = $request->period;
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $request->startDate)));
        $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->endDate)));
        $departments = $request->departments ? $request->departments : [];
        $departmentCode = auth()->user()->department->department_code;
        $approval = Approval::where('department', $departmentCode)->first();
        $approvalDtl = $approval->details()->where('level', '>=', 3)
            ->orderBy('level', 'asc')
            ->first();

        if (!$approvalDtl) {
            abort(400);
        }

        $dirId = $approvalDtl->user_id;

        // total capex plan by department
        $capex = Capex::select('budget_plan')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type)
            ->get()
            ->sum('budget_plan');

        $expense = Expense::select('budget_plan')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type)
            ->get()
            ->sum('budget_plan');

        $totalCx = ApprovalMaster::select('total')
            ->when($type, function($q, $type) {
                $q->whereHas('details', function($q) use($type) {
                    $q->whereHas('capex', function($q) use($type) {
                        $q->where('is_revised', $type);
                    });
                });
            })
            ->whereIn('department', $departments)
            ->where('budget_type', 'cx')
            ->where('status', '>=', '3')
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum('total');

        $totalUc = ApprovalMaster::select('total')
            ->whereHas('approverUsers', function ($q) use ($dirId) {
                $q->where('user_id', $dirId)
                    ->where('is_approve', 1);
            })
            ->whereIn('department', $departments)
            ->where('budget_type', 'uc')
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum('total');

        $totalEx = ApprovalMaster::select('total')
            ->whereHas('approverUsers', function ($q) use ($dirId) {
                $q->where('user_id', $dirId)
                    ->where('is_approve', 1);
            })
            ->when($type, function($q, $type) {
                $q->whereHas('details', function($q) use($type) {
                    $q->whereHas('expense', function($q) use($type) {
                        $q->where('is_revised', $type);
                    });
                });
            })
            ->whereIn('department', $departments)
            ->where('budget_type', 'ex')
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum('total');

        $totalUe = ApprovalMaster::select('total')
            ->whereHas('approverUsers', function ($q) use ($dirId) {
                $q->where('user_id', $dirId)
                    ->where('is_approve', 1);
            })
            ->whereIn('department', $departments)
            ->where('budget_type', 'ue')
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum('total');

        return response()->json([
            'data' => [
                'total_capex' => $capex == 0 ? 0 : $totalCx,
                'total_expense' => $expense == 0 ? 0 : $totalEx,
                'total_uc' => $capex == 0 ? 0 : $totalUc,
                'total_ue' => $expense == 0 ? 0 : $totalUe,
                'capex' => $capex,
                'expense' => $expense
            ],
            'success' => true,
            'message' => 'Data retrieved successfully'
        ]);
    }

    /**
     * Data form summary chart in dashboard
     */
    public function getSummary(Request $request)
    {
        $type = $request->type;
        $period = $request->period;
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $request->startDate)));
        $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->endDate)));
        $departments = $request->departments ? $request->departments : [];

        $departmentCode = auth()->user()->department->department_code;
        $approval = Approval::where('department', $departmentCode)->first();
        $approvalDtl = $approval->details()->where('level', '>=', 3)->orderBy('level', 'asc')->first();

        if (!$approvalDtl) {
            abort(400);
        }

        $approverMasterIds = DB::table('approver_users')->select('approval_master_id')
            ->where('user_id', $approvalDtl->user_id)
            ->where('is_approve', '1')
            ->get()->pluck('approval_master_id')->toArray();

        // total capex plan by department
        $capex = Capex::selectRaw('sum(budget_plan) total, SUBSTRING(budget_no, 11, 2) month')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $expense = Expense::selectRaw('sum(budget_plan) total, SUBSTRING(budget_no, 11, 2) month')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $totalCx = Capex::selectRaw('sum(ad.actual_price_user) total, SUBSTRING(capexes.budget_no, 11, 2) month')
            ->leftJoin('approval_details as ad', 'ad.budget_no', '=', 'capexes.budget_no')
            ->leftJoin('approval_masters as am', 'am.id', '=', 'ad.approval_master_id')
            ->whereIn('capexes.department', $departments)
            ->whereIn('am.id', $approverMasterIds)
            ->where('capexes.is_revised', $type)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $totalEx = Expense::selectRaw('sum(ad.actual_price_user) total, SUBSTRING(expenses.budget_no, 11, 2) month')
            ->leftJoin('approval_details as ad', 'ad.budget_no', '=', 'expenses.budget_no')
            ->leftJoin('approval_masters as am', 'am.id', '=', 'ad.approval_master_id')
            ->whereIn('expenses.department', $departments)
            ->whereIn('am.id', $approverMasterIds)
            ->where('expenses.is_revised', $type)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $totalUc = ApprovalMaster::selectRaw('sum(total) total, MONTH(created_at) month')
            ->whereIn('department', $departments)
            ->where('budget_type', 'uc')
            ->whereIn('id', $approverMasterIds)
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $totalUe = ApprovalMaster::selectRaw('sum(total) total, MONTH(created_at) month')
            ->whereIn('department', $departments)
            ->where('budget_type', 'ue')
            ->whereIn('id', $approverMasterIds)
            ->where('approval_number', 'like', '%-'.substr($period, -2).'-%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        return response()->json([
            'data' => [
                'total_capex_per_month' => $totalCx,
                'total_expense_per_month' => $totalEx,
                'total_uc_per_month' => $totalUc,
                'total_ue_per_month' => $totalUe,
                'capexes' => $capex,
                'expenses' => $expense
            ],
            'success' => true,
            'message' => 'Data retrieved successfully'
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

        $department = null;
        $division = null;

        if(\Entrust::hasRole(['user', 'department-head'])) {
            $department = auth()->user()->department->department_code;
        } elseif (\Entrust::hasRole(['budget', 'director', 'admin'])) {
            $department = $request->department;
        } elseif (\Entrust::hasRole('gm')) {
            $division = auth()->user()->division->division_code;
        }

        $approvals = ApprovalDetail::with(['approval', 'capex', 'expense'])
            ->when($division, function($query, $division) {
                $query->whereHas('approval', function($where) use ($division){
                    $where->where('division', $division);
                });
            })
            ->when($department, function($query, $department) {
                $query->whereHas('approval', function($where) use ($department){
                    $where->where('department', $department);
                });
            })
            ->whereDate('created_at', '>=', $date_from)
            ->whereDate('created_at', '<=', $date_to)
            ->get();

        $approvals = $approvals->map(function($detail, $key) {

            $budgetDesc = '-';
            $status = '-';
            $planGr = '-';
            switch ($detail->approval->budget_type) {
                case 'cx':
                    $budgetType = 'Capex';
                    $budgetDesc = $detail->capex->equipment_name;
                    break;
                case 'uc':
                    $budgetType = 'Unbudget Capex';
                    break;
                case 'ex':
                    $budgetDesc = $detail->expense->description;
                    $budgetType = 'Expense';
                    break;
                case 'ue':
                    $budgetType = 'Unbudget Expense';
                    break;
                default:
                    $budgetType = '-';
                    break;
            }

            switch ($detail->approval->status) {
                case '0':
                    $statusApproval = 'User Created';
                    break;
                case '1':
                    $statusApproval = 'Validasi Budget';
                    break;
                case '2':
                    $statusApproval = 'Approved by Dept. Head';
                    break;
                case '3':
                    $statusApproval = 'Approved by GM';
                    break;
                case '4':
                    $statusApproval = 'Approved by Director';
                    break;
                case '-1':
                    $statusApproval = 'Canceled on Quotation Validation';
                    break;
                case '-2':
                    $statusApproval = 'Canceled Dept. Head Approval';
                    break;
                default:
                    $statusApproval = '';
                    break;
            }

            if ($detail->approval->budget_type == 'cx' || $detail->approval->budget_type == 'ex') {
                if ($detail->isOver){
                    $status = "Over Budget";
                }else{
                    $status = "Under Budget";
                }

                $planGr = $detail->planGrDate;
            }

            return [
                'No.' => $key + 1,
                'Department' => $detail->approval->departments->department_name,
                'Type' => $budgetType,
                'Approval No.' =>  $detail->approval->approval_number,
                'Budget No.' => $detail->budget_no,
                'Asset No.' => $detail->asset_no ? $detail->asset_no : '-',
                'SAP Track No.' => $detail->sap_track_no,
                'Budget Description' => $budgetDesc,
                'Project Name' => $detail->project_name,
                'Actual Qty' => (int) $detail->actual_qty,
                'Budget Reserved' => (int) $detail->budget_reserved,
                'Actual Price User' => (int) $detail->actual_price_user,
                'Actual Price Purchasing' => (int) $detail->actual_price_purchasing,
                'Status Approval' => $statusApproval,
                'Status Budget' => $status,
                'Plan GR' => $planGr,
                'Actual GR' => Carbon::parse($detail->actual_gr)->format('d M \'y'),
                'PO No.' => $detail->po_number,
                'Remarks' => $detail->remarks,
                'GL Account' => $detail->sap_account_code,
                'GL Account Name' => $detail->sap_account_text ? $detail->sap_account_text : '-',
                'Cost Center' => $detail->sap_cc_code,
                'Created At' => Carbon::parse($detail->created_at)->format('d M \'y')
            ];
        });

        ob_end_clean(); // this
        ob_start(); // and this

        Excel::create('CSV2dashboard.'.$date_from.'.'.$date_to, function($excel) use($approvals) {
            $excel->sheet('Sheet 1', function($sheet) use($approvals) {
                $sheet->fromArray($approvals->toArray());
            });
        })->export('xlsx');
    }

    public function downloadApproval(Request $request)
    {
        $type = $request->type;
        $period = $request->period;
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $request->startDate)));
        $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $request->endDate)));
        $departments = $request->departments ? $request->departments : [];

        $approvals = ApprovalDetail::with('approval')
            ->whereHas('approval', function($q) use($period, $departments) {
                $q->whereIn('department', $departments)
                    ->where('approval_number', 'like', '%-'.substr($period, -2).'-%');
            })
            ->when($type, function($q, $type) {
                $q->where(function ($q) use ($type) {
                    $q->whereHas('expense', function($q) use($type) {
                        $q->where('is_revised', $type);
                    })
                    ->orWhereHas('capex', function($q) use($type) {
                        $q->where('is_revised', $type);
                    });
                });
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $approvals = $approvals->map(function($detail, $key) {
            $budgetDesc = '-';
            $status = '-';
            $planGr = '-';
            switch ($detail->approval->budget_type) {
                case 'cx':
                    $budgetType = 'Capex';
                    $budgetDesc = $detail->capex->equipment_name;
                    break;
                case 'uc':
                    $budgetType = 'Unbudget Capex';
                    break;
                case 'ex':
                    $budgetDesc = $detail->expense->description;
                    $budgetType = 'Expense';
                    break;
                case 'ue':
                    $budgetType = 'Unbudget Expense';
                    break;
                default:
                    $budgetType = '-';
                    break;
            }

            switch ($detail->approval->status) {
                case '0':
                    $statusApproval = 'User Created';
                    break;
                case '1':
                    $statusApproval = 'Validasi Budget';
                    break;
                case '2':
                    $statusApproval = 'Approved by Dept. Head';
                    break;
                case '3':
                    $statusApproval = 'Approved by GM';
                    break;
                case '4':
                    $statusApproval = 'Approved by Director';
                    break;
                case '-1':
                    $statusApproval = 'Canceled on Quotation Validation';
                    break;
                case '-2':
                    $statusApproval = 'Canceled Dept. Head Approval';
                    break;
                default:
                    $statusApproval = '';
                    break;
            }

            if ($detail->approval->budget_type == 'cx' || $detail->approval->budget_type == 'ex') {
                if ($detail->isOver){
                    $status = "Over Budget";
                }else{
                    $status = "Under Budget";
                }

                $planGr = $detail->planGrDate;
            }

            return [
                'No.' => $key + 1,
                'Department' => $detail->approval->departments->department_name,
                'Type' => $budgetType,
                'Approval No.' =>  $detail->approval->approval_number,
                'Budget No.' => $detail->budget_no,
                'Asset No.' => $detail->asset_no ? $detail->asset_no : '-',
                'SAP Track No.' => $detail->sap_track_no,
                'Budget Description' => $budgetDesc,
                'Project Name' => $detail->project_name,
                'Actual Qty' => (int) $detail->actual_qty,
                'Budget Reserved' => (int) $detail->budget_reserved,
                'Actual Price User' => (int) $detail->actual_price_user,
                'Actual Price Purchasing' => (int) $detail->actual_price_purchasing,
                'Status Approval' => $statusApproval,
                'Status Budget' => $status,
                'Plan GR' => $planGr,
                'Actual GR' => Carbon::parse($detail->actual_gr)->format('d M \'y'),
                'PO No.' => $detail->po_number,
                'Remarks' => $detail->remarks,
                'GL Account' => $detail->sap_account_code,
                'GL Account Name' => $detail->sap_account_text ? $detail->sap_account_text : '-',
                'Cost Center' => $detail->sap_cc_code,
                'Created At' => Carbon::parse($detail->created_at)->format('d M \'y')
            ];
        });

        ob_end_clean(); // this
        ob_start(); // and this

        Excel::create('APPROVAL_LIST_' . date('Ymd', strtotime($startDate)) . '_TO_' . date('Ymd', strtotime($endDate)), function($excel) use($approvals) {
            $excel->sheet('Sheet 1', function($sheet) use($approvals) {
                $sheet->fromArray($approvals->toArray(), null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'J' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'K' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'L' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'M' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ));
            });
        })->export('xlsx');
    }

    public function downloadBudget(Request $request)
    {
        $type = $request->type;
        $period = $request->period;
        $departments = $request->departments ? $request->departments : [];

        $capex = Capex::select('budget_no', 'equipment_name as budget_name', 'budget_plan', 'budget_used', 'budget_remaining')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type);

        $budgets = Expense::select('budget_no', 'description as budget_name', 'budget_plan', 'budget_used', 'budget_remaining')
            ->whereIn('department', $departments)
            ->where(DB::raw('SUBSTRING(budget_no, 4, 2)'), substr($period, -2))
            ->where('is_revised', $type)
            ->union($capex)
            ->get()->toArray();

        $budgets = array_map(function($budget) {
            return [
                'Budget Number' => $budget['budget_no'],
                'Budget Name' => $budget['budget_name'],
                'Budget Plan' => (int) $budget['budget_plan'],
                'Budget Used' => (int) $budget['budget_used'],
                'Budget Remain' => (int) $budget['budget_remaining']
            ];
        }, $budgets);
        ob_end_clean(); // this
        ob_start(); // and this

        Excel::create('BUDGET_LIST_FY_'.$period, function($excel) use($budgets) {
            $excel->sheet('Sheet 1', function($sheet) use($budgets) {
                $sheet->fromArray($budgets, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'C' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'D' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ));
            });
        })->export('xlsx');
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

        ob_end_clean(); // this
        ob_start(); // and this

        Excel::create('CSVdashboard.'.$date_from.'.'.$date_to, function($excel) use($capex_expenses) {
            $excel->sheet('Sheet 1', function($sheet) use($capex_expenses) {
                $sheet->fromArray($capex_expenses->toArray());
            });
        })->export('xlsx');

    }
}
