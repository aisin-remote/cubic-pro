<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterPrice;
use App\TemporaryMasterPrice;
use App\Part;
use App\Supplier;
use DataTables;
use App\Exports\MasterPricesExport;
use Excel;
use DB;
use App\System;
use Storage;

class MasterPriceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $masterprices = MasterPrice::all();
            return response()->json($masterprices);
        }

        return view('pages.masterprice.index');
    }

    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $temporarymasterprices = TemporaryMasterPrice::all();
            return response()->json($temporarymasterprices);
        }

        return view('pages.masterprice.temporary');
    }

   
     public function create()
    {
        $parts = Part::get();
        $suppliers = Supplier::get();
        return view('pages.masterprice.create',compact(['parts', 'suppliers']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $masterprice                = new MasterPrice;
        $masterprice->part_id       = $request->part_id;
        $masterprice->supplier_id   = $request->supplier_id;
        $masterprice->source        = $request->source;
        $masterprice->price_apr     = $request->price_apr;
        $masterprice->price_may     = $request->price_may;
        $masterprice->price_jun     = $request->price_jun;
        $masterprice->price_jul     = $request->price_jul;
        $masterprice->price_aug     = $request->price_aug;
        $masterprice->price_sep     = $request->price_sep;
        $masterprice->price_oct     = $request->price_oct;
        $masterprice->price_nov     = $request->price_nov;
        $masterprice->price_dec     = $request->price_dec;
        $masterprice->price_jan     = $request->price_jan;
        $masterprice->price_feb     = $request->price_feb;
        $masterprice->price_mar     = $request->price_mar;
        $masterprice->fiscal_year   = $request->fiscal_year;
        $masterprice->save();

        if ($request->wantsJson()){
            return response()->json($masterprice->load(['part_id','supplier_id']), 200);    
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil disimpan!'
                ];

        return redirect()
                    ->route('masterprice.index')
                    ->with($res);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $masterprice = MasterPrice::with(['parts','suppliers'])->find($id);
        
        if (empty($part)) {
            return response()->json('MasterPrice not found', 500);
        }

        return response()->json($part->load(['parts','suppliers']), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\masterprice  $masterprice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parts                                  = Part::get();
        $suppliers                              = Supplier::get();
        $masterprice                            = MasterPrice::find($id);

        return view('pages.masterprice.edit', compact(['suppliers', 'parts', 'masterprice']));
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

            $masterprice                = MasterPrice::find($id);
            $masterprice->part_id       = $request->part_id;
            $masterprice->supplier_id   = $request->supplier_id;
            $masterprice->source        = $request->source;
            $masterprice->price_apr     = $request->price_apr;
            $masterprice->price_may     = $request->price_may;
            $masterprice->price_jun     = $request->price_jun;
            $masterprice->price_jul     = $request->price_jul;
            $masterprice->price_aug     = $request->price_aug;
            $masterprice->price_sep     = $request->price_sep;
            $masterprice->price_oct     = $request->price_oct;
            $masterprice->price_nov     = $request->price_nov;
            $masterprice->price_dec     = $request->price_dec;
            $masterprice->price_jan     = $request->price_jan;
            $masterprice->price_feb     = $request->price_feb;
            $masterprice->price_mar     = $request->price_mar;
            $masterprice->fiscal_year   = $request->fiscal_year;

            $masterprice->save();
         });
        $res = [
                    'title'                     => 'Sukses',
                    'type'                      => 'success',
                    'message'                   => 'Data berhasil diubah!'
                ];
       
        return redirect()
                    ->route('masterprice.index')
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
            $masterprice                         = MasterPrice::find($id);
            $masterprice->delete();
        });

        $res = [
                    'title'                     => 'Sukses',
                    'type'                      => 'success',
                    'message'                   => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('masterprice.index')
                    ->with($res);
    }

    public function destroy_temporary($id)
    {
        DB::transaction(function() use ($id){
            $temporarymasterprices              = TemporaryMasterPrice::find($id);
            $temporarymasterprices->delete();
        });

        $res = [
                    'title'                     => 'Sukses',
                    'type'                      => 'success',
                    'message'                   => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('masterprice.temporary')
                    ->with($res);
    }
    public function getData(Request $request)
    {
        $masterprices = MasterPrice::with(['parts', 'suppliers'])->get();

        return DataTables::of($masterprices)

        ->rawColumns(['options'])

        ->addColumn('options', function($masterprice){
            return '
                <a href="'.route('masterprice.edit', $masterprice->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$masterprice->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('masterprice.destroy', $masterprice->id).'" method="POST" id="form-delete-'.$masterprice->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }
    public function getData_temporary(Request $request)
    {
        $temporarymasterprices = TemporaryMasterPrice::get();
       
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
        $masterprices = MasterPrice::select('fiscal_year','parts_masterprice.part_number as part_number', 'parts_masterprice.part_name as part_name','suppliers.supplier_code','suppliers.supplier_name', 'source','price')
                    ->join('parts as parts_masterprice', 'master_prices.part_id', '=', 'parts_masterprice.id')
                    ->join('suppliers', 'master_prices.supplier_id', '=', 'suppliers.id')
                    ->get();
        return Excel::create('Data Master Price', function($excel) use ($masterprices){
             $excel->sheet('mysheet', function($sheet) use ($masterprices){
                 $sheet->fromArray($masterprices);
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

                        $price                  = new TemporaryMasterPrice;
                        $price->part_id         = !empty($part_id) ? $part_id->id : 0;
                        $price->supplier_id     = !empty($supplier_id) ? $supplier_id->id : 0;
                        $price->part_number     = $data->part_number;
                        $price->supplier_code   = $data->supplier_code;
                        $price->source          = $data->source;
                        $price->price_apr       = $data->price_apr;
                        $price->price_may       = $data->price_may;
                        $price->price_jun       = $data->price_jun;
                        $price->price_jul       = $data->price_jul;
                        $price->price_aug       = $data->price_aug;
                        $price->price_sep       = $data->price_sep;
                        $price->price_oct       = $data->price_oct;
                        $price->price_nov       = $data->price_nov;
                        $price->price_dec       = $data->price_dec;
                        $price->price_jan       = $data->price_jan;
                        $price->price_feb       = $data->price_feb;
                        $price->price_mar       = $data->price_mar;
                        $price->fiscal_year     = $data->fiscal_year;
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
                            ->route('masterprice.temporary')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('masterprice.temporary')
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
        TemporaryMasterPrice::truncate();

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('masterprice.index')
                ->with($res);

    }

    public function save(){
        $temps = TemporaryMasterPrice::get();
        TemporaryMasterPrice::truncate();
        foreach ($temps as $temp) {

            if (!empty($temp->parts) && !empty($temp->suppliers)) {
                $price = new MasterPrice;
                $price->part_id     = $temp->part_id;
                $price->supplier_id = $temp->supplier_id;
                $price->source      = $temp->source;
                $price->price_apr   = $temp->price_apr;
                $price->price_may   = $temp->price_may;
                $price->price_jun   = $temp->price_jun;
                $price->price_jul   = $temp->price_jul;
                $price->price_aug   = $temp->price_aug;
                $price->price_sep   = $temp->price_sep;
                $price->price_oct   = $temp->price_oct;
                $price->price_nov   = $temp->price_nov;
                $price->price_dec   = $temp->price_dec;
                $price->price_jan   = $temp->price_jan;
                $price->price_feb   = $temp->price_feb;
                $price->price_mar   = $temp->price_mar;
                $price->fiscal_year = $temp->fiscal_year;
                $price->save();
                $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil di Di Simpan !'
                ]; 
            }
        }
        return redirect()
                ->route('masterprice.index')
                ->with($res);
    }
    public function templateMasterPrice() 
    {
       return Excel::create('Format Upload Data MasterPrice', function($excel){
             $excel->sheet('mysheet', function($sheet){
                 // $sheet->fromArray($boms);
                $sheet->cell('A1', function($cell) {$cell->setValue('fiscal_year');});
                $sheet->cell('B1', function($cell) {$cell->setValue('part_number');});
                $sheet->cell('C1', function($cell) {$cell->setValue('supplier_code');});
                $sheet->cell('D1', function($cell) {$cell->setValue('source');});
                $sheet->cell('E1', function($cell) {$cell->setValue('price_apr');});
                $sheet->cell('F1', function($cell) {$cell->setValue('price_may');});
                $sheet->cell('G1', function($cell) {$cell->setValue('price_jun');});
                $sheet->cell('H1', function($cell) {$cell->setValue('price_jul');});
                $sheet->cell('I1', function($cell) {$cell->setValue('price_aug');});
                $sheet->cell('J1', function($cell) {$cell->setValue('price_sep');});
                $sheet->cell('K1', function($cell) {$cell->setValue('price_oct');});
                $sheet->cell('L1', function($cell) {$cell->setValue('price_nov');});
                $sheet->cell('M1', function($cell) {$cell->setValue('price_dec');});
                $sheet->cell('N1', function($cell) {$cell->setValue('price_jan');});
                $sheet->cell('O1', function($cell) {$cell->setValue('price_feb');});
                $sheet->cell('P1', function($cell) {$cell->setValue('price_mar');});
                $sheet->cell('A2', function($cell) {$cell->setValue('2018');});
                $sheet->cell('B2', function($cell) {$cell->setValue('423176-10200');});
                $sheet->cell('C2', function($cell) {$cell->setValue('SUP01');});
                $sheet->cell('D2', function($cell) {$cell->setValue('Local');});
                $sheet->cell('E2', function($cell) {$cell->setValue('0');});
                $sheet->cell('F2', function($cell) {$cell->setValue('0');});
                $sheet->cell('G2', function($cell) {$cell->setValue('0');});
                $sheet->cell('H2', function($cell) {$cell->setValue('0');});
                $sheet->cell('I2', function($cell) {$cell->setValue('0');});
                $sheet->cell('J2', function($cell) {$cell->setValue('0');});
                $sheet->cell('K2', function($cell) {$cell->setValue('0');});
                $sheet->cell('L2', function($cell) {$cell->setValue('0');});
                $sheet->cell('M2', function($cell) {$cell->setValue('0');});
                $sheet->cell('N2', function($cell) {$cell->setValue('0');});
                $sheet->cell('O2', function($cell) {$cell->setValue('0');});
                $sheet->cell('P2', function($cell) {$cell->setValue('0');});

            
                 
             });

        })->download('csv');
    }
    
}
