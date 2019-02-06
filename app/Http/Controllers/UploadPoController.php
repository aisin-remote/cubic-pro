<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UploadPurchaseOrder;
use App\ApprovalMaster;
use Excel;
use Storage;
use DB;

use DataTables;


class UploadPoController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $po = UploadPurchaseOrder::get();
        

        if ($request->wantsJson()) {
            return response()->json($po, 200);
        }

        return view('pages.upload_po');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'approval_number' => 'required',
        //     'po_number' => 'required',
        //     'po_date' => 'required'
        // ]);

        $po = new UploadPurchaseOrder;
        $po->approval_number = $request->approval_number;
        $po->po_number = $request->po_number;
        $po->po_date = $request->po_date;
        $po->save();

        if ($request->wantsJson()) {
            return response()->json($po);
        }

        $res = [
                    'title' => 'Succses',
                    'type' => 'success',
                    'message' => 'Data Saved Success!'
                ];

        return redirect()
                ->route('upload_po.index')
                ->with($res);

    }
    
    public function show($id)
    {
        $po = UploadPurchaseOrder::find($id);

        if (empty($po)) {
            return response()->json('PO not found', 500);
        }
        return response()->json($po, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'approval_number' => 'required',
            'po_number' => 'required',
            'po_date' => 'required'
        ]);

        $po = UploadPurchaseOrder::find($id);

        if (empty($po)) {
            return response()->json('Type not found', 500);
        }

        $po->po_number = $request->po_number;
        $po->po_date = $request->po_date;
        $po->save();

        if ($request->wantsJson()) {
            return response()->json($po);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('upload_po.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $po = UploadPurchaseOrder::find($id);

        if (empty($po)) {
            return response()->json('Type not found', 500);
        }

        $po->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('upload_po.index')
                ->with($res);

    }

    public function getData(Request $request)
    {
        $po = UploadPurchaseOrder::get();
        return DataTables::of($po)
        ->rawColumns(['options'])

        ->addColumn('options', function($po){
            return '
                <a href="'.route('upload_po.edit', $po->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$po->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('upload_po.destroy', $po->id).'" method="POST" id="form-delete-'.$po->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function create()
    {
        return view('pages.upload_po.create');
    }

    public function edit($id)
    {
        $po = UploadPurchaseOrder::find($id);
        
        return view('pages.upload_po.edit', compact(['po']));
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();

                if ($datas->first()->has('po_number')){
                    foreach ($datas as $data) {

                        $po = UploadPurchaseOrder::where('po_number', $data->po_number)->first();
                        
                        $po                     = new UploadPurchaseOrder;
                        $po->approval_number    = $data->approval_number;
                        $po->po_number          = $data->po_number;
                        $po->po_date            = $data->po_date;
                        $po->save();                  
                    }  

                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Upload Data Success!'
                            ];
                    Storage::delete('public/uploads/'.$name); 
                    return redirect()
                            ->route('upload_po.index')
                            ->with($res);

                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('upload_po.index')
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

    public function template_upload_po() 
    {
       return Excel::create('Template Upload PO', function($excel){
             $excel->sheet('mysheet', function($sheet){
                $sheet->cell('A1', function($cell) {$cell->setValue('approval_number');});
                $sheet->cell('B1', function($cell) {$cell->setValue('po_number');});
                $sheet->cell('C1', function($cell) {$cell->setValue('po_date');});
             });

        })->download('csv');
    } 
}
