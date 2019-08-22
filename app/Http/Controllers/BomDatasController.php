<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BomData;
use App\Part;
use App\Supplier;
use App\Temporary\TemporaryBom;
use App\Temporary\TemporaryBomData;
use DataTables;
use DB;
use Cart;

class BomDatasController extends Controller
{
    public function getData()
    {
        $details = Cart::instance('bom')->content();

        if (Cart::count() > 0) {

            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = Cart::count();
            $result['recordsFiltered'] = Cart::count();

            foreach ($details as $detail) {

                $result['data'][] = [
                                        'part_number' => $detail->name,
                                        'supplier_name' => $detail->options->supplier_name,
                                        'qty' => $detail->qty,
                                        'source' => $detail->options->source,
                                        'option' => ' 
                                            <button class="btn btn-danger btn-xs btn-bordered" onclick="onDelete(\''.$detail->rowId.'\')" data-toggle="tooltip" title="Hapus"><i class="mdi mdi-close"></i></button>'
                                    ];
            }

        } else {
            $result = [];
            $result['draw'] = 0;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $result['data'] = [];
        }

        return $result;
    }

    public function store(Request $request)
    {

        $part = Part::find($request->part_id);
        $supplier = Supplier::find($request->supplier_id);

        Cart::instance('bom')->add([
                    'id' => $request->part_id,
                    'name' => $part->part_name,
                    'qty' => $request->qty,
                    'price' => 1,
                    'options' => [
                        'part_id' => $part->id,
                        'supplier_id' => $supplier->id,
                        'supplier_name' => $supplier->supplier_name,
                        'source' => $request->source,
                    ]
                ]);


        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been inserted'
                ];

        return response()
                ->json($res);
    }

    public function update(Request $request)
    {

        $part = Part::find($request->part_id);
        $supplier = Supplier::find($request->supplier_id);

        Cart::instance('bom')->add([
                    'id' => $request->part_id,
                    'name' => $part->part_name,
                    'qty' => $request->qty,
                    'price' => 1,
                    'options' => [
                        'part_id' => $part->id,
                        'supplier_id' => $supplier->id,
                        'supplier_name' => $supplier->supplier_name,
                        'source' => $request->source,
                    ]
                ]);


        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been inserted'
                ];

        return response()
                ->json($res);
    }
    function show($id)
    {
        

    }

    function destroy($id)
    {
        Cart::instance('bom')->remove($id);

        $res = [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Data has been removed'
                ];

        return response()
                ->json($res);

    }
}
