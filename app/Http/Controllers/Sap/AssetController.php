<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SapModel\SapAsset;
use DataTables;

class AssetController extends Controller
{
    public function index(Request $request)
    {

    	$asset = SapAsset::get();
    	if ($request->wantsJson()) {
            return response()->json($asset, 200);
        }

        return view('pages.sap.sapAsset.index');
    }
    public function create()
    {
    	$asset = SapAsset::get();

    	return view('pages.sap.sapAsset.create', compact('asset')) ;
    }

    public function store(Request $request)
    {
    	$asset 					= new SapAsset;
    	$asset->asset_code 		= $request->asset_code;
        $asset->asset_name 		= $request->asset_name;
        $asset->asset_type 		= $request->asset_type;
        $asset->asset_class 	= $request->asset_class;
        $asset->asset_content 	= $request->asset_content;
        $asset->asset_account 	= $request->asset_account;
        $asset->asset_acctext 	= $request->asset_acctext;
    	$asset->save();

    	if ($request->wantsJson()) {
            return response()->json($asset);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('asset.index')
                ->with($res);
    }

    public function getData()
    {

    	$asset = SapAsset::orderBy('id','DESC')->get();
    	
    	return DataTables::of($asset)
    	->rawColumns(['options'])

        ->addColumn('options', function($asset){
            return '
                <a href="'.route('asset.edit', $asset->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$asset->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('asset.destroy', $asset->id).'" method="POST" id="form-delete-'.$asset->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $asset = SapAsset::find($id);
        return view('pages.sap.sapAsset.edit', compact(['asset']));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_type' => 'required',
            'asset_code' => 'required'
        ]);

        $asset = SapAsset::find($id);

        if (empty($asset)) {
            return response()->json('Type not found', 500);
        }

        $asset->asset_code 		= $request->asset_code;
        $asset->asset_name 		= $request->asset_name;
        $asset->asset_type 		= $request->asset_type;
        $asset->asset_class 	= $request->asset_class;
        $asset->asset_content 	= $request->asset_content;
        $asset->asset_account 	= $request->asset_account;
        $asset->asset_acctext 	= $request->asset_acctext;
        
        $asset->save();

        if ($request->wantsJson()) {
            return response()->json($asset);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('asset.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $asset = SapAsset::find($id);

        if (empty($asset)) {
            return response()->json('Type not found', 500);
        }

        $asset->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('asset.index')
                ->with($res);
    }

    public function getCmbAsset()
	{
		$asset_account = SapAsset::all();
		$data = [];
		foreach($asset_account as $asset)
		{
			$data[] = array('value'=>$asset->asset_account,'text'=>$asset->asset_type);
		}
		
		return response()->json($data, 200);
	}
}
