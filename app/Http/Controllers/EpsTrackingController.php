<?php

namespace App\Http\Controllers;

use App\ApprovalDetail;
use Illuminate\Http\Request;
use App\EpsTracking;
use App\Department;
use App\Http\DataTables\CollectionCustom;
use App\Period;
use DataTables;
use DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class EpsTrackingController extends Controller
{
    public function index(Request $request)
    {
        $periods = new Period;
        $periodFrom = $periods->where('name', 'fyear_open_from')->first()->value;
        $periodTo = $periods->where('name', 'fyear_open_to')->first()->value;
        $periodFrom = date('Y/m/d', strtotime($periodFrom));
        $periodTo = date('Y/m/d', strtotime($periodTo));

        return view('pages.eps_tracking', compact('periodFrom', 'periodTo'));
    }

    public function show($id)
    {
        $division = Division::find($id);
        if (empty($division)) {
            return response()->json('Type not found', 500);
        }
        return response()->json($division, 200);
    }


    public function getData(Request $request)
    {
        $limit  = $request->length;
        $start = $request->start;
        $search = $request->search['value'];
        $prCreated = $request->pr_created;

        if ($prCreated){
            $intervals = explode('-', $prCreated);

            if (count($intervals) > 1) {
                $from = date('Y-m-d', strtotime(trim($intervals[0])));
                $to = date('Y-m-d', strtotime(trim($intervals[1])));
            }
		}

        $totalRecords = ApprovalDetail::when($search, function($q, $search) {
            $q->whereHas('approval', function($q) use($search){
                $q->where('approval_number', 'like', '%'.$search.'%');
            });
        })->when($prCreated, function ($q) use($from, $to) {
            $q->whereHas('approval', function ($q) use($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            });
        })
        ->count();

        $user  = auth()->user();

        $query = "SELECT am.approval_number, ad.project_name, am.created_at as user_create, am.id, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 1 limit 1) LIMIT 1) AS approval_budget, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 2 limit 1) LIMIT 1) AS approval_dep_head, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 3 limit 1) LIMIT 1) AS approval_div_head, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 4 limit 1) LIMIT 1) AS approval_dir, upo.pr_receive, upo.po_date, upo.po_number, i.item_code, i.item_description, ad.actual_qty, ad.pr_uom, ad.actual_price_user, v.vendor_fname as supplier_name, u.name, gcd.gr_no, gcd.created_at as gr_date, gcd.qty_receive, gcd.qty_outstanding, gcd.notes FROM approval_details ad LEFT OUTER JOIN approval_masters am ON ad.approval_master_id = am.id LEFT OUTER JOIN upload_purchase_orders upo ON ad.id = upo.approval_detail_id LEFT OUTER JOIN items i on ad.item_id = i.id LEFT OUTER JOIN sap_vendors v ON v.vendor_code = ad.sap_vendor_code LEFT OUTER JOIN users u on am.created_by = u.id LEFT OUTER JOIN gr_confirm_details gcd ON ad.id = gcd.approval_detail_id ";

        if ($prCreated){
            $intervals = explode('-', $prCreated);

            $query .= "WHERE (am.created_at > '$from' && am.created_at < '$to') ";
		}

        if ($search) {
            if (!$prCreated) {
                $query .= "WHERE";
            } else {
                $query .= "AND";
            }

            $query .= " am.approval_number like '%" . $search ."%' ";
        }

        if ($user->hasRole('department-head') || $user->hasRole('user')) {
            $deptCode = $user->department->department_code;
            if (!$search && !$prCreated) {
                $query .= "WHERE";
            } else {
                $query .= "AND";
            }

            $query .= " am.department = '$deptCode' ";
        }

        $query .= "LIMIT $start, $limit";

        $eps_tracking  = DB::select($query);

        Config::set('datatables.engines.collection', CollectionCustom::class);

        return DataTables::of($eps_tracking)
            ->setTotalRecords($totalRecords)
            ->toJson();
    }

    public function getDepartmentByDivision($division_id)
    {
        $division = Division::find($division_id);
        $result = [['id' => '', 'text' => '']];

        foreach ($division->department as $department) {
            $result[] = ['id' => $department->id, 'text' => $department->department_name];
        }

        return response()->json($result);
    }
}