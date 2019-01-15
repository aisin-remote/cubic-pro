<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Excel;
use DB;
use Storage;
use Carbon\Carbon;
use PDF;

class OutputMasterController extends Controller
{
    public function index(Request $request)
    {
        ini_set('max_execution_time', 0);

        $fiscal_year = !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y');

        return view('pages.output_master', compact(['fiscal_year']));


    }

     public function Download(Request $request)
    {
      $data =[

          'fiscal_year' => !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y')
      ];
      
      
      $pdf = PDF::loadView('pdf.output_material',$data);
      
      return $pdf->setPaper('a4', 'landscape')
                 ->stream('Output_Material.pdf');   

    }
}
