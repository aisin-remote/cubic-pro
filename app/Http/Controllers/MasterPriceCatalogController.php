<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterPriceCatalog;
use App\TemporaryMasterPriceCatalog;
use App\Part;
use App\Supplier;
use DataTables;
use App\Exports\MasterPriceCatalogsExport;
use Excel;
use DB;
use Storage;

class MasterPriceCatalogController extends Controller
{
	 public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $catalog = MasterPriceCatalog::all();
            return response()->json($catalog);
        }

        return view('pages.price_catalogue.index');
    }

    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $temporary = TemporaryMasterPriceCatalog::all();
            return response()->json($temporary);
        }

        return view('pages.price_catalogue.temporary');
    }

   
     public function create()
    {
        $parts = Part::get();
        $suppliers = Supplier::get();
        return view('pages.price_catalogue.create',compact(['parts', 'suppliers']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $catalog                = new MasterPriceCatalog;
        $catalog->part_id       = $request->part_id;
        $catalog->supplier_id   = $request->supplier_id;
        $catalog->source        = $request->source;
        $catalog->price         = $request->price;
        $catalog->save();

        $res = [
                    'title' 	=> 'Sukses',
                    'type' 		=> 'success',
                    'message' 	=> 'Data berhasil disimpan!'
                ];

        return redirect()
                    ->route('price_catalogue.index')
                    ->with($res);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function show(Catalog $catalog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parts          = Part::get();
        $suppliers      = Supplier::get();
        $catalog       = MasterPriceCatalog::find($id);

        return view('pages.price_catalogue.edit', compact(['catalog', 'parts', 'suppliers']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        DB::transaction(function() use ($request, $id){

            $catalog                         = MasterPriceCatalog::find($id);
            $catalog->part_id                = $request->part_id;
            $catalog->supplier_id            = $request->supplier_id;
            $catalog->source                 = $request->source;
            $catalog->price                  = $request->price;
            $catalog->save();
         });
        $res = [
                    'title'   		=> 'Sukses',
                    'type'    		=> 'success',
                    'message' 		=> 'Data berhasil diubah!'
                ];
       
        return redirect()
                    ->route('price_catalogue.index')
                    ->with($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $catalog 			= MasterPriceCatalog::find($id);
            $catalog->delete();
        });

        $res = [
                    'title' 		=> 'Sukses',
                    'type' 			=> 'success',
                    'message' 		=> 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('price_catalogue.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
        $catalog = MasterPriceCatalog::with(['parts', 'suppliers'])->get();
        // dd($catalog);

        return DataTables::of($catalog)

        ->rawColumns(['options'])

        ->addColumn('options', function($catalog){
            return '
                <a href="'.route('price_catalogue.edit', $catalog->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$catalog->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('price_catalogue.destroy', $catalog->id).'" method="POST" id="form-delete-'.$catalog->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }
    public function getData_temporary(Request $request)
    {
        $temporarymasterprices = TemporaryMasterPriceCatalog::get();
       
        return DataTables::of($temporarymasterprices)

        ->addColumn('suppliers.supplier_code', function($temporarymasterprices) {
            return !empty($temporarymasterprices->suppliers) ? $temporarymasterprices->suppliers->supplier_code : $temporarymasterprices->supplier_code.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($temporarymasterprices) {
            return !empty($temporarymasterprices->parts) ? $temporarymasterprices->parts->part_number : $temporarymasterprices->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($temporarymasterprices) {
            
            return !empty($temporarymasterprices->parts) && !empty($temporarymasterprices->suppliers)? 'alert-success' : 'alert-warning';
        })
        ->setRowData([
            'id' => '1',
        ])
        ->setRowAttr([
            'color' => 'red',
        ])
        

        ->toJson();
    }
    
    public function export() 
    {
        $catalogs = MasterPriceCatalog::all();

        return Excel::create('data_masterprice', function($excel) use ($catalogs){
             $excel->sheet('mysheet', function($sheet) use ($catalogs){
                 $sheet->fromArray($catalogs);
             });

        })->download('csv');

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
                if ($datas->first()->has('part_number') && $datas->first()->has('supplier_code')) {
                    foreach ($datas as $data) {

                        $part_id = Part::where('part_number', $data->part_number)->first();
                        $supplier_id = Supplier::where('supplier_code', $data->supplier_code)->first();

                        $price                  = new TemporaryMasterPriceCatalog;
                        $price->part_id         = !empty($part_id) ? $part_id->id : 0;
                        $price->supplier_id     = !empty($supplier_id) ? $supplier_id->id : 0;
                        $price->part_number     = $data->part_number;
                        $price->supplier_code   = $data->supplier_code;
                        $price->source          = $data->source;
                        $price->price           = $data->price;
                        $price->save();                  
                    }  

                // });
                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Data berhasil di Upload!'
                            ];
                    Storage::delete('public/uploads/'.$name); 
                    return redirect()
                            ->route('price_catalogue.temporary')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('price_catalogue.temporary')
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

    public function cancel(){
        TemporaryMasterPriceCatalog::truncate();

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('price_catalogue.index')
                ->with($res);

    }

    public function save(){
        $temps = TemporaryMasterPriceCatalog::get();
        TemporaryMasterPriceCatalog::truncate();
        foreach ($temps as $temp) {

            if (!empty($temp->parts) && !empty($temp->suppliers)) {
                $price              = new MasterPriceCatalog;
                $price->part_id     =   $temp->part_id;
                $price->supplier_id =   $temp->supplier_id;
                $price->source      =   $temp->source;
                $price->price       =   $temp->price;
                $price->save();
                $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil di Di Simpan !'
                ]; 
            }
        }
        return redirect()
                ->route('price_catalogue.index')
                ->with($res);
    }
}
