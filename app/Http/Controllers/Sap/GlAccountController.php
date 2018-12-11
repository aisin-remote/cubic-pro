<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapGlAccount;
use DataTables;

class GlAccountController extends Controller
{
    public function index(Request $request)
    {

    	$gl_account = SapGlAccount::get();
    	if ($request->wantsJson()) {
            return response()->json($gl_account, 200);
        }

        return view('pages.sap.sapGlAccount.index');
    }
    public function create()
    {
    	$gl_account = SapGlAccount::get();

    	return view('pages.sap.sapGlAccount.create', compact('gl_account')) ;
    }

    public function store(Request $request)
    {
    	$gl_account 					= new SapGlAccount;
    	$gl_account->gl_gcode 			= $request->gl_gcode;
        $gl_account->gl_gname 			= $request->gl_gname;
        $gl_account->gl_acode 			= $request->gl_acode;
        $gl_account->gl_aname 			= $request->gl_aname;
        $gl_account->dep_key 			= $request->dep_key;

    	$gl_account->save();

    	if ($request->wantsJson()) {
            return response()->json($gl_account);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('gl_account.index')
                ->with($res);
    }

    public function getData()
    {

    	$gl_account = SapGlAccount::get();
    	
    	return DataTables::of($gl_account)
    	->rawColumns(['options'])

        ->addColumn('options', function($gl_account){
            return '
                <a href="'.route('gl_account.edit', $gl_account->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$gl_account->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('gl_account.destroy', $gl_account->id).'" method="POST" id="form-delete-'.$gl_account->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $gl_account = SapGlAccount::find($id);
        return view('pages.sap.sapGlAccount.edit', compact(['gl_account']));
    }

    public function update(Request $request, $id)
    {
        $gl_account = SapGlAccount::find($id);

        if (empty($gl_account)) {
            return response()->json('Type not found', 500);
        }

        $gl_account->gl_gcode 			= $request->gl_gcode;
        $gl_account->gl_gname 			= $request->gl_gname;
        $gl_account->gl_acode 			= $request->gl_acode;
        $gl_account->gl_aname 			= $request->gl_aname;
        $gl_account->dep_key 			= $request->dep_key;
        
        $gl_account->save();

        if ($request->wantsJson()) {
            return response()->json($gl_account);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('gl_account.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $gl_account = SapGlAccount::find($id);

        if (empty($gl_account)) {
            return response()->json('Type not found', 500);
        }

        $gl_account->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('gl_account.index')
                ->with($res);
    }
}
