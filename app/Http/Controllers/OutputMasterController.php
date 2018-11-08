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

class OutputMasterController extends Controller
{
    public function index(Request $request)
    {
  //   	$sales_data = DB::table('sales_datas')->sum('sales_datas.jan_amount')
		// 		    ->where('parts.product_code', '=', 'hd')
		// 		    ->select('sales_datas.jan_amount')
		// 		    ->get();

		// dd($sales_data);die;

  //   	if ($request->wantsJson()) {
		// 	$SalesDatas = SalesData::with(['parts', 'customers'])->get();

  //       	return DataTables::of($SalesDatas)

		// 	->toJson();
  //       }

        return view('pages.output_master');


    }

     public function getData(Request $request)
    {
        $SalesDatas = SalesData::with(['parts', 'customers'])->get();

        return DataTables::of($SalesDatas)

        ->toJson();
    }
}
