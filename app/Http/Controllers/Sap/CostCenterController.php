<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapCostCenter;
use DataTables;

class CostCenterController extends Controller
{
public function index(Request $request)
    {

    	$cost = SapCostCenter::get();
    	if ($request->wantsJson()) {
            return response()->json($cost, 200);
        }

        return view('pages.sap.sapCostCenter.index');
    }
    public function create()
    {
    	$cost = SapCostCenter::get();

    	return view('pages.sap.sapCostCenter.create', compact('cost')) ;
    }

    public function store(Request $request)
    {
    	$cost 					= new SapCostCenter;
    	$cost->cc_code 			= $request->cc_code;
        $cost->cc_sname 		= $request->cc_sname;
        $cost->cc_fname 		= $request->cc_fname;
        $cost->cc_gcode 		= $request->cc_gcode;
        $cost->cc_gtext 		= $request->cc_gtext;

    	$cost->save();

    	if ($request->wantsJson()) {
            return response()->json($cost);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('cost_center.index')
                ->with($res);
    }

    public function getData()
    {

    	$cost = SapCostCenter::get();
    	
    	return DataTables::of($cost)
    	->rawColumns(['options'])

        ->addColumn('options', function($cost){
            return '
                <a href="'.route('cost_center.edit', $cost->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$cost->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('cost_center.destroy', $cost->id).'" method="POST" id="form-delete-'.$cost->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $cost = SapCostCenter::find($id);
        return view('pages.sap.sapCostCenter.edit', compact(['cost']));
    }

    public function update(Request $request, $id)
    {
        $cost = SapCostCenter::find($id);

        if (empty($cost)) {
            return response()->json('Type not found', 500);
        }

        $cost->cc_code 			= $request->cc_code;
        $cost->cc_sname 		= $request->cc_sname;
        $cost->cc_fname 		= $request->cc_fname;
        $cost->cc_gcode 		= $request->cc_gcode;
        $cost->cc_gtext 		= $request->cc_gtext;
        
        $cost->save();

        if ($request->wantsJson()) {
            return response()->json($cost);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('cost_center.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $cost = SapCostCenter::find($id);

        if (empty($cost)) {
            return response()->json('Type not found', 500);
        }

        $cost->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('cost_center.index')
                ->with($res);
    }
}
