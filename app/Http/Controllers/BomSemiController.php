<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BomSemi;
use App\BomSemiData;
use App\TemporaryBomSemi;
use App\TemporaryBomSemiData;
use App\Part;
use App\Supplier;
use App\Jobs\ImportBom;
use DataTables;
use App\Exports\BomsExport;
use Excel;
use DB;
use Storage;
use Cart;

class BomSemiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $bom_semi = BomSemi::with(['part', 'supplier'])->get();
            return response()->json($bom_semi);
        }

        return view('pages.bom_semi.index');
    }
    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $bom_semi = TemporaryBomSemi::with(['part', 'supplier'])->get();
            return response()->json($bom_semi);
        }

        return view('pages.bom_semi.temporary');
    }

   
     public function create()
    {
        $parts      = Part::get();
        $suppliers  = Supplier::get();
        return view('pages.bom_semi.create', compact(['parts','suppliers']));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       if($request->ajax())
        {
            $bom_semi                         = new BomSemi;
            $bom_semi->part_id                = $request->part_id;
            $bom_semi->supplier_id            = $request->supplier_id;
            $bom_semi->model                  = $request->model;
            // $bom_semi->fiscal_year            = $request->fiscal_year;
            $bom_semi->save();
                $res = ['title' => 'success', 'type' => 'success', 'message' => 'Data berhasil disimpan'];
                        return response()->json($res);

        } else {

            $res = '';

        DB::transaction(function() use ($request, &$res){
            // Save data in Tabel Bom
            $bom_semi                         = new BomSemi;
            $bom_semi->part_id                = $request->part_id;
            $bom_semi->supplier_id            = $request->supplier_id;
            $bom_semi->model                  = $request->model;
            // $bom_semi->fiscal_year            = $request->fiscal_year;
            $bom_semi->save();

            foreach (Cart::instance('bom_semi')->content() as $bom_semi_data) {

                $details              = new BomSemiData;
                $details->part_id     = $bom_semi_data->id;
                $details->supplier_id = $bom_semi_data->options->supplier_id;
                $details->source      = $bom_semi_data->options->source;
                $details->qty         = $bom_semi_data->qty;

                $bom_semi->details()->save($details);
            }

            $res = [
                        'title' => 'Sukses',
                        'type' => 'success',
                        'message' => 'Data berhasil disimpan!'
                    ];

        });

            Cart::destroy();
            return redirect()
                        ->route('bom_semi.index')
                        ->with($res);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */
    public function show(Bom $bom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $suppliers  = Supplier::get();
        $parts      = Part::get();
        $bom_data   = BomSemiData::get();
        $bom_semi   = BomSemi::find($id);

        foreach ($bom_semi->details as $detail) {

            Cart::instance('bom_semi')->add([
                'id' => $detail->part_id,
                'name' => $detail->parts->part_name,
                'qty' => $detail->qty,
                'price' => 1,
                'options' => [
                    'part_id' => $detail->part_id,
                    'supplier_id' => $detail->supplier_id,
                    'supplier_name' => $detail->suppliers->supplier_name,
                    'source' => $detail->source,
                ]
            ]);

        }

        return view('pages.bom_semi.edit', compact(['suppliers', 'parts', 'bom_semi','bom_data']));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $res = '';

        DB::transaction(function() use ($request, $id, &$res){
            // Save data in Tabel Bom
            $bom_semi                         = BomSemi::find($id);
            $bom_semi->part_id                = $request->part_id;
            $bom_semi->supplier_id            = $request->supplier_id;
            $bom_semi->model                  = $request->model;
            $bom_semi->save();

            $bom_semi->details()->delete();

            foreach (Cart::instance('bom_semi')->content() as $bom_semi_data) {

                $details              = BomSemiData::firstOrNew(['part_id' => $bom_semi_data->id]);
                $details->part_id     = $bom_semi_data->id;
                $details->supplier_id = $bom_semi_data->options->supplier_id;
                $details->source      = $bom_semi_data->options->source;
                $details->qty         = $bom_semi_data->qty;

                $bom_semi->details()->save($details);
            }

            $res = [
                        'title' => 'Sukses',
                        'type' => 'success',
                        'message' => 'Data berhasil disimpan!'
                    ];

        });

            Cart::instance('bom_semi')->destroy();
            return redirect()
                        ->route('bom_semi.index')
                        ->with($res);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bom  $bom
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $bom_semi = BomSemi::find($id);
            $bom_semi->delete();
        });
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('bom_semi.index')
                    ->with($res);
    }
    public function cancel(){
    	// DB::transaction(function() use ($id){
            $bom_semi = TemporaryBomSemi::truncate();
            $bom_semi = TemporaryBomSemiData::truncate();
            $bom_semi->truncate();
        // });

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('bom_semi.index')
                ->with($res);

    }

    public function getData(Request $request)
    {
        $bom_semi = BomSemi::with(['parts', 'suppliers'])->get();

        return DataTables::of($bom_semi)

        ->rawColumns(['options'])

        ->addColumn('options', function($bom_semi){
            return '
                <a href="'.route('bom_semi.edit', $bom_semi->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$bom_semi->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('bom_semi.destroy', $bom_semi->id).'" method="POST" id="form-delete-'.$bom_semi->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })
        ->addColumn('details_url', function($bom_semi) {
            return url('bom_semi/details-data/' . $bom_semi->id);
        })

        ->toJson();
    }
    public function getDetailsData($id)
    {
        $details = BomSemi::with('details')
        		->find($id)
                ->details()
                ->with(['parts', 'suppliers'])
                ->get();

        return Datatables::of($details)
        ->toJson();
        
    }

    public function getData_temporary(Request $request)
    {

        // $bom_semis = TemporaryBomSemi::getDataTable($request->search, $request->start, $request->length);
        // $recordsTotal = TemporaryBomSemi::getDataTable($request->search, $request->start, '-1');

        // $result = [];
        // $result['draw'] = $request->draw;
        // $result['recordsTotal'] = count($recordsTotal);
        // $result['recordsFiltered'] = count($recordsTotal);

        // foreach ($bom_semis as $bom_semi) {

        //      $result['data'][] = [
        //                         'fiscal_year' => $bom_semi->fiscal_year,
        //                         'part_number' => !empty($bom_semi->parts) ? $bom_semi->parts->part_number : $bom_semi->part_number.' Tidak Ada',
        //                         'supplier_code' => !empty($bom_semi->suppliers) ? $bom_semi->suppliers->supplier_code : $bom_semi->supplier_code.' Tidak Ada',
        //                         'model' => $bom_semi->model,
                                
        //                         'option' => ' 
        //                             <button class="btn btn-danger btn-xs btn-bordered" onclick="onDelete(\''.$bom_semi->rowId.'\')" data-toggle="tooltip" title="Hapus"><i class="mdi mdi-close"></i></button>'
        //                     ];


        // }




        // return $result;
        $bom_semi = TemporaryBomSemi::with(['parts', 'suppliers'])->get();

        return DataTables::of($bom_semi)
        ->addColumn('details_url_temporary', function($bom_semi) {
            return url('bom_semi/details-datatemp/' . $bom_semi->id);
        })

        ->rawColumns(['options'])

        ->addColumn('options', function($bom_semi){
            return '
                
            ';
        })

        ->addColumn('suppliers.supplier_code', function($bom_semi) {
            return !empty($bom_semi->suppliers) ? $bom_semi->suppliers->supplier_code : $bom_semi->supplier_code.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($bom_semi) {
            return !empty($bom_semi->parts) ? $bom_semi->parts->part_number : $bom_semi->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($bom_semi) {
            
            return !empty($bom_semi->parts) && !empty($bom_semi->suppliers)? 'alert-success' : 'alert-warning';
        })
        ->setRowData([
            'id' => '1',
        ])
        ->setRowAttr([
            'color' => 'red',
        ])
        

        ->toJson();
    }

    public function getDetails_temporary($id)
    {
        $details = TemporaryBomSemi::with('details_temporary')
        			->find($id)
        			->details_temporary()
        			->with(['parts', 'suppliers'])
        			->get();

        return Datatables::of($details)
        ->addColumn('suppliers.supplier_code', function($bom_semi) {
            return !empty($bom_semi->suppliers) ? $bom_semi->suppliers->supplier_code : $bom_semi->supplier_code.' Tidak Ada';
        })

        ->addColumn('suppliers.supplier_name', function($bom_semi) {
            return !empty($bom_semi->suppliers) ? $bom_semi->suppliers->supplier_name : $bom_semi->supplier_name.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($bom_semi) {
            return !empty($bom_semi->parts) ? $bom_semi->parts->part_number : $bom_semi->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($bom_semi) {
            
            return !empty($bom_semi->parts) && !empty($bom_semi->suppliers)? 'alert-success' : 'alert-warning';
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
        $boms = BomSemi::select('parts_bom.part_number as part_number', 'parts_bom.part_name as part_name', 'model','parts_bom_semi_datas.part_number as part_number_details','suppliers.supplier_code','suppliers.supplier_name', 'bom_semi_datas.source','bom_semi_datas.qty')
                    ->join('parts as parts_bom', 'bom_semis.part_id', '=', 'parts_bom.id')
                    ->join('bom_semi_datas', 'bom_semi_datas.part_id_head', '=', 'bom_semis.part_id')
                    ->join('parts as parts_bom_semi_datas', 'bom_semi_datas.part_id', '=', 'parts_bom_semi_datas.id')
                    ->join('suppliers', 'bom_semis.supplier_id', '=', 'suppliers.id')
                    ->get();

       return Excel::create('Data Bom Semi Finish Good', function($excel) use ($boms){
             $excel->sheet('mysheet', function($sheet) use ($boms){
                 $sheet->fromArray($boms);
             });

        })->download('csv');

    }
    public function save(){
        $temps  = TemporaryBomSemi::with(['details_temporary'])->get();
        DB::transaction(function() use ($temps) {
    		foreach ($temps as $temp) {

	            if (!empty($temp->parts) && !empty($temp->suppliers))  {

	                $bom_semi              =   BomSemi::firstOrNew(['part_id'=>$temp->part_id]);
	                $bom_semi->part_id     =   $temp->part_id;
	                $bom_semi->supplier_id =   $temp->supplier_id;
                    $bom_semi->model       =   $temp->model;
	                // $bom_semi->fiscal_year =   $temp->fiscal_year;
	                $bom_semi->save();

	                foreach ($temp->details_temporary as $temp_det) {

                        $details 		   	  = new BomSemiData;
                        // $details->bom_semi_id = $temp_det->part_id_head;
		                $details->part_id     =   $temp_det->part_id;
		                $details->supplier_id =   $temp_det->supplier_id;
		                $details->source      =   $temp_det->source;
                        $details->qty         =   $temp_det->qty;
                        // $details->save();
		                $bom_semi->details()->save($details);
	                }
	                
	            }
	            
	        }

        });
        TemporaryBomSemi::truncate();
        TemporaryBomSemiData::truncate();

        $res = [
                'title' => 'Sukses',
                'type' => 'success',
                'message' => 'Data berhasil di Di Simpan !'
            ]; 
        
        return redirect()
                ->route('bom_semi.index');
                // ->with($res);
    }
     public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);
        $array = [];
        if ($request->hasFile('file')) {
            Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($array){
				$array_datas = [];
                foreach ($reader->all() as $data) {

                    $part_id = Part::where('part_number', $data->part_number)->first();
                    $supplier_id = Supplier::where('supplier_code', $data->supplier_code)->first();
                    $part_details = Part::where('part_number', $data->part_number_details)->first();
                    $supplier_details = Supplier::where('supplier_code', $data->supplier_code_details)->first();

                    $array[] = [
                                    'part_id'               => !empty($part_id) ? $part_id->id : 0,
                                    'supplier_id'           => !empty($supplier_id) ? $supplier_id->id : 0,
                                    'part_number'           => $data->part_number,
                                    'supplier_code'         => $data->supplier_code,
                                    'model'                 => $data->model
                                ];

                    $array_datas[] = [
                                        'part_id_head'   => !empty($part_id) ? $part_id->id : 0,
                                        'part_id'        => !empty($part_details) ? $part_details->id : 0,
                                        'supplier_id'    => !empty($supplier_details) ? $supplier_details->id : 0,
                                        'part_number'    => $data->part_number_details,
                                        'supplier_code'  => $data->supplier_code_details,
                                        'source'         => $data->source,
                                        'qty'            => $data->qty
                                    ];
                }

                TemporaryBomSemi::insert($array);
                TemporaryBomSemiData::insert($array_datas);
               
            });
            $res = [
                        'title'                 => 'Sukses',
                        'type'                  => 'success',
                        'message'               => 'Data berhasil di Upload!'
                    ]; 
            return redirect()
                    ->route('bom_semi.temporary')
                    ->with($res);
            
        }
                               
    }
    public function templateBomSemi() 
    {
       return Excel::create('Format Upload Data BOM Semi', function($excel){
             $excel->sheet('mysheet', function($sheet){
                 // $sheet->fromArray($boms);
                // $sheet->cell('A1', function($cell) {$cell->setValue('fiscal_year');});
                $sheet->cell('A1', function($cell) {$cell->setValue('part_number');});
                $sheet->cell('B1', function($cell) {$cell->setValue('supplier_code');});
                $sheet->cell('C1', function($cell) {$cell->setValue('model');});
                $sheet->cell('D1', function($cell) {$cell->setValue('part_number_details');});
                $sheet->cell('E1', function($cell) {$cell->setValue('supplier_code_details');});
                $sheet->cell('F1', function($cell) {$cell->setValue('source');});
                $sheet->cell('G1', function($cell) {$cell->setValue('qty');});
                // $sheet->cell('A2', function($cell) {$cell->setValue('2018');});
                $sheet->cell('A2', function($cell) {$cell->setValue('423176-10200');});
                $sheet->cell('B2', function($cell) {$cell->setValue('SUP01');});
                $sheet->cell('C2', function($cell) {$cell->setValue('Plat');});
                $sheet->cell('D2', function($cell) {$cell->setValue('423176-20200');});
                $sheet->cell('E2', function($cell) {$cell->setValue('SUP01');});
                $sheet->cell('F2', function($cell) {$cell->setValue('Local');});
                $sheet->cell('G2', function($cell) {$cell->setValue('12');});
                 
             });

        })->download('csv');
    }
    
}
