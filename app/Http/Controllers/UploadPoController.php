<?php

namespace App\Http\Controllers;

use App\ApprovalDetail;
use Illuminate\Http\Request;
use App\UploadPurchaseOrder;
use App\ApprovalMaster;
use App\Exports\UploadPoExport;
use Excel;
use Storage;
use DB;

use DataTables;
use PhpParser\Node\Stmt\Return_;

class UploadPoController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $po = DB::table('approval_masters')
                    ->select('approval_details.id as approval_detail_id','approval_masters.approval_number', 'approval_details.remarks', 'approval_masters.created_at','upload_purchase_orders.po_number','upload_purchase_orders.po_date','upload_purchase_orders.quotation','upload_purchase_orders.pr_receive')
                    ->Join('approval_details', 'approval_details.approval_master_id','=', 'approval_masters.id')
                    ->leftJoin('upload_purchase_orders', 'approval_details.id', '=', 'upload_purchase_orders.approval_detail_id')
                    ->where('approval_masters.status','4')
                    ->get();


        if ($request->wantsJson()) {
            return response()->json($po, 200);
        }

        return view('pages.upload_po');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $po = new UploadPurchaseOrder;
        $po->approval_master_id = $request->id;
        $po->approval_number = $request->approval_number;
        $po->pr_receive = $request->pr_receive;
        $po->po_number = $request->po_number;
        $po->po_date = $request->po_date;
        $po->quotation = $request->quotation;
        $po->save();

        if ($request->wantsJson()) {
            return response()->json($po);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('upload_po.index')
                ->with($res);

    }

    public function show($id)
    {
        $po = UploadPurchaseOrder::find($id);

        if (empty($po)) {
            return response()->json('PO not found', 500);
        }
        return response()->json($po, 200);
    }

    public function getData(Request $request)
    {
        $notShownPo = DB::table('approver_users')->select('approval_master_id')
            ->where('is_approve', '!=', '1')
            ->groupBy('approval_master_id')
            ->pluck('approval_master_id')->toArray();

		$po = DB::table('approval_masters')
                    ->select('approval_details.id as approval_detail_id','approval_masters.approval_number', 'approval_details.remarks', 'approval_masters.created_at','upload_purchase_orders.po_number','upload_purchase_orders.po_date','upload_purchase_orders.quotation','upload_purchase_orders.pr_receive')
                    ->Join('approval_details', 'approval_details.approval_master_id','=', 'approval_masters.id')
                    ->leftJoin('upload_purchase_orders', 'approval_details.id', '=', 'upload_purchase_orders.approval_detail_id')
                    ->whereNotIn('approval_masters.id', $notShownPo);

		if($request->interval){
            $intervals = explode('-', $request->interval);

            if (count($intervals) > 1) {
                $from = date('Y-m-d', strtotime(trim($intervals[0])));
                $to = date('Y-m-d', strtotime(trim($intervals[1])));
			    $po->whereBetween('upload_purchase_orders.pr_receive', [$from, $to]);
            }
		}

        $po = $po->orderBy('approval_masters.id','DESC')->get();
        // dd($po);

        return DataTables::of($po)
            ->setRowId(function($po){
                return $po->approval_detail_id;
            })
            ->editColumn("created_at", function ($po) {
                    return date('d-M-Y H:i:s',strtotime($po->created_at));
            })
            ->editColumn("pr_receive", function ($po) {
                    return $po->pr_receive ? date('d-M-Y',strtotime($po->pr_receive)) : null;
            })
            ->editColumn("po_date", function ($po) {
                return $po->po_date && $po->po_date != '0000-00-00' ? date('d-M-Y',strtotime($po->po_date)) : null;
            })
            ->toJson();
    }

    public function create()
    {
        return view('pages.upload_po.create');
    }

    public function export(Request $request)
    {
        $notShownPo = DB::table('approver_users')->select('approval_master_id')
            ->where('is_approve', '!=', '1')
            ->groupBy('approval_master_id')
            ->pluck('approval_master_id')->toArray();

        $pos = DB::table('approval_masters')
                    ->select('approval_details.id as approval_detail_id','approval_masters.approval_number', 'approval_details.remarks', 'approval_details.sap_vendor_code', 'approval_masters.created_at','upload_purchase_orders.po_number','upload_purchase_orders.po_date','upload_purchase_orders.quotation','upload_purchase_orders.pr_receive', 'sap_vendors.vendor_fname')
                    ->Join('approval_details', 'approval_details.approval_master_id','=', 'approval_masters.id')
                    ->leftJoin('upload_purchase_orders', 'approval_details.id', '=', 'upload_purchase_orders.approval_detail_id')
                    ->leftJoin('sap_vendors', 'approval_details.sap_vendor_code', '=', 'sap_vendors.vendor_code')
                    ->whereNotIn('approval_masters.id', $notShownPo);

        if($request->interval){
            $intervals = explode('-', $request->interval);

            if (count($intervals) > 1) {
                $from = date('Y-m-d', strtotime(trim($intervals[0])));
                $to = date('Y-m-d', strtotime(trim($intervals[1])));
                $pos->whereBetween('upload_purchase_orders.pr_receive', [$from, $to]);
            }
        }

        $pos = $pos->orderBy('approval_masters.id','DESC')->get();

        $array = [];
        foreach ($pos as $po) {
            $array[] = [
                'Approval Number' => $po->approval_number,
                'Name Of Good' => $po->remarks,
                'User Create PR Date' => $po->created_at,
                'PR Receive' => $po->pr_receive,
                'PO Number' => $po->po_number,
                'PO Date' => $po->po_date && $po->po_date != '0000-00-00' ? date('Y-m-d', strtotime($po->po_date)) : '',
                'Vendor Code' => $po->sap_vendor_code,
                'Vendor Name' => $po->vendor_fname,
                'Quotation' => $po->quotation,
            ];
        }

        ob_end_clean();
        ob_start();
        return Excel::create('Data Input PO', function($excel) use ($array){
            $excel->sheet('Sheet 1', function($sheet) use ($array) {

                $sheet->fromArray($array);

            });
        })->export('xlsx');
    }

    public function xedit(Request $request)
    {
        $upoCheck = UploadPurchaseOrder::where('approval_detail_id', $request->pk)->first();
        if ($upoCheck) {

            $name = $request->name;
            
            if ($upoCheck->$name == NULL || $upoCheck->$name == '0000-00-00' ) {
                $approval_detail = ApprovalDetail::where('id', $request->pk)->first();
                $approval_number = ApprovalDetail::where('approval_master_id', $approval_detail->approval_master_id)->get();

                foreach ($approval_number as $key => $value) {
                    $name = $request->name;
                    $upo = UploadPurchaseOrder::firstOrNew(['approval_detail_id' => $value->id]);
                    $upo->approval_detail_id = $value->id;
                    $upo->$name = $request->value;
                    $upo->save();
                }
            } else {
                $upo = UploadPurchaseOrder::firstOrNew(['approval_detail_id' => $request->pk]);
                $upo->approval_detail_id = $request->pk;
                $upo->$name = $request->value;
                $upo->save();
            }
    
        } else {
            $approval_detail = ApprovalDetail::where('id', $request->pk)->first();
            $approval_number = ApprovalDetail::where('approval_master_id', $approval_detail->approval_master_id)->get();

            foreach ($approval_number as $key => $value) {
                $name = $request->name;
                $upo = UploadPurchaseOrder::firstOrNew(['approval_detail_id' => $value->id]);
                $upo->approval_detail_id = $value->id;
                $upo->$name = $request->value;
                $upo->save();
            }

        }


        return response()->json(['value' => $request->value], 200);

    }
}
