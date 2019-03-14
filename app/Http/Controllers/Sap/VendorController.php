<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SapModel\SapVendor;
use DataTables;

class VendorController extends Controller
{
    public function index(Request $request)
    {

    	$vendor = SapVendor::get();
    	if ($request->wantsJson()) {
            return response()->json($vendor, 200);
        }

        return view('pages.sap.sapVendor.index');
    }
    public function create()
    {
    	$vendor = SapVendor::get();

    	return view('pages.sap.sapVendor.create', compact('vendor')) ;
    }

    public function store(Request $request)
    {
    	$vendor 					= new SapVendor;
    	$vendor->vendor_code 		= $request->vendor_code;
        $vendor->vendor_sname 		= $request->vendor_sname;
        $vendor->vendor_fname 		= $request->vendor_fname;
       
    	$vendor->save();

    	if ($request->wantsJson()) {
            return response()->json($vendor);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('vendor.index')
                ->with($res);
    }

    public function getData()
    {

    	$vendor = SapVendor::get();
    	
    	return DataTables::of($vendor)
    	->rawColumns(['options'])

        ->addColumn('options', function($vendor){
            return '
                <a href="'.route('vendor.edit', $vendor->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$vendor->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('vendor.destroy', $vendor->id).'" method="POST" id="form-delete-'.$vendor->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $vendor = SapVendor::find($id);
        return view('pages.sap.sapVendor.edit', compact(['vendor']));
    }

    public function update(Request $request, $id)
    {
        $vendor = SapVendor::find($id);

        if (empty($vendor)) {
            return response()->json('Type not found', 500);
        }

        $vendor->vendor_code 		= $request->vendor_code;
        $vendor->vendor_sname 		= $request->vendor_sname;
        $vendor->vendor_fname 		= $request->vendor_fname;
        $vendor->save();

        if ($request->wantsJson()) {
            return response()->json($vendor);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('vendor.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $vendor = SapVendor::find($id);

        if (empty($vendor)) {
            return response()->json('Type not found', 500);
        }

        $vendor->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('vendor.index')
                ->with($res);
    }
	
	public function getCmbVendor()
	{
		$vendor = SapVendor::all();
		$data = [];
		foreach($vendor as $v)
		{
			$data[] = array('value'=>$v->vendor_code,'text'=>$v->vendor_fname);
		}
		
		return response()->json($data, 200);
	}
}
