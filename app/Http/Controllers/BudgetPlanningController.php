<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BudgetPlanning;
use App\TemporaryBudget;
use App\Part;
use App\Customer;
use DataTables;
use App\Exports\BudgetPlanningsExport;
use Excel;
use DB;
use Storage;

class BudgetPlanningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            $budgetplannings = BudgetPlanning::all();
            return response()->json($budgetplannings);
        }

        return view('pages.budget_planning.index');
    }

    public function temporary(Request $request)
    {
        if ($request->wantsJson()) {

            $temporarybudgetplanning = TemporaryBudget::all();
            return response()->json($temporarybudgetplanning);
        }

        return view('pages.budget_planning.temporary');
    }

   
     public function create()
    {
        $parts      = Part::get();
        $customers  = Customer::get();
        return view('pages.budget_planning.create', compact(['parts','customers']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $budgetplanning                 = new BudgetPlanning;
        $budgetplanning->part_id        = $request->part_id;
        $budgetplanning->customer_id    = $request->customer_id;
        $budgetplanning->market         = $request->market;
        $budgetplanning->fiscal_year    = $request->fiscal_year;
        $budgetplanning->jan_qty        = $request->jan_qty;
        $budgetplanning->feb_qty        = $request->feb_qty;
        $budgetplanning->mar_qty        = $request->mar_qty;
        $budgetplanning->apr_qty        = $request->apr_qty;
        $budgetplanning->may_qty        = $request->may_qty;
        $budgetplanning->june_qty       = $request->june_qty;
        $budgetplanning->july_qty       = $request->july_qty;
        $budgetplanning->august_qty     = $request->august_qty;
        $budgetplanning->sep_qty        = $request->sep_qty;
        $budgetplanning->okt_qty        = $request->okt_qty;
        $budgetplanning->nov_qty        = $request->nov_qty;
        $budgetplanning->des_qty        = $request->des_qty;
        $budgetplanning->jan_amount     = $request->jan_amount;
        $budgetplanning->feb_amount     = $request->feb_amount;
        $budgetplanning->mar_amount     = $request->mar_amount;
        $budgetplanning->apr_amount     = $request->apr_amount;
        $budgetplanning->may_amount     = $request->may_amount;
        $budgetplanning->june_amount    = $request->june_amount;
        $budgetplanning->july_amount    = $request->july_amount;
        $budgetplanning->august_amount  = $request->august_amount;
        $budgetplanning->sep_amount     = $request->sep_amount;
        $budgetplanning->okt_amount     = $request->okt_amount;
        $budgetplanning->nov_amount     = $request->nov_amount;
        $budgetplanning->des_amount     = $request->des_amount;
        $budgetplanning->save();

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil disimpan!'
                ];

        return redirect()
                    ->route('budgetplanning.index')
                    ->with($res);
    }

    public function save(){
        $temps = TemporaryBudget::get();
        TemporaryBudget::truncate();
        foreach ($temps as $temp) {

            if (!empty($temp->parts) && !empty($temp->customers)) {
                $budgetplanning                 = new BudgetPlanning;
                $budgetplanning->part_id        = $temp->part_id;
                $budgetplanning->customer_id    = $temp->customer_id;
                $budgetplanning->market         = $temp->market;
                $budgetplanning->fiscal_year    = $temp->fiscal_year;
                $budgetplanning->jan_qty        = $temp->jan_qty;
                $budgetplanning->feb_qty        = $temp->feb_qty;
                $budgetplanning->mar_qty        = $temp->mar_qty;
                $budgetplanning->apr_qty        = $temp->apr_qty;
                $budgetplanning->may_qty        = $temp->may_qty;
                $budgetplanning->june_qty       = $temp->june_qty;
                $budgetplanning->july_qty       = $temp->july_qty;
                $budgetplanning->august_qty     = $temp->august_qty;
                $budgetplanning->sep_qty        = $temp->sep_qty;
                $budgetplanning->okt_qty        = $temp->okt_qty;
                $budgetplanning->nov_qty        = $temp->nov_qty;
                $budgetplanning->des_qty        = $temp->des_qty;
                $budgetplanning->jan_amount     = $temp->jan_amount;
                $budgetplanning->feb_amount     = $temp->feb_amount;
                $budgetplanning->mar_amount     = $temp->mar_amount;
                $budgetplanning->apr_amount     = $temp->apr_amount;
                $budgetplanning->may_amount     = $temp->may_amount;
                $budgetplanning->june_amount    = $temp->june_amount;
                $budgetplanning->july_amount    = $temp->july_amount;
                $budgetplanning->august_amount  = $temp->august_amount;
                $budgetplanning->sep_amount     = $temp->sep_amount;
                $budgetplanning->okt_amount     = $temp->okt_amount;
                $budgetplanning->nov_amount     = $temp->nov_amount;
                $budgetplanning->des_amount     = $temp->des_amount;
                $budgetplanning->save();
                 
            }
        }
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil di Di Simpan !'
                ];
        return redirect()
                ->route('budgetplanning.index')
                ->with($res);

    }

    public function cancel(){
        TemporaryBudget::truncate();

        $res = [
            'title'          => 'Sukses',
            'type'           => 'success',
            'message'        => 'Data berhasil di Kosongkan!'
        ]; 

        return redirect()
                ->route('budgetplanning.index')
                ->with($res);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\budgetplanning  $budgetplanning
     * @return \Illuminate\Http\Response
     */
    public function show(BudgetPlanning $budgetplanning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\budgetplanning  $budgetplanning
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customers              = Customer::get();
        $parts                  = Part::get();
        $budgetplanning         = BudgetPlanning::find($id);

        return view('pages.budget_planning.edit', compact(['customers', 'parts', 'budgetplanning']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\budgetplanning  $budgetplanning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         DB::transaction(function() use ($request, $id){
            $budgetplanning = BudgetPlanning::find($id);
            $budgetplanning->part_id        = $request->part_id;
            $budgetplanning->customer_id    = $request->customer_id;
            $budgetplanning->market         = $request->market;
            $budgetplanning->fiscal_year    = $request->fiscal_year;
            $budgetplanning->jan_qty        = $request->jan_qty;
            $budgetplanning->feb_qty        = $request->feb_qty;
            $budgetplanning->mar_qty        = $request->mar_qty;
            $budgetplanning->apr_qty        = $request->apr_qty;
            $budgetplanning->may_qty        = $request->may_qty;
            $budgetplanning->june_qty       = $request->june_qty;
            $budgetplanning->july_qty       = $request->july_qty;
            $budgetplanning->august_qty     = $request->august_qty;
            $budgetplanning->sep_qty        = $request->sep_qty;
            $budgetplanning->okt_qty        = $request->okt_qty;
            $budgetplanning->nov_qty        = $request->nov_qty;
            $budgetplanning->des_qty        = $request->des_qty;
            $budgetplanning->jan_amount     = $request->jan_amount;
            $budgetplanning->feb_amount     = $request->feb_amount;
            $budgetplanning->mar_amount     = $request->mar_amount;
            $budgetplanning->apr_amount     = $request->apr_amount;
            $budgetplanning->may_amount     = $request->may_amount;
            $budgetplanning->june_amount    = $request->june_amount;
            $budgetplanning->july_amount    = $request->july_amount;
            $budgetplanning->august_amount  = $request->august_amount;
            $budgetplanning->sep_amount     = $request->sep_amount;
            $budgetplanning->okt_amount     = $request->okt_amount;
            $budgetplanning->nov_amount     = $request->nov_amount;
            $budgetplanning->des_amount     = $request->des_amount;
            $budgetplanning->save();;
        });
        $res = [
                    'title'   => 'Sukses',
                    'type'    => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                    ->route('budgetplanning.index')
                    ->with($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\budgetplanning  $budgetplanning
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $budgetplanning = BudgetPlanning::find($id);
            $budgetplanning->delete();
        });

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('budgetplanning.index')
                    ->with($res);
    }

    public function getData(Request $request)
    {
         $budgetplannings = BudgetPlanning::with(['parts', 'customers'])->get();

        return DataTables::of($budgetplannings)

        ->rawColumns(['options'])

        ->addColumn('options', function($budgetplanning){
            return '
                <a href="'.route('budgetplanning.edit', $budgetplanning->id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$budgetplanning->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('budgetplanning.destroy', $budgetplanning->id).'" method="POST" id="form-delete-'.$budgetplanning->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }
    public function getData_temporary(Request $request)
    {
        $temporarybudgetplanning = TemporaryBudget::get();
       
        return DataTables::of($temporarybudgetplanning)

        ->rawColumns(['options'])

        ->addColumn('options', function($temporarybudgetplanning){
            return '
                
            ';
        })

        ->addColumn('customers.customer_code', function($temporarybudgetplanning) {
            return !empty($temporarybudgetplanning->customers) ? $temporarybudgetplanning->customers->customer_code : $temporarybudgetplanning->customer_code.' Tidak Ada';
        })

        ->addColumn('parts.part_number', function($temporarybudgetplanning) {
            return !empty($temporarybudgetplanning->parts) ? $temporarybudgetplanning->parts->part_number : $temporarybudgetplanning->part_number.' Tidak Ada';
        })

        ->editColumn('id', '{{$id}}')
        ->setRowId('id')

        ->setRowClass(function ($temporarybudgetplanning) {
            
            return !empty($temporarybudgetplanning->parts) && !empty($temporarybudgetplanning->customers)? 'alert-success' : 'alert-warning';
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
        $budgetplannings = BudgetPlanning::all();

        return Excel::create('data_budgetplanning', function($excel) use ($budgetplannings){
             $excel->sheet('mysheet', function($sheet) use ($budgetplannings){
                 $sheet->fromArray($budgetplannings);
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

            // $file = public_path('storage/uploads/1534217112.xls');
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();
            // Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){
            if ($datas->first()->has('part_number') && $datas->first()->has('customer_code')) {

                foreach ($datas as $data) {

                    $part_id                    = Part::where('part_number', $data->part_number)->first();
                    $customer_id                = Customer::where('customer_code', $data->customer_code)->first();

                    $budgetplanning                  = new TemporaryBudget;
                    $budgetplanning->part_id         = !empty($part_id) ? $part_id->id : 0;
                    $budgetplanning->customer_id     = !empty($customer_id) ? $customer_id->id : 0;
                    $budgetplanning->part_number     = $data->part_number;
                    $budgetplanning->customer_id     = $data->customer_code;
                    $budgetplanning->market          = $data->market;
                    $budgetplanning->market          = $data->fiscal_year;
                    $budgetplanning->jan_qty         = $data->jan_qty;
                    $budgetplanning->feb_qty         = $data->feb_qty;
                    $budgetplanning->mar_qty         = $data->mar_qty;
                    $budgetplanning->apr_qty         = $data->apr_qty;
                    $budgetplanning->may_qty         = $data->may_qty;
                    $budgetplanning->june_qty        = $data->june_qty;
                    $budgetplanning->july_qty        = $data->july_qty;
                    $budgetplanning->august_qty      = $data->august_qty;
                    $budgetplanning->sep_qty         = $data->sep_qty;
                    $budgetplanning->okt_qty         = $data->okt_qty;
                    $budgetplanning->nov_qty         = $data->nov_qty;
                    $budgetplanning->des_qty         = $data->des_qty;
                    $budgetplanning->jan_amount      = $data->jan_amount;
                    $budgetplanning->feb_amount      = $data->feb_amount;
                    $budgetplanning->mar_amount      = $data->mar_amount;
                    $budgetplanning->apr_amount      = $data->apr_amount;
                    $budgetplanning->may_amount      = $data->may_amount;
                    $budgetplanning->june_amount     = $data->june_amount;
                    $budgetplanning->july_amount     = $data->july_amount;
                    $budgetplanning->august_amount   = $data->august_amount;
                    $budgetplanning->sep_amount      = $data->sep_amount;
                    $budgetplanning->okt_amount      = $data->okt_amount;
                    $budgetplanning->nov_amount      = $data->nov_amount;
                    $budgetplanning->des_amount      = $data->des_amount;
                    $budgetplanning->save();           
                }
                // });
                Storage::delete('public/uploads/'.$name);
                $res = [
                            'title'                 => 'Sukses',
                            'type'                  => 'success',
                            'message'               => 'Data berhasil di Upload!'
                        ]; 
                return redirect()
                        ->route('budgetplanning.temporary')
                        ->with($res);
            } else {

                Storage::delete('public/uploads/'.$name);

                return redirect()
                        ->route('budgetplanning.temporary')
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
}
