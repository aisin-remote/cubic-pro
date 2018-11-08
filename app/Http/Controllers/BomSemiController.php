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
            $bom_semi->save();

            foreach (Cart::content() as $bom_semi_data) {

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
        $bom_semi        = BomSemi::find($id);

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
        // if($request->ajax())
        // {
        //     $bom_semi                         = BomSemi::find($id);
        //     $bom_semi->part_id                = $request->part_id;
        //     $bom_semi->supplier_id            = $request->supplier_id;
        //     $bom_semi->model                  = $request->model;
        //     $bom_semi->save();
        //         $res = ['title' => 'success', 'type' => 'success', 'message' => 'Data berhasil disimpan'];
        //                 return response()->json($res);

        // } else {

            $res = '';

        DB::transaction(function() use ($request, $id, &$res){
            // Save data in Tabel Bom
            $bom_semi                         = BomSemi::find($id);
            $bom_semi->part_id                = $request->part_id;
            $bom_semi->supplier_id            = $request->supplier_id;
            $bom_semi->model                  = $request->model;
            $bom_semi->save();

            foreach (Cart::content() as $bom_semi_data) {

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
        $boms = BomSemi::select('parts.part_number', 'parts.part_name', 'parts.model', 'parts.component_part_no','parts.component_part_name', 'parts.source', 'parts.qty','parts.unit','parts.customer_assy_part_no','suppliers.supplier_code','suppliers.supplier_name')
                    ->join('parts', 'boms.part_id', '=', 'parts.id')
                    ->join('suppliers', 'boms.supplier_id', '=', 'suppliers.id')
                    ->get();

       return Excel::create('data_bom', function($excel) use ($boms){
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

	                $bom_semi = new BomSemi;
	                $bom_semi->part_id     =   $temp->part_id;
	                $bom_semi->supplier_id =   $temp->supplier_id;
	                $bom_semi->model       =   $temp->model;
	                $bom_semi->save();

	                foreach ($temp->details_temporary as $temp_det) {
	                	$details 		   	  = new BomSemiData;
		                $details->part_id     =   $temp_det->part_id;
		                $details->supplier_id =   $temp_det->supplier_id;
		                $details->source      =   $temp_det->source;
		                $details->qty         =   $temp_det->qty;
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
        $data = [];
        if ($request->hasFile('file')) {
            Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){

                foreach ($reader->all() as $data) {

                    $part_id                    = Part::where('part_number', $data->part_number)->first();
                    $supplier_id                = Supplier::where('supplier_code', $data->supplier_code)->first();
                    $part_details               = Part::where('part_number', $data->part_number_details)->first();
                    $supplier_details           = Supplier::where('supplier_code', $data->supplier_code_details)->first();

                    $bom_semi                   = TemporaryBomSemi::firstOrNew(['part_id' => $part_id]);
                    $bom_semi->part_id          = !empty($part_id) ? $part_id->id : 0;
                    $bom_semi->supplier_id      = !empty($supplier_id) ? $supplier_id->id : 0;
                    $bom_semi->model            = $data->model;
                    $bom_semi->save();

                    $details                    = new TemporaryBomSemiData;
                    $details->supplier_id       = !empty($supplier_details) ? $supplier_details->id : 0;
                    $details->part_id           = !empty($part_details) ? $part_details->id : 0;;
                    $details->source            = $data->source;
                    $details->qty               = $data->qty;
                    $bom_semi->details_temporary()->save($details);
           
                }
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
    
}
