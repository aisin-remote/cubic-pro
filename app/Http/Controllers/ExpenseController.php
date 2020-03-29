<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Storage;
use App\Capex;
use App\CapexArchive;
use App\Helper;
use App\User;
use App\Department;
use Carbon\Carbon;
use App\Period;
use App\Expense;
use App\ExpenseArchive;
use App\ApprovalMaster;
use App\ApprovalDetail;
use Excel;
use Config;

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
        $user = auth()->user();
        $department = Department::findOrFail($request->department_id);
        $departmentCode = $department->department_code;
      	$capex 				          = new Expense;
        $capex->department  	= $departmentCode;
        $capex->budget_no       = $request->budget_no;
        $capex->budget_plan     = $request->budget_plan;
        $capex->qty_plan     	= $request->qty_plan;
        $capex->description  	= $request->description;
        $capex->plan_gr         = date('Y-m-d H:i:s', strtotime($request->plan_gr));
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
	public function show($budget_no)
	{
		$expense = Expense::where('budget_no',$budget_no)->first();
        $approval_details = ApprovalDetail::with('approval')
            ->where('budget_no',$budget_no)
            ->get();

		return view('pages.expense.view',compact('expense','approval_details'));
	}
    public function getData(Request $request)
    {
        $expenses = Expense::ability();
        $expenses = $expenses->get();

        return DataTables::of($expenses)
            ->editColumn("status", function ($expense) {
                // $expense->is_closed="ABS";
                if ($expense->status=='0'){
                    return "Under";
                }else{
                    return "Over";
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

                if ($expense->budget_used != 0) {
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

        if (!is_null($request->overwrite)) {
            // Capex::truncate();   //v3.2 by Ferry, Dangerous!
        }

        $is_revision = !is_null($request->revision);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = $this->getCsvFile(public_path('storage/uploads/'.$name));

                if ($datas->first()->has('budget_no')) {
                    foreach ($datas as $data) {

                        $expense = Expense::where('budget_no', $data->budget_no)->first();

                        $gr = strtotime($data->gr);
                        $gr_date = date('Y-m-d',$gr);

                        if (!empty($expense->budget_no) == $data->budget_no and $is_revision == false){
                            return redirect()
                            ->route('expense.index')
                            ->with(
                                [
                                    'title' => 'Error',
                                    'type' => 'error',
                                    'message' => 'Duplicate Entry!'
                                ]
                            );
                        } else if (!empty($expense->budget_no) != $data->budget_no) {
                            $expense                    = new Expense;
                            $expense->budget_no         = $data->budget_no;
                            $expense->sap_cc_code       = $data->sap_cc_code;
                            $expense->department        = $data->dep;
                            $expense->description       = $data->equipment_name;
                            $expense->qty_plan          = $data->qty;
                            $expense->qty_remaining     = $data->qty;
                            $expense->plan_gr           = $gr_date;
                            $expense->budget_plan       = $data->price;
                            $expense->budget_remaining  = $data->price;
                            $expense->save();
                        } else {
                            $expense                    = Expense::firstOrNew(['budget_no' => $data->budget_no]);
                            $expense->budget_no         = $data->budget_no;
                            $expense->sap_cc_code       = $data->sap_cc_code;
                            $expense->department        = $data->dep;
                            $expense->description       = $data->equipment_name;
                            $expense->qty_plan          = $data->qty;
                            $expense->qty_remaining     = $data->qty;
                            $expense->plan_gr           = $gr_date;
                            $expense->budget_plan       = $data->price;
                            $expense->budget_remaining  = $data->price;
                            $expense->is_revised        = $is_revision;
                            $expense->revised_by        = \Auth::user()->id;
                            $expense->revised_at        = date('Y-m-d H:i:s');
                            $expense->save();
                        }

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

	public function archive()
	{
		$src_dest = 'src';
        $title = 'List of Expense Moving (Used <= 0) To Archive For Revision';
		return view('pages.expense.archive',compact('src_dest','title'));
	}
	public function viewArchive()
	{
		$src_dest = 'nsrc';
        $title = 'List of Expense Achive Data';
		return view('pages.expense.archive',compact('src_dest','title'));
	}
	public function getArchiveAjaxSource()
    {
        $expenses = Expense::where('budget_used', '<=', 0 )->orderBy('id','DESC')->get();

         return DataTables::of($expenses)
		  ->editColumn("status", function ($expense) {
				if ($expense->status=='0'){
					return "Underbudget";
				}else{
					return "Overbudget";
				}
		  })
		   ->editColumn("plan_gr", function ($expense) {
				return date('d-M-Y',strtotime($expense->plan_gr));
		  })
		  ->editColumn("is_closed", function ($expense) {
            if ($expense->is_closed=='0'){
                return "Open";

            }else{
                return "Closed";
            }
        })->toJson();
    }
	public function getArchiveAjaxDestination()
	{
		$expensesarchive = ExpenseArchive::orderBy('id','DESC')->get();
		return DataTables::of($expensesarchive)
		  ->editColumn("status", function ($expense) {
				if ($expense->status=='0'){
					return "Underbudget";
				}else{
					return "Overbudget";
				}
		  })
		  ->editColumn("is_closed", function ($expense) {
            if ($expense->is_closed=='0'){
                return "Open";

            }else{
                return "Closed";
            }
        })->toJson();
	}
	public function execArchive(Request $request) {
        try {
           DB::transaction(function() use ($request){

				foreach ($request->budget_number as $budget_number) {

					if (Expense::where('budget_no', $budget_number['value'])->first()) {

						$newInsert = Expense::where('budget_no', $budget_number['value'])->first()->replicate();
						ExpenseArchive::insert($newInsert->toArray('archiving'));

						// Delete the source after success copy!
						Expense::where('budget_no', $budget_number['value'])->delete();
					}
				}
		   });

            $data['success'] = 'Data successfully archived!';

        } catch (\Exception $e) {

            $data['error'] = $e->getMessage();
        }

        return $data;
    }
	public function execUndoArchive(Request $request){
		try {
           DB::transaction(function() use ($request){

				foreach ($request->budget_number as $budget_number) {

					if ($expenseArchive = ExpenseArchive::where('budget_no', $budget_number['value'])->first()) {

						$expense 					= new Expense();
						$expense->fyear 			= $expenseArchive->fyear;
						$expense->budget_no 		= $expenseArchive->budget_no;
						$expense->sap_cc_code 		= $expenseArchive->sap_cc_code;
						$expense->dir 				= $expenseArchive->dir;
						$expense->division 			= $expenseArchive->division;
						$expense->department 		= $expenseArchive->department;
						$expense->description 		= $expenseArchive->description;
						$expense->qty_plan 			= $expenseArchive->qty_plan;
						$expense->qty_used 			= $expenseArchive->qty_used;
						$expense->qty_remaining 	= $expenseArchive->qty_remaining;
						$expense->budget_plan 		= $expenseArchive->budget_plan;
						$expense->budget_reserved 	= $expenseArchive->budget_reserved;
						$expense->budget_used 		= $expenseArchive->budget_used;
						$expense->budget_remaining 	= $expenseArchive->budget_remaining;
						$expense->plan_gr 			= $expenseArchive->plan_gr;
						$expense->is_closed 		= $expenseArchive->is_closed;
						$expense->is_revised 		= $expenseArchive->is_revised;
						$expense->revised_by 		= $expenseArchive->revised_by;
						$expense->revised_at 		= $expenseArchive->revised_at;
						$expense->status 			= $expenseArchive->status;
						$expense->save();
						// Delete the source after success copy!
						ExpenseArchive::where('budget_no', $budget_number['value'])->delete();
					}
				}
		   });

            $data['success'] = 'Data successfully archived!';

        } catch (\Exception $e) {

            $data['error'] = $e->getMessage();
        }

        return $data;
	}
	public function listClosing()
	{
		return view('pages/expense/closing');
	}
	public function getListClosing($page_name)
	{
        $user = auth()->user();
        $expenses = Expense::select('budget_no','description','budget_plan','budget_used','budget_remaining','plan_gr','status','is_closed');

        if (\Entrust::hasRole('user')) {
            $expenses->where('department', $user->department->department_code);
        }

        if (\Entrust::hasRole('department_head')) {
            $expenses->whereIn('department', [$user->department->department_code]);
        }

        if (\Entrust::hasRole('gm')) {
            $expenses->where('division', $user->division->division_code);
        }

        if (\Entrust::hasRole('director')) {
            $expenses->where('dir', $user->dir);
        }
		$expenses->orderBy('id','DESC');
        if($page_name=="current"){
            return Datatables::of($expenses)
					->addColumn("action", function ($expenses) {
						return "<div class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a class='btn btn-danger' href='javascript:deleteBudget(&#39;$expenses->budget_no&#39;,&#39;$expenses->equipment_name&#39;)'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
					})
					->editColumn("is_closed", function ($expenses) {
						if ($expenses->is_closed=='0'){
							return "Open";
						}else{
							return "Closed";
						}
						})
					->editColumn("budget_plan", function ($expenses) {
							return number_format($expenses->budget_plan);
						})
					->editColumn("budget_used", function ($expenses) {
							return number_format($expenses->budget_used);
						})
					->editColumn("budget_remaining", function ($expenses) {
							return number_format($expenses->budget_remaining);
						})
					->editColumn("plan_gr", function ($expenses) {
							return date('d-M-Y',strtotime($expenses->plan_gr));
						})
					->editColumn("status", function ($expenses) {
							if ($expenses->status=='0'){
								return "Underbudget";
							}else{
								return "Overbudget";
							}
						})
					->make(true);
        }
        elseif($page_name == 'approval'){
            return $expenses->get();
        }
        else{
            return Datatables::of($expenses)
				->addColumn('action', function ($expenses) {
				   if(\Entrust::hasRole('budget')){
					   return '<input type="checkbox" name="budget_number" class="budget_number" value="'.$expenses->budget_no.'" onClick="recount();">';
				   }else{
					   return "-";
				   }
			    })
				->editColumn("is_closed", function ($expenses) {
					if ($expenses->is_closed=='0'){
						return "Open";
					}else{
						return "Closed";
					}
				})
				->editColumn("budget_plan", function ($expenses) {
					return number_format($expenses->budget_plan);
				})
				->editColumn("budget_used", function ($expenses) {
					return number_format($expenses->budget_used);
				})
				->editColumn("budget_remaining", function ($expenses) {
					return number_format($expenses->budget_remaining);
				})
				->editColumn("plan_gr", function ($expenses) {
						return date('d-M-Y',strtotime($expenses->plan_gr));
					})
				->editColumn("status", function ($expenses) {
					if ($expenses->status=='0'){
						return "Underbudget";
					}else{
						return "Overbudget";
					}
				})
				->make(true);
        }
	}
	public function closingUpdate(Request $request)
    {

        try {
            DB::transaction(function() use ($request){

				$budget_numbers = [];
				foreach ($request->budget_number as $budget_number) {
					$budget_numbers[] = $budget_number['value'];
				}

				Expense::query()
				->whereIn('budget_no', $budget_numbers)
				->update(['is_closed' => $request->status]);

			});

            $data['success'] = 'Closing updated.';

        } catch (\Exception $e) {

            $data['error'] = $e->getMessage();
        }

        return $data;
    }

    /**
     * generate csv file base on delimiter
     * @param  string $file
     * @return collection $data
     */
    private function getCsvFile($file)
    {
        $delimiters = [",", ";", "\t"];

        foreach ($delimiters as $delimiter) {
            Config::set('excel.csv.delimiter', $delimiter);
            $datas = Excel::load($file, function($reader){})->get();
            if ($datas->first()->has('budget_no')) {
                break;
            }
        }

        return $datas;
    }
}
