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

class CapexController extends Controller
{
    public function index(Request $request)
    {

      	if ($request->wantsJson()) {
      		
      		$capex = Capex::get();
      		return response()->json($capex);
      	}
    	return view('pages.capex.index');
    }

    public function create()
    {
    	$capex         = Capex::get();
      $period        = Config('period.fyear.open');
      $department    = Department::get();

    	return view('pages.capex.create', compact('capex', 'period', 'department'));
    }

    public function store(Request $request)
    {

      	$capex 				          = new Capex;
        $capex->department_id   = $request->department_id;
        $capex->budget_no       = $request->budget_no;
        $capex->budget_plan     = $request->budget_plan;
        $capex->equipment_name  = $request->equipment_name;
        $capex->plan_gr         = $request->plan_gr;
        // $capex->budget_remaining= $request->budget_plan;
        // $capex->is_closed       = $request->is_closed;
        $capex->budget_remaining= $request->budget_plan;
        // $capex->status          = $request->status;
        $capex->save();

      	$res = [

      				'title' 		=> 'Success',
      				'type'			=> 'success',
      				'message'		=> 'Data Saved Success'
    			];
      	return redirect()
      			->route('capex.index')
      			->with($res);
    }

    public function getData(Request $request)
    {
        $capexs = Capex::get();

        return DataTables::of($capexs)

        ->rawColumns(['options', 'is_closed'])

        ->addColumn('options', function($capex){
            return '
                
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Hapus" onclick="on_delete('.$capex->id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('capex.destroy', $capex->id).'" method="POST" id="form-delete-'.$capex->id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
                
            ';
        })
        ->editColumn("status", function ($capex) {
               // $expense->is_closed="ABS";
            if ($capex->status=='0'){
                return "Underbudget";
            }else{
                return "Overbudget";
            }
        })
        ->editColumn("is_closed", function ($capex) {
               // $expense->is_closed="ABS";
            if ($capex->is_closed=='0'){
                return "Open";

            }else{
                return "Closed";
            }
        })
        ->editColumn("budget_plan", function ($capexes) {
                return number_format($capexes->budget_plan);
        })
        ->editColumn("budget_used", function ($capexes) {
                return number_format($capexes->budget_used);
        })
        ->editColumn("budget_remaining", function ($capexes) {
                return number_format($capexes->budget_remaining);
        })
        ->toJson();
    }

    public function xedit(Request $request)
    {
        $capex = '';

        DB::transaction(function() use ($request, &$capex){

            $capex = Capex::where('budget_no', $request->pk)->first();

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
          $capex->$name = $request->value;

          $capex->save();

          if (($request->name == 'budget_plan') || ($request->name == 'budget_remaining')) {
              $capex->$name = number_format($capex->$name, 0);
          }

          $capex = $capex->$name;

        });

        return $capex;

    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id){
            $capex      = Capex::find($id);
            $capex->delete();
        });

        $res = [
                    'title'     => 'Sukses',
                    'type'      => 'success',
                    'message'     => 'Data berhasil dihapus!'
                ];

        return redirect()
                    ->route('capex.index')
                    ->with($res);
    }
}
