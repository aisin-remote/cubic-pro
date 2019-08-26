<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrConfirm;
use App\GrConfirmDetail;
use App\ApprovalMaster;
use App\ApprovalDetail;
use App\UploadPurchaseOrder;
use App\User;
use App\Department;


use DataTables;
use DB;
use Storage;
use Cart;

class GrConfirmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $gr_confirm = GrConfirm::with(['approval_master','user'])->get();
            $department = Department::with(['user'])->get();
            return response()->json($gr_confirm);
        }

        return view('pages.gr_confirm.index');
    }
  
    public function create()
    {
        $po         = UploadPurchaseOrder::get();
        return view('pages.gr_confirm.create', compact(['po', 'user', 'detail']));
    }

    public function getData(Request $request)
    {
        $gr_confirms = GrConfirm::with(['approval_master', 'user.department'])->get();
        		
        return DataTables::of($gr_confirms)

        ->rawColumns(['options'])

        ->addColumn('options', function($gr_confirms){
            return '
                <a href="'.route('gr_confirm.edit', $gr_confirms->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$gr_confirms->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('gr_confirm.destroy', $gr_confirms->id).'" method="POST" id="form-delete-'.$gr_confirms->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })
        ->addColumn('details_url', function($gr_confirms) {
            return url('gr_confirm/details-data/' . $gr_confirms->id);
        })

        ->toJson();
    }
    public function getDetailsData($id)
    {
        $details = GrConfirm::find($id)
                ->details()
                ->with(['approval_master','approval_detail'])
                ->get();

        return Datatables::of($details)->make(true);
    }

    public function getUser($po_number)
    {
        $approval_master = UploadPurchaseOrder::where('po_number', $po_number)->first();
        $approval_detail = ApprovalDetail::where('id',$approval_master->approval_detail_id)->first();
        $result = ApprovalMaster::where('id', $approval_detail->approval_master_id )->first();

        $result->user_name = $result->user->name;
        $result->department_name = $result->user->department->department_name;

        return response()->json($result);

    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'po_number' => 'required',
        //     'approval_id' => 'required'
        // ]);

        // $gr = new GrConfirm;
        // $gr->po_number = $request->po_number;
        // $gr->approval_id = $request->approval_master_id;
        // $gr->save();

        // if ($request->wantsJson()) {
        //     return response()->json($gr);
        // }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('gr_confirm.index')
                ->with($res);

    }

    public function edit($id)
    {
        $gr = GrConfirm::find($id);
        $approval_no = ApprovalMaster::find($gr->approval_id);
        $user = User::find($gr->user_id);
        $department = Department::find($user->department_id);
        return view('pages.gr_confirm.edit', compact(['gr', 'approval_no', 'user', 'department', 'detail']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GrConfirm  $bom
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $gr = GrConfirm::find($id);
            $gr->details()->delete();
            $gr->delete();

        });
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('gr_confirm.index')
                    ->with($res);
    }
   
}

