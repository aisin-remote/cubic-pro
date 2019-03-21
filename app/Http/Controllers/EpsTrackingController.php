<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EpsTracking;
use App\Department;
use DataTables;
use DB;

use Carbon\Carbon;

class EpsTrackingController extends Controller
{
    public function index(Request $request)
    {
        // $fiscal_year = !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y');
        // $year = '2018';
        return view('pages.eps_tracking');
        // return view('pages.eps_tracking', compact(['year']));
    }

    public function show($id)
    {
        $division = Division::find($id);
        if (empty($division)) {
            return response()->json('Type not found', 500);
        }
        return response()->json($division, 200);
    }


    public function getData(Request $request)
    {
        $eps_tracking  = DB::table('v_eps_tracking')
                        ->get();
       
        return DataTables::of($eps_tracking)
       
        ->toJson();
        
        
    }

    public function getDepartmentByDivision($division_id)
    {
        $division = Division::find($division_id);
        $result = [['id' => '', 'text' => '']];

        foreach ($division->department as $department) {
            $result[] = ['id' => $department->id, 'text' => $department->department_name];
        }

        return response()->json($result);
    }
}