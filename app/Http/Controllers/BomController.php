<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bom;
use App\BomData;
use App\Temporary\TemporaryBom;
use App\Temporary\TemporaryBomData;
use App\Part;
use App\Supplier;

use App\Jobs\ImportBom;
use DataTables;
use App\Exports\BomsExport;
use Excel;
use DB;
use Storage;

use Cart;

class BomController extends Controller
{
   public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $boms = Bom::with(['part', 'supplier'])->get();
            return response()->json($boms);
        }

        return view('pages.bom.index');
    }

    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $bom_semi = TemporaryBom::with(['part', 'supplier'])->get();
            return response()->json($bom_semi);
        }

        return view('pages.bom.temporary');
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
            $bom->save();

            foreach (Cart::content() as $bom_data) {

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

            Cart::add([
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
            $bom->save();

            foreach (Cart::content() as $bom_data) {

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
        $boms = Bom::select('parts.part_number', 'parts.part_name', 'parts.model', 'parts.component_part_no','parts.component_part_name', 'parts.source', 'parts.qty','parts.unit','parts.customer_assy_part_no','suppliers.supplier_code','suppliers.supplier_name')
                    ->join('parts', 'boms.part_id', '=', 'parts.id')
                    ->join('suppliers', 'boms.supplier_id', '=', 'suppliers.id')
                    ->get();

       return Excel::create('data_bom', function($excel) use ($boms){
             $excel->sheet('mysheet', function($sheet) use ($boms){
                 $sheet->fromArray($boms);
             });

        })->download('csv');

    }
    // public function import(Request $request)
    // {
    //     // $validation = $request->validate([
    //     //     'file' => 'required|mimes:xls'
    //     // ]);

    //     $file = $request->file('file');
    //     $name = time() . '.' . $file->getClientOriginalExtension();
    //     $path = $file->storeAs('public/uploads', $name);
    //     $invalid_part = [];
    //     $invalid_supplier = [];
    //     $invalid_part_details = [];
    //     $invalid_supplier_details = [];
    //     $res = '';

    //     if ($request->hasFile('file')) {

    //         // $file = public_path('storage/uploads/1534217112.xls');
    //         $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();
            
    //        if ($datas->first()->has('part_number') && $datas->first()->has('supplier_code')) {
                
    //             foreach ($datas as $data) {
                    
    //                 if (!empty($data->part_number)&&!empty($data->supplier_code)) {

    //                     DB::transaction(function() use ($data, &$invalid_part,&$invalid_supplier,&$invalid_part_details,&$invalid_supplier_details, &$res, &$name){

                            
    //                         $part = Part::where('part_number', $data->part_number)->first();
    //                         $supplier = Supplier::where('supplier_code', $data->supplier_code)->first();
    //                         $part_details = Part::where('part_number', $data->part_number_details)->first();
    //                         $supplier_details = Supplier::where('supplier_code', $data->supplier_code_details)->first();

    //                         if (!empty($part)) {
    //                             $part_id = $part->id;
    //                         } else {
    //                             $invalid_part[] = $data->part_number;
    //                             $part_id = null;
    //                         }

    //                         if (!empty($supplier)) {
    //                             $supplier_id = $supplier->id;
    //                         } else {
    //                             $invalid_supplier[] = $data->supplier_code;
    //                             $supplier_id = null;
    //                         }

    //                         if (!empty($part_details)) {
    //                             $part_details_id = $part_details->id;
    //                         } else {
    //                             $invalid_part_details[] = $data->part_number_details;
    //                             $part_details_id = null;
    //                         }

    //                         if (!empty($supplier_details)) {
    //                             $supplier_details_id = $supplier_details->id;
    //                         } else {
    //                             $invalid_supplier_details[] = $data->supplier_code_details;
    //                             $supplier_details_id = null;
    //                         }

    //                         if (!empty($part_id) && !empty($supplier_id) && !empty($part_details_id) && !empty($supplier_details_id)) {

    //                             $bom = Bom::firstOrNew(['part_id' => $part_id]);
    //                             $bom->part_id = $part_id;
    //                             $bom->supplier_id = $supplier_id;
    //                             $bom->model = $data->model;
    //                             $bom->save();

    //                             $details = new BomData;
    //                             $details->supplier_id = $supplier_details_id;
    //                             $details->part_id = $part_details_id;
    //                             $details->source = $data->source;
    //                             $details->qty = $data->qty;
                                
    //                             $bom->details()->save($details);

    //                             $res = [
    //                                 'title' => 'Sukses',
    //                                 'type' => 'success',
    //                                 'message' => 'Data berhasil di import!'
    //                             ];

    //                         } else {

    //                             $invalids = [$invalid_supplier, $invalid_part, $invalid_supplier_details, $invalid_part_details];
    //                                 $check = collect($invalids)->flatten()->implode(',');
    //                                 $res = [
    //                                     'title' => 'Error',
    //                                     'type' => 'error',
    //                                     'message' => 'Format Buruk!'.$check
    //                                 ];

    //                         }
                            

    //                     });
    //                 }

    //                return redirect()
    //                     ->route('bom.index')
    //                     ->with($res);
    //             }
    //             // Storage::delete('public/uploads/'.$name);
                
               
    //         }
                

    //     }

        
    // }
     public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);
        $data = [];
        if ($request->hasFile('file')) {
            Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){

                foreach ($reader->all() as $data) {

                    $part_id                    = Part::where('part_number', $data->part_number)->first();
                    $supplier_id                = Supplier::where('supplier_code', $data->supplier_code)->first();
                    $part_details               = Part::where('part_number', $data->part_number_details)->first();
                    $supplier_details           = Supplier::where('supplier_code', $data->supplier_code_details)->first();

                    $bom                        = TemporaryBom::firstOrNew(['part_id' => $part_id]);
                    $bom->part_id               = !empty($part_id) ? $part_id->id : 0;
                    $bom->supplier_id           = !empty($supplier_id) ? $supplier_id->id : 0;
                    $bom->model                 = $data->model;
                    $bom->save();

                    $details                    = new TemporaryBomData;
                    $details->supplier_id       = !empty($supplier_details) ? $supplier_details->id : 0;
                    $details->part_id           = !empty($part_details) ? $part_details->id : 0;;
                    $details->source            = $data->source;
                    $details->qty               = $data->qty;
                    $bom->details_temporary()->save($details);
           
                }
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

                if (!empty($temp->parts) && !empty($temp->suppliers))  {

                    $bom = new Bom;
                    $bom->part_id     =   $temp->part_id;
                    $bom->supplier_id =   $temp->supplier_id;
                    $bom->model       =   $temp->model;
                    $bom->save();

                    foreach ($temp->details_temporary as $temp_det) {
                        $details              = new BomData;
                        $details->part_id     =   $temp_det->part_id;
                        $details->supplier_id =   $temp_det->supplier_id;
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
}
