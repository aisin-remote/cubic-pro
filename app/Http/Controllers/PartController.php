<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Part;
use App\System;
use App\Exports\MasterPartExport;
use Excel;
use Storage;

use DataTables;

class PartController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $part = Part::get();
        $uom = System::configmultiply('uom');
        $plant = System::config('plant');
        $category_part = System::config('category_part');
        $product_code = System::configmultiply('product_code');
        $category_fg = System::config('category_fg');
        $assy_part  = System::config('assy_part');
        $group_material = System::config('group_material');

        
        if ($request->wantsJson()) {
            return response()->json($part, 200);
        }

        return view('pages.part');
        
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
            'part_number' => 'required',
            'part_name' => 'required'
        ]); 
        
        $part = new Part;
        $part->part_number = $request->part_number;
        $part->part_name = $request->part_name;
        $part->uom = $request->uom;
        $part->plant = $request->plant;
        $part->category_part = $request->category_part;
        $part->product_code = $request->product_code;
        $part->category_fg = $request->category_fg;
        $part->assy_part = $request->assy_part;
        $part->group_material = $request->group_material;
        $part->save();

        if ($request->wantsJson()){
            return response()->json($part->load(['uom','plat','category_part', 'product_code', 'category_fg', 'assy_part', 'group_material']), 200);    
        }
        
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data success!'
                ];

        return redirect()
                ->route('part.index')
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
        $part = Part::with(['uom','plat','category_part', 'product_code', 'category_fg', 'assy_part', 'group_material'])->find($id);
        
        if (empty($part)) {
            return response()->json('Part not found', 500);
        }

        return response()->json($part->load(['uom','plat','category_part', 'product_code', 'category_fg', 'assy_part', 'group_material']), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'part_number' => 'required',
            'part_name' => 'required'
        ]);
        
        $part = Part::find($id);

        if (empty($part)) {
            return response()->json('Part not found', 500);
        }

        $part->part_number = $request->part_number;
        $part->part_name = $request->part_name;
        $part->uom = $request->uom;
        $part->plant = $request->plant;
        $part->category_part = $request->category_part;
        $part->product_code = $request->product_code;
        $part->category_fg = $request->category_fg;
        $part->assy_part = $request->assy_part;
        $part->group_material = $request->group_material;
        $part->save();

        // return response()->json($department->load(['division']), 200);
        if ($request->wantsJson()) {
            return response()->json($part);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('part.index')
                ->with($res);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $part = Part::find($id);

        if (empty($part)) {
            return response()->json('Part not found', 500);
        }

        $part->delete();

        if($request->wantsJson()) {
            return response()->json('Part deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                    ->route('part.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
        $part = Part::get();
        return DataTables::of($part)
        ->rawColumns(['options'])

        ->addColumn('options', function($part){
            return '
                <a href="'.route('part.edit', $part->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$part->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('part.destroy', $part->id).'" method="POST" id="form-delete-'.$part->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }


    public function create()
    {
        $part = Part::get();
        $uom = System::configmultiply('uom');
        $plant = System::config('plant');
        $category_part = System::config('category_part');
        $product_code = System::configmultiply('product_code');
        $category_fg = System::config('category_fg');
        $assy_part  = System::config('assy_part');
        $group_material = System::config('group_material');

        return view('pages.part.create', compact(['uom','plant','category_part', 'product_code', 'category_fg', 'assy_part', 'group_material']));
        // dd($dir_keys);
    }

    public function edit($id)
    {
        $part = Part::find($id);
        $uom = System::configmultiply('uom');
        $plant = System::config('plant');
        $category_part = System::config('category_part');
        $product_code = System::configmultiply('product_code');
        $category_fg = System::config('category_fg');
        $assy_part  = System::config('assy_part');
        $group_material = System::config('group_material');


        return view('pages.part.edit', compact(['part', 'uom','plant','category_part', 'product_code', 'category_fg', 'assy_part', 'group_material']));
    }


    public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();

            // $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){
                if ($datas->first()->has('part_number')){
                    foreach ($datas as $data) {

                        $part_id = Part::where('part_number', $data->part_number)->first();
                        
                        $part                  = new Part;
                        $part->part_number     = $data->part_number;
                        $part->part_name       = $data->part_name;
                        $part->uom             = $data->uom;
                        $part->plant           = $data->plant;
                        $part->category_part   = $data->category_part;
                        $part->product_code    = $data->product_code;
                        $part->category_fg     = $data->category_fg;
                        $part->assy_part       = $data->assy_part;
                        $part->group_material  = $data->group_material;
                        $part->save();                  
                    }  

                // });
                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Upload Data Success!'
                            ];
                    Storage::delete('public/uploads/'.$name); 
                    return redirect()
                            ->route('part.index')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('part.index')
                            ->with(
                                [
                                    'title' => 'Error',
                                    'type' => 'error',
                                    'message' => 'Wrong Format!'
                                ]
                            );
                }
        }
    }

    public function export() 
    {
        $part = Part::all();

        return Excel::create('master_part', function($excel) use ($masterprices){
             $excel->sheet('mysheet', function($sheet) use ($masterprices){
                 $sheet->fromArray($masterparts);
             });

        })->download('csv');

    }


}
    