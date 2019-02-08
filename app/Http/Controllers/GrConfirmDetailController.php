<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrConfirmDetail;
use App\ApprovalDetail;
use App\ApprovalMaster;
use DataTables;
use DB;
use App\GrConfirm;
use App\UploadPurchaseOrder;

class GrConfirmDetailController extends Controller
{
    public function getData(Request $request)
    {

        if (!empty($request->po_number)) {
            $gr = GrConfirm::where('po_number', $request->po_number)->first();

            if (!empty($gr)) {

                $details = $gr->details()->with('approval_detail')
                                    ->get();

                return DataTables::of($details)
                        ->setRowId(function ($detail) {
                            return $detail->id;
                        })
                        ->toJson();

            } else {
                
                $upload = UploadPurchaseOrder::where('po_number', $request->po_number)->first();
                $new_gr = ApprovalMaster::where('approval_number', $upload->approval_number)->first();
                
                DB::transaction(function() use ($new_gr, $request){

                    $save_new = new GrConfirm;
                    $save_new->po_number = $request->po_number;
                    $save_new->approval_id = $new_gr->id;
                    $save_new->user_id = $new_gr->created_by;
                    $save_new->save();

                    foreach ($new_gr->details as $new_gr_details) {

                        $details_new = new GrConfirmDetail;
                        $details_new->qty_order = $new_gr_details->actual_qty;
                        $details_new->approval_detail_id = $new_gr_details->id;
                        $save_new->details()->save($details_new);
                    }   

                });

                $gr = GrConfirm::where('po_number', $request->po_number)->first();
                $details = $gr->details()->with('approval_detail')
                                ->get();

                return DataTables::of($details)
                ->setRowId(function ($detail) {
                    return $detail->id;
                })
                ->toJson();

                // return response()->json($new_gr->details);

            }   

        } else {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $result['data'] = [];

            return response()->json($result);
        }
        
    }
    
    public function xedit(Request $request)
    {
        
        $gr_detail = GrConfirmDetail::find($request->pk);

        if ($request->value > $gr_detail->qty_order ){
              throw new \Exception("Value more than Qty Order.", 1);
        }

        $name = $request->name;
        $gr_detail->$name = $request->value;
        $gr_detail->qty_outstanding = $request->qty_outstanding;
        $gr_detail->save();

        return response()->json($gr_detail);

    }
}