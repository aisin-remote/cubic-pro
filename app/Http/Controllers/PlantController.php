<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plant;

use DataTables;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $plant = Plant::get();

        if ($request->wantsJson()) {
            return response()->json($divisions, 200);
        }

        return view('pages.plant');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'plant_code' => 'required',
            'plant_name' => 'required'
        ]);

        $plant = new Plant;
        $plant->plant_code = $request->plant_code;
        $plant->plant_name = $request->plant_name;
        $plant->save();

        if ($request->wantsJson()) {
            return response()->json($plant);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('plant.index')
                ->with($res);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Park  $park
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plant = Plant::find($id);
        if (empty($plant)) {
            return response()->json('Type not found', 500);
        }
        return response()->json($plant, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'plant_code' => 'required',
            'plant_name' => 'required'
        ]);

        $plant = Plant::find($id);

        if (empty($plant)) {
            return response()->json('Type not found', 500);
        }

        $plant->plant_code = $request->plant_code;
        $plant->plant_name = $request->plant_name;
        $plant->save();

        if ($request->wantsJson()) {
            return response()->json($plant);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('plant.index')
                ->with($res);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $plant = Plant::find($id);

        if (empty($plant)) {
            return response()->json('Type not found', 500);
        }

        $plant->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('plant.index')
                ->with($res);

    }

    public function getData(Request $request)
    {
        $plant = Plant::get();
        return DataTables::of($plant)
        ->rawColumns(['options'])

        ->addColumn('options', function($plant){
            return '
                <a href="'.route('plant.edit', $plant->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$plant->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('plant.destroy', $plant->id).'" method="POST" id="form-delete-'.$plant->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function create()
    {
        return view('pages.plant.create');
    }

    public function edit($id)
    {
        $plant = Plant::find($id);
        return view('pages.plant.edit', compact(['plant']));
    }

}