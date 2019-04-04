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
use App\VSalesData;
use App\VMaterialProduct;
class OutputMasterController extends Controller
{
    public function index(Request $request)
    {
        ini_set('max_execution_time', 0);

        $fiscal_year = !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y');

        return view('pages.output_master_2', compact(['fiscal_year']));


    }

     public function Download2(Request $request)
    {
      $data =[

          'fiscal_year' => !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y')
      ];
      
      
      $pdf = PDF::loadView('pdf.output_material',$data);
      
      return $pdf->setPaper('a4', 'landscape')
                 ->stream('Output_Material.pdf');   

    }
	public function search($collection,$field,$value)
	{
		$idx=-1;
		foreach($collection as $i=>$col){
			if($col->{$field} == $value){
				$idx=$i;
				break;
			}
			
		}
		return $idx;
	}
	public function search2($collection,$field,$value,$field2,$value2)
	{
		$idx=-1;
		foreach($collection as $i=>$col){
			if($col->{$field} == $value && $col->{$field2} == $value2){
				$idx=$i;
				break;
			}
			
		}
		return $idx;
	}
	private function getSalesDataExcel($fiscal_year,$sales_datas,$product_code){
		
		$apr_total=$may_total=$jun_total=$jul_total=$aug_total=$sep_total=$oct_total=$nov_total=$dec_total=$jan_total=$feb_total=$mar_total=$total_total= 0;
		$data[]   	 	= array('','','SALES AMOUNT','Product Code','Apr-'.$fiscal_year,'May-'.$fiscal_year,'Jun-'.$fiscal_year,'Jul-'.$fiscal_year,'Aug-'.$fiscal_year,'Sep-'.$fiscal_year,'Oct-'.$fiscal_year,'Nov-'.$fiscal_year,'Dec-'.$fiscal_year,'Jan-'.$fiscal_year,'Feb-'.$fiscal_year,'Mar-'.$fiscal_year,'Total '.$fiscal_year);
		
		foreach($product_code as $i=>$code){
			$i++;
			$idx=$this->search($sales_datas,'product_code',$code['id']);
			$vsalesdata = $idx=='-1'?[]:$sales_datas[$idx];
			$apr = empty($vsalesdata)?0:$vsalesdata->apr_amount;
			$may = empty($vsalesdata)?0:$vsalesdata->may_amount;
			$jun = empty($vsalesdata)?0:$vsalesdata->jun_amount;
			$jul = empty($vsalesdata)?0:$vsalesdata->jul_amount;
			$aug = empty($vsalesdata)?0:$vsalesdata->aug_amount;
			$sep = empty($vsalesdata)?0:$vsalesdata->sep_amount;
			$oct = empty($vsalesdata)?0:$vsalesdata->oct_amount;
			$nov = empty($vsalesdata)?0:$vsalesdata->nov_amount;
			$dec = empty($vsalesdata)?0:$vsalesdata->dec_amount;
			$jan = empty($vsalesdata)?0:$vsalesdata->jan_amount;
			$feb = empty($vsalesdata)?0:$vsalesdata->feb_amount;
			$mar = empty($vsalesdata)?0:$vsalesdata->mar_amount;
			$total = empty($vsalesdata)?0:$vsalesdata->total;
			
			$apr_total = $apr_total + $apr;
			$may_total = $may_total + $may;
			$jun_total = $jun_total + $jun;
			$jul_total = $jul_total + $jul;
			$aug_total = $aug_total + $aug;
			$sep_total = $sep_total + $sep;
			$oct_total = $oct_total + $oct;
			$nov_total = $nov_total + $nov;
			$dec_total = $dec_total + $dec;
			$jan_total = $jan_total + $jan;
			$feb_total = $feb_total + $feb;
			$mar_total = $mar_total + $mar;
			$total_total = $total_total + $total;
			
			$data[$i][] = "";
			$data[$i][] = "";
			$data[$i][] = $code['text'];
			$data[$i][] = $code['id'];
			$data[$i][] = number_format($apr);
			$data[$i][] = number_format($may);
			$data[$i][] = number_format($jun);
			$data[$i][] = number_format($jul);
			$data[$i][] = number_format($aug);
			$data[$i][] = number_format($sep);
			$data[$i][] = number_format($oct);
			$data[$i][] = number_format($nov);
			$data[$i][] = number_format($dec);
			$data[$i][] = number_format($jan);
			$data[$i][] = number_format($feb);
			$data[$i][] = number_format($mar);
			$data[$i][] = number_format($total);
		}
		array_push($data,array('','','Total','',number_format($apr_total),number_format($may_total),number_format($jun_total),number_format($jul_total),number_format($aug_total),number_format($sep_total),number_format($oct_total),number_format($nov_total),number_format($dec_total),number_format($jan_total),number_format($feb_total),number_format($mar_total),number_format($total_total)));
		
		return $data;
	}
	public function getSalesMaterialExcel($fiscal_year,$sales_datas,$materials,$product_code)
	{
		$apr_total=$may_total=$jun_total=$jul_total=$aug_total=$sep_total=$oct_total=$nov_total=$dec_total=$jan_total=$feb_total=$mar_total=$total_total= 0;
		$data[]		 = array('','','Material (%Sales)');
		$data[]   	 = array('','','PRODUCT','Product Code','Apr-'.$fiscal_year,'May-'.$fiscal_year,'Jun-'.$fiscal_year,'Jul-'.$fiscal_year,'Aug-'.$fiscal_year,'Sep-'.$fiscal_year,'Oct-'.$fiscal_year,'Nov-'.$fiscal_year,'Dec-'.$fiscal_year,'Jan-'.$fiscal_year,'Feb-'.$fiscal_year,'Mar-'.$fiscal_year,'Total '.$fiscal_year);
		
		$i = 2;
		foreach($product_code as $code){
			$idx=$this->search($sales_datas,'product_code',$code['id']);
			$idx2 = $this->search($materials,'product_code',$code['id']);
			$vsalesdata 	= $idx=='-1'?[]:$sales_datas[$idx];
			$vmaterialdata 	= $idx2=='-1'?[]:$materials[$idx2]; 
			$apr = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->apr_amount/$vsalesdata->apr_amount)/100);
			$may = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->may_amount/$vsalesdata->may_amount)/100);
			$jun = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->jun_amount/$vsalesdata->jun_amount)/100);
			$jul = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->jul_amount/$vsalesdata->jul_amount)/100);
			$aug = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->aug_amount/$vsalesdata->aug_amount)/100);
			$sep = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->sep_amount/$vsalesdata->sep_amount)/100);
			$oct = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->oct_amount/$vsalesdata->oct_amount)/100);
			$nov = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->nov_amount/$vsalesdata->nov_amount)/100);
			$dec = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->dec_amount/$vsalesdata->dec_amount)/100);
			$jan = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->jan_amount/$vsalesdata->jan_amount)/100);
			$feb = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->feb_amount/$vsalesdata->feb_amount)/100);
			$mar = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->mar_amount/$vsalesdata->mar_amount)/100);
			$total = empty($vsalesdata)||empty($vmaterialdata)?0:(($vmaterialdata->total/$vsalesdata->total)/100);
			
			$apr_total = $apr_total + $apr;
			$may_total = $may_total + $may;
			$jun_total = $jun_total + $jun;
			$jul_total = $jul_total + $jul;
			$aug_total = $aug_total + $aug;
			$sep_total = $sep_total + $sep;
			$oct_total = $oct_total + $oct;
			$nov_total = $nov_total + $nov;
			$dec_total = $dec_total + $dec;
			$jan_total = $jan_total + $jan;
			$feb_total = $feb_total + $feb;
			$mar_total = $mar_total + $mar;
			$total_total = $total_total + $total;
			
			$data[$i][] = "";
			$data[$i][] = "";
			$data[$i][] = $code['text'];
			$data[$i][] = $code['id'];
			$data[$i][] = (round($apr,2)).'%';
			$data[$i][] = (round($may,2)).'%';
			$data[$i][] = (round($jun,2)).'%';
			$data[$i][] = (round($jul,2)).'%';
			$data[$i][] = (round($aug,2)).'%';
			$data[$i][] = (round($sep,2)).'%';
			$data[$i][] = (round($oct,2)).'%';
			$data[$i][] = (round($nov,2)).'%';
			$data[$i][] = (round($dec,2)).'%';
			$data[$i][] = (round($jan,2)).'%';
			$data[$i][] = (round($feb,2)).'%';
			$data[$i][] = (round($mar,2)).'%';
			$data[$i][] = (round($total,2)).'%';
			$i++;
		}
		array_push($data,array('','','Total',''
								,(round($apr_total/count($product_code),2)).'%'
								,(round($may_total/count($product_code),2)).'%'
								,(round($jun_total/count($product_code),2)).'%'
								,(round($jul_total/count($product_code),2)).'%'
								,(round($aug_total/count($product_code),2)).'%'
								,(round($sep_total/count($product_code),2)).'%'
								,(round($oct_total/count($product_code),2)).'%'
								,(round($nov_total/count($product_code),2)).'%'
								,(round($dec_total/count($product_code),2)).'%'
								,(round($jan_total/count($product_code),2)).'%'
								,(round($feb_total/count($product_code),2)).'%'
								,(round($mar_total/count($product_code),2)).'%'
								,(round(($total_total)/count($product_code),2)).'%'
							));
		
		return $data;
	}
	public function getMaterialExcel($fiscal_year,$materials,$product_code){
		$apr_total=$may_total=$jun_total=$jul_total=$aug_total=$sep_total=$oct_total=$nov_total=$dec_total=$jan_total=$feb_total=$mar_total=$total_total= 0;
		$data[]		 	= array('','','Material (IDR)');
		$data[]   	 	= array('','','Total Material','Product Code','Apr-'.$fiscal_year,'May-'.$fiscal_year,'Jun-'.$fiscal_year,'Jul-'.$fiscal_year,'Aug-'.$fiscal_year,'Sep-'.$fiscal_year,'Oct-'.$fiscal_year,'Nov-'.$fiscal_year,'Dec-'.$fiscal_year,'Jan-'.$fiscal_year,'Feb-'.$fiscal_year,'Mar-'.$fiscal_year,'Total '.$fiscal_year);
		$i = 2;
		foreach($product_code as $code){
			
			$idx=$this->search($materials,'product_code',$code['id']);
			$vsalesdata = $idx=='-1'?[]:$materials[$idx];
			$apr = empty($vsalesdata)?0:$vsalesdata->apr_amount;
			$may = empty($vsalesdata)?0:$vsalesdata->may_amount;
			$jun = empty($vsalesdata)?0:$vsalesdata->jun_amount;
			$jul = empty($vsalesdata)?0:$vsalesdata->jul_amount;
			$aug = empty($vsalesdata)?0:$vsalesdata->aug_amount;
			$sep = empty($vsalesdata)?0:$vsalesdata->sep_amount;
			$oct = empty($vsalesdata)?0:$vsalesdata->oct_amount;
			$nov = empty($vsalesdata)?0:$vsalesdata->nov_amount;
			$dec = empty($vsalesdata)?0:$vsalesdata->dec_amount;
			$jan = empty($vsalesdata)?0:$vsalesdata->jan_amount;
			$feb = empty($vsalesdata)?0:$vsalesdata->feb_amount;
			$mar = empty($vsalesdata)?0:$vsalesdata->mar_amount;
			$total = empty($vsalesdata)?0:$vsalesdata->total;
			
			$apr_total = $apr_total + $apr;
			$may_total = $may_total + $may;
			$jun_total = $jun_total + $jun;
			$jul_total = $jul_total + $jul;
			$aug_total = $aug_total + $aug;
			$sep_total = $sep_total + $sep;
			$oct_total = $oct_total + $oct;
			$nov_total = $nov_total + $nov;
			$dec_total = $dec_total + $dec;
			$jan_total = $jan_total + $jan;
			$feb_total = $feb_total + $feb;
			$mar_total = $mar_total + $mar;
			$total_total = $total_total + $total;
			
			$data[$i][] = "";
			$data[$i][] = "";
			$data[$i][] = $code['text'];
			$data[$i][] = $code['id'];
			$data[$i][] = number_format($apr);
			$data[$i][] = number_format($may);
			$data[$i][] = number_format($jun);
			$data[$i][] = number_format($jul);
			$data[$i][] = number_format($aug);
			$data[$i][] = number_format($sep);
			$data[$i][] = number_format($oct);
			$data[$i][] = number_format($nov);
			$data[$i][] = number_format($dec);
			$data[$i][] = number_format($jan);
			$data[$i][] = number_format($feb);
			$data[$i][] = number_format($mar);
			$data[$i][] = number_format($total);
			$i++;
		}
		array_push($data,array('','','Total',''
								,number_format($apr_total)
								,number_format($may_total)
								,number_format($jun_total)
								,number_format($jul_total)
								,number_format($aug_total)
								,number_format($sep_total)
								,number_format($oct_total)
								,number_format($nov_total)
								,number_format($dec_total)
								,number_format($jan_total)
								,number_format($feb_total)
								,number_format($mar_total)
								,number_format($total_total)
							));
		
		return $data;
	}
	public function getGroupMaterialExcel($fiscal_year,$material_group,$sales_data,$group_codes,$product_codes){
		$apr_total=$may_total=$jun_total=$jul_total=$aug_total=$sep_total=$oct_total=$nov_total=$dec_total=$jan_total=$feb_total=$mar_total=$total_total= 0;
		$i = 0;
		$j = 1;
		$data = [];
		foreach ($group_codes as $group_code){
			$apr_total=$may_total=$jun_total=$jul_total=$aug_total=$sep_total=$oct_total=$nov_total=$dec_total=$jan_total=$feb_total=$mar_total=$total_total= 0;
			$apr_total2=$may_total2=$jun_total2=$jul_total2=$aug_total2=$sep_total2=$oct_total2=$nov_total2=$dec_total2=$jan_total2=$feb_total2=$mar_total2=$total_total2= 0;
			$data[$i]   	 = array('','Group',$group_code['text'],'Product Code','Apr-'.$fiscal_year,'May-'.$fiscal_year,'Jun-'.$fiscal_year,'Jul-'.$fiscal_year,'Aug-'.$fiscal_year,'Sep-'.$fiscal_year,'Oct-'.$fiscal_year,'Nov-'.$fiscal_year,'Dec-'.$fiscal_year,'Jan-'.$fiscal_year,'Feb-'.$fiscal_year,'Mar-'.$fiscal_year,'Total '.$fiscal_year);
            $i++;
			foreach ($product_codes as $product_code) {
				$idx 			= $this->search($sales_data,'product_code',$product_code['id']);
				$idx2			= $this->search2($material_group,'group_material',$group_code['id'],'product_code',$product_code['id']);
				$salesData	   	= $idx=='-1'?[]:$sales_data[$idx];
				$materialGroup 	= $idx2=='-1'?[]:$material_group[$idx2];
				
				$apr2 	= empty($salesData)?0:$salesData->apr_amount;
				$may2 	= empty($salesData)?0:$salesData->may_amount;
				$jun2 	= empty($salesData)?0:$salesData->jun_amount;
				$jul2 	= empty($salesData)?0:$salesData->jul_amount;
				$aug2 	= empty($salesData)?0:$salesData->aug_amount;
				$sep2 	= empty($salesData)?0:$salesData->sep_amount;
				$oct2 	= empty($salesData)?0:$salesData->oct_amount;
				$nov2 	= empty($salesData)?0:$salesData->nov_amount;
				$dec2 	= empty($salesData)?0:$salesData->dec_amount;
				$jan2 	= empty($salesData)?0:$salesData->jan_amount;
				$feb2 	= empty($salesData)?0:$salesData->feb_amount;
				$mar2 	= empty($salesData)?0:$salesData->mar_amount;
				$total2 = empty($salesData)?0:$salesData->total;
				
				$apr = empty($materialGroup)?0:$materialGroup->apr_amount;
				$may = empty($materialGroup)?0:$materialGroup->may_amount;
				$jun = empty($materialGroup)?0:$materialGroup->jun_amount;
				$jul = empty($materialGroup)?0:$materialGroup->jul_amount;
				$aug = empty($materialGroup)?0:$materialGroup->aug_amount;
				$sep = empty($materialGroup)?0:$materialGroup->sep_amount;
				$oct = empty($materialGroup)?0:$materialGroup->oct_amount;
				$nov = empty($materialGroup)?0:$materialGroup->nov_amount;
				$dec = empty($materialGroup)?0:$materialGroup->dec_amount;
				$jan = empty($materialGroup)?0:$materialGroup->jan_amount;
				$feb = empty($materialGroup)?0:$materialGroup->feb_amount;
				$mar = empty($materialGroup)?0:$materialGroup->mar_amount;
				$total = empty($materialGroup)?0:$materialGroup->total;
				
				$apr_total2 = $apr_total2 + $apr2;
				$may_total2 = $may_total2 + $may2;
				$jun_total2 = $jun_total2 + $jun2;
				$jul_total2 = $jul_total2 + $jul2;
				$aug_total2 = $aug_total2 + $aug2;
				$sep_total2 = $sep_total2 + $sep2;
				$oct_total2 = $oct_total2 + $oct2;
				$nov_total2 = $nov_total2 + $nov2;
				$dec_total2 = $dec_total2 + $dec2;
				$jan_total2 = $jan_total2 + $jan2;
				$feb_total2 = $feb_total2 + $feb2;
				$mar_total2 = $mar_total2 + $mar2;
				$total_total2 = $total_total2 + $total2;
				
				
				$apr_total = $apr_total + $apr;
				$may_total = $may_total + $may;
				$jun_total = $jun_total + $jun;
				$jul_total = $jul_total + $jul;
				$aug_total = $aug_total + $aug;
				$sep_total = $sep_total + $sep;
				$oct_total = $oct_total + $oct;
				$nov_total = $nov_total + $nov;
				$dec_total = $dec_total + $dec;
				$jan_total = $jan_total + $jan;
				$feb_total = $feb_total + $feb;
				$mar_total = $mar_total + $mar;
				$total_total = $total_total + $total;
			
				$data[$i][] = "";
				$data[$i][] = "";
				$data[$i][] = $product_code['id'];
				$data[$i][] = $product_code['text'];
				$data[$i][] = number_format($apr);
				$data[$i][] = number_format($may);
				$data[$i][] = number_format($jun);
				$data[$i][] = number_format($jul);
				$data[$i][] = number_format($aug);
				$data[$i][] = number_format($sep);
				$data[$i][] = number_format($oct);
				$data[$i][] = number_format($nov);
				$data[$i][] = number_format($dec);
				$data[$i][] = number_format($jan);
				$data[$i][] = number_format($feb);
				$data[$i][] = number_format($mar);
				$data[$i][] = number_format($total);
				$i++;
			}
			$data[$i][] ="";
			$data[$i][] ="";
			$data[$i][] ="Total";
			$data[$i][] ="";
			$data[$i][] = number_format($apr_total);
			$data[$i][] = number_format($may_total);
			$data[$i][] = number_format($jun_total);
			$data[$i][] = number_format($jul_total);
			$data[$i][] = number_format($aug_total);
			$data[$i][] = number_format($sep_total);
			$data[$i][] = number_format($oct_total);
			$data[$i][] = number_format($nov_total);
			$data[$i][] = number_format($dec_total);
			$data[$i][] = number_format($jan_total);
			$data[$i][] = number_format($feb_total);
			$data[$i][] = number_format($mar_total);
			$data[$i][] = number_format($total_total);
			$i++;
			$data[$i][] ="";
			$data[$i][] ="";
			$data[$i][] ="Percentace";
			$data[$i][] ="";
			$data[$i][] = $apr_total2 == 0?'0%':(round(($apr_total/$apr_total2)/100,2)).'%';
			$data[$i][] = $may_total2 == 0?'0%':(round(($may_total/$may_total2)/100,2)).'%';
			$data[$i][] = $jun_total2 == 0?'0%':(round(($jun_total/$jun_total2)/100,2)).'%';
			$data[$i][] = $jul_total2 == 0?'0%':(round(($jul_total/$jul_total2)/100,2)).'%';
			$data[$i][] = $aug_total2 == 0?'0%':(round(($aug_total/$aug_total2)/100,2)).'%';
			$data[$i][] = $sep_total2 == 0?'0%':(round(($sep_total/$sep_total2)/100,2)).'%';
			$data[$i][] = $oct_total2 == 0?'0%':(round(($oct_total/$oct_total2)/100,2)).'%';
			$data[$i][] = $nov_total2 == 0?'0%':(round(($nov_total/$nov_total2)/100,2)).'%';
			$data[$i][] = $dec_total2 == 0?'0%':(round(($dec_total/$dec_total2)/100,2)).'%';
			$data[$i][] = $jan_total2 == 0?'0%':(round(($jan_total/$jan_total2)/100,2)).'%';
			$data[$i][] = $feb_total2 == 0?'0%':(round(($feb_total/$feb_total2)/100,2)).'%';
			$data[$i][] = $mar_total2 == 0?'0%':(round(($mar_total/$mar_total2)/100,2)).'%';
			$data[$i][] = $total_total2 == 0?'0%':(round(($total_total/$total_total2)/100,2)).'%';
			$i++;
			$j++;
        }
		return $data;
	}
	public function Download(Request $request)
	{
		$fiscal_year 		= !isset($request->fiscal_year) && $request->fiscal_year==""?date('Y'):$request->fiscal_year;
		$group_codes 		= System::config('group_material');
        $product_codes 		= System::configMultiply('product_code');
		$sales_data 		= DB::table('v_sales_datas')
								->where('fiscal_year', $fiscal_year)
								->get();
		$materials  		= DB::table('v_material_product')
								->where('fiscal_year', $fiscal_year)
								->get();
		$material_group  	= DB::table('v_material_group')
								->where('fiscal_year', $fiscal_year)
								->get();
		$center_style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
		$fill_style = array(
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'bec7d6')
				)
		);
		$font = array('font'  => array(
								'bold'  => true,
								// 'color' => array('rgb' => 'FF0000'),
								// 'size'  => 12,
								// 'name'  => 'Verdana'
							)
		);
		$excel = new \PHPExcel();	 
		$excel->createSheet();
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle('Output Material Budget');
		$excel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
		$excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('F')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('I')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('J')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('L')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('M')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('N')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('O')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('P')->setWidth("30");
		$excel->getActiveSheet()->getColumnDimension('Q')->setWidth("30");
		
		$fiscal_year = !empty($request->fiscal_year) ? $request->fiscal_year : Carbon::now()->format('Y');
		$data1        = $this->getSalesDataExcel($fiscal_year,$sales_data,$product_codes);
		$ndata1 	  = count($data1);
		$excel->getActiveSheet()->getStyle('C1:Q1')->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C'.$ndata1.':Q'.$ndata1)->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C1:Q'.$ndata1)->applyFromArray($center_style);
		$data2 		  = $this->getSalesMaterialExcel($fiscal_year,$sales_data,$materials,$product_codes);
		$ndata2 	  = count($data2);
		$excel->getActiveSheet()->getStyle('E1:Q1')->applyFromArray($fill_style);
		$excel->getActiveSheet()->getStyle('E'.($ndata1+2).':Q'.($ndata1+2))->applyFromArray($fill_style);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+2).':Q'.($ndata1+2))->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+$ndata2).':Q'.($ndata1+$ndata2))->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+2).':Q'.($ndata1+$ndata2))->applyFromArray($center_style);
		$data3        = $this->getMaterialExcel($fiscal_year,$materials,$product_codes);
		$ndata3 	  = count($data3);
		$excel->getActiveSheet()->getStyle('E'.($ndata1+$ndata2+2).':Q'.($ndata1+$ndata2+2))->applyFromArray($fill_style);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+$ndata2+2).':Q'.($ndata1+$ndata2+2))->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+$ndata2+$ndata3).':Q'.($ndata1+$ndata2+$ndata3))->applyFromArray($font);
		$excel->getActiveSheet()->getStyle('C'.($ndata1+$ndata2+2).':Q'.($ndata1+$ndata2+$ndata3))->applyFromArray($center_style);
		$data4 		  = $this->getGroupMaterialExcel($fiscal_year,$material_group,$sales_data,$group_codes,$product_codes);
		$ndata4 	  = count($data4);
		$idx = $ndata1+$ndata2+$ndata3;
		for($i=0;$i<count($group_codes);$i++){
			$excel->getActiveSheet()->setCellValueByColumnAndRow(1,($idx+2) ,($i+1));
			$excel->getActiveSheet()->mergeCells("B".($idx+2).":B".($idx+count($product_codes)+3));
			$excel->getActiveSheet()->getStyle('E'.($idx+1).':Q'.($idx+1))->applyFromArray($fill_style);
			$excel->getActiveSheet()->getStyle('B'.($idx+1).':Q'.($idx+1))->applyFromArray($font);
			$excel->getActiveSheet()->getStyle('B'.($idx+2+count($product_codes)).':Q'.($idx+3+count($product_codes)))->applyFromArray($font);
			$excel->getActiveSheet()->getStyle('B'.($idx+1).':Q'.($idx+count($product_codes)+3))->applyFromArray($center_style);
			$idx = $idx+count($product_codes)+3;
		}
		$data = array_merge($data1,$data2,$data3,$data4);
        $objWorksheet = $excel->getActiveSheet();
        $objWorksheet->fromArray($data);
		$writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->save(storage_path().'/app/public/output_master.xlsx');
		header('Location:'.url('storage/output_master.xlsx'));
		exit;
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
