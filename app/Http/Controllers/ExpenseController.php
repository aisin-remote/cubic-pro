<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Storage;
use App\Capex;
use App\Capex_archive;      
use App\Helper;
use App\User;
use App\Department;
use Carbon\Carbon;          
use App\Period;             
use App\Expense;            
use App\Expense_archive;    
use App\Approval_master;    
use App\Approval_detail;
use Excel;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {

      	if ($request->wantsJson()) {
      		
      		$capex = Expense::get();
      		return response()->json($capex);
      	}
    	return view('pages.expense.index');
    }

    public function create()
    {
    	$expense         = Expense::get();
      	$period        	 = Config('period.fyear.open');
     	$department    	 = Department::get();

    	return view('pages.expense.create', compact('expense', 'period', 'department'));
    }

    public function store(Request $request)
    {

      	$capex 				          = new Expense;
        $capex->department_id   = $request->department_id;
        $capex->budget_no       = $request->budget_no;
        $capex->budget_plan     = $request->budget_plan;
        $capex->qty_plan     	= $request->qty_plan;
        $capex->equipment_name  = $request->equipment_name;
        $capex->plan_gr         = $request->plan_gr;
        // $capex->budget_remaining= $request->budget_plan;
        // $capex->is_closed       = $request->is_closed;
        $capex->qty_remaining= $request->qty_plan;
        // $capex->status          = $request->status;
        $capex->save();

      	$res = [

      				'title' 		=> 'Success',
      				'type'			=> 'success',
      				'message'		=> 'Data Saved Success'
    			];
      	return redirect()
      			->route('expense.index')
      			->with($res);
    }

    public function getData(Request $request)
    {
        $expenses = Expense::get();

        return DataTables::of($expenses)

        ->rawColumns(['options', 'is_closed'])

        ->addColumn('options', function($expense){
            if(\Entrust::hasRole('user')) {
                    return '
                        
                    ';
                }elseif(\Entrust::hasRole('budget')) {
                    return '
                        
                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$expense->id.')"><i class="mdi mdi-close"></i></button>
                        <form action="'.route('expense.destroy', $expense->id).'" method="POST" id="form-delete-'.$expense->id .'" style="display:none">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                        
                    ';
                }else{
                    return '
                        
                    ';
                }

        })
        ->editColumn("status", function ($expense) {
               // $expense->is_closed="ABS";
            if ($expense->status=='0'){
                return "Underbudget";
            }else{
                return "Overbudget";
            }
        })
        ->editColumn("is_closed", function ($expense) {
               // $expense->is_closed="ABS";
            if ($expense->is_closed=='0'){
                return "Open";

            }else{
                return "Closed";
            }
        })
        ->editColumn("budget_plan", function ($expense) {
                return number_format($expense->budget_plan);
        })
        ->editColumn("budget_used", function ($expense) {
                return number_format($expense->budget_used);
        })
        ->editColumn("budget_remaining", function ($expense) {
                return number_format($expense->budget_remaining);
        })
        ->toJson();
    }

    public function xedit(Request $request)
    {
        $expense = '';

        DB::transaction(function() use ($request, &$expense){

            $expense = Expense::where('budget_no', $request->pk)->first();

            if (($request->name == 'budget_plan') || ($request->name == 'budget_remaining')) {
                $request->value = str_replace(',', '', $request->value);

                if (!is_numeric($request->value)) {
                  throw new \Exception("Value should be numeric", 1);
                }

                if ($capex->budget_used != 0) {
                  throw new \Exception("Could not update: Capex already used.", 1);
                }
            }

            if ($request->name == 'is_closed') {
              if ($request->value == 'Open') {
                  $request->value = 0;
              } else {
                  $request->value = 1;
              }
          }

          // update attribute
          $name = $request->name;
          $expense->$name = $request->value;

          $expense->save();

          if (($request->name == 'budget_plan') || ($request->name == 'budget_remaining')) {
              $expense->$name = number_format($expense->$name, 0);
          }

          $expense = $expense->$name;

        });

        return $expense;

    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $expense      = Expense::find($id);
            $expense->delete();
        });

        $res = [
                    'title'     => 'Sukses',
                    'type'      => 'success',
                    'message'     => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('expense.index')
                    ->with($res);
    }
    public function upload(Request $request)
    {
      return view('pages.expense.upload');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader){})->get();
            // dd($datas);
            // $datas = Excel::load(public_path('storage/uploads/'.$name), function($reader) use ($data){
                if ($datas->first()->has('department_code')) {
                    foreach ($datas as $data) {

                        $department = Department::where('department_code', $data->department_code)->first();

                        $expense                    = new Expense;
                        $expense->department_id     = !empty($department) ? $department->id : 0;
                        $expense->budget_no         = $data->budget_no;
                        $expense->sap_cc_code       = $data->sap_cc_code;
                        $expense->equipment_name    = $data->equipment_name;
                        $expense->plan_gr           = $data->plan_gr;
                        $expense->budget_plan       = $data->budget_plan;
                        $expense->budget_remaining       = $data->budget_plan;
                        $expense->save();  
                                        
                    }  

                // });
                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Data berhasil di Upload!'
                            ];
                    Storage::delete('public/uploads/'.$name); 
                    return redirect()
                            ->route('expense.index')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('expense.index')
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
