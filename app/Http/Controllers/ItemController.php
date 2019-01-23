<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;
use App\Item;
use App\SapUom;
use App\Supplier;
use App\Exports\MasterItemExport;
use Excel;
use Storage;

use DataTables;

class ItemController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $item = Item::with(['item_category','uom','supplier'])->get();
                
        if ($request->wantsJson()) {
            return response()->json($item, 200);
        }

        return view('pages.item');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {       
        $item = new Item;
        $item->item_category_id = $request->item_category_id;
        $item->item_code = $request->item_code;
        $item->item_description = $request->item_description;
        $item->item_spesification = $request->item_spesification;
        $item->item_brand = $request->item_brand;
        $item->item_price = $request->item_price;
        $item->uom_id = $request->uom_id;
        $item->supplier_id = $request->supplier_id;
        $item->lead_times = $request->lead_times;
        $item->remarks = $request->remarks;
        $item->feature_image = $request->feature_image;
        $item->save();
        
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data success!'
                ];

        return redirect()
                ->route('item.index')
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
        $item = Item::with(['item_category','uom','supplier'])->find($id);
        
        if (empty($item)) {
            return response()->json('Item not found', 500);
        }

        return response()->json($item->load(['item_category','uom','supplier']), 200);
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
        $item = Item::find($id);

        if (empty($item)) {
            return response()->json('Item not found', 500);
        }

        $item->item_code = $request->item_code;
        $item->item_description = $request->item_description;
        $item->item_spesification = $request->item_spesification;
        $item->item_brand = $request->item_brand;
        $item->item_price = $request->item_price;
        $item->uom_id = $request->uom_id;
        $item->supplier_id = $request->supplier_id;
        $item->lead_times = $request->lead_times;
        $item->remark = $request->remark;
        $item->feature_image = $request->feature_image;
        $item->save();

        // return response()->json($department->load(['division']), 200);
        if ($request->wantsJson()) {
            return response()->json($item);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('item.index')
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
        $item = Item::find($id);

        if (empty($item)) {
            return response()->json('Item not found', 500);
        }

        $item->delete();

        if($request->wantsJson()) {
            return response()->json('Item deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                    ->route('item.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
        $item = Item::with(['item_category','uom','supplier'])->get();
        return DataTables::of($item)
        ->rawColumns(['options'])

        ->addColumn('options', function($item){
            return '
                <a href="'.route('item.edit', $item->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$item->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('item.destroy', $item->id).'" method="POST" id="form-delete-'.$item->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }


    public function create()
    {
        // $item = Item::with(['item_category','uom','supplier'])->get();
        $item = Item::get();
        $item_category = ItemCategory::get();
        $uom = SapUom::get();
        $supplier = Supplier::get();
        
        // dd($item_category);
        return view('pages.item.create', compact(['item_category','uom','supplier']));
        
    }

    public function edit($id)
    {
        $item = Item::with(['item_category','uom','supplier'])->get();
        
        return view('pages.item.edit', compact(['item', 'item_category','uom','supplier']));
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
                if ($datas->first()->has('item_code')){
                    foreach ($datas as $data) {

                        $item_id = Item::where('item_code', $data->item_code)->first();
                        
                        $item                       = new Item;
                        $item->item_category_id     = $data->item_category_id;
                        $item->item_code            = $data->item_code;
                        $item->item_description     = $data->item_description;
                        $item->item_spesification   = $data->item_spesification;
                        $item->item_brand           = $data->item_brand;
                        $item->item_price           = $data->item_price;
                        $item->uom_id               = $data->uom_id;
                        $item->supplier_id          = $data->supplier_id;
                        $item->lead_times           = $data->lead_times;
                        $item->remark               = $data->remark;
                        $item->save();                  
                    }  

                // });
                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Upload Data Success!'
                            ];
                    Storage::delete('public/uploads/'.$name); 
                    return redirect()
                            ->route('item.index')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('item.index')
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
        $item = Item::all();

        return Excel::create('master_item', function($excel) use ($masterprices){
             $excel->sheet('mysheet', function($sheet) use ($masterprices){
                 $sheet->fromArray($masterparts);
             });

        })->download('csv');

    }
}



    
