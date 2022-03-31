<?php

namespace App\Http\Controllers\Sap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Capex;
use App\CapexArchive;
use App\Expense;
use App\ExpenseArchive;
use App\ApprovalMaster;
use App\ApprovalDetail;
use App\SapAsset;
use App\SapNumber;
use App\SapCostCenter;
use App\SapUom;
use App\SapGlAccout;

use DataTables;
use Carbon\Carbon;
use DB;
use Excel;

class PrController extends Controller
{
	//link to sap
    public function index(Request $request)
    {
        return view('pages.sap.pr');
    }
	public function pr_convert_excel($approval_number)
	{
		$data 							= array();
        $data_collective_number_array 	= array();
        $data_purchase_doc_array 		= array();
        $acct_assign 					= substr($approval_number, 0,2);
		$dep_key    					= "";
		$dep_name   					= "";
		$sap_key    					= "";
		$name       					= "";

    	//header
    	$data = array(array('Document Registration Key', 'Purchasing Doc. Type','Vendor','Purch. Organization','Purch. Group',
    		'Document Date','Collective No.','Our Reference','Purchasing Doc.','DO#','Container No.','INVOICE No.','Incoterms1',
    		'Incoterms2','Doc. Currency','Partner Function1','Reference to other vendor1','Partner Function2','Reference to other vendor2',
    		'Partner Function3','Reference to other vendor3','Partner Function4','Reference to other vendor4', 'PO Header Text1','PO Header Text2',
    		'PO Header Text3','PO Header Text4','PO Header Text5','Purch. Doc. Item','Acct Assgt Cat.','Item Category','Quantity','Order Unit',
    		'Delivery Date','Net Price','Price Unit','Material No.','Short Text','Standard','Plant','Material Group','Storage Location',
    		'Free of Charge','Returns Item','Vendor Mat. No.','Asset','Subnumber','G/L Account','Cost Center','Resp.Center','Requirment No','Tax code',
    		'No ERS','Non-receipt Standard Billing'
    		));

    		//merubah status download menjadi 1, untuk history
    		$approval = ApprovalMaster::where('approval_number', '=', $approval_number)->firstOrFail();
    		$approval->is_download = 1;
            $created_by = $approval->created_by;
    		$approval->save();

            $departments_query = User::join('departments','departments.id', '=', 'users.department_id')
                                 ->select('departments.department_code', 'departments.department_name as dep_name', 'departments.sap_key','users.name')
                                 ->Where('users.id', '=', $created_by)
                                 ->get();

            foreach ($departments_query as $departments_query){
                 $dep_key    = $departments_query->dep_key;
                 $dep_name   = $departments_query->dep_name;
                 $sap_key    = $departments_query->sap_key;
                 $name       = $departments_query->name;
            }

             //untuk mengecek vendor code, kemudian di filter array kemudian mengamnil key nya untuk registration key
            $array_check = DB::table('approval_details')
                ->join('approval_masters', 'approval_details.approval_master_id', '=', 'approval_masters.id')
                ->Select('approval_details.po_number')
                ->where('approval_masters.approval_number',$approval_number)
                ->groupBy('approval_details.sap_vendor_code')
                ->orderBy('approval_details.id')
                ->get();

            //looping kemudian add ke dalam array
            foreach ($array_check as $checking) {
                $data_collective_number_array[] = array("id"=>$checking->po_number);
            }


            switch ($acct_assign) {
                 case 'CX':
                     $acct_assign_cat = 'A';
                     $pur_doc_type    = 'ZNB6';
                     $f               = 'capexes';
                     $material_group  = '2101';
                     break;
                 case 'EX':
                     $acct_assign_cat = 'K';
                     $pur_doc_type    = 'ZNB3';
                     $f               = 'expenses';
                     $material_group  = '2201';
                     break;
                 case 'UC':
                     $acct_assign_cat = 'A';
                     $pur_doc_type    = 'ZNB6';
                     $f               = ''; //hotfix-4.1.6, by yudo 20170507
                     $material_group  = '2101';
                     break;
                 case 'UE':
                     $acct_assign_cat = 'K';
                     $pur_doc_type    = 'ZNB3';
                     $f               = ''; //hotfix-4.1.6, by yudo 20170507
                     $material_group  = '2201';
                     break;
                 default:
                     # code...
                     break;
             }

             //hotfix-4.1.1 by yudo, 20170426, add cc_code untuk responsbility cc_dode
            $print = DB::select('SELECT a.*, b.*, g.cc_gcode,
									(SELECT  COUNT(*)FROM approval_details c
											INNER JOIN approval_masters m ON c.approval_master_id = m.id
											WHERE   c.po_number = a.po_number AND c.id <= a.id AND m.approval_number = "'.$approval_number.'" ORDER BY c.sap_vendor_code) AS RowNumber
								FROM approval_details a INNER JOIN approval_masters b ON a.approval_master_id = b.id
										'.($f == null ? '' : 'INNER JOIN '.$f.' f ON a.budget_no = f.budget_no').'
										INNER JOIN sap_cost_centers g ON g.cc_code = a.sap_cc_code
								WHERE b.approval_number = "'.$approval_number.'" ORDER BY a.po_number');

	    	foreach($print as $prints) {
                 $newDate = date("d.m.Y");
                 $actual_gr = date("d.m.Y", strtotime($prints->actual_gr));
                 //mencari array dan mendapatkan key index dari array untuk registration_key
                 // $registration_key = array_search($prints->sap_vendor_code, $data_collective_number_array);
                 $po_number  = array_search($prints->po_number, array_column($data_collective_number_array, "id"));
                 //sbstr -> untuk mengambil range beberapa karakter, strpad untuk count replace misalnya 0000 jadi 0001, 0099
                 // $collective_number = $acct_assign_cat.$sap_key.substr($approval_number, 9,1).substr($approval_number, 13,5).str_pad($prints->po_number,3,'0',STR_PAD_LEFT);
                 $collective_number = $sap_key.substr($approval_number, 8,2).substr($approval_number, 12,5).str_pad($prints->po_number,2,'0',STR_PAD_LEFT); //hotfix-4.1.4, by yudo maryanto, 20170703, merubah pattern collective number

                 //hotfix-4.1.1, jika cc_gcode 8 = production
                 if ($prints->cc_gcode == "8"){
                    $resp_cc_code = "100000";
                 }
                 else{
                    $resp_cc_code = "";
                 }

                 switch($prints->sap_tax_code){
                    case "V0":
                    $purch_group      = "Z13";
                    $standard_billing = "x";
                    break;

                    case "V1":
                    $purch_group      = "Z11";
                    $standard_billing = "";
                    break;

                    case "V8":
                    $purch_group      = "Z11";
                    $standard_billing = "";
                    break;

                    case "": //Bug
                    $purch_group      = "";
                    $standard_billing = "";
                    break;

                    default:
                    break;

                 }

                 //dev-4.0, by yudo, 20170316, menata di excel untuk qty, pr input,
                 if(($prints->price_to_download % $prints->actual_qty) == 0)
                 {
                    //hotfix-4.1.1, by yudo, sap allow commas hanya di design untuk mata uang USD
                    //selain USD maka tidak ada comma atau decimals
                    if($prints->currency == 'USD'){
                        $price_temp = $prints->price_to_download / $prints->actual_qty;
                        $price      = str_replace('.', ',', $price_temp);
                    }
                    else{
                        $price = round($prints->price_to_download / $prints->actual_qty);
                    }

                    $unit  = 1;
                    $qty   = $prints->actual_qty;
                 }
                 else{
                    if($prints->price_to_download > $prints->actual_qty){
                        $x  = $prints->price_to_download;
                        $y  = $prints->actual_qty;
                    }
                    else{
                        $x = $prints->actual_qty;
                        $y = $prints->price_to_download;
                    }

                    while(($temp = $x % $y) != 0){
                        $x = $y;
                        $y = $temp;
                    }

                    //hotfix-4.1.1, by yudo, sap allow commas hanya di design untuk mata uang USD
                    //selain USD maka tidak ada comma atau decimals
                    if($prints->currency == 'USD'){
                        $price_temp = $prints->price_to_download / $y;
                        $price      = str_replace('.', ',', $price_temp);
                    }
                    else{
                        $price = round($prints->price_to_download / $y);
                    }

                    $unit  = $prints->actual_qty / $y;
                    $qty   = $prints->actual_qty;
                 }



                 $data[] = array(
                    //kolom untuk upload ke SAP
                    $prints->po_number, //A, registrationkey
                    $pur_doc_type, //B
                    "vendor"=>$prints->sap_vendor_code, //C
                    config('cubic.sap.pur_organization'), //D
                    $purch_group, //E
                    $newDate, //F
                    $collective_number , //G, collective number
                    // "purchase_doc_item"=>$purchase_doc_item, //H
                    "", //purchase doc item, H
                    "", //I
                    "", //J
                    "", //K
                    "", //L
                    "", //M
                    "", //N
                    $prints->currency, //O
                    // config('global.sap.partner_function.receiving_doc'), //P
                    "", //P
                    "", //Q
                    "", //R
                    "", //S
                    "", //T
                    "", //U
                    "", //V
                    "", //W
                    "PIC : ".$name.' ( '.$dep_name.' ) ',
                    "", //Y
                    "", //Z
                    "", //AA
                    "", //AB
                    $prints->RowNumber, //AC, purchase doc item
                    $acct_assign_cat , //AD, actual assign cat
                    "", //AE
                    $qty, //AF
                    $prints->pr_uom, //AG
                    $actual_gr, //AH
                    $price, //AI
                    $unit, //AJ
                    "", //AK
                    $prints->remarks, //AL //dev-4.0, by yudo on 20170130, add remarks
                    "", //AM
                    config('cubic.sap.plant'),//AN
                    $material_group, //AO
                    "L001", //AP
                    "", //AQ
                    "", //AR
                    "", //AS
                    $prints->sap_asset_no, //AT
                    "", //AU
                    $prints->sap_account_code, //AV
                    $prints->sap_cc_code, //AW
                    $resp_cc_code, //AX -> responsbility cc_code
                    $prints->sap_track_no, //requirement_no ->AY
                    $prints->sap_tax_code, //AZ
                    "", //BA
                    $standard_billing, //BB

                );
            }
        ob_end_clean(); // this
        ob_start(); // and this
        Excel::create('Filename', function($excel) use ($data){
            $excel->sheet('Data', function($sheet) use ($data) {

                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->setFilename($approval_number)
          ->export('xls');

        return $data;
	}
}
