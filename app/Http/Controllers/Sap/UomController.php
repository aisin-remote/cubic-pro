<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapUom;
use DataTables;

class UomController extends Controller
{
    public function index(Request $request)
    {

    	$uom = SapUom::get();
    	if ($request->wantsJson()) {
            return response()->json($uom, 200);
        }

        return view('pages.sap.sapUom.index');
    }
    public function create()
    {
    	$uom = SapUom::get();

    	return view('pages.sap.sapUom.create', compact('uom')) ;
    }

    public function store(Request $request)
    {
    	$uom 					= new SapUom;
    	$uom->uom_code 			= $request->uom_code;
        $uom->uom_sname 		= $request->uom_sname;
        $uom->uom_fname 		= $request->uom_fname;
       
    	$uom->save();

    	if ($request->wantsJson()) {
            return response()->json($uom);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('uom.index')
                ->with($res);
    }

    public function getData()
    {

    	$uom = SapUom::orderBy('id','DESC')->get();
    	
    	return DataTables::of($uom)
    	->rawColumns(['options'])

        ->addColumn('options', function($uom){
            return '
                <a href="'.route('uom.edit', $uom->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$uom->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('uom.destroy', $uom->id).'" method="POST" id="form-delete-'.$uom->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $uom = SapUom::find($id);
        return view('pages.sap.sapUom.edit', compact(['uom']));
    }

    public function update(Request $request, $id)
    {
        $uom = SapUom::find($id);

        if (empty($uom)) {
            return response()->json('Type not found', 500);
        }

        $uom->uom_code 			= $request->uom_code;
        $uom->uom_sname 		= $request->uom_sname;
        $uom->uom_fname 		= $request->uom_fname;
        $uom->save();

        if ($request->wantsJson()) {
            return response()->json($uom);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('uom.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $uom = SapUom::find($id);

        if (empty($uom)) {
            return response()->json('Type not found', 500);
        }

        $uom->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('uom.index')
                ->with($res);
    }
}
