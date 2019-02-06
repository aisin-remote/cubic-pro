<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrConfirm;
use App\GrConfirmDetail;
use App\ApprovalMaster;

use DataTables;
use DB;
use Storage;
use Cart;

class GrConfirmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $gr_confirm = GrConfirm::get();
            return response()->json($gr_confirm);
        }

        return view('pages.gr_confirm.index');
    }
  
    public function create()
    {
        $parts      = Part::get();
        $suppliers  = Supplier::get();
        Cart::destroy();

        return view('pages.bom.create', compact(['parts','suppliers']));
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
            $bom                         = new Bom;
            $bom->part_id                = $request->part_id;
            $bom->supplier_id            = $request->supplier_id;
            $bom->model                  = $request->model;
            // $bom->fiscal_year            = $request->fiscal_year;
            $bom->save();
                $res = ['title' => 'success', 'type' => 'success', 'message' => 'Data berhasil disimpan'];
                        return response()->json($res);

        } else {

            $res = '';

        DB::transaction(function() use ($request, &$res){
            // Save data in Tabel Bom
            $bom                         = new Bom;
            $bom->part_id                = $request->part_id;
            $bom->supplier_id            = $request->supplier_id;
            $bom->model                  = $request->model;
            // $bom->fiscal_year            = $request->fiscal_year;
            $bom->save();



            foreach (Cart::instance('bom')->content() as $bom_data) {

                $details              = new BomData;
                $details->part_id     = $bom_data->id;
                $details->supplier_id = $bom_data->options->supplier_id;
                $details->source      = $bom_data->options->source;
                $details->qty         = $bom_data->qty;

                $bom->details()->save($details);
            }

            $res = [
                        'title' => 'Sukses',
                        'type' => 'success',
                        'message' => 'Data berhasil disimpan!'
                    ];

        });

            Cart::destroy();
            return redirect()
                        ->route('bom.index')
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
        $bom_data   = BomData::get();
        $bom        = Bom::find($id);

        foreach ($bom->details as $detail) {

            Cart::instance('bom')->add([
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

        return view('pages.bom.edit', compact(['suppliers', 'parts', 'bom','bom_data']));
        
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
            $bom                         = Bom::find($id);
            $bom->part_id                = $request->part_id;
            $bom->supplier_id            = $request->supplier_id;
            $bom->model                  = $request->model;
            // $bom->fiscal_year            = $request->fiscal_year;
            $bom->save();

            $bom->details()->delete();

            foreach (Cart::instance('bom')->content() as $bom_data) {

                $details              = BomData::firstOrNew(['part_id' => $bom_data->id]);
                $details->part_id     = $bom_data->id;
                $details->supplier_id = $bom_data->options->supplier_id;
                $details->source      = $bom_data->options->source;
                $details->qty         = $bom_data->qty;

                $bom->details()->save($details);
            }

            $res = [
                        'title' => 'Sukses',
                        'type' => 'success',
                        'message' => 'Data berhasil disimpan!'
                    ];

        });

            Cart::instance('bom')->destroy();
            return redirect()
                        ->route('bom.index')
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
            $bom = Bom::find($id);
            $bom->delete();
        });
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('bom.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
        $boms = Bom::with(['parts', 'suppliers'])->get();

        return DataTables::of($boms)

        ->rawColumns(['options'])

        ->addColumn('options', function($bom){
            return '
                <a href="'.route('bom.edit', $bom->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$bom->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('bom.destroy', $bom->id).'" method="POST" id="form-delete-'.$bom->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })
        ->addColumn('details_url', function($bom) {
            return url('bom/details-data/' . $bom->id);
        })

        ->toJson();
    }
    public function getDetailsData($id)
    {
        $details = Bom::find($id)
                ->details()
                ->with(['parts', 'suppliers'])
                ->get();

        return Datatables::of($details)->make(true);
    }
    
    public function export() 
    {
        $boms = Bom::select('parts_bom.part_number as part_number', 'parts_bom.part_name as part_name', 'model','parts_bom_datas.part_number as part_number_details','suppliers.supplier_code','suppliers.supplier_name', 'bom_datas.source','bom_datas.qty')
                    ->join('parts as parts_bom', 'boms.part_id', '=', 'parts_bom.id')
                    ->join('bom_datas', 'bom_datas.bom_id', '=', 'boms.id')
                    ->join('parts as parts_bom_datas', 'bom_datas.part_id', '=', 'parts_bom_datas.id')
                    ->join('suppliers', 'boms.supplier_id', '=', 'suppliers.id')
                    ->get();

       return Excel::create('Data Bom Finish Good', function($excel) use ($boms){
             $excel->sheet('mysheet', function($sheet) use ($boms){
                 $sheet->fromArray($boms);

             });

        })->download('csv');

    }
   
    public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);
        $array = [];
        if ($request->hasFile('file')) {
            Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($array){

                foreach ($reader->all() as $data) {

                    
                    $array[] = [
                        'part_number'           => $data->part_number,
                        'supplier_code'         => $data->supplier_code,
                        'model'                 => $data->model
                    ];

                    $array_datas[] = [
                            'part_id_head'   => $data->part_number,
                            'part_number'    => $data->part_number_details,
                            'supplier_code'  => $data->supplier_code_details,
                            'source'         => $data->source,
                            'qty'            => $data->qty
                        ];
                }
                
                TemporaryBom::insert(collect($array)->unique('part_number')->toArray());
                TemporaryBomData::insert($array_datas);
            });
            $res = [
                        'title'                 => 'Sukses',
                        'type'                  => 'success',
                        'message'               => 'Data berhasil di Upload!'
                    ]; 
            return redirect()
                    ->route('bom.temporary')
                    ->with($res);
            
        }
                               
    }

    public function save(){
        $temps  = TemporaryBom::with(['details_temporary'])->get();

        DB::transaction(function() use ($temps) {
            foreach ($temps as $temp) {
                $part_id                    = Part::where('part_number', $temp->part_number)->first();
                $supplier_id                = Supplier::where('supplier_code', $temp->supplier_code)->first();
                
                if (!empty($temp->parts) && !empty($temp->suppliers))  {

                    $bom              = Bom::firstOrNew(['part_id'=> $temp->part_id]);
                    $bom->part_id     =   $part_id->id;
                    $bom->supplier_id =   $supplier_id->id;
                    $bom->model       =   $temp->model;
                    // $bom->fiscal_year =   $temp->fiscal_year;
                    $bom->save();

                    foreach ($temp->details_temporary as $temp_det) {
                        $part_details               = Part::where('part_number', $temp_det->part_number)->first();
                        $supplier_details           = Supplier::where('supplier_code', $temp_det->supplier_code)->first();

                        $details              = new BomData;
                        $details->part_id     =   $part_details->id;
                        $details->supplier_id =   $supplier_details->id;
                        $details->source      =   $temp_det->source;
                        $details->qty         =   $temp_det->qty;
                        $bom->details()->save($details);
                    }
                    
                }
                
            }

        });
        TemporaryBom::truncate();
        TemporaryBomData::truncate();

        $res = [
                'title' => 'Sukses',
                'type' => 'success',
                'message' => 'Data berhasil di Di Simpan !'
            ]; 
        
        return redirect()
                ->route('bom.index')
                ->with($res);
    }
    public function cancel(){
        // DB::transaction(function() use ($id){
            $bom = TemporaryBom::truncate();
            $bom = TemporaryBomData::truncate();
            $bom->truncate();
        // });

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('bom.index')
                ->with($res);

    }
    public function getData_temporary(Request $request)
    {
        $bom = TemporaryBom::with(['parts', 'suppliers'])->get();

        return DataTables::of($bom)
        ->addColumn('details_url_temporary', function($bom) {
            return url('bom/details-datatemp/' . $bom->id);
        })

        ->rawColumns(['options'])

        ->addColumn('options', function($bom){
            return '
                
            ';
        })

        ->addColumn('suppliers.supplier_code', function($bom) {
            return !empty($bom->suppliers) ? $bom->suppliers->supplier_code : $bom->supplier_code.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($bom) {
            return !empty($bom->parts) ? $bom->parts->part_number : $bom->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($bom) {
            
            return !empty($bom->parts) && !empty($bom->suppliers)? 'alert-success' : 'alert-warning';
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
        $details = TemporaryBom::with('details_temporary')
                    ->find($id)
                    ->details_temporary()
                    ->with(['parts', 'suppliers'])
                    ->get();

        return Datatables::of($details)
        ->addColumn('suppliers.supplier_code', function($bom) {
            return !empty($bom->suppliers) ? $bom->suppliers->supplier_code : $bom->supplier_code.' Tidak Ada';
        })

        ->addColumn('suppliers.supplier_name', function($bom) {
            return !empty($bom->suppliers) ? $bom->suppliers->supplier_name : $bom->supplier_name.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($bom) {
            return !empty($bom->parts) ? $bom->parts->part_number : $bom->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($bom) {
            
            return !empty($bom->parts) && !empty($bom->suppliers)? 'alert-success' : 'alert-warning';
        })
        ->setRowData([
            'id' => '1',
        ])
        ->setRowAttr([
            'color' => 'red',
        ])
        
        ->toJson();

    }
    public function template_bom() 
    {
       return Excel::create('Format Upload Data BOM', function($excel){
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

