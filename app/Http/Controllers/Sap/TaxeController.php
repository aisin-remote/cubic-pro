<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapTaxe;
use DataTables;

class TaxeController extends Controller
{
    public function index(Request $request)
    {

    	$taxe = SapTaxe::get();
    	if ($request->wantsJson()) {
            return response()->json($taxe, 200);
        }

        return view('pages.sap.sapTaxe.index');
    }
    public function create()
    {
    	$taxe = SapTaxe::get();

    	return view('pages.sap.sapTaxe.create', compact('taxe')) ;
    }

    public function store(Request $request)
    {
    	$taxe 					= new SapTaxe;
    	$taxe->taxe_code 			= $request->taxe_code;
        $taxe->taxe_name 			= $request->taxe_name;
       
        
    	$taxe->save();

    	if ($request->wantsJson()) {
            return response()->json($taxe);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('taxe.index')
                ->with($res);
    }

    public function getData()
    {

    	$taxe = SapTaxe::get();
    	
    	return DataTables::of($taxe)
    	->rawColumns(['options'])

        ->addColumn('options', function($taxe){
            return '
                <a href="'.route('taxe.edit', $taxe->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$taxe->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('taxe.destroy', $taxe->id).'" method="POST" id="form-delete-'.$taxe->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $taxe = SapTaxe::find($id);
        return view('pages.sap.sapTaxe.edit', compact(['taxe']));
    }

    public function update(Request $request, $id)
    {
        $taxe = SapTaxe::find($id);

        if (empty($taxe)) {
            return response()->json('Type not found', 500);
        }

        $taxe->taxe_code 			= $request->taxe_code;
        $taxe->taxe_name 			= $request->taxe_name;
        $taxe->save();

        if ($request->wantsJson()) {
            return response()->json($taxe);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('taxe.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $taxe = SapTaxe::find($id);

        if (empty($taxe)) {
            return response()->json('Type not found', 500);
        }

        $taxe->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('taxe.index')
                ->with($res);
    }
	
	public function getCmbTax()
	{
		$sap_tax = SapTaxe::all();
		$data = [];
		foreach($sap_tax as $tax)
		{
			$data[] = array('value'=>$tax->tax_code,'text'=>$tax->tax_name);
		}
		
		return response()->json($data, 200);
	}
}
