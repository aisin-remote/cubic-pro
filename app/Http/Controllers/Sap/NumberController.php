<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapNumber;
use DataTables;

class NumberController extends Controller
{
    public function index(Request $request)
    {

    	$number = SapNumber::get();
    	if ($request->wantsJson()) {
            return response()->json($number, 200);
        }

        return view('pages.sap.sapNumber.index');
    }
    public function create()
    {
    	$number = SapNumber::get();

    	return view('pages.sap.sapNumber.create', compact('number')) ;
    }

    public function store(Request $request)
    {
    	$number 					= new SapNumber;
    	$number->number_type 			= $request->number_type;
        $number->number_booked 			= $request->number_booked;
        $number->number_current 		= $request->number_current;
        
    	$number->save();

    	if ($request->wantsJson()) {
            return response()->json($number);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('number.index')
                ->with($res);
    }

    public function getData()
    {

    	$number = SapNumber::orderBy('id','DESC')->get();
    	
    	return DataTables::of($number)
    	->rawColumns(['options'])

        ->addColumn('options', function($number){
            return '
                <a href="'.route('number.edit', $number->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$number->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('number.destroy', $number->id).'" method="POST" id="form-delete-'.$number->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $number = SapNumber::find($id);
        return view('pages.sap.sapNumber.edit', compact(['number']));
    }

    public function update(Request $request, $id)
    {
        $number = SapNumber::find($id);

        if (empty($number)) {
            return response()->json('Type not found', 500);
        }

        $number->number_type 			= $request->number_type;
        $number->number_booked 			= $request->number_booked;
        $number->number_current 		= $request->number_current;
        $number->save();

        if ($request->wantsJson()) {
            return response()->json($number);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('number.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $number = SapNumber::find($id);

        if (empty($number)) {
            return response()->json('Type not found', 500);
        }

        $number->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('number.index')
                ->with($res);
    }
}
