<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ApprovalController;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use App\Park;
use App\Type;
use App\User;
use App\GroupLocation;
use App\Setting;
use App\Department;
use App\Division;
use App\Period;
class DashboardController extends Controller
{
    public function index(Request $request)
    {

       

   //  	$types = Type::get();
   //      $group_locations = GroupLocation::get();

   //  	if ($request->ajax()) {

			// $data['other_park'] = number_format($parks->whereNotIn('type_id', [$vehicle_type->car, $vehicle_type->motorcycle])->pluck('rate_total')->sum(), 2, ',', '.');

			// return response()->json($data);

   //  	}

        // if (auth()->user()->hasRole('approval')) {
            return view('home');
        // }

        // abort(404);

    	// return view('pages.dashboard', compact([ 'types', 'group_locations']));
    }
	public function view( Request $request,$group_type)
	{
		$departments =[];
		$divisions 	 =[];
		if($group_type == 'department')
		{
			$departments = Department::all();
		}elseif($group_type == 'division'){
			$divisions   = Division::all();
		}elseif($group_type == 'all'){
			$divisions   = Division::all();
			$departments = Department::all();
		}
		
		return view('pages.division.index',['departments'=>$departments,'divisions'=>$divisions,'group_type'=>$group_type]);
	}
	public function getJSONData(Request $request)
	{
		$group_type     = $request->group_type;
		$group_name 	= '';
		if(isset($request->division) && $request->division != "")
		{
			$group_type 	= 'division';
			$group_name		= [$request->division];
			
		}elseif(isset($request->department) && $request->department != "")
		{
			$group_type  	= 'department';
			$group_name     = [$request->department];
		}
		
		$budget_type 	= $request->budget_type;	
		$filter   		= urldecode($request->interval);
		
		$plan  			= $request->plan;
		$type			= $request->type;
		
		$result  		= [];
		$period 		= Period::all();
		
		if(!empty($period) && count($period) >=6)
		{
			if(strlen($filter) == 23)
			{
				$fyear_open_from = substr($filter,0,10);
				$fyear_open_to	 = substr($filter,13,23);
			}else{			   																				
				$fyear_open_from = $period[2]->value;																		
				$fyear_open_to   = $period[3]->value;
			}
			$fyear_plan_code = $plan;
			
			$totPlan 		= ApprovalController::sumBudgetPlan($budget_type, $fyear_plan_code,$group_name,$group_type);	
			$totActual 		= ApprovalController::sumBudgetActual($budget_type,array($fyear_open_from,$fyear_open_to),$group_name,$group_type);				 		
			$totUnbudget 	= ApprovalController::sumBudgetActual('u'.substr($budget_type, 0, 1),array($fyear_open_from,$fyear_open_to),$group_name,$group_type);

			if ($type == 'pie' && $budget_type == 'cx') {
				$result['text'] = "Capex Plan : IDR ".$totPlan." Billion";
				$totPlan = $totPlan == 0?0:$totPlan - ($totActual + $totUnbudget);
				$result['data'] = array(
									array('label'=>'Free','data'=>$totPlan),
									array('label'=>'Unbudget Used','data'=>$totUnbudget),
									array('label'=>'Normal Used','data'=>$totActual),
								  );
				
			}elseif ($type == 'pie' && $budget_type == 'ex') {
				$result['text'] = "Expense Plan : IDR ".$totPlan." Billion";
				$totPlan = $totPlan == 0?0:$totPlan - ($totActual + $totUnbudget);
				
				$result['data'] = array(
									array('label'=>'Free','data'=>$totPlan),
									array('label'=>'Unbudget Used','data'=>$totUnbudget),
									array('label'=>'Normal Used','data'=>$totActual),
								  );
			}elseif ($type == 'bar'){
				list($arrPlan, $arrPlanCum,$month) 							=  ApprovalController::sumBudgetPlanMonthly($budget_type, array($fyear_open_from,$fyear_open_to), $fyear_plan_code,$group_name,$group_type);
				list($arrUnbudgetActual, $arrUnbudgetActualCum,$month2) 	=  ApprovalController::sumBudgetActualMonthly('u'.substr($budget_type, 0, 1),array($fyear_open_from,$fyear_open_to),$group_name,$group_type);
				list($arrNormalActual, $arrNormalActualCum,$month3) 		=  ApprovalController::sumBudgetActualMonthly($budget_type, array($fyear_open_from,$fyear_open_to),$group_name,$group_type);						
				
				$arrActualCum = $this->sumArrays($arrUnbudgetActualCum, $arrNormalActualCum);
				
				$arrJSON = array(
								["dataPlan" => $arrPlan],
								["dataUnbudget" => $arrUnbudgetActual],
								["dataNormal" => $arrNormalActual],
								["dataCumPlan" => $arrPlanCum],
								["dataCumActual" => $arrActualCum],
							);
			}
			
			
		}
		return json_encode($result);
	}
	private function sumArrays ($array1, $array2) {
        // Sum between arrays cx + uc
        $arrSum = array_map(function () {
                return array_sum(func_get_args());
        	}, $array1, $array2);
        return $arrSum;
	}
    public function getChart(Request $request)
    {

        $vehicle_type = Setting::getValue('vehicle_type');

    	if ($request->type == 'line') {


    		$data = [

    			[ 
    				'y' => '00:00',
    				'a' => Park::countPark($vehicle_type->car, '00:00', '01:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '00:00', '01:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '00:00', '01:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '01:00',
    				'a' => Park::countPark($vehicle_type->car, '01:00', '02:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '01:00', '02:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '01:00', '02:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '02:00',
    				'a' => Park::countPark($vehicle_type->car, '02:00', '03:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '02:00', '03:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '02:00', '03:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '03:00',
    				'a' => Park::countPark($vehicle_type->car, '03:00', '04:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '03:00', '04:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '03:00', '04:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '04:00',
    				'a' => Park::countPark($vehicle_type->car, '04:00', '05:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '04:00', '05:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '04:00', '05:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '05:00',
    				'a' => Park::countPark($vehicle_type->car, '05:00', '06:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '05:00', '06:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '05:00', '06:00', $request->date_filter)->count(),
				],


				[ 
					'y' => '06:00',
    				'a' => Park::countPark($vehicle_type->car, '06:00', '07:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '06:00', '07:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '06:00', '07:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '07:00',
    				'a' => Park::countPark($vehicle_type->car, '07:00', '08:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '07:00', '08:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '07:00', '08:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '08:00',
    				'a' => Park::countPark($vehicle_type->car, '08:00', '09:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '08:00', '09:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '08:00', '09:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '09:00',
    				'a' => Park::countPark($vehicle_type->car, '09:00', '10:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '09:00', '10:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '09:00', '10:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '10:00',
    				'a' => Park::countPark($vehicle_type->car, '10:00', '11:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '10:00', '11:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '10:00', '11:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '11:00',
    				'a' => Park::countPark($vehicle_type->car, '11:00', '12:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '11:00', '12:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '11:00', '12:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '12:00',
    				'a' => Park::countPark($vehicle_type->car, '12:00', '13:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '12:00', '13:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '12:00', '13:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '13:00',
    				'a' => Park::countPark($vehicle_type->car, '13:00', '14:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '13:00', '14:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '13:00', '14:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '14:00',
    				'a' => Park::countPark($vehicle_type->car, '14:00', '15:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '14:00', '15:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '14:00', '15:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '15:00',
    				'a' => Park::countPark($vehicle_type->car, '15:00', '16:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '15:00', '16:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '15:00', '16:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '16:00',
    				'a' => Park::countPark($vehicle_type->car, '16:00', '17:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '16:00', '17:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '16:00', '17:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '17:00',
    				'a' => Park::countPark($vehicle_type->car, '17:00', '18:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '17:00', '18:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '17:00', '18:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '18:00',
    				'a' => Park::countPark($vehicle_type->car, '18:00', '19:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '18:00', '19:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '18:00', '19:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '19:00',
    				'a' => Park::countPark($vehicle_type->car, '19:00', '20:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '19:00', '20:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '19:00', '20:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '20:00',
    				'a' => Park::countPark($vehicle_type->car, '20:00', '21:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '20:00', '21:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '20:00', '21:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '21:00',
    				'a' => Park::countPark($vehicle_type->car, '21:00', '22:00', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '21:00', '22:00', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '21:00', '22:00', $request->date_filter)->count(),
				],

				[ 
					'y' => '23:00',
    				'a' => Park::countPark($vehicle_type->car, '23:00', '23:59', $request->date_filter)->count(),
    				'b' => Park::countPark($vehicle_type->motorcycle, '23:00', '23:59', $request->date_filter)->count(),
    				'c' => Park::countPark(null, '23:00', '23:59', $request->date_filter)->count(),
				],

    		];

    		return response()->json($data);

    	} else {


    		$data = [

    			[
    				'label' => 'Mobil',
    				'value' => Park::where('type_id', $vehicle_type->car)->whereDate('park_at', $request->date_filter)->count()
    			],
    			[
    				'label' => 'Motor',
    				'value' => Park::where('type_id', $vehicle_type->motorcycle)->whereDate('park_at', $request->date_filter)->count()
    			],
    			[
    				'label' => 'Lainnya',
    				'value' => Park::whereNotIn('type_id', [$vehicle_type->car, $vehicle_type->motorcycle])->whereDate('park_at', $request->date_filter)->count()
    			],

    		];

    		return response()->json($data);

    	}
    }

    public function getDataPark(Request $request)
    {
        ini_set('memory_limit', '2048M');
        
    	$parks = Park::with(['user.user_data', 'rate', 'type', 'groupLocation'])
    				->where(function($where) use ($request){
    					
    					if (!empty($request->date_from) && !empty($request->date_to)) {

    						$where->whereDate('park_at', '>=', $request->date_from)
    								->whereDate('park_at', '<=', $request->date_to);
    					}

    					if ($request->type_id != 'all') {
    						$where->where('type_id', $request->type_id);
    					}

                        if ($request->group_location_id != 'all') {
                            $where->where('group_location_id', $request->group_location_id);
                        }

                        if (!empty($request->name)) {
                            $where->whereHas('user.user_data', function($where) use ($request){
                                $where->where('name', 'like', '%'.$request->name.'%');
                            });
                        }

    				})
    				->orderBy('id', 'desc')
    				->get();

    	return DataTables::of($parks)->toJson();
    }

    public function getDataUser(Request $request)
    {
    	$users = User::with(['user_data', 'tokens'])->where(function($where) use ($request){

    				if ($request->status == 'Online') {

    					$where->whereHas('tokens');
    				}

    				if ($request->status == 'Offline') {
    					$where->doesntHave('tokens');
    				}

                    if (!empty($request->name)) {
                        $where->whereHas('user_data', function($where) use ($request){
                            $where->where('name', 'like', '%'.$request->name.'%');
                        });
                    }

    			})->get();

    	return DataTables::of($users)

    	->rawColumns(['options'])

    	->addColumn('status', function($user){
    		if (count($user->tokens) > 0) {
    			return 'Online';
    		} else {
    			return 'Offline';
    		}
    	})

    	->addColumn('options', function($user){

    		return count($user->tokens) > 0 ? '<a class="btn btn-link text-danger" href="'.url('dashboard/revoke/'.$user->id).'" data-toggle="tooltip" title="Revoke Access">
    					<i class="fa fa-close"></i>
					</a>' : '';

    	})

    	->toJson();
    }

    public function revoke($id)
    {
    	$user = User::find($id);

    	$user->tokens->each(function($token, $key){
            $token->delete();
        });

        return redirect()
        		->back();
    }
}
