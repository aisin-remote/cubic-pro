<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;
use DataTables;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $item_category = ItemCategory::get();
        
        if ($request->wantsJson()) {
            return response()->json($item_categorys, 200);
        }

        return view('pages.item_category');
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
            'category_code' => 'required',
            'category_name' => 'required'
        ]);

        $item_category = new ItemCategory;
        $item_category->category_code = $request->category_code;
        $item_category->category_name = $request->category_name;
        $item_category->save();

        if ($request->wantsJson()) {
            return response()->json($item_category);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('item_category.index')
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
        $item_category = ItemCategory::find($id);
        if (empty($item_category)) {
            return response()->json('Type not found', 500);
        }
        return response()->json($item_category, 200);
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
            'category_code' => 'required',
            'category_name' => 'required'
        ]);

        $item_category = ItemCategory::find($id);

        if (empty($item_category)) {
            return response()->json('Type not found', 500);
        }

        $item_category->category_code = $request->category_code;
        $item_category->category_name = $request->category_name;
        $item_category->save();

        if ($request->wantsJson()) {
            return response()->json($item_category);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('item_category.index')
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
        $item_category = ItemCategory::find($id);

        if (empty($item_category)) {
            return response()->json('Type not found', 500);
        }

        $item_category->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('item_category.index')
                ->with($res);

    }

    public function getData(Request $request)
    {
        $item_category = ItemCategory::get();
        return DataTables::of($item_category)
        ->rawColumns(['options'])

        ->addColumn('options', function($item_category){
            return '
                <a href="'.route('item_category.edit', $item_category->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$item_category->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('item_category.destroy', $item_category->id).'" method="POST" id="form-delete-'.$item_category->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function create()
    {
        return view('pages.item_category.create');
    }

    public function edit($id)
    {
        $item_category = ItemCategory::find($id);
        return view('pages.item_category.edit', compact(['item_category']));
    }

    // public function getDepartmentByDivision($division_id)
    // {
    //     $division = Division::find($division_id);
    //     $result = [['id' => '', 'text' => '']];

    //     foreach ($division->department as $department) {
    //         $result[] = ['id' => $department->id, 'text' => $department->department_name];
    //     }

    //     return response()->json($result);
    // }

    public function import(Request $request)
    {

        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        if ($request->hasFile('file')) {

            // $file = public_path('storage/uploads/1534217112.xls');
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();

            if ($datas->first()->has('category_code')) {
                
                foreach ($datas as $data) {
                    
                    if (!empty($data->category_code)) {

                        DB::transaction(function() use ($data){

                            $item_category = ItemCategory::firstOrNew(['category_code' => $data->category_code]);
                            $item_category->category_code = $data->category_code;
                            
                            $item_category->save();

                            $item_category->user_data()->delete();

                            $item_category = new ItemCategory;
                            $item_category->category_code = $data->category_code;
                            $item_category->category_name = $data->category_name;

                            $item_category->$item_categorys()->save($item_categorys);

                        });
                    }
                    
                }

                Storage::delete('public/uploads/'.$name);

                return redirect()
                        ->route('item_category.index')
                        ->with(
                            [
                                'title' => 'Sukses',
                                'type' => 'success',
                                'message' => 'Data berhasil di import!'
                            ]
                        );

            } else {

                Storage::delete('public/uploads/'.$name);

                return redirect()
                        ->route('item_category.index')
                        ->with(
                            [
                                'title' => 'Error',
                                'type' => 'error',
                                'message' => 'Format Buruk!'
                            ]
                        );
            }
                

        }

        
    }
}
