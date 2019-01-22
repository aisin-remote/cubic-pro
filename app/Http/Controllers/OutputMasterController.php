<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Excel;
use DB;
use Storage;
use Carbon\Carbon;
use PDF;
use App\System;

class OutputMasterController extends Controller
{
    public function index(Request $request)
    {
        ini_set('max_execution_time', 0);

        $fiscal_year = !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y');

        return view('pages.output_master_2', compact(['fiscal_year']));


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

    public function getSalesData($fiscal_year)
    {
        $product_codes = System::configMultiply('product_code');
        
        $sales_datas  = DB::table('v_sales_datas')
                        ->where('fiscal_year', $fiscal_year)
                        ->get();
        
        $sum_sales_datas = DB::table('v_sum_sales_data')
                        ->where('fiscal_year', $fiscal_year)
                        ->first();
        

        $results = [];
            foreach ($product_codes as $product_code) {
                $results[] = [
                    'product_code' => $product_code['id'],
                    'product_name' => $product_code['text'],
                    'apr_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->apr_amount, 0, '.', ',') : 0,
                    'may_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->may_amount, 0, '.', ',') : 0,
                    'jun_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->jun_amount, 0, '.', ',') : 0,
                    'jul_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->jul_amount, 0, '.', ',') : 0,
                    'aug_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->aug_amount, 0, '.', ',') : 0,
                    'sep_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->sep_amount, 0, '.', ',') : 0,
                    'oct_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->oct_amount, 0, '.', ',') : 0,
                    'nov_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->nov_amount, 0, '.', ',') : 0,
                    'dec_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->dec_amount, 0, '.', ',') : 0,
                    'jan_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->jan_amount, 0, '.', ',') : 0,
                    'feb_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->feb_amount, 0, '.', ',') : 0,
                    'mar_amount' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->mar_amount, 0, '.', ',') : 0,
                    'total' => !empty($sales_datas->firstWhere('product_code', $product_code['id'])) ? number_format($sales_datas->firstWhere('product_code', $product_code['id'])->total, 0, '.', ',') : 0,
                    // Total Sales Data
                    'sum_apr' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_apr, 0, '.', ',') : 0,
                    'sum_may' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_may, 0, '.', ',') : 0,
                    'sum_jun' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_jun, 0, '.', ',') : 0,
                    'sum_jul' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_jul, 0, '.', ',') : 0,
                    'sum_aug' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_aug, 0, '.', ',') : 0,
                    'sum_sep' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_sep, 0, '.', ',') : 0,
                    'sum_oct' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_oct, 0, '.', ',') : 0,
                    'sum_nov' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_nov, 0, '.', ',') : 0,
                    'sum_dec' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_dec, 0, '.', ',') : 0,
                    'sum_jan' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_jan, 0, '.', ',') : 0,
                    'sum_feb' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_feb, 0, '.', ',') : 0,
                    'sum_mar' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->sum_mar, 0, '.', ',') : 0,
                    'sum_total' => !empty($sum_sales_datas) ? number_format($sum_sales_datas->total, 0, '.', ',') : 0,
                ];
            }

        return response()->json($results);

    }

    public function getMaterial($fiscal_year)
    {
        $product_codes = System::configMultiply('product_code');

        $materials  = DB::table('v_material_product')
                        ->where('fiscal_year', $fiscal_year)
                        ->get();

        $sum_materials = DB::table('v_sum_material_product')
                        ->where('fiscal_year', $fiscal_year)
                        ->first();

        $results = [];
            foreach ($product_codes as $product_code) {
                $results[] = [
                    'product_code' => $product_code['id'],
                    'product_name' => $product_code['text'],
                    'apr_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->apr_amount, 0, '.', ',') : 0,
                    'may_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->may_amount, 0, '.', ',') : 0,
                    'jun_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->jun_amount, 0, '.', ',') : 0,
                    'jul_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->jul_amount, 0, '.', ',') : 0,
                    'aug_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->aug_amount, 0, '.', ',') : 0,
                    'sep_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->sep_amount, 0, '.', ',') : 0,
                    'oct_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->oct_amount, 0, '.', ',') : 0,
                    'nov_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->nov_amount, 0, '.', ',') : 0,
                    'dec_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->dec_amount, 0, '.', ',') : 0,
                    'jan_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->jan_amount, 0, '.', ',') : 0,
                    'feb_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->feb_amount, 0, '.', ',') : 0,
                    'mar_amount' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->mar_amount, 0, '.', ',') : 0,
                    'total' => !empty($materials->firstWhere('product_code', $product_code['id'])) ? number_format($materials->firstWhere('product_code', $product_code['id'])->total, 0, '.', ',') : 0,
                    // Total Material
                    'sum_apr' => !empty($sum_materials) ? number_format($sum_materials->sum_apr, 0, '.', ',') : 0,
                    'sum_may' => !empty($sum_materials) ? number_format($sum_materials->sum_may, 0, '.', ',') : 0,
                    'sum_jun' => !empty($sum_materials) ? number_format($sum_materials->sum_jun, 0, '.', ',') : 0,
                    'sum_jul' => !empty($sum_materials) ? number_format($sum_materials->sum_jul, 0, '.', ',') : 0,
                    'sum_aug' => !empty($sum_materials) ? number_format($sum_materials->sum_aug, 0, '.', ',') : 0,
                    'sum_sep' => !empty($sum_materials) ? number_format($sum_materials->sum_sep, 0, '.', ',') : 0,
                    'sum_oct' => !empty($sum_materials) ? number_format($sum_materials->sum_oct, 0, '.', ',') : 0,
                    'sum_nov' => !empty($sum_materials) ? number_format($sum_materials->sum_nov, 0, '.', ',') : 0,
                    'sum_dec' => !empty($sum_materials) ? number_format($sum_materials->sum_dec, 0, '.', ',') : 0,
                    'sum_jan' => !empty($sum_materials) ? number_format($sum_materials->sum_jan, 0, '.', ',') : 0,
                    'sum_feb' => !empty($sum_materials) ? number_format($sum_materials->sum_feb, 0, '.', ',') : 0,
                    'sum_mar' => !empty($sum_materials) ? number_format($sum_materials->sum_mar, 0, '.', ',') : 0,
                    'sum_total' => !empty($sum_materials) ? number_format($sum_materials->total, 0, '.', ',') : 0,
                ];
            }

        return response()->json($results);

    }

    public function getSalesMaterial($fiscal_year)
    {
        $product_codes = System::configMultiply('product_code');
        
        $material_sales  = DB::table('v_presentage_material_product')
                        ->where('fiscal_year', $fiscal_year)
                        ->get();

        $sum_material_sales = DB::table('v_presentage_sum_material_to_sales')
                        ->where('fiscal_year', $fiscal_year)
                        ->first();

        $results = [];
            foreach ($product_codes as $product_code) {
                $results[] = [
                    'product_code' => $product_code['id'],
                    'product_name' => $product_code['text'],
                    'apr_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_apr, 2, '.', ',') : 0,
                    'may_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_may, 2, '.', ',') : 0,
                    'jun_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_jun, 2, '.', ',') : 0,
                    'jul_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_jul, 2, '.', ',') : 0,
                    'aug_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_aug, 2, '.', ',') : 0,
                    'sep_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_sep, 2, '.', ',') : 0,
                    'oct_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_oct, 2, '.', ',') : 0,
                    'nov_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_nov, 2, '.', ',') : 0,
                    'dec_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_dec, 2, '.', ',') : 0,
                    'jan_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_jan, 2, '.', ',') : 0,
                    'feb_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_feb, 2, '.', ',') : 0,
                    'mar_amount' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->perc_mar, 2, '.', ',') : 0,
                    'total' => !empty($material_sales->firstWhere('product_code', $product_code['id'])) ? number_format($material_sales->firstWhere('product_code', $product_code['id'])->total, 2, '.', ',') : 0,
                    // Total Material
                    'sum_apr' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_apr, 2, '.', ',') : 0,
                    'sum_may' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_may, 2, '.', ',') : 0,
                    'sum_jun' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_jun, 2, '.', ',') : 0,
                    'sum_jul' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_jul, 2, '.', ',') : 0,
                    'sum_aug' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_aug, 2, '.', ',') : 0,
                    'sum_sep' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_sep, 2, '.', ',') : 0,
                    'sum_oct' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_oct, 2, '.', ',') : 0,
                    'sum_nov' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_nov, 2, '.', ',') : 0,
                    'sum_dec' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_dec, 2, '.', ',') : 0,
                    'sum_jan' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_jan, 2, '.', ',') : 0,
                    'sum_feb' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_feb, 2, '.', ',') : 0,
                    'sum_mar' => !empty($sum_material_sales) ? number_format($sum_material_sales->perc_mar, 2, '.', ',') : 0,
                    'sum_total' => !empty($sum_material_sales) ? number_format($sum_material_sales->total, 2, '.', ',') : 0,
                ];
            }

        return response()->json($results);

    }

    public function getGroupMaterial($fiscal_year)
    {
        $group_codes = System::config('group_material');
        
        $product_codes = System::configMultiply('product_code');
        
        $material_group  = DB::table('v_material_group')
                        ->where('fiscal_year', $fiscal_year)
                        ->get();
        
        $sum_material_group = DB::table('v_sum_material_group')
                            ->where('fiscal_year', $fiscal_year)
                            ->get();
                        
        $prec_material_group = DB::table('v_percentage_material_group')
                            ->where('fiscal_year', $fiscal_year)
                            ->get();

        $results = [];

        foreach ($group_codes as $group_code){ 
            foreach ($product_codes as $product_code) {

                $results[$group_code['id']][] = [
                    'product_code' => $product_code['id'],
                    'product_name' => $product_code['text'],
                    'apr_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->apr_amount, 0, '.', ',') : 0,
                    'may_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->may_amount, 0, '.', ',') : 0,
                    'jun_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->jun_amount, 0, '.', ',') : 0,
                    'jul_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->jul_amount, 0, '.', ',') : 0,
                    'aug_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->aug_amount, 0, '.', ',') : 0,
                    'sep_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->sep_amount, 0, '.', ',') : 0,
                    'oct_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->oct_amount, 0, '.', ',') : 0,
                    'nov_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->nov_amount, 0, '.', ',') : 0,
                    'dec_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->dec_amount, 0, '.', ',') : 0,
                    'jan_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->jan_amount, 0, '.', ',') : 0,
                    'feb_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->feb_amount, 0, '.', ',') : 0,
                    'mar_amount' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->mar_amount, 0, '.', ',') : 0,
                    'total' => !empty($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()) ? number_format($material_group->where('group_material', $group_code['id'])->where('product_code', $product_code['id'])->first()->total, 0, '.', ',') : 0,
                    // Total Material Group
                    'sum_apr' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_apr, 0, '.', ',') : 0,
                    'sum_may' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_may, 0, '.', ',') : 0,
                    'sum_jun' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_jun, 0, '.', ',') : 0,
                    'sum_jul' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_jul, 0, '.', ',') : 0,
                    'sum_aug' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_aug, 0, '.', ',') : 0,
                    'sum_sep' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_sep, 0, '.', ',') : 0,
                    'sum_oct' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_oct, 0, '.', ',') : 0,
                    'sum_nov' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_nov, 0, '.', ',') : 0,
                    'sum_dec' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_dec, 0, '.', ',') : 0,
                    'sum_jan' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_jan, 0, '.', ',') : 0,
                    'sum_feb' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_feb, 0, '.', ',') : 0,
                    'sum_mar' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->sum_mar, 0, '.', ',') : 0,
                    'sum_total' => !empty($sum_material_group->where('group_material', $group_code['id'])->first()) ? number_format($sum_material_group->where('group_material', $group_code['id'])->first()->total, 0, '.', ',') : 0,
                    // Presentage Material Group
                    'perc_apr' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_apr, 2, '.', ',') : 0,
                    'perc_may' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_may, 2, '.', ',') : 0,
                    'perc_jun' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_jun, 2, '.', ',') : 0,
                    'perc_jul' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_jul, 2, '.', ',') : 0,
                    'perc_aug' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_aug, 2, '.', ',') : 0,
                    'perc_sep' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_sep, 2, '.', ',') : 0,
                    'perc_oct' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_oct, 2, '.', ',') : 0,
                    'perc_nov' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_nov, 2, '.', ',') : 0,
                    'perc_dec' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_dec, 2, '.', ',') : 0,
                    'perc_jan' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_jan, 2, '.', ',') : 0,
                    'perc_feb' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_feb, 2, '.', ',') : 0,
                    'perc_mar' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->perc_mar, 2, '.', ',') : 0,
                    'perc_total' => !empty($prec_material_group->where('group_material', $group_code['id'])->first()) ? number_format($prec_material_group->where('group_material', $group_code['id'])->first()->total, 2, '.', ',') : 0,
                ];
            }
        }
        return response()->json($results);

    }
}
