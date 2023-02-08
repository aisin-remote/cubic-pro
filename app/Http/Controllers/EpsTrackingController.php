<?php

namespace App\Http\Controllers;

use App\ApprovalDetail;
use Illuminate\Http\Request;
use App\Http\DataTables\CollectionCustom;
use App\Period;
use DataTables;
use DB;
use Illuminate\Support\Facades\Config;
use Excel;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
        // diable karena CollectionCustom nit found, tetapi tetap running, by Handika.
        //Config::set('datatables.engines.collection', CollectionCustom::class);

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

    public function export(Request $request)
    {

        ob_end_clean();
        ob_start();
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Aiia')
            ->setLastModifiedBy('Aiia')
            ->setTitle('Office 2021 XLSX Aiia Document')
            ->setSubject('Office 2021 XLSX Aiia Document')
            ->setDescription('Office 2021 XLSX Aiia Document.')
            ->setKeywords('Office 2021 XLSX Aiia Document')
            ->setCategory('Office 2021 XLSX Aiia Document');

        $spreadsheet->setActiveSheetIndex(0);


        $prCreated = $request->pr_created;

        if ($prCreated) {
            $intervals = explode('-', $prCreated);

            if (count($intervals) > 1) {
                $from = date('Y-m-d', strtotime(trim($intervals[0])));
                $to = date('Y-m-d', strtotime(trim($intervals[1])));
            }
        }

        $user  = auth()->user();
        DB::enableQueryLog();
        $query = "SELECT am.approval_number as `APPROVAL NUMBER`, ad.project_name as `PROJECT NAME`, am.created_at as `PR CREATED AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 1 limit 1) LIMIT 1) AS `BUDGET APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 2 limit 1) LIMIT 1) AS `DEPT HEAD APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 3 limit 1) LIMIT 1) AS `GM APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 4 limit 1) LIMIT 1) AS `DIR. APPROVE AT`, upo.pr_receive as `PR RECEIVED AT`, upo.po_date as `PO DATE`, upo.po_number as `PO NUMBER`, i.item_code as `ITEM CODE`, i.item_description as `ITEM DESCRIPTION`, ad.actual_qty as `ACTUAL QTY`, ad.pr_uom as `PR UOM`, ad.actual_price_user as `ACTUAL PRICE USER`, v.vendor_fname as `SUPPLIER NAME`, u.name as `USER NAME`, gcd.gr_no as `GR NO.`, gcd.created_at as `GR DATE`, gcd.qty_receive as `QTY RECEIVE`, gcd.qty_outstanding as `QTY OUTSTANDING`, gcd.notes as `NOTES` FROM approval_details ad LEFT OUTER JOIN approval_masters am ON ad.approval_master_id = am.id LEFT OUTER JOIN upload_purchase_orders upo ON ad.id = upo.approval_detail_id LEFT OUTER JOIN items i on ad.item_id = i.id LEFT OUTER JOIN sap_vendors v ON v.vendor_code = ad.sap_vendor_code LEFT OUTER JOIN users u on am.created_by = u.id LEFT OUTER JOIN gr_confirm_details gcd ON ad.id = gcd.approval_detail_id";

        if ($prCreated) {
            $intervals = explode('-', $prCreated);

            $query .= "WHERE (am.created_at > '$from' && am.created_at < '$to') ";
        }

        if ($user->hasRole('department-head') || $user->hasRole('user')) {
            $deptCode = $user->department->department_code;
            if (!$prCreated) {
                $query .= " WHERE";
            } else {
                $query .= "AND";
            }
            $query .= " am.department= '$deptCode' ";
            // dd($query);

        }
        DB::disableQueryLog();

        $queries = DB::getQueryLog();
        
        $last_query = end($queries);
        // dd($last_query);
        $eps_tracking  = DB::select($query);
        $data = json_decode(json_encode($eps_tracking), true);

        // dd($data[0]);

        if (count($data) > 0) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'APPROVAL NUMBER')
                ->setCellValue('B1', 'PROJECT NAME')
                ->setCellValue('C1', 'PR CREATED AT')
                ->setCellValue('D1', 'BUDGET APPROVE AT')
                ->setCellValue('E1', 'DEPT HEAD APPROVE AT')
                ->setCellValue('F1', 'GM APPROVE AT')
                ->setCellValue('G1', 'DIR. APPROVE AT')
                ->setCellValue('H1', 'PR RECEIVED AT')
                ->setCellValue('I1', 'PO DATE')
                ->setCellValue('J1', 'PO NUMBER')
                ->setCellValue('K1', 'ITEM CODE')
                ->setCellValue('L1', 'ITEM DESCRIPTION')
                ->setCellValue('M1', 'ACTUAL QTY')
                ->setCellValue('N1', 'PR UOM')
                ->setCellValue('O1', 'ACTUAL PRICE USER')
                ->setCellValue('P1', 'SUPPLIER NAME')
                ->setCellValue('Q1', 'USER NAME')
                ->setCellValue('R1', 'GR NO.')
                ->setCellValue('S1', 'GR DATE')
                ->setCellValue('T1', 'QTY RECEIVE')
                ->setCellValue('U1', 'QTY OUTSTANDING')
                ->setCellValue('V1', 'NOTES');
            $x = 2;
            foreach ($data as $val) {

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $x, $val['APPROVAL NUMBER'])
                    ->setCellValue('B' . $x, $val['PROJECT NAME'])
                    ->setCellValue('C' . $x, $val['PR CREATED AT'])
                    ->setCellValue('D' . $x, $val['BUDGET APPROVE AT'])
                    ->setCellValue('E' . $x, $val['DEPT HEAD APPROVE AT'])
                    ->setCellValue('F' . $x, $val['GM APPROVE AT'])
                    ->setCellValue('G' . $x, $val['DIR. APPROVE AT'])
                    ->setCellValue('H' . $x, $val['PR RECEIVED AT'])
                    ->setCellValue('I' . $x, $val['PO DATE'])
                    ->setCellValue('J' . $x, $val['PO NUMBER'])
                    ->setCellValue('K' . $x, $val['ITEM CODE'])
                    ->setCellValue('L' . $x, $val['ITEM DESCRIPTION'])
                    ->setCellValue('M' . $x, $val['ACTUAL QTY'])
                    ->setCellValue('N' . $x, $val['PR UOM'])
                    ->setCellValue('O' . $x, $val['ACTUAL PRICE USER'])
                    ->setCellValue('P' . $x, $val['SUPPLIER NAME'])
                    ->setCellValue('Q' . $x, $val['USER NAME'])
                    ->setCellValue('R' . $x, $val['GR NO.'])
                    ->setCellValue('S' . $x, $val['GR DATE'])
                    ->setCellValue('T' . $x, $val['QTY RECEIVE'])
                    ->setCellValue('U' . $x, $val['QTY OUTSTANDING'])
                    ->setCellValue('V' . $x, $val['NOTES']);

                $x++;
            }
        }

        $spreadsheet->getActiveSheet()->setTitle('Data');
        $sheet = $spreadsheet->getSheet(0);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $sheet->getStyle('A1:V1')->applyFromArray(
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'textRotation' => 0,
                ],
                'font' => [
                    'bold' => true,
                ],
            ]
        );
        $sheet->getStyle('A1' . ':V' . (count($data) + 1))->applyFromArray(
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]
        );
        $filename = 'EPS Tracking';

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_end_clean(); // this
        ob_start(); // and
        $writer->save('php://output');
        exit;
    }
    public function exportold(Request $request)
    {
        $prCreated = $request->pr_created;

        if ($prCreated){
            $intervals = explode('-', $prCreated);

            if (count($intervals) > 1) {
                $from = date('Y-m-d', strtotime(trim($intervals[0])));
                $to = date('Y-m-d', strtotime(trim($intervals[1])));
            }
		}

        $user  = auth()->user();

        $query = "SELECT am.approval_number as `APPROVAL NUMBER`, ad.project_name as `PROJECT NAME`, am.created_at as `PR CREATED AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 1 limit 1) LIMIT 1) AS `BUDGET APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 2 limit 1) LIMIT 1) AS `DEPT HEAD APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 3 limit 1) LIMIT 1) AS `GM APPROVE AT`, (SELECT au.created_at FROM approver_users au WHERE au.approval_master_id = am.id and au.user_id = (select adt.user_id from approval_dtls adt where adt.approval_id = (SELECT aps.id from approvals aps where aps.department = am.department) and adt.level = 4 limit 1) LIMIT 1) AS `DIR. APPROVE AT`, upo.pr_receive as `PR RECEIVED AT`, upo.po_date as `PO DATE`, upo.po_number as `PO NUMBER`, i.item_code as `ITEM CODE`, i.item_description as `ITEM DESCRIPTION`, ad.actual_qty as `ACTUAL QTY`, ad.pr_uom as `PR UOM`, ad.actual_price_user as `ACTUAL PRICE USER`, v.vendor_fname as `SUPPLIER NAME`, u.name as `USER NAME`, gcd.gr_no as `GR NO.`, gcd.created_at as `GR DATE`, gcd.qty_receive as `QTY RECEIVE`, gcd.qty_outstanding as `QTY OUTSTANDING`, gcd.notes as `NOTES` FROM approval_details ad LEFT OUTER JOIN approval_masters am ON ad.approval_master_id = am.id LEFT OUTER JOIN upload_purchase_orders upo ON ad.id = upo.approval_detail_id LEFT OUTER JOIN items i on ad.item_id = i.id LEFT OUTER JOIN sap_vendors v ON v.vendor_code = ad.sap_vendor_code LEFT OUTER JOIN users u on am.created_by = u.id LEFT OUTER JOIN gr_confirm_details gcd ON ad.id = gcd.approval_detail_id ";

        if ($prCreated){
            $intervals = explode('-', $prCreated);

            $query .= "WHERE (am.created_at > '$from' && am.created_at < '$to') ";
		}

        if ($user->hasRole('department-head') || $user->hasRole('user')) {
            $deptCode = $user->department->department_code;
            if (!$prCreated) {
                $query .= "WHERE";
            } else {
                $query .= "AND";
            }

            $query .= " am.department = '$deptCode' ";
        }

        $eps_tracking  = DB::select($query);
        $data = json_decode(json_encode($eps_tracking), true);

        ob_end_clean();
        ob_start();
        return Excel::create('EPS Tracking', function($excel) use ($data){
            $excel->sheet('EPS Tracking', function($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->export('xlsx');
    }
}