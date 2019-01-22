<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesData;
use App\TemporarySalesData;
use App\Part;
use App\Customer;
use DataTables;
use App\Exports\SalesDataExport;
use Excel;
use DB;
use Storage;
use Carbon\Carbon;

class SalesDataController extends Controller
{
   public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $SalesDatas = SalesData::all();
            return response()->json($SalesDatas);
        }        

        return view('pages.sales_data.index');
    }

    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $temporary = TemporarySalesData::all();
            return response()->json($temporary);
        }

        return view('pages.sales_data.temporary');
    }

   
     public function create()
    {
        $parts      = Part::get();
        $customers  = Customer::get();
        return view('pages.sales_data.create', compact(['parts','customers']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $salesdata                 = new SalesData;
        $salesdata->part_id        = $request->part_id;
        $salesdata->customer_id    = $request->customer_id;
        $salesdata->market         = $request->market;
        $salesdata->fiscal_year    = $request->fiscal_year;
        $salesdata->jan_qty        = $request->jan_qty;
        $salesdata->feb_qty        = $request->feb_qty;
        $salesdata->mar_qty        = $request->mar_qty;
        $salesdata->apr_qty        = $request->apr_qty;
        $salesdata->may_qty        = $request->may_qty;
        $salesdata->june_qty       = $request->june_qty;
        $salesdata->july_qty       = $request->july_qty;
        $salesdata->august_qty     = $request->august_qty;
        $salesdata->sep_qty        = $request->sep_qty;
        $salesdata->okt_qty        = $request->okt_qty;
        $salesdata->nov_qty        = $request->nov_qty;
        $salesdata->des_qty        = $request->des_qty;
        $salesdata->jan_amount     = $request->jan_amount;
        $salesdata->feb_amount     = $request->feb_amount;
        $salesdata->mar_amount     = $request->mar_amount;
        $salesdata->apr_amount     = $request->apr_amount;
        $salesdata->may_amount     = $request->may_amount;
        $salesdata->june_amount    = $request->june_amount;
        $salesdata->july_amount    = $request->july_amount;
        $salesdata->august_amount  = $request->august_amount;
        $salesdata->sep_amount     = $request->sep_amount;
        $salesdata->okt_amount     = $request->okt_amount;
        $salesdata->nov_amount     = $request->nov_amount;
        $salesdata->des_amount     = $request->des_amount;
        $salesdata->save();

        $res = [
                    'title'         => 'Sukses',
                    'type'          => 'success',
                    'message'       => 'Data berhasil disimpan!'
                ];

        return redirect()
                    ->route('salesdata.index')
                    ->with($res);
    }

    public function save(){
        $temps = TemporarySalesData::get();
        TemporarySalesData::truncate();
        foreach ($temps as $temp) {

            if (!empty($temp->parts) && !empty($temp->customers)) {
                $salesdata                 = new SalesData;
                $salesdata->part_id        = $temp->part_id;
                $salesdata->customer_id    = $temp->customer_id;
                $salesdata->market         = $temp->market;
                $salesdata->fiscal_year    = $temp->fiscal_year;
                $salesdata->jan_qty        = $temp->jan_qty;
                $salesdata->feb_qty        = $temp->feb_qty;
                $salesdata->mar_qty        = $temp->mar_qty;
                $salesdata->apr_qty        = $temp->apr_qty;
                $salesdata->may_qty        = $temp->may_qty;
                $salesdata->june_qty       = $temp->june_qty;
                $salesdata->july_qty       = $temp->july_qty;
                $salesdata->august_qty     = $temp->august_qty;
                $salesdata->sep_qty        = $temp->sep_qty;
                $salesdata->okt_qty        = $temp->okt_qty;
                $salesdata->nov_qty        = $temp->nov_qty;
                $salesdata->des_qty        = $temp->des_qty;
                $salesdata->jan_amount     = $temp->jan_amount;
                $salesdata->feb_amount     = $temp->feb_amount;
                $salesdata->mar_amount     = $temp->mar_amount;
                $salesdata->apr_amount     = $temp->apr_amount;
                $salesdata->may_amount     = $temp->may_amount;
                $salesdata->june_amount    = $temp->june_amount;
                $salesdata->july_amount    = $temp->july_amount;
                $salesdata->august_amount  = $temp->august_amount;
                $salesdata->sep_amount     = $temp->sep_amount;
                $salesdata->okt_amount     = $temp->okt_amount;
                $salesdata->nov_amount     = $temp->nov_amount;
                $salesdata->des_amount     = $temp->des_amount;
                $salesdata->save();
                
            }
        }
        $res = [
                    'title'                => 'Sukses',
                    'type'                 => 'success',
                    'message'              => 'Data berhasil di Di Simpan !'
                ]; 
        return redirect()
                ->route('salesdata.index')
                ->with($res);

        

    }
    public function cancel(){
        TemporarySalesData::truncate();

        $res = [
            'title' => 'Sukses',
            'type' => 'success',
            'message' => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('salesdata.index')
                ->with($res);

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\SalesData  $SalesData
     * @return \Illuminate\Http\Response
     */
    public function show(SalesData $SalesData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalesData  $SalesData
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parts      = Part::get();
        $customers  = Customer::get();
        $salesdata  = SalesData::find($id);

        return view('pages.sales_data.edit', compact(['customers', 'parts', 'salesdata']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalesData  $SalesData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         DB::transaction(function() use ($request, $id){
            $salesdata                 = SalesData::find($id);
            $salesdata->part_id        = $request->part_id;
            $salesdata->customer_id    = $request->customer_id;
            $salesdata->market         = $request->market;
            $salesdata->fiscal_year    = $request->fiscal_year;
            $salesdata->jan_qty        = $request->jan_qty;
            $salesdata->feb_qty        = $request->feb_qty;
            $salesdata->mar_qty        = $request->mar_qty;
            $salesdata->apr_qty        = $request->apr_qty;
            $salesdata->may_qty        = $request->may_qty;
            $salesdata->june_qty       = $request->june_qty;
            $salesdata->july_qty       = $request->july_qty;
            $salesdata->august_qty     = $request->august_qty;
            $salesdata->sep_qty        = $request->sep_qty;
            $salesdata->okt_qty        = $request->okt_qty;
            $salesdata->nov_qty        = $request->nov_qty;
            $salesdata->des_qty        = $request->des_qty;
            $salesdata->jan_amount     = $request->jan_amount;
            $salesdata->feb_amount     = $request->feb_amount;
            $salesdata->mar_amount     = $request->mar_amount;
            $salesdata->apr_amount     = $request->apr_amount;
            $salesdata->may_amount     = $request->may_amount;
            $salesdata->june_amount    = $request->june_amount;
            $salesdata->july_amount    = $request->july_amount;
            $salesdata->august_amount  = $request->august_amount;
            $salesdata->sep_amount     = $request->sep_amount;
            $salesdata->okt_amount     = $request->okt_amount;
            $salesdata->nov_amount     = $request->nov_amount;
            $salesdata->des_amount     = $request->des_amount;
            $salesdata->save();;
        });
        $res = [
                    'title'      => 'Sukses',
                    'type'       => 'success',
                    'message'    => 'Data berhasil diubah!'
                ];  

        return redirect()
                    ->route('salesdata.index')
                    ->with($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalesData  $SalesData
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $SalesData = SalesData::find($id);
            $SalesData->delete();
        });

        $res = [
                    'title'     => 'Sukses',
                    'type'      => 'success',
                    'message'   => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('salesdata.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
         $SalesDatas = SalesData::with(['parts', 'customers'])->get();

        return DataTables::of($SalesDatas)

        ->rawColumns(['options'])

        ->addColumn('options', function($SalesData){
            return '
                <a href="'.route('salesdata.edit', $SalesData->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$SalesData->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('salesdata.destroy', $SalesData->id).'" method="POST" id="form-delete-'.$SalesData->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function getData_temporary(Request $request)
    {
        $temporary = TemporarySalesData::get();
       
        return DataTables::of($temporary)

        ->rawColumns(['options'])

        ->addColumn('options', function($temporary){
            return '
                
            ';
        })

        ->addColumn('customers.customer_code', function($temporary) {
            return !empty($temporary->customers) ? $temporary->customers->customer_code : $temporary->customer_code.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($temporary) {
            return !empty($temporary->parts) ? $temporary->parts->part_number : $temporary->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($temporary) {
            
            return !empty($temporary->parts) && !empty($temporary->customers)? 'alert-success' : 'alert-warning';
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
        $SalesDatas = SalesData::select('parts.part_number as part_number', 'parts.part_name as part_name','customers.customer_code','customers.customer_name','apr_qty','apr_amount','may_qty','may_amount','june_qty','june_amount','july_qty','july_amount','august_qty','august_amount','sep_qty','sep_amount','okt_qty','okt_amount','nov_qty','nov_amount','des_qty','des_amount', 'jan_qty','jan_amount','feb_qty','feb_amount','mar_qty','mar_amount')
                    ->join('parts', 'sales_datas.part_id', '=', 'parts.id')
                    ->join('customers', 'sales_datas.customer_id', '=', 'customers.id')
                    ->get();

        return Excel::create('Data Sales', function($excel) use ($SalesDatas){
             $excel->sheet('mysheet', function($sheet) use ($SalesDatas){
                 $sheet->fromArray($SalesDatas);
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

            // $file = public_path('storage/uploads/1534217112.xls');
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();
            // Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){
            if ($datas->first()->has('part_number') && $datas->first()->has('customer_code')) {

                foreach ($datas as $data) {

                    $part_id                    = Part::where('part_number', $data->part_number)->first();
                    $customer_id                = Customer::where('customer_code', $data->customer_code)->first();
                   
                    $array[] = [
                                    'part_id'         => !empty($part_id) ? $part_id->id : 0,
                                    'customer_id'     => !empty($customer_id) ? $customer_id->id : 0,
                                    'part_number'     => $data->part_number,
                                    'customer_code'   => $data->customer_code,
                                    'market'          => $data->market,  
                                    'fiscal_year'     => $data->fiscal_year,
                                    'jan_qty'         => $data->jan_qty,     
                                    'feb_qty'         => $data->feb_qty,
                                    'mar_qty'         => $data->mar_qty,
                                    'apr_qty'         => $data->apr_qty,
                                    'may_qty'         => $data->may_qty,
                                    'june_qty'        => $data->june_qty,
                                    'july_qty'        => $data->july_qty,
                                    'august_qty'      => $data->august_qty,
                                    'sep_qty'         => $data->sep_qty,
                                    'okt_qty'         => $data->okt_qty,
                                    'nov_qty'         => $data->nov_qty,
                                    'des_qty'         => $data->des_qty,
                                    'jan_amount'      => $data->jan_amount,
                                    'feb_amount'      => $data->feb_amount,
                                    'mar_amount'      => $data->mar_amount,
                                    'apr_amount'      => $data->apr_amount,
                                    'may_amount'      => $data->may_amount,
                                    'june_amount'     => $data->june_amount,
                                    'july_amount'     => $data->july_amount,
                                    'august_amount'   => $data->august_amount,
                                    'sep_amount'      => $data->sep_amount,
                                    'okt_amount'      => $data->okt_amount,
                                    'nov_amount'      => $data->nov_amount,
                                    'des_amount'      => $data->des_amount                          
                                ];           
                }

                TemporarySalesData::insert($array);
                
                Storage::delete('public/uploads/'.$name);
                $res = [
                            'title'                 => 'Sukses',
                            'type'                  => 'success',
                            'message'               => 'Data berhasil di Upload!'
                        ]; 
                return redirect()
                        ->route('salesdata.temporary')
                        ->with($res);
            } else {

                Storage::delete('public/uploads/'.$name);

                return redirect()
                        ->route('salesdata.temporary')
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
    public function templateSalesData() 
    {
       return Excel::create('Format Upload Sales Data', function($excel){
             $excel->sheet('mysheet', function($sheet){
                $sheet->cell('A1', function($cell) {$cell->setValue('fiscal_year');});
                $sheet->cell('B1', function($cell) {$cell->setValue('part_number');});
                $sheet->cell('C1', function($cell) {$cell->setValue('customer_code');});
                $sheet->cell('D1', function($cell) {$cell->setValue('market');});
                $sheet->cell('E1', function($cell) {$cell->setValue('apr_qty');});
                $sheet->cell('F1', function($cell) {$cell->setValue('apr_amount');});
                $sheet->cell('G1', function($cell) {$cell->setValue('may_qty');});
                $sheet->cell('H1', function($cell) {$cell->setValue('may_amount');});
                $sheet->cell('I1', function($cell) {$cell->setValue('june_qty');});
                $sheet->cell('J1', function($cell) {$cell->setValue('june_amount');});
                $sheet->cell('K1', function($cell) {$cell->setValue('july_qty');});
                $sheet->cell('L1', function($cell) {$cell->setValue('july_amount');});
                $sheet->cell('M1', function($cell) {$cell->setValue('august_qty');});
                $sheet->cell('N1', function($cell) {$cell->setValue('august_amount');});
                $sheet->cell('O1', function($cell) {$cell->setValue('sep_qty');});
                $sheet->cell('P1', function($cell) {$cell->setValue('sep_amount');});
                $sheet->cell('Q1', function($cell) {$cell->setValue('okt_qty');});
                $sheet->cell('R1', function($cell) {$cell->setValue('okt_amount');});
                $sheet->cell('S1', function($cell) {$cell->setValue('nov_qty');});
                $sheet->cell('T1', function($cell) {$cell->setValue('nov_amount');});
                $sheet->cell('U1', function($cell) {$cell->setValue('des_qty');});
                $sheet->cell('V1', function($cell) {$cell->setValue('des_amount');});
                $sheet->cell('W1', function($cell) {$cell->setValue('jan_qty');});
                $sheet->cell('X1', function($cell) {$cell->setValue('jan_amount');});
                $sheet->cell('Y1', function($cell) {$cell->setValue('feb_qty');});
                $sheet->cell('Z1', function($cell) {$cell->setValue('feb_amount');});
                $sheet->cell('AA1', function($cell) {$cell->setValue('mar_qty');});
                $sheet->cell('AB1', function($cell) {$cell->setValue('mar_amount');});

                $sheet->cell('A2', function($cell) {$cell->setValue('2018');});
                $sheet->cell('B2', function($cell) {$cell->setValue('423176-10200');});
                $sheet->cell('C2', function($cell) {$cell->setValue('CS01');});
                $sheet->cell('D2', function($cell) {$cell->setValue('Local');});
                $sheet->cell('E2', function($cell) {$cell->setValue('12');});
                $sheet->cell('F2', function($cell) {$cell->setValue('12.300');});
                $sheet->cell('G2', function($cell) {$cell->setValue('14');});
                $sheet->cell('H2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('I2', function($cell) {$cell->setValue('15');});
                $sheet->cell('J2', function($cell) {$cell->setValue('70.000');});
                $sheet->cell('K2', function($cell) {$cell->setValue('18');});
                $sheet->cell('L2', function($cell) {$cell->setValue('70.000');});
                $sheet->cell('M2', function($cell) {$cell->setValue('14');});
                $sheet->cell('N2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('O2', function($cell) {$cell->setValue('14');});
                $sheet->cell('P2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('Q2', function($cell) {$cell->setValue('14');});
                $sheet->cell('R2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('S2', function($cell) {$cell->setValue('14');});
                $sheet->cell('T2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('U2', function($cell) {$cell->setValue('14');});
                $sheet->cell('V2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('W2', function($cell) {$cell->setValue('14');});
                $sheet->cell('X2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('Y2', function($cell) {$cell->setValue('14');});
                $sheet->cell('Z2', function($cell) {$cell->setValue('40.000');});
                $sheet->cell('AA2', function($cell) {$cell->setValue('14');});
                $sheet->cell('AB2', function($cell) {$cell->setValue('40.000');});

             });

        })->download('csv');
    }
}
