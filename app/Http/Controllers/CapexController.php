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

      	$capex                  = new Capex;
        $capex->department   	= $request->department_id;
        $capex->budget_no       = $request->budget_no;
        $capex->budget_plan     = $request->budget_plan;
        $capex->equipment_name  = $request->equipment_name;
        $capex->plan_gr         = date('Y-m-d H:i:s', strtotime($request->plan_gr));
        // $capex->budget_remaining= $request->budget_plan;
        // $capex->is_closed       = $request->is_closed;
        $capex->budget_remaining= $request->budget_plan;
        // $capex->status          = $request->status;
		$capex->fyear			= date('Y');
        $capex->save();

      	$res = [

      				'title' 		=> 'Success',
      				'type'			=> 'success',
      				'message'		=> 'Data has been inserted'
    			];
      	return redirect()
      			->route('capex.index')
      			->with($res);
    }

    public function getData(Request $request)
    {
		$capexs = Capex::ability();
	    $capexs = $capexs->get();
        return DataTables::of($capexs)
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
	public function show($budget_no)
	{
		$capex = Capex::where('budget_no',$budget_no)->first();
		$approval_details = ApprovalDetail::join('approval_masters','approval_details.approval_master_id','=','approval_masters.id')
											->where('approval_details.budget_no',$budget_no)->get();

		return view('pages.capex.view',compact('capex','approval_details'));
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
                    'message'     => 'Data has been removed!'
                ];

        return redirect()
                    ->route('capex.index')
                    ->with($res);
    }

    public function upload(Request $request)
    {
      return view('pages.capex.upload');
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

                        $capex = Capex::where('budget_no', $data->budget_no)->first();
                        $gr = strtotime($data->gr);
                        $gr_date = date('Y-m-d',$gr);

                        if ( !empty($capex->budget_no) == $data->budget_no and $is_revision == false){
                            return redirect()
                            ->route('capex.index')
                            ->with(
                                [
                                    'title' => 'Error',
                                    'type' => 'error',
                                    'message' => 'Duplicate Entry!'
                                ]
                            );
                        } else if (!empty($capex->budget_no) != $data->budget_no) {
                            $capex                    = new Capex;
                            $capex->budget_no         = $data->budget_no;
                            $capex->sap_cc_code       = $data->sap_cc_code;
                            $capex->department        = $data->dep;
                            $capex->equipment_name    = $data->equipment_name;
                            $capex->plan_gr           = $gr_date;
                            $capex->budget_plan       = $data->price;
                            $capex->budget_remaining  = $data->price;
                            $capex->save();
                        } else {
                            $capex                    = Capex::firstOrNew(['budget_no' => $data->budget_no]);
                            $capex->budget_no         = $data->budget_no;
                            $capex->sap_cc_code       = $data->sap_cc_code;
                            $capex->department        = $data->dep;
                            $capex->equipment_name    = $data->equipment_name;
                            $capex->plan_gr           = $gr_date;
                            $capex->budget_plan       = $data->price;
                            $capex->budget_remaining  = $data->price;
                            $capex->is_revised        = $is_revision;
                            $capex->revised_by        = \Auth::user()->id;
                            $capex->revised_at        = date('Y-m-d H:i:s');
                            $capex->save();

                        }

                    }

                    if ($is_revision) {

                        $period_revision = Period::where('name', 'fyear_plan_code')->first();
                        if ($period_revision) {
                            $period_revision->value = 'R';
                        }
                        else {
                            $period_revision = Period::firstOrNew(['name' => 'fyear_plan_code', 'value' => 'R']);
                        }
                        $period_revision->save();
                    }

                    $res = [
                                'title'             => 'Sukses',
                                'type'              => 'success',
                                'message'           => 'Upload Success!'
                            ];
                    Storage::delete('public/uploads/'.$name);
                    return redirect()
                            ->route('capex.index')
                            ->with($res);

        // }
                } else {

                    Storage::delete('public/uploads/'.$name);

                    return redirect()
                            ->route('capex.index')
                            ->with(
                                [
                                    'title' => 'Error',
                                    'type' => 'error',
                                    'message' => 'Bad Format!'
                                ]
                            );
                }
        }
    }

	public function archive()
	{
		$src_dest = 'src';
        $title = 'List of Capex Moving (Used <= 0) To Archive For Revision';
		return view('pages.capex.archive',compact('src_dest','title'));
	}
	public function viewArchive()
	{
		$src_dest = 'nsrc';
        $title = 'List of Capex Achive Data';
		return view('pages.capex.archive',compact('src_dest','title'));
	}
	public function getArchiveAjaxSource()
    {
        $capexs = Capex::where('budget_used', '<=', 0 )->orderBy('id','DESC')->get();

         return DataTables::of($capexs)
		  ->editColumn("status", function ($capex) {
				if ($capex->status=='0'){
					return "Underbudget";
				}else{
					return "Overbudget";
				}
		  })
		  ->editColumn("plan_gr", function ($capex) {
				return date('d-M-Y',strtotime($capex->plan_gr));
		  })
		  ->editColumn("is_closed", function ($capex) {
            if ($capex->is_closed=='0'){
                return "Open";

            }else{
                return "Closed";
            }
        })->toJson();
    }
	public function getArchiveAjaxDestination()
	{
		$capexsarchive = CapexArchive::orderBy('id','DESC')->get();
		return DataTables::of($capexsarchive)
		  ->editColumn("status", function ($capex) {
				if ($capex->status=='0'){
					return "Underbudget";
				}else{
					return "Overbudget";
				}
		  })
		  ->editColumn("is_closed", function ($capex) {
            if ($capex->is_closed=='0'){
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

					if (Capex::where('budget_no', $budget_number['value'])->first()) {

						$newInsert = Capex::where('budget_no', $budget_number['value'])->first()->replicate();
						CapexArchive::insert($newInsert->toArray('archiving'));

						// Delete the source after success copy!
						Capex::where('budget_no', $budget_number['value'])->delete();
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

					if ($capexArchive = CapexArchive::where('budget_no', $budget_number['value'])->first()) {

						$capex 						= new Capex();
						$capex->fyear 				= $capexArchive->fyear;
						$capex->budget_no 			= $capexArchive->budget_no;
						$capex->sap_cc_code 		= $capexArchive->sap_cc_code;
						$capex->dir 				= $capexArchive->dir;
						$capex->division 			= $capexArchive->division;
						$capex->department 			= $capexArchive->department;
						$capex->equipment_name 		= $capexArchive->equipment_name;
						$capex->budget_plan 		= $capexArchive->budget_plan;
						$capex->budget_reserved 	= $capexArchive->budget_reserved;
						$capex->budget_used 		= $capexArchive->budget_used;
						$capex->budget_remaining 	= $capexArchive->budget_remaining;
						$capex->plan_gr 			= $capexArchive->plan_gr;
						$capex->is_closed 			= $capexArchive->is_closed;
						$capex->is_revised 			= $capexArchive->is_revised;
						$capex->revised_by 			= $capexArchive->revised_by;
						$capex->revised_at 			= $capexArchive->revised_at;
						$capex->status 				= $capexArchive->status;
						$capex->save();
						// Delete the source after success copy!
						CapexArchive::where('budget_no', $budget_number['value'])->delete();
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
		return view('pages/capex/closing');
	}
	public function getListClosing($page_name)
	{
        $user = auth()->user();
        $capexes = Capex::select('budget_no','equipment_name','budget_plan','budget_used','budget_remaining','plan_gr','status','is_closed');

        if (\Entrust::hasRole('user')) {
            $capexes->where('department', $user->department->department_code);
        }

        if (\Entrust::hasRole('department_head')) {
            $capexes->whereIn('department', [$user->department->department_code]);
        }

        if (\Entrust::hasRole('gm')) {
            $capexes->where('division', $user->division->division_code);
        }

        if (\Entrust::hasRole('director')) {
            $capexes->where('dir', $user->dir);
        }
		$capexes->orderBy('id','DESC');
        if($page_name=="current"){
            return Datatables::of($capexes)
					->addColumn("action", function ($capexes) {
						return "<div class='btn-group btn-group-xs' role='group' aria-label='Extra-small button group'><a class='btn btn-danger' href='javascript:deleteBudget(&#39;$capexes->budget_no&#39;,&#39;$capexes->equipment_name&#39;)'><span class='glyphicon glyphicon-remove' aria-hiden='true'></span></a></div>";
					})
					->editColumn("is_closed", function ($capexes) {
						if ($capexes->is_closed=='0'){
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
					->editColumn("plan_gr", function ($capexes) {
							return date('d-M-Y',strtotime($capexes->plan_gr));
						})
					->editColumn("status", function ($capexes) {
							if ($capexes->status=='0'){
								return "Underbudget";
							}else{
								return "Overbudget";
							}
						})
					->make(true);
        }
        elseif($page_name == 'approval'){
            return $capexes->get();
        }
        else{
            return Datatables::of($capexes)
				->addColumn('action', function ($capexes) {
				   if(\Entrust::hasRole('budget')){
					   return '<input type="checkbox" name="budget_number" class="budget_number" value="'.$capexes->budget_no.'" onClick="recount();">';
				   }else{
					   return "-";
				   }
			    })
				->editColumn("is_closed", function ($capexes) {
					if ($capexes->is_closed=='0'){
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
				->editColumn("plan_gr", function ($capexes) {
							return date('d-M-Y',strtotime($capexes->plan_gr));
						})
				->editColumn("status", function ($capexes) {
					if ($capexes->status=='0'){
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

				Capex::query()
				->whereIn('budget_no', $budget_numbers)
				->update(['is_closed' => $request->status]);

			});

            $data['success'] = 'Closing updated.';

        } catch (\Exception $e) {

            $data['error'] = $e->getMessage();
        }

        return $data;
    }
	public function fiscalYearClosing()
    {
		$period = Period::all();
		if(!empty($period) && count($period)>= 6){

			$fyear_open 		= $period[0]->value;
			$fyear_close 		= $period[1]->value;
			$fyear_open_from    = $period[2]->value;
			$fyear_open_to      = $period[3]->value;
			$fyear_close_from   = $period[4]->value;
			$fyear_close_to     = $period[5]->value;

		}else{
			$fyear_open         = "";
			$fyear_close        = "";
			$fyear_open_from    = "";
			$fyear_open_to      = "";
			$fyear_close_from   = "";
			$fyear_close_to     = "";
		}


        return view('fyear.closing', compact('fyear_open', 'fyear_close','fyear_open_from','fyear_open_to','fyear_close_from','fyear_close_to'));
    }

	public function doFiscalYearClosing(Request $request)
    {
        // \Cache::forget('period');       // Hotfix-3.4.7 forgetting cache of Init Fiscal year

        try {
            // start transcact

			DB::transaction(function() use ($request){
				$period = Period::all();
				if(!empty($period) && count($period)>= 6){

					$fyear_open 		= $period[0]->value;
					$fyear_close 		= $period[1]->value;
					$fyear_open_from    = $period[2]->value;
					$fyear_open_to      = $period[3]->value;
					$fyear_close_from   = $period[4]->value;
					$fyear_close_to     = $period[5]->value;

				}else{
					$data['error'] = 'Period data is not active';

					return $data;
				}

				if (($fyear_open == '') || ($fyear_close == '')) {
					$data['error'] = 'Fiscal Year Closing Error - No valid fiscal year';
					return $data;
				}

				// Closing Capex -- Move all Data to period_master_budgets table

				$budgets = Capex::where('fyear', $fyear_open)->get();

				foreach ($budgets as $budget) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = Capex::where('id', $budget->id)->first()->replicate();
					\DB::table('period_master_budgets')->insert($newInsert->toArray('fyear_closing', $budget->id, $now));  // toArray overriden in Capex Model

					// Delete the source after success copy!
					Capex::where('id', $budget->id)->delete();
				}

				// Closing Capex_Archive -- Move all Data to period_master_budgets table

				$budgets = CapexArchive::where('fyear', $fyear_open)->get();

				foreach ($budgets as $budget) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = CapexArchive::where('id', $budget->id)->first()->replicate();
					\DB::table('period_master_budgets')->insert($newInsert->toArray('fyear_closing', $budget->id, $now));  // toArray overriden in Capex_archive Model

					// Delete the source after success copy!
					CapexArchive::where('id', $budget->id)->delete();
				}

				// Closing Expense -- Move all Data to period_master_budgets table

				$budgets = Expense::where('fyear', $fyear_open)->get();

				foreach ($budgets as $budget) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = Expense::where('id', $budget->id)->first()->replicate();
					\DB::table('period_master_budgets')->insert($newInsert->toArray('fyear_closing', $budget->id, $now));  // toArray overriden in Expense Model

					// Delete the source after success copy!
					Expense::where('id', $budget->id)->delete();
				}

				// Closing Expense_Archive -- Move all Data to period_master_budgets table

				$budgets = ExpenseArchive::where('fyear', $fyear_open)->get();

				foreach ($budgets as $budget) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = ExpenseArchive::where('id', $budget->id)->first()->replicate();
					\DB::table('period_master_budgets')->insert($newInsert->toArray('fyear_closing', $budget->id, $now));  // toArray overriden in Expense_archive Model

					// Delete the source after success copy!
					ExpenseArchive::where('id', $budget->id)->delete();
				}

				// Closing Approval_detail -- Move all Data to period_approval_detail table
				$approval_details = ApprovalDetail::where('fyear', $fyear_open)->get();

				foreach ($approval_details as $approval_detail) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = ApprovalDetail::where('id', $approval_detail->id)->first()->replicate();
					\DB::table('period_approval_details')->insert($newInsert->toArray('fyear_closing', $approval_detail->id, $now));  // toArray overriden in Approval_detail Model

					// Don't Delete, Be careful, Deletion only on master due to relation hasmany
					// Approval_detail::where('id', $approval_detail->id)->delete();
				}

				// Closing Approval_master -- Move all Data to period_approval_master table
				$approvals = ApprovalMaster::where('fyear', $fyear_open)->get();

				foreach ($approvals as $approval) {

					// Uniform Timestamps
					$now = Carbon::now()->format('Y-m-d H:i:s');
					// Get the source & save to archives
					$newInsert = ApprovalMaster::where('id', $approval->id)->first()->replicate();
					\DB::table('period_approval_masters')->insert($newInsert->toArray('fyear_closing', $approval->id, $now));  // toArray overriden in Approval_master Model

					// Delete the source after success copy! cascade with Approval Details --> hasMany
					ApprovalMaster::where('id', $approval->id)->delete();
				}

				// hotfix 3.4.11 by Andre mengganti konstanta dengan variabel
				// Set periods closing
				$period = Period::where('name','fyear_open')->first();
				$period->value = $fyear_open + 1;
				$period->save();

				$period = Period::where('name','fyear_close')->first();
				$period->value = $fyear_open;
				$period->save();

				$period = Period::where('name','fyear_open_from')->first();
				$period->value = ($fyear_open + 1).substr($period->value, 4, 4);
				$period->save();

				$period = Period::where('name','fyear_open_to')->first();
				// $y = Carbon::now()->format('Y');
				$period->value = ($fyear_open + 2).substr($period->value, 4, 4);
				$period->save();

				$period = Period::where('name','fyear_close_from')->first();
				// $y = Carbon::now()->format('Y');
				$period->value = $fyear_open.substr($period->value, 4, 4);
				$period->save();

				$period = Period::where('name','fyear_close_to')->first();
				// $y = Carbon::now()->format('Y');
				$period->value = ($fyear_open + 1).substr($period->value, 4, 4);
				$period->save();
				// end hotfix 3.4.11 by Andre mengganti konstanta dengan variabel

				// hotfix-3.4.16, Ferry, 20161006, Periode Revisi berganti ke Original Plan utk siklus FY Berikutnya
				$period_revision = Period::where('name', 'fyear_plan_code')->first();
				if ($period_revision) {
					$period_revision->value = 'O';
				}
				else {
					$period_revision = Period::firstOrNew(['name' => 'fyear_plan_code', 'value' => 'O']);
				}
				$period_revision->save();

			});

            // \Cache::forget('period');           // Hotfix-3.4.7 forgetting cache of New Fiscal year

            $data['success'] = 'Fiscal Year Successfully Closed!';

        } catch (\Exception $e) {

            $data['error'] = 'Fiscal Year Closing Error - '.$e->getMessage();
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
