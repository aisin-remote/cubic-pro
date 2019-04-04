<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UploadPurchaseOrder;
use App\ApprovalMaster;
use App\Exports\UploadPoExport;
use Excel;
use Storage;
use DB;

use DataTables;


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
		$po = DB::table('approval_masters')
                    ->select('approval_details.id as approval_detail_id','approval_masters.approval_number', 'approval_details.remarks', 'approval_masters.created_at','upload_purchase_orders.po_number','upload_purchase_orders.po_date','upload_purchase_orders.quotation','upload_purchase_orders.pr_receive')
                    ->Join('approval_details', 'approval_details.approval_master_id','=', 'approval_masters.id')
                    ->leftJoin('upload_purchase_orders', 'approval_details.id', '=', 'upload_purchase_orders.approval_detail_id')
                    ->where('approval_masters.status','4');
					
		if($request->from != "" && $request->to != ""){
			$from = date('Y-m-d',strtotime($request->from));
			$to   = date('Y-m-d',strtotime($request->to));
			$po->whereBetween('approval_masters.created_at', [$from, $to]);
		}
        
		$po = $po->orderBy('approval_masters.id','DESC')->get();
                    
        return DataTables::of($po)
        ->setRowId(function($po){
            return $po->approval_detail_id;
        })
		 ->editColumn("created_at", function ($po) {
				return date('d-M-Y H:i:s',strtotime($po->created_at));
		 })
		 ->editColumn("pr_receive", function ($po) {
				return date('d-M-Y',strtotime($po->pr_receive));
		 })
		  ->editColumn("po_date", function ($po) {
				return date('d-M-Y',strtotime($po->po_date));
		 })
        ->toJson();
    }

    public function create()
    {
        return view('pages.upload_po.create');
    }

    public function export() 
    {
        $pos = DB::table('approval_masters')
                    ->select('approval_details.id as approval_detail_id','approval_masters.approval_number', 'approval_details.remarks', 'approval_masters.created_at','upload_purchase_orders.po_number','upload_purchase_orders.po_date','upload_purchase_orders.quotation','upload_purchase_orders.pr_receive')
                    ->Join('approval_details', 'approval_details.approval_master_id','=', 'approval_masters.id')
                    ->leftJoin('upload_purchase_orders', 'approval_details.id', '=', 'upload_purchase_orders.approval_detail_id')
                    ->where('approval_masters.status','4')
                    // ->take(10)
                    ->get();
        $array = [];

        foreach ($pos as $po) {
            $array[] = [
                        'Approval Number' => $po->approval_number, 
                        'Name Of Good' => $po->remarks,   
                        'User Create PR Date' => $po->created_at,
                        'PR Receive' => $po->pr_receive,
                        'PO Number' => $po->po_number,
                        'PO Date' => $po->po_date,
                        'Quotation' => $po->quotation,
                        ];
        }

        return Excel::create('Data Input PO', function($excel) use ($array){
            $excel->sheet('Sheetname', function($sheet) use ($array) {

                $sheet->fromArray($array);
        
            });
        })->download('csv');

    }

    public function xedit(Request $request)
    {
        // dd($request->pk);
        $name = $request->name;
        $upo = UploadPurchaseOrder::firstOrNew(['approval_detail_id' => $request->pk]);
        $upo->approval_detail_id = $request->pk;
        // $upo->approval_number = $request->approval_number;
        $upo->$name = $request->value;
        $upo->save();

        return response()->json(['value' => $request->value], 200);

    }
}
