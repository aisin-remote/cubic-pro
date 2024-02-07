<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use file;
use Config;
use Response;
use App\Period;
use DataTables;
use App\CapexRb;
use App\LaborRb;
use App\SalesRb;
use App\ExpenseRb;
use App\MasterAccountCode;
use Carbon\Carbon;
use App\MasterCode;
use App\DmaterialRb;
use App\Department;
use App\Exports\RbExport;
use App\Imports\CapexImport;
use App\Imports\SalesImport;
use App\Imports\LaborImport;
// use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Helpers\ImportBinder;
use App\Imports\ExpenseImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DirectMaterialImport;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Helpers\Helper;

class RequestController extends Controller
{

    public function __construct()
    {
        set_time_limit(0);
    }
    //for trial
    public function temp()
    {
        return view('pages.request_budget.rb_temp');
    }

    public function tempimp(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = $this->getCsvFile3(public_path('storage/uploads/' . $name));
            $tes = [];
            if ($datas->first()->has('budget_no')) {
                foreach ($datas as $key => $value) {
                    if ($key >= 0) {
                        array_push($tes, $value);
                    }
                }
                return $tes;
            } else {
                return "CSV";
            }
        }
    }
    //end

    public function salesview()
    {

        return view('pages.request_budget.rb_sales');
    }

    public function laborindex(Request $request)
    {
        if ($request->wantsJson()) {

            $sales = LaborRb::get();
            return response()->json($sales);
        }
        return view('pages.request_budget.laborindex');
    }

    public function getDataLabor(Request $request)
    {
        $Sls = LaborRb::select('dept', 'acc_code', 'acc_name', 'group', 'code', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret', 'fy_first', 'fy_second', 'fy_total')->get();
        return DataTables::of($Sls)->toJson();
    }


    public function laborview()
    {

        return view('pages.request_budget.rb_labor');
    }

    public function laborimportcek(Request $request)
    {
        $file = $request->file('file');
        // dd($file);
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();
        $success = true;
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $pesan = 'Format tidak sesuai';
            $total = 0;
        }

        if ($hasil == 1) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheet(0);

            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {

                if ($sheet->getCell("A$rw")->getCalculatedValue()) {

                    $arrayPush[$i]['acc_code'] = $sheet->getCell("B$rw")->getCalculatedValue();
                    $arrayPush[$i]['fy_total'] = $sheet->getCell("T$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("A$rw")->getValue() == "") {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }

            if (count($arrayPush) > 0) {
                $pesan = '';
                $total = 0;
                foreach ($arrayPush as $key => $val) {
                    if (strpos($val['fy_total'], ".") !== false) {
                        $success = false;
                    }
                    $total = $total + $val['fy_total'];
                }
            }

            // dd($total);
        }
        $res = [
            'success' => $success,
            'pesan' => $pesan,
            'total' => $total
        ];

        return response()->json($res);
    }



    public function laborimport(Request $request)
    {
        $file = $request->file('file');
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();

        // /** Upload file ke storage public */
        // $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        // dd($file);
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $title = 'Gagal';
            $type = 'error';
            $message = 'Data gagal di Upload  bukan xlsx!';
        }

        if ($hasil == 1) {


            $objReader = IOFactory::createReader('Xlsx');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

            $array_data = array();
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowIndex = $row->getRowIndex();
                $array_data[$rowIndex] = array('A' => '', 'B' => '');
                foreach ($cellIterator as $cell) {
                    if ('A' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('B' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('C' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('D' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('E' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('F' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('G' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('H' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('I' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('J' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('K' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('L' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('M' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('N' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('O' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('P' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('Q' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('R' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('S' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('T' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    }
                }
            }

            // dd($array_data);
        }
        if (count($array_data) > 0) {
            unset($array_data[0]);
            unset($array_data[1]);
            foreach ($array_data as $key => $row) {
                $array_data[$key]['created_at'] = now();
                $array_data[$key]['updated_at'] = now();
            }
            // dd($array_data);



            DB::beginTransaction();

            try {
                foreach ($array_data as $val) {

                    $dept = isset($val['A']) ? $val['A'] : null;
                    $acc_code = isset($val['B']) ? $val['B'] : null;
                    $acc_name = isset($val['C']) ? $val['C'] : null;
                    $group = isset($val['D']) ? $val['D'] : null;
                    $code = isset($val['E']) ? $val['E'] : null;
                    $apr = isset($val['F']) ? $val['F'] : null;
                    $may = isset($val['G']) ? $val['G'] : null;
                    $jun = isset($val['H']) ? $val['H'] : null;
                    $jul = isset($val['I']) ? $val['I'] : null;
                    $aug = isset($val['J']) ? $val['J'] : null;
                    $sept = isset($val['K']) ? $val['K'] : null;
                    $oct = isset($val['L']) ? $val['L'] : null;
                    $nov = isset($val['M']) ? $val['M'] : null;
                    $dec = isset($val['N']) ? $val['N'] : null;
                    $jan = isset($val['O']) ? $val['O'] : null;
                    $feb = isset($val['P']) ? $val['P'] : null;
                    $mar = isset($val['Q']) ? $val['Q'] : null;
                    $fy_2022_1st = isset($val['R']) ? $val['R'] : null;
                    $fy_2022_2nd = isset($val['S']) ? $val['S'] : null;
                    $fy_2022_total = isset($val['T']) ? $val['T'] : null;

                    if ($acc_code) {
                        $cek = LaborRb::where([
                            'acc_code' => $acc_code,
                            'group' => $group,
                            'code' => $code
                        ])->first();
                        // dd($cek);
                        if ($cek) {
                            $salesrb = LaborRb::where([
                                'acc_code' => $acc_code,
                                'group' => $group,
                                'code' => $code
                            ])->update([
                                        'april' => $apr,
                                        'mei' => $may,
                                        'juni' => $jun,
                                        'juli' => $jul,
                                        'agustus' => $aug,
                                        'september' => $sept,
                                        'oktober' => $oct,
                                        'november' => $nov,
                                        'december' => $dec,
                                        'januari' => $jan,
                                        'februari' => $feb,
                                        'maret' => $mar,
                                        'fy_first' => $fy_2022_1st,
                                        'fy_second' => $fy_2022_2nd,
                                        'fy_total' => $fy_2022_total
                                    ]);
                        } else {
                            $salesrb = new LaborRb;
                            $salesrb->dept = $dept;
                            $salesrb->acc_code = $acc_code;
                            $salesrb->acc_name = $acc_name;
                            $salesrb->group = $group;
                            $salesrb->code = $code;
                            $salesrb->april = $apr;
                            $salesrb->mei = $may;
                            $salesrb->juni = $jun;
                            $salesrb->juli = $jul;
                            $salesrb->agustus = $aug;
                            $salesrb->september = $sept;
                            $salesrb->oktober = $oct;
                            $salesrb->november = $nov;
                            $salesrb->december = $dec;
                            $salesrb->januari = $jan;
                            $salesrb->februari = $feb;
                            $salesrb->maret = $mar;
                            $salesrb->fy_first = $fy_2022_1st;
                            $salesrb->fy_second = $fy_2022_2nd;
                            $salesrb->fy_total = $fy_2022_total;
                            $salesrb->save();
                        }
                    }
                }

                $hasil = 1;
                $title = 'Success';
                $type = 'success';
                $message = 'Data berhasil di upload';
                DB::commit();
            } catch (Exception $ex) {
                $hasil = 0;
                $title = 'Gagal';
                $type = 'error';
                $message = $ex->getMessage();
                DB::rollBack();
            }
        }
        // dd($upload);
        $res = [
            'title' => $title,
            'type' => $type,
            'message' => $message
        ];


        return redirect()->route('labor.view')->with($res);
    }

    public function slsindex(Request $request)
    {

        if ($request->wantsJson()) {

            $sales = SalesRb::get();
            return response()->json($sales);
        }
        return view('pages.request_budget.slsindex');
    }

    public function getDataSales(Request $request)
    {
        $Sls = SalesRb::select('dept','acc_code', 'acc_name', 'group', 'code', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret', 'fy_first', 'fy_second', 'fy_total')->get();
        // $sls = $slsx->get();
        return DataTables::of($Sls)->toJson();
    }


    public function slsimportcek(Request $request)
    {
        $file = $request->file('file');
        // dd($file);
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();
        $success = true;
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $pesan = 'Format tidak sesuai';
            $total = 0;
        }

        if ($hasil == 1) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheet(0);

            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {

                if ($sheet->getCell("A$rw")->getCalculatedValue()) {

                    $arrayPush[$i]['acc_code'] = $sheet->getCell("B$rw")->getCalculatedValue();
                    $arrayPush[$i]['fy_total'] = $sheet->getCell("T$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("A$rw")->getValue() == "") {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }

            if (count($arrayPush) > 0) {
                $pesan = '';
                $total = 0;
                foreach ($arrayPush as $key => $val) {
                    if (strpos($val['fy_total'], ".") !== false) {
                        $success = false;
                    }
                    $total = $total + $val['fy_total'];
                }
            }

            // dd($total);
        }
        $res = [
            'success' => $success,
            'pesan' => $pesan,
            'total' => $total
        ];

        return response()->json($res);
    }

    public function slsimport(Request $request)
    {
        $file = $request->file('file');
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();

        // /** Upload file ke storage public */
        // $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        // dd($file);
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $title = 'Gagal';
            $type = 'error';
            $message = 'Data gagal di Upload  bukan xlsx!';
        }

        if ($hasil == 1) {


            $objReader = IOFactory::createReader('Xlsx');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

            $array_data = array();
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowIndex = $row->getRowIndex();
                $array_data[$rowIndex] = array('A' => '', 'B' => '');
                foreach ($cellIterator as $cell) {
                    if ('A' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('B' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('C' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('D' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('E' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('F' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('G' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('H' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('I' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('J' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('K' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('L' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('M' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('N' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('O' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('P' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('Q' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('R' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('S' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('T' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    }
                }
            }

            // dd($array_data);
        }
        if (count($array_data) > 0) {
            unset($array_data[0]);
            unset($array_data[1]);
            foreach ($array_data as $key => $row) {
                $array_data[$key]['created_at'] = now();
                $array_data[$key]['updated_at'] = now();
            }
            // dd($array_data);



            DB::beginTransaction();

            try {
                foreach ($array_data as $val) {
                    $dept = isset($val['A']) ? $val['A'] : null;
                    $acc_code = isset($val['B']) ? $val['B'] : null;
                    $acc_name = isset($val['C']) ? $val['C'] : null;
                    $group = isset($val['D']) ? $val['D'] : null;
                    $code = isset($val['E']) ? $val['E'] : null;
                    $apr = isset($val['F']) ? $val['F'] : null;
                    $may = isset($val['G']) ? $val['G'] : null;
                    $jun = isset($val['H']) ? $val['H'] : null;
                    $jul = isset($val['I']) ? $val['I'] : null;
                    $aug = isset($val['J']) ? $val['J'] : null;
                    $sept = isset($val['K']) ? $val['K'] : null;
                    $oct = isset($val['L']) ? $val['L'] : null;
                    $nov = isset($val['M']) ? $val['M'] : null;
                    $dec = isset($val['N']) ? $val['N'] : null;
                    $jan = isset($val['O']) ? $val['O'] : null;
                    $feb = isset($val['P']) ? $val['P'] : null;
                    $mar = isset($val['Q']) ? $val['Q'] : null;
                    $fy_2022_1st = isset($val['R']) ? $val['R'] : null;
                    $fy_2022_2nd = isset($val['S']) ? $val['S'] : null;
                    $fy_2022_total = isset($val['T']) ? $val['T'] : null;

                    if ($acc_code) {
                        $cek = SalesRb::where([
                            'acc_code' => $acc_code,
                            'group' => $group,
                            'code' => $code
                        ])->first();
                        // dd($cek);
                        if ($cek) {
                            $salesrb = SalesRb::where([
                                'acc_code' => $acc_code,
                                'group' => $group,
                                'code' => $code
                            ])->update([
                                        'april' => $apr,
                                        'mei' => $may,
                                        'juni' => $jun,
                                        'juli' => $jul,
                                        'agustus' => $aug,
                                        'september' => $sept,
                                        'oktober' => $oct,
                                        'november' => $nov,
                                        'december' => $dec,
                                        'januari' => $jan,
                                        'februari' => $feb,
                                        'maret' => $mar,
                                        'fy_first' => $fy_2022_1st,
                                        'fy_second' => $fy_2022_2nd,
                                        'fy_total' => $fy_2022_total
                                    ]);
                        } else {
                            $salesrb = new SalesRb;
                            $salesrb->dept = $dept;
                            $salesrb->acc_code = $acc_code;
                            $salesrb->acc_name = $acc_name;
                            $salesrb->group = $group;
                            $salesrb->code = $code;
                            $salesrb->april = $apr;
                            $salesrb->mei = $may;
                            $salesrb->juni = $jun;
                            $salesrb->juli = $jul;
                            $salesrb->agustus = $aug;
                            $salesrb->september = $sept;
                            $salesrb->oktober = $oct;
                            $salesrb->november = $nov;
                            $salesrb->december = $dec;
                            $salesrb->januari = $jan;
                            $salesrb->februari = $feb;
                            $salesrb->maret = $mar;
                            $salesrb->fy_first = $fy_2022_1st;
                            $salesrb->fy_second = $fy_2022_2nd;
                            $salesrb->fy_total = $fy_2022_total;
                            $salesrb->save();
                        }
                    }
                }

                $hasil = 1;
                $title = 'Success';
                $type = 'success';
                $message = 'Data berhasil di upload';
                DB::commit();
            } catch (Exception $ex) {
                $hasil = 0;
                $title = 'Gagal';
                $type = 'error';
                $message = $ex->getMessage();
                DB::rollBack();
            }
        }
        // dd($upload);
        $res = [
            'title' => $title,
            'type' => $type,
            'message' => $message
        ];


        return redirect()->route('sales.view')->with($res);
    }



    public function materialview()
    {
        return view('pages.request_budget.rb_material');
    }

    public function dmindex(Request $request)
    {

        if ($request->wantsJson()) {

            $dmat = DmaterialRb::get();
            return response()->json($dmat);
        }
        return view('pages.request_budget.dmindex');
    }

    public function getDataDM(Request $request)
    {
        $dm = DmaterialRb::select('dept', 'acc_code', 'acc_name', 'group', 'code', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret', 'fy_first', 'fy_second', 'fy_total')->get();
        
        return DataTables::of($dm)->toJson();
    }

    public function materialimportcek(Request $request)
    {
        $file = $request->file('file');
        // dd($file);
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();
        $success = true;
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $pesan = 'Format tidak sesuai';
            $total = 0;
        }

        if ($hasil == 1) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheet(0);

            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {

                if ($sheet->getCell("A$rw")->getCalculatedValue()) {

                    $arrayPush[$i]['acc_code'] = $sheet->getCell("B$rw")->getCalculatedValue();
                    $arrayPush[$i]['fy_total'] = $sheet->getCell("T$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("A$rw")->getValue() == "") {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }

            if (count($arrayPush) > 0) {
                $pesan = '';
                $total = 0;
                foreach ($arrayPush as $key => $val) {
                    if (strpos($val['fy_total'], ".") !== false) {
                        $success = false;
                    }
                    $total = $total + $val['fy_total'];
                }
            }

            // dd($total);
        }
        $res = [
            'success' => $success,
            'pesan' => $pesan,
            'total' => $total
        ];

        return response()->json($res);
    }



    public function materialimport(Request $request)
    {
        $file = $request->file('file');
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();

        // /** Upload file ke storage public */
        // $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        // dd($file);
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $title = 'Gagal';
            $type = 'error';
            $message = 'Data gagal di Upload  bukan xlsx!';
        }

        if ($hasil == 1) {


            $objReader = IOFactory::createReader('Xlsx');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

            $array_data = array();
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowIndex = $row->getRowIndex();
                $array_data[$rowIndex] = array('A' => '', 'B' => '');
                foreach ($cellIterator as $cell) {
                    if ('A' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('B' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('C' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('D' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    } else if ('E' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('F' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('G' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('H' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('I' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('J' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('K' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('L' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('M' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('N' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('O' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('P' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('Q' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('R' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('S' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    } else if ('T' == $cell->getColumn()) {
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getFormattedValue();
                    }
                }
            }

            // dd($array_data);
        }
        if (count($array_data) > 0) {
            unset($array_data[0]);
            unset($array_data[1]);
            foreach ($array_data as $key => $row) {
                $array_data[$key]['created_at'] = now();
                $array_data[$key]['updated_at'] = now();
            }
            // dd($array_data);



            DB::beginTransaction();

            try {
                foreach ($array_data as $val) {

                    $dept = isset($val['A']) ? $val['A'] : null;
                    $acc_code = isset($val['B']) ? $val['B'] : null;
                    $acc_name = isset($val['C']) ? $val['C'] : null;
                    $group = isset($val['D']) ? $val['D'] : null;
                    $code = isset($val['E']) ? $val['E'] : null;
                    $apr = isset($val['F']) ? $val['F'] : null;
                    $may = isset($val['G']) ? $val['G'] : null;
                    $jun = isset($val['H']) ? $val['H'] : null;
                    $jul = isset($val['I']) ? $val['I'] : null;
                    $aug = isset($val['J']) ? $val['J'] : null;
                    $sept = isset($val['K']) ? $val['K'] : null;
                    $oct = isset($val['L']) ? $val['L'] : null;
                    $nov = isset($val['M']) ? $val['M'] : null;
                    $dec = isset($val['N']) ? $val['N'] : null;
                    $jan = isset($val['O']) ? $val['O'] : null;
                    $feb = isset($val['P']) ? $val['P'] : null;
                    $mar = isset($val['Q']) ? $val['Q'] : null;
                    $fy_2022_1st = isset($val['R']) ? $val['R'] : null;
                    $fy_2022_2nd = isset($val['S']) ? $val['S'] : null;
                    $fy_2022_total = isset($val['T']) ? $val['T'] : null;

                    if ($acc_code) {
                        $cek = DmaterialRb::where([
                            'acc_code' => $acc_code,
                            'group' => $group,
                            'code' => $code
                        ])->first();
                        // dd($cek);
                        if ($cek) {
                            $salesrb = DmaterialRb::where([
                                'acc_code' => $acc_code,
                                'group' => $group,
                                'code' => $code
                            ])->update([
                                        'april' => $apr,
                                        'mei' => $may,
                                        'juni' => $jun,
                                        'juli' => $jul,
                                        'agustus' => $aug,
                                        'september' => $sept,
                                        'oktober' => $oct,
                                        'november' => $nov,
                                        'december' => $dec,
                                        'januari' => $jan,
                                        'februari' => $feb,
                                        'maret' => $mar,
                                        'fy_first' => $fy_2022_1st,
                                        'fy_second' => $fy_2022_2nd,
                                        'fy_total' => $fy_2022_total
                                    ]);
                        } else {
                            $salesrb = new DmaterialRb;
                            $salesrb->dept = $dept;
                            $salesrb->acc_code = $acc_code;
                            $salesrb->acc_name = $acc_name;
                            $salesrb->group = $group;
                            $salesrb->code = $code;
                            $salesrb->april = $apr;
                            $salesrb->mei = $may;
                            $salesrb->juni = $jun;
                            $salesrb->juli = $jul;
                            $salesrb->agustus = $aug;
                            $salesrb->september = $sept;
                            $salesrb->oktober = $oct;
                            $salesrb->november = $nov;
                            $salesrb->december = $dec;
                            $salesrb->januari = $jan;
                            $salesrb->februari = $feb;
                            $salesrb->maret = $mar;
                            $salesrb->fy_first = $fy_2022_1st;
                            $salesrb->fy_second = $fy_2022_2nd;
                            $salesrb->fy_total = $fy_2022_total;
                            $salesrb->save();
                        }
                    }
                }

                $hasil = 1;
                $title = 'Success';
                $type = 'success';
                $message = 'Data berhasil di upload';
                DB::commit();
            } catch (Exception $ex) {
                $hasil = 0;
                $title = 'Gagal';
                $type = 'error';
                $message = $ex->getMessage();
                DB::rollBack();
            }
        }
        // dd($upload);
        $res = [
            'title' => $title,
            'type' => $type,
            'message' => $message
        ];


        return redirect()->route('material.view')->with($res);
    }

    public function capexview()
    {
        return view('pages.request_budget.rb_capex');
    }

    public function cpxindex(Request $request)
    {

        if ($request->wantsJson()) {

            $capex = CapexRb::get();
            return response()->json($capex);
        }

        $dep_data = CapexRb::select('dept')->groupBy('dept')->get();
        return view('pages.request_budget.cpxindex', compact('dep_data'));
    }

    public function getDataCPX(Request $request)
    {
        $cpx = CapexRb::select('dept', 'budget_no', 'line', 'profit_center', 'profit_center_code', 'cost_center', 'type', 'project_name', 'import_domestic', 'items_name', 'equipment', 'qty', 'curency', 'original_price', 'exchange_rate', 'price', 'sop', 'gr', 'first_dopayment_term', 'first_dopayment_amount', 'final_payment_term', 'final_payment_amount', 'owner_asset', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret')->get();
        // $sls = $slsx->get();
        // dd($cpx);
        return DataTables::of($cpx)->toJson();
    }


    public function capexexport(Request $request)
    {
        ini_set('max_execution_time', 0);
        ob_start();
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load(public_path('files/Template_Capex_export.xlsx'));

        $dept = $request->post('dept');
        $data = CapexRb::where([
            'dept' => $dept
        ])->get();
        // dd($dept);
        if (count($data) > 0) {
            $i = 0;
            $x = 2;
            foreach ($data as $key => $row) {
                // dd($row->dept);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('B' . 2, $row->dept)
                    ->setCellValue('D' . $x, $row->budget_no)
                    ->setCellValue('E' . $x, $row->line)
                    ->setCellValue('F' . $x, $row->profit_center)
                    ->setCellValue('G' . $x, $row->profit_center_code)
                    ->setCellValue('H' . $x, $row->cost_center)
                    ->setCellValue('I' . $x, $row->type)
                    ->setCellValue('J' . $x, $row->project_name)
                    ->setCellValue('K' . $x, $row->import_domestic)
                    ->setCellValue('L' . $x, $row->items_name)
                    ->setCellValue('M' . $x, $row->equipment)
                    ->setCellValue('N' . $x, $row->qty)
                    ->setCellValue('O' . $x, $row->currency)
                    ->setCellValue('P' . $x, $row->original_price)
                    ->setCellValue('Q' . $x, $row->exchange_rate)
                    ->setCellValue('R' . $x, $row->price)
                    ->setCellValue('S' . $x, $row->sop)
                    ->setCellValue('T' . $x, $row->gr)
                    ->setCellValue('U' . $x, $row->first_dopayment_term)
                    ->setCellValue('V' . $x, $row->first_dopayment_amount)
                    ->setCellValue('W' . $x, $row->final_payment_term)
                    ->setCellValue('X' . $x, $row->final_payment_amount)
                    ->setCellValue('Y' . $x, $row->owner_asset)
                    ->setCellValue('Z' . $x, $row->april)
                    ->setCellValue('AA' . $x, $row->mei)
                    ->setCellValue('AB' . $x, $row->juni)
                    ->setCellValue('AC' . $x, $row->juli)
                    ->setCellValue('AD' . $x, $row->agustus)
                    ->setCellValue('AE' . $x, $row->september)
                    ->setCellValue('AF' . $x, $row->oktober)
                    ->setCellValue('AG' . $x, $row->november)
                    ->setCellValue('AH' . $x, $row->december)
                    ->setCellValue('AI' . $x, $row->januari)
                    ->setCellValue('AJ' . $x, $row->februari)
                    ->setCellValue('AK' . $x, $row->maret);

                $i++;
                $x++;
            }
        }
        $filename = 'Data Capex' . $dept . date('d/m/Y');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'op' => 'ok',
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }


    public function capeximportcek(Request $request)
    {
        try {
            $email = auth()->user()->email;
            $dept_id = auth()->user()->department_id;
            $deptuser = Department::where('id', '=', $dept_id)->first()->department_code;
            $file = $request->file('file');
            // dd($file);
            // $name = time() . '.' . $file->getClientOriginalExtension();
            $ext = $file->getClientOriginalExtension();
            $success = true;
            $pesan = '';
            $total = 0;
            if ($ext != 'xlsx' && $ext != 'xls') {
                $total = 0;
                throw new Exception("Format tidak sesuai");
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheetByName('CAPEX');

            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {
                // dd($sheet->getCell("D$rw")->getCalculatedValue());
                if ($sheet->getCell("A1")->getCalculatedValue() != "" || $sheet->getCell("A1")->getOldCalculatedValue()) {

                    $dept = $sheet->getCell("A1")->getCalculatedValue() ?? $sheet->getCell("A1")->getOldCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() != "" || $sheet->getCell("D$rw")->getCalculatedValue()) {

                    $arrayPush[$i]['budget_no'] = $sheet->getCell("D$rw")->getOldCalculatedValue() ?? $sheet->getCell("D$rw")->getCalculatedValue();
                    $arrayPush[$i]['price'] = $sheet->getCell("R$rw")->getOldCalculatedValue() ?? $sheet->getCell("R$rw")->getCalculatedValue();
                    $arrayPush[$i]['kodevalid'] = $sheet->getCell("AQ$rw")->getOldCalculatedValue() ?? $sheet->getCell("AQ$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() == "" && $sheet->getCell("D$rw")->getCalculatedValue()) {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }

            // dd($arrayPush);
            if (count($arrayPush) > 0) {
                $pesan = '';
                $total = 0;
                foreach ($arrayPush as $key => $val) {
                    if ($dept != $deptuser && $email != 'budget@aiia.co.id') {
                        $success = false;
                        throw new Exception("Departemen Salah");
                    }
                    if (strpos($val['budget_no'], $val['kodevalid']) === false) {
                        $success = false;
                        throw new Exception('Budget Nomor : ' . $val['budget_no'] . ' Tidak Sesuai dengan kreteria , mohon cek kembali');
                    }
                    $total = $total + $val['price'];
                }
            }

            // dd($total);
        } catch (\Throwable $th) {
            $success = false;
            $pesan = $th->getMessage();
        }

        $res = [
            'success' => $success,
            'pesan' => $pesan,
            'total' => $total
        ];

        return response()->json($res);
    }
    public function capeximport(Request $request)
    {
        $file = $request->file('file');
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();

        // /** Upload file ke storage public */
        // $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        // dd($ext);
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls') {
            $hasil = 0;
            $title = 'Gagal';
            $type = 'error';
            $message = 'Data gagal di Upload  bukan xlsx!';
        }

        if ($hasil == 1) {

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheet(1);

            $rw = 2;
            $dept = "";
            $arrayPush = array();

            $i = 0;
            // dd($sheet->getCell("Y6")->getCalculatedValue());
            foreach ($sheet->getRowIterator() as $row) {
                if ($sheet->getCell("A1")->getCalculatedValue() != "" || $sheet->getCell("A1")->getOldCalculatedValue() != "") {

                    $dept = $sheet->getCell("A1")->getCalculatedValue() ?? $sheet->getCell("A1")->getOldCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() != "" || $sheet->getCell("D$rw")->getCalculatedValue() != "") {

                    $arrayPush[$i]['budget_no'] = $sheet->getCell("D$rw")->getOldCalculatedValue() ?? $sheet->getCell("D$rw")->getCalculatedValue();
                    $arrayPush[$i]['line'] = $sheet->getCell("E$rw")->getValue();
                    $arrayPush[$i]['profit_center'] = $sheet->getCell("F$rw")->getValue();
                    $arrayPush[$i]['profit_center_code'] = $sheet->getCell("G$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['cost_center'] = $sheet->getCell("H$rw")->getValue();
                    $arrayPush[$i]['type'] = $sheet->getCell("I$rw")->getValue();
                    $arrayPush[$i]['project_name'] = $sheet->getCell("J$rw")->getValue();
                    $arrayPush[$i]['import_domestic'] = $sheet->getCell("K$rw")->getValue();
                    $arrayPush[$i]['items_name'] = $sheet->getCell("L$rw")->getValue();
                    $arrayPush[$i]['equipment'] = $sheet->getCell("M$rw")->getValue();
                    $arrayPush[$i]['qty'] = $sheet->getCell("N$rw")->getValue();
                    $arrayPush[$i]['curency'] = $sheet->getCell("O$rw")->getValue();
                    $arrayPush[$i]['original_price'] = $sheet->getCell("P$rw")->getCalculatedValue();
                    $arrayPush[$i]['exchange_rate'] = $sheet->getCell("Q$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['price'] = $sheet->getCell("R$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['sop'] = $sheet->getCell("S$rw")->getFormattedValue();
                    $arrayPush[$i]['gr'] = $sheet->getCell("T$rw")->getFormattedValue();
                    $arrayPush[$i]['first_dopayment_term'] = $sheet->getCell("U$rw")->getFormattedValue();
                    $arrayPush[$i]['first_dopayment_amount'] = $sheet->getCell("V$rw")->getCalculatedValue();
                    $arrayPush[$i]['final_payment_term'] = $sheet->getCell("W$rw")->getFormattedValue();
                    $arrayPush[$i]['final_payment_amount'] = $sheet->getCell("X$rw")->getCalculatedValue();
                    $arrayPush[$i]['owner_asset'] = $sheet->getCell("Y$rw")->getValue();
                    $arrayPush[$i]['april'] = $sheet->getCell("Z$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['mei'] = $sheet->getCell("AA$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['juni'] = $sheet->getCell("AB$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['juli'] = $sheet->getCell("AC$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['agustus'] = $sheet->getCell("AD$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['september'] = $sheet->getCell("AE$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['oktober'] = $sheet->getCell("AF$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['november'] = $sheet->getCell("AG$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['december'] = $sheet->getCell("AH$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['januari'] = $sheet->getCell("AI$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['februari'] = $sheet->getCell("AJ$rw")->getOldCalculatedValue();
                    $arrayPush[$i]['maret'] = $sheet->getCell("AK$rw")->getOldCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() == "" && $sheet->getCell("D$rw")->getCalculatedValue() == "") {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }
        }

        // dd($arrayPush);
        if (count($arrayPush) > 0) {
            foreach ($arrayPush as $key => $row) {
                $arrayPush[$key]['dept'] = $dept;
                $arrayPush[$key]['created_at'] = now();
                $arrayPush[$key]['updated_at'] = now();
            }

            DB::beginTransaction();

            try {
                $delete = CapexRb::where([
                    'dept' => $dept
                ])->delete();
                if (!$delete) {
                    $hasil = 0;
                    $title = 'Gagal';
                    $type = 'error';
                    $message = 'Data gagal di Upload';
                    DB::rollBack();
                }

                $capexrb = new CapexRb;

                if (!$capexrb->insert($arrayPush)) {
                    $hasil = 0;
                    $title = 'Gagal';
                    $type = 'error';
                    $message = 'Data gagal di Upload  insert';
                    DB::rollBack();
                } else {
                    $hasil = 1;
                    $title = 'Success';
                    $type = 'success';
                    $message = 'Data berhasil di upload';
                    DB::commit();
                }
            } catch (Exception $ex) {
                $hasil = 0;
                $title = 'Gagal';
                $type = 'error';
                $message = $ex->getMessage();
                DB::rollBack();
            }
        }
        // dd($upload);
        $res = [
            'title' => $title,
            'type' => $type,
            'message' => $message
        ];


        return response()->json($res);
    }

    public function expenseview()
    {


        return view('pages.request_budget.rb_expense');
    }

    public function expindex(Request $request)
    {

        if ($request->wantsJson()) {

            $exp = ExpenseRb::get();
            return response()->json($exp);
        }
        $dep_data = ExpenseRb::select('dept')->groupBy('dept')->get();
        // dd($dep_data);
        return view('pages.request_budget.expindex', compact('dep_data'));
    }

    public function getDataEXP(Request $request)
    {
        $exp = ExpenseRb::select('dept', 'budget_no', 'group', 'code', 'line', 'profit_center', 'profit_center_code', 'cost_center', 'acc_code', 'project_name', 'equipment_name', 'import_domestic', 'qty', 'cur', 'price_per_qty', 'exchange_rate', 'budget_before', 'cr', 'budgt_aft_cr', 'po', 'gr', 'sop', 'first_dopayment_term', 'first_dopayment_amount', 'final_payment_term', 'final_payment_amount', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret')->get();
        // $sls = $slsx->get();
        return DataTables::of($exp)->toJson();
    }

    public function expenseexport(Request $request)
    {
        ini_set('max_execution_time', 0);
        ob_start();
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load(public_path('files/Template_Expense_export.xlsx'));
        // dd($spreadsheet);
        $dept = $request->post('dept');
        $data = ExpenseRb::where([
            'dept' => $dept
        ])->get();
        // dd($data);
        if (count($data) > 0) {
            $i = 0;
            $x = 2;
            foreach ($data as $key => $row) {
                // dd($row->dept);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('B' . 2, $row->dept)
                    ->setCellValue('C' . $x, $i + 1)
                    ->setCellValue('D' . $x, $row->budget_no)
                    ->setCellValue('E' . $x, $row->group)
                    ->setCellValue('F' . $x, $row->line)
                    ->setCellValue('G' . $x, $row->profit_center)
                    ->setCellValue('H' . $x, $row->profit_center_code)
                    ->setCellValue('I' . $x, $row->cost_center)
                    ->setCellValue('J' . $x, $row->acc_code)
                    ->setCellValue('K' . $x, $row->project_name)
                    ->setCellValue('L' . $x, $row->equipment_name)
                    ->setCellValue('M' . $x, $row->import_domestic)
                    ->setCellValue('N' . $x, $row->qty)
                    ->setCellValue('O' . $x, $row->cur)
                    ->setCellValue('P' . $x, $row->price_per_qty)
                    ->setCellValue('Q' . $x, $row->exchange_rate)
                    ->setCellValue('R' . $x, $row->budget_before)
                    ->setCellValue('S' . $x, $row->cr)
                    ->setCellValue('T' . $x, $row->budgt_aft_cr)
                    ->setCellValue('U' . $x, $row->po)
                    ->setCellValue('V' . $x, $row->gr)
                    ->setCellValue('W' . $x, $row->sop)
                    ->setCellValue('X' . $x, $row->first_dopayment_term)
                    ->setCellValue('Y' . $x, $row->first_dopayment_amount)
                    ->setCellValue('Z' . $x, $row->final_payment_term)
                    ->setCellValue('AA' . $x, $row->final_payment_amount)
                    ->setCellValue('AB' . $x, $row->april)
                    ->setCellValue('AC' . $x, $row->mei)
                    ->setCellValue('AD' . $x, $row->juni)
                    ->setCellValue('AE' . $x, $row->juli)
                    ->setCellValue('AF' . $x, $row->agustus)
                    ->setCellValue('AG' . $x, $row->september)
                    ->setCellValue('AH' . $x, $row->oktober)
                    ->setCellValue('AI' . $x, $row->november)
                    ->setCellValue('AJ' . $x, $row->december)
                    ->setCellValue('AK' . $x, $row->januari)
                    ->setCellValue('AL' . $x, $row->februari)
                    ->setCellValue('AM' . $x, $row->maret)
                    ->setCellValue('AN' . $x, $row->checking);

                $i++;
                $x++;
            }

            $sheet = $spreadsheet->getSheet(0);
            $sheet->getStyle('C2' . ':AN' . (count($data) + 1))->applyFromArray(
                [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]
            );
        }
        $filename = 'Data Expense' . $dept . date('d/m/Y');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'op' => 'ok',
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function expenseimportcek(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '256M');
        try {
            $email = auth()->user()->email;
            $dept_id = auth()->user()->department_id;
            $deptuser = Department::where('id', '=', $dept_id)->first()->department_code;
            // dd($deptuser);
            $file = $request->file('file');
            // dd($file);
            // $name = time() . '.' . $file->getClientOriginalExtension();
            $ext = $file->getClientOriginalExtension();
            $success = true;
            $pesan = '';
            $total = 0;
            if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'xlsx') {
                $total = 0;
                throw new Exception("Format tidak sesuai");
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getSheetByName('EXPENSE');
            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {
                if ($sheet->getCell("A1")->getCalculatedValue() != "" || $sheet->getCell("A1")->getOldCalculatedValue() != "") {
                    $dept = $sheet->getCell("A1")->getCalculatedValue() ?? $sheet->getCell("A1")->getOldCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() != "" || $sheet->getCell("D$rw")->getCalculatedValue() != "") {

                    $arrayPush[$i]['budget_no'] = $sheet->getCell("D$rw")->getCalculatedValue() ?? $sheet->getCell("D$rw")->getCalculatedValue();
                    $arrayPush[$i]['budget_after_cr'] = $sheet->getCell("T$rw")->getCalculatedValue();
                    $arrayPush[$i]['account_code'] = $sheet->getCell("J$rw")->getCalculatedValue() ?? $sheet->getCell("D$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() == "" && $sheet->getCell("D$rw")->getCalculatedValue() == "") {
                    break;
                }

                $i++;
                $rw++;
            }
            // dd($dept);

            if (count($arrayPush) > 0) {
                $pesan = '';
                $total = 0;
                foreach ($arrayPush as $key => $val) {
                    if ($dept != $deptuser && $email != 'budget@aiia.co.id') {
                        $success = false;
                        throw new Exception("Departemen Salah");
                    }

                    $ArrayCode = MasterAccountCode::where('department_code', $dept)->pluck('acc_code')->toArray();

                    if (empty($ArrayCode)) {
                        $success = false;
                        throw new Exception("Master account code belum ada");
                    }
                    // dd($ArrayCode);

                    if (!in_array($val['account_code'], $ArrayCode)) {
                        $success = false;
                        throw new Exception("Acc code: {$val['account_code']} tidak ditemukan dalam Master Account Code");
                    }

                    $total = $total + $val['budget_after_cr'];
                }
            }
        } catch (\Throwable $th) {
            $success = false;
            $pesan = $th->getMessage();
        }

        $res = [
            'success' => $success,
            'pesan' => $pesan,
            'total' => $total
        ];

        return response()->json($res);
    }

    public function expenseimport(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '256M');
        $file = $request->file('file');
        // $name = time() . '.' . $file->getClientOriginalExtension();
        $ext = $file->getClientOriginalExtension();

        // /** Upload file ke storage public */
        // $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        // dd($ext);
        $hasil = 1;
        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'xlsx') {
            $hasil = 0;
            $title = 'Gagal';
            $type = 'error';
            $message = 'Data gagal di Upload  bukan xlsx!';
        }

        if ($hasil == 1) {

            $reader = IOFactory::createReader('Xlsx');


            $spreadsheet = $reader->load($file);
            // $sheetData = $spreadsheet->getActiveSheet()->toArray();
            // dd($sheetData);
            $sheet = $spreadsheet->getSheetByName('EXPENSE');
            $rw = 2;
            $dept = "";
            $arrayPush = array();
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {

                if ($sheet->getCell("A1")->getCalculatedValue() != "" || $sheet->getCell("E$rw")->getCalculatedValue() != "") {

                    $dept = $sheet->getCell("A1")->getCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() != "" || $sheet->getCell("D$rw")->getCalculatedValue() != "") {

                    $arrayPush[$i]['budget_no'] = $sheet->getCell("D$rw")->getOldCalculatedValue() ?? $sheet->getCell("D$rw")->getCalculatedValue();
                    $arrayPush[$i]['group'] = $sheet->getCell("E$rw")->getCalculatedValue();
                    $arrayPush[$i]['line'] = $sheet->getCell("F$rw")->getCalculatedValue();
                    $arrayPush[$i]['profit_center'] = $sheet->getCell("G$rw")->getCalculatedValue();
                    $arrayPush[$i]['profit_center_code'] = $sheet->getCell("H$rw")->getCalculatedValue();
                    $arrayPush[$i]['cost_center'] = $sheet->getCell("I$rw")->getCalculatedValue();
                    $arrayPush[$i]['acc_code'] = $sheet->getCell("J$rw")->getCalculatedValue();
                    $arrayPush[$i]['project_name'] = $sheet->getCell("K$rw")->getCalculatedValue();
                    $arrayPush[$i]['equipment_name'] = $sheet->getCell("L$rw")->getCalculatedValue();
                    $arrayPush[$i]['import_domestic'] = $sheet->getCell("M$rw")->getCalculatedValue();
                    $arrayPush[$i]['qty'] = $sheet->getCell("N$rw")->getCalculatedValue();
                    $arrayPush[$i]['cur'] = $sheet->getCell("O$rw")->getCalculatedValue();
                    $arrayPush[$i]['price_per_qty'] = $sheet->getCell("P$rw")->getCalculatedValue();
                    $arrayPush[$i]['exchange_rate'] = $sheet->getCell("Q$rw")->getCalculatedValue();
                    $arrayPush[$i]['budget_before'] = $sheet->getCell("R$rw")->getCalculatedValue();
                    $arrayPush[$i]['cr'] = $sheet->getCell("S$rw")->getCalculatedValue();
                    $arrayPush[$i]['budgt_aft_cr'] = $sheet->getCell("T$rw")->getCalculatedValue();
                    // $po =  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCell("U$rw")->getValue());
                    // // $po = $po['date'];
                    // $po = date('Y-m-d', $po);
                    $arrayPush[$i]['po'] = $sheet->getCell("U$rw")->getFormattedValue();



                    $arrayPush[$i]['gr'] = $sheet->getCell("V$rw")->getFormattedValue();
                    $arrayPush[$i]['sop'] = $sheet->getCell("W$rw")->getFormattedValue();
                    $arrayPush[$i]['first_dopayment_term'] = $sheet->getCell("X$rw")->getFormattedValue();
                    $arrayPush[$i]['first_dopayment_amount'] = $sheet->getCell("Y$rw")->getCalculatedValue();
                    $arrayPush[$i]['final_payment_term'] = $sheet->getCell("Z$rw")->getFormattedValue();
                    $arrayPush[$i]['final_payment_amount'] = $sheet->getCell("AA$rw")->getCalculatedValue();
                    $arrayPush[$i]['april'] = $sheet->getCell("AB$rw")->getCalculatedValue();
                    $arrayPush[$i]['mei'] = $sheet->getCell("AC$rw")->getCalculatedValue();
                    $arrayPush[$i]['juni'] = $sheet->getCell("AD$rw")->getCalculatedValue();
                    $arrayPush[$i]['juli'] = $sheet->getCell("AE$rw")->getCalculatedValue();
                    $arrayPush[$i]['agustus'] = $sheet->getCell("AF$rw")->getCalculatedValue();
                    $arrayPush[$i]['september'] = $sheet->getCell("AG$rw")->getCalculatedValue();
                    $arrayPush[$i]['oktober'] = $sheet->getCell("AH$rw")->getCalculatedValue();
                    $arrayPush[$i]['november'] = $sheet->getCell("AI$rw")->getCalculatedValue();
                    $arrayPush[$i]['december'] = $sheet->getCell("AJ$rw")->getCalculatedValue();
                    $arrayPush[$i]['januari'] = $sheet->getCell("AK$rw")->getCalculatedValue();
                    $arrayPush[$i]['februari'] = $sheet->getCell("AL$rw")->getCalculatedValue();
                    $arrayPush[$i]['maret'] = $sheet->getCell("AM$rw")->getCalculatedValue();
                    $arrayPush[$i]['checking'] = $sheet->getCell("AN$rw")->getCalculatedValue();
                }
                if ($sheet->getCell("D$rw")->getOldCalculatedValue() == "" && $sheet->getCell("D$rw")->getCalculatedValue() == "") {
                    break;
                }

                $i++;
                $rw++;
                // dd($dept);

            }
        }
        if (count($arrayPush) > 0) {
            foreach ($arrayPush as $key => $row) {
                $arrayPush[$key]['dept'] = $dept;
                $arrayPush[$key]['created_at'] = now();
                $arrayPush[$key]['updated_at'] = now();
            }
            // dd($arrayPush);
            DB::beginTransaction();

            try {
                // foreach ($arrayPush as $val) {
                //     $budget_no = isset($val['budget_no']) ? $val['budget_no'] : null;
                //     $group = isset($val['group']) ? $val['group'] : null;
                //     $line = isset($val['line']) ? $val['line'] : null;
                //     $profit_center = isset($val['profit_center']) ? $val['profit_center'] : null;
                //     $profit_center_code = isset($val['profit_center_code']) ? $val['profit_center_code'] : null;
                //     $cost_center = isset($val['cost_center']) ? $val['cost_center'] : null;
                //     $acc_code = isset($val['acc_code']) ? $val['acc_code'] : null;
                //     $project_name = isset($val['project_name']) ? $val['project_name'] : null;
                //     $equipment_name = isset($val['equipment_name']) ? $val['equipment_name'] : null;
                //     $import_domestic = isset($val['import_domestic']) ? $val['import_domestic'] : null;
                //     $qty = isset($val['qty']) ? $val['qty'] : null;
                //     $cur = isset($val['cur']) ? $val['cur'] : null;
                //     $price_per_qty = isset($val['price_per_qty']) ? $val['price_per_qty'] : null;
                //     $exchange_rate = isset($val['exchange_rate']) ? $val['exchange_rate'] : null;
                //     $budget_before = isset($val['budget_before']) ? $val['budget_before'] : null;
                //     $cr = isset($val['cr']) ? $val['cr'] : null;
                //     $budgt_aft_cr = isset($val['budgt_aft_cr']) ? $val['budgt_aft_cr'] : null;
                //     $po = isset($val['po']) ? $val['po'] : null;
                //     $gr = isset($val['gr']) ? $val['gr'] : null;
                //     $sop = isset($val['sop']) ? $val['sop'] : null;
                //     $first_dopayment_term = isset($val['first_dopayment_term']) ? $val['first_dopayment_term'] : null;
                //     $first_dopayment_amount = isset($val['first_dopayment_amount']) ? $val['first_dopayment_amount'] : null;
                //     $final_payment_term = isset($val['final_payment_term']) ? $val['final_payment_term'] : null;
                //     $final_payment_amount = isset($val['final_payment_amount']) ? $val['final_payment_amount'] : null;
                //     $april = isset($val['april']) ? $val['april'] : null;
                //     $mei = isset($val['mei']) ? $val['mei'] : null;
                //     $juni = isset($val['juni']) ? $val['juni'] : null;
                //     $juli = isset($val['juli']) ? $val['juli'] : null;
                //     $agustus = isset($val['agustus']) ? $val['agustus'] : null;
                //     $september = isset($val['september']) ? $val['september'] : null;
                //     $oktober = isset($val['oktober']) ? $val['oktober'] : null;
                //     $november = isset($val['november']) ? $val['november'] : null;
                //     $december = isset($val['december']) ? $val['december'] : null;
                //     $januari = isset($val['januari']) ? $val['januari'] : null;
                //     $februari = isset($val['februari']) ? $val['februari'] : null;
                //     $maret = isset($val['maret']) ? $val['maret'] : null;
                //     $checking = isset($val['checking']) ? $val['checking'] : null;
                //     $dept = isset($val['dept']) ? $val['dept'] : null;

                //     if ($dept && $budget_no) {
                //         $cek =  ExpenseRb::where([
                //             'line' => $line
                //         ])->delete();
                //         // dd($cek);
                //         if ($cek) {
                //             $exprb = ExpenseRb::where([
                //                 'line' => $line
                //             ])->update([
                //                 'budget_no' => $budget_no,
                //                 'group' => $group,
                //                 'line' => $line,
                //                 'profit_center' => $profit_center,
                //                 'profit_center_code' => $profit_center_code,
                //                 'cost_center' => $cost_center,
                //                 'acc_code' => $acc_code,
                //                 'project_name' => $project_name,
                //                 'equipment_name' => $equipment_name,
                //                 'import_domestic' => $import_domestic,
                //                 'qty' => $qty,
                //                 'cur' => $cur,
                //                 'price_per_qty' => $price_per_qty,
                //                 'exchange_rate' => $exchange_rate,
                //                 'budget_before' => $budget_before,
                //                 'cr' => $cr,
                //                 'budgt_aft_cr' => $budgt_aft_cr,
                //                 'po' => $po,
                //                 'gr' => $gr,
                //                 'sop' => $sop,
                //                 'first_dopayment_term' => $first_dopayment_term,
                //                 'first_dopayment_amount' => $first_dopayment_amount,
                //                 'final_payment_term' => $final_payment_term,
                //                 'final_payment_amount' => $final_payment_amount,
                //                 'april' => $april,
                //                 'mei' => $mei,
                //                 'juni' => $juni,
                //                 'juli' => $juli,
                //                 'agustus' => $agustus,
                //                 'september' => $september,
                //                 'oktober' => $oktober,
                //                 'november' => $november,
                //                 'december' => $december,
                //                 'januari' => $januari,
                //                 'februari' => $februari,
                //                 'maret' => $maret,
                //                 'checking' => $checking,
                //                 'dept' => $dept
                //             ]);
                //         } else {
                //             $exprb = new ExpenseRb;
                //             $exprb->budget_no = $budget_no;
                //             $exprb->group = $group;
                //             $exprb->line = $line;
                //             $exprb->profit_center = $profit_center;
                //             $exprb->profit_center_code = $profit_center_code;
                //             $exprb->cost_center = $cost_center;
                //             $exprb->acc_code = $acc_code;
                //             $exprb->project_name = $project_name;
                //             $exprb->equipment_name = $equipment_name;
                //             $exprb->import_domestic = $import_domestic;
                //             $exprb->qty = $qty;
                //             $exprb->cur = $cur;
                //             $exprb->price_per_qty = $price_per_qty;
                //             $exprb->exchange_rate = $exchange_rate;
                //             $exprb->budget_before = $budget_before;
                //             $exprb->cr = $cr;
                //             $exprb->budgt_aft_cr = $budgt_aft_cr;
                //             $exprb->po = $po;
                //             $exprb->gr = $gr;
                //             $exprb->sop = $sop;
                //             $exprb->first_dopayment_term = $first_dopayment_term;
                //             $exprb->first_dopayment_amount = $first_dopayment_amount;
                //             $exprb->final_payment_term = $final_payment_term;
                //             $exprb->final_payment_amount = $final_payment_amount;
                //             $exprb->april = $april;
                //             $exprb->mei = $mei;
                //             $exprb->juni = $juni;
                //             $exprb->juli = $juli;
                //             $exprb->agustus = $agustus;
                //             $exprb->september = $september;
                //             $exprb->oktober = $oktober;
                //             $exprb->november = $november;
                //             $exprb->december = $december;
                //             $exprb->januari = $januari;
                //             $exprb->februari = $februari;
                //             $exprb->maret = $maret;
                //             $exprb->checking = $checking;
                //             $exprb->dept = $dept;

                //             $exprb->save();
                //         }
                //     }
                // }
                $linetemp = '';
                foreach ($arrayPush as $val) {
                    if ($linetemp != $val['line']) {

                        $delete = ExpenseRb::where([
                            'dept' => $val['dept']
                        ])->delete();
                    }
                    $linetemp = $val['line'];
                }
                $capexrb = new ExpenseRb;
                if (!$capexrb->insert($arrayPush)) {
                    $hasil = 0;
                    $title = 'Gagal';
                    $type = 'error';
                    $message = 'Data gagal di Upload  insert';
                    DB::rollBack();
                } else {
                    $hasil = 1;
                    $title = 'Success';
                    $type = 'success';
                    $message = 'Data berhasil di upload';
                    DB::commit();
                }
            } catch (Exception $ex) {
                $hasil = 0;
                $title = 'Gagal';
                $type = 'error';
                $message = $ex->getMessage();
                DB::rollBack();
            }
        }
        // dd($upload);
        $res = [
            'title' => $title,
            'type' => $type,
            'message' => $message
        ];


        return response()->json($res);
    }

    public function exportview()
    {

        return view('pages.request_budget.rb_export');
    }

    public function cekData($array, $data)
    {
        $flag = 0;
        foreach ($array as $x) {
            if ($x == $data) {
                $flag = 1;
            }
        }
        if ($flag == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function draw($file, $data, $master_account_code, $start)
    {
        //BODY
        $bulan = array('april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret');
        foreach ($master_account_code as $master) {
            foreach ($data as $d) {
                // dd($d->acc_code);
                $cut = substr($d->acc_code, 10, 1);
                if ($cut == '_') {
                    $hasil = substr($d->acc_code, 0, 10);
                } else {
                    $hasil = substr($d->acc_code, 0, 12);
                }
                if ($master->acc_code == $hasil) {
                    // return $hasil;
                    // $row = MasterCode::where('acc_code', $hasil)->first();
                    // if(!is_null($row)){
                    $j = 0;
                    $mulai = $start;
                    $end = $mulai + 12;
                    // dd($end);
                    for ($i = $mulai; $i < $end; $i++) {
                        // dd($start);
                        $file->setActiveSheetIndex(2)->setCellValueByColumnAndRow($mulai, $master->cell, $d[$bulan[$j]]);
                        $mulai++;
                        $j++;
                        if ($start == ($end - 1)) {
                            $mulai = $start;
                        }
                    }

                    // }
                }
            }
        }
    }
    public function exporttotemplate()
    {

        ob_end_clean();
        ob_start();

        $master_code = MasterCode::all();

        $codes = ExpenseRb::select(
            'acc_code',
            DB::raw('sum(april) as april'),
            DB::raw('sum(mei) as mei'),
            DB::raw('sum(juni) as juni'),
            DB::raw('sum(juli) as juli'),
            DB::raw('sum(agustus) as agustus'),
            DB::raw('sum(september) as september'),
            DB::raw('sum(oktober) as oktober'),
            DB::raw('sum(november) as november'),
            DB::raw('sum(december) as december'),
            DB::raw('sum(januari) as januari'),
            DB::raw('sum(februari) as februari'),
            DB::raw('sum(maret) as maret')
        )
            ->where('group', 'body')
            ->groupBy('dept','acc_code')
            ->get();

        $codesU = ExpenseRb::select(
            'acc_code',
            DB::raw('sum(april) as april'),
            DB::raw('sum(mei) as mei'),
            DB::raw('sum(juni) as juni'),
            DB::raw('sum(juli) as juli'),
            DB::raw('sum(agustus) as agustus'),
            DB::raw('sum(september) as september'),
            DB::raw('sum(oktober) as oktober'),
            DB::raw('sum(november) as november'),
            DB::raw('sum(december) as december'),
            DB::raw('sum(januari) as januari'),
            DB::raw('sum(februari) as februari'),
            DB::raw('sum(maret) as maret')
        )
            ->where('group', 'unit')
            ->groupBy('dept','acc_code')
            ->get();
        // return $codesU;
        $salesB = SalesRb::select(
            'acc_name',
            DB::raw('sum(april) as sapril'),
            DB::raw('sum(mei) as smei'),
            DB::raw('sum(juni) as sjuni'),
            DB::raw('sum(juli) as sjuli'),
            DB::raw('sum(agustus) as sagustus'),
            DB::raw('sum(september) as sseptember'),
            DB::raw('sum(oktober) as soktober'),
            DB::raw('sum(november) as snovember'),
            DB::raw('sum(december) as sdecember'),
            DB::raw('sum(januari) as sjanuari'),
            DB::raw('sum(februari) as sfebruari'),
            DB::raw('sum(maret) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('acc_name')
            ->get();

        $salesU = SalesRb::select(
            'acc_name',
            DB::raw('sum(april) as sapril'),
            DB::raw('sum(mei) as smei'),
            DB::raw('sum(juni) as sjuni'),
            DB::raw('sum(juli) as sjuli'),
            DB::raw('sum(agustus) as sagustus'),
            DB::raw('sum(september) as sseptember'),
            DB::raw('sum(oktober) as soktober'),
            DB::raw('sum(november) as snovember'),
            DB::raw('sum(december) as sdecember'),
            DB::raw('sum(januari) as sjanuari'),
            DB::raw('sum(februari) as sfebruari'),
            DB::raw('sum(maret) as smaret')
        )
            ->where('group', 'unit')
            ->groupBy('acc_name')
            ->get();

        $dmB = DmaterialRb::select(
            'acc_name',
            DB::raw('sum(april) as dapril'),
            DB::raw('sum(mei) as dmei'),
            DB::raw('sum(juni) as djuni'),
            DB::raw('sum(juli) as djuli'),
            DB::raw('sum(agustus) as dagustus'),
            DB::raw('sum(september) as dseptember'),
            DB::raw('sum(oktober) as doktober'),
            DB::raw('sum(november) as dnovember'),
            DB::raw('sum(december) as ddecember'),
            DB::raw('sum(januari) as djanuari'),
            DB::raw('sum(februari) as dfebruari'),
            DB::raw('sum(maret) as dmaret')
        )
            ->where('group', 'body')
            ->groupBy('acc_name')
            ->get();

        $dmU = DmaterialRb::select(
            'acc_name',
            DB::raw('sum(april) as dapril'),
            DB::raw('sum(mei) as dmei'),
            DB::raw('sum(juni) as djuni'),
            DB::raw('sum(juli) as djuli'),
            DB::raw('sum(agustus) as dagustus'),
            DB::raw('sum(september) as dseptember'),
            DB::raw('sum(oktober) as doktober'),
            DB::raw('sum(november) as dnovember'),
            DB::raw('sum(december) as ddecember'),
            DB::raw('sum(januari) as djanuari'),
            DB::raw('sum(februari) as dfebruari'),
            DB::raw('sum(maret) as dmaret')
        )
            ->where('group', 'unit')
            ->groupBy('acc_name')
            ->get();

        // return $codes;
        // $ArCode = array();
        // foreach ($codes as $code) {

        //     $cut = substr($code->acc_code, 10, 1);
        //     if ($cut == '_') {
        //         $hasil = substr($code->acc_code, 0, 10);
        //     }
        //     else {
        //         $hasil = substr($code->acc_code, 0, 12);

        //     }
        //     array_push($ArCode, $hasil);
        // }
        // // return $ArCode;
        // $row1 = $this->cekData($ArCode,'5120290101-1');
        // $row2 = $this->cekData($ArCode,'5330990101');


        // if($row2){
        //     return "YESyu";
        // }else{
        //     return "no";
        // }
        // // return $ArCode;
        // // 5120290101-1


        Excel::load('/public/files/AIIA-PNL.xlsx', function ($file) use ($salesB, $salesU, $dmB, $dmU, $master_code, $codes, $codesU) {
            // $part_number    = $part->part_number;
            // $model          = $part->product;
            // $part_name      = $part->part_name;

            $this->draw($file, $codes, $master_code, 20);
            $this->draw($file, $codesU, $master_code, 35);
            //body sales
            $file->setActiveSheetIndex(2)->setCellValue('U6', $salesB[6]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U7', $salesB[5]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U8', $salesB[2]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U9', $salesB[1]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U10', $salesB[3]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U11', $salesB[4]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('U12', $salesB[0]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('V6', $salesB[6]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V7', $salesB[5]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V8', $salesB[2]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V9', $salesB[1]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V10', $salesB[3]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V11', $salesB[4]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('V12', $salesB[0]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('W6', $salesB[6]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W7', $salesB[5]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W8', $salesB[2]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W9', $salesB[1]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W10', $salesB[3]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W11', $salesB[4]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('W12', $salesB[0]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('X6', $salesB[6]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X7', $salesB[5]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X8', $salesB[2]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X9', $salesB[1]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X10', $salesB[3]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X11', $salesB[4]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('X12', $salesB[0]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('Y6', $salesB[6]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y7', $salesB[5]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y8', $salesB[2]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y9', $salesB[1]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y10', $salesB[3]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y11', $salesB[4]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y12', $salesB[0]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Z6', $salesB[6]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z7', $salesB[5]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z8', $salesB[2]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z9', $salesB[1]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z10', $salesB[3]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z11', $salesB[4]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z12', $salesB[0]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AA6', $salesB[6]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA7', $salesB[5]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA8', $salesB[2]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA9', $salesB[1]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA10', $salesB[3]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA11', $salesB[4]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA12', $salesB[0]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AB6', $salesB[6]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB7', $salesB[5]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB8', $salesB[2]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB9', $salesB[1]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB10', $salesB[3]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB11', $salesB[4]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB12', $salesB[0]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AC6', $salesB[6]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC7', $salesB[5]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC8', $salesB[2]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC9', $salesB[1]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC10', $salesB[3]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC11', $salesB[4]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC12', $salesB[0]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AD6', $salesB[6]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD7', $salesB[5]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD8', $salesB[2]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD9', $salesB[1]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD10', $salesB[3]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD11', $salesB[4]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD12', $salesB[0]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AE6', $salesB[6]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE7', $salesB[5]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE8', $salesB[2]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE9', $salesB[1]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE10', $salesB[3]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE11', $salesB[4]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE12', $salesB[0]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AF6', $salesB[6]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF7', $salesB[5]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF8', $salesB[2]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF9', $salesB[1]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF10', $salesB[3]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF11', $salesB[4]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF12', $salesB[0]->smaret);

            // // //unit sales
            $file->setActiveSheetIndex(2)->setCellValue('AJ6', $salesU[6]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ7', $salesU[5]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ8', $salesU[2]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ9', $salesU[1]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ0', $salesU[3]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ11', $salesU[4]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ12', $salesU[0]->sapril);
            $file->setActiveSheetIndex(2)->setCellValue('AK6', $salesU[6]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK7', $salesU[5]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK8', $salesU[2]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK9', $salesU[1]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK10', $salesU[3]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK11', $salesU[4]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AK12', $salesU[0]->smei);
            $file->setActiveSheetIndex(2)->setCellValue('AL6', $salesU[6]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL7', $salesU[5]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL8', $salesU[2]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL9', $salesU[1]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL10', $salesU[3]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL11', $salesU[4]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL12', $salesU[0]->sjuni);
            $file->setActiveSheetIndex(2)->setCellValue('AM6', $salesU[6]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM7', $salesU[5]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM8', $salesU[2]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM9', $salesU[1]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM10', $salesU[3]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM11', $salesU[4]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM12', $salesU[0]->sjuli);
            $file->setActiveSheetIndex(2)->setCellValue('AN6', $salesU[6]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN7', $salesU[5]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN8', $salesU[2]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN9', $salesU[1]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN10', $salesU[3]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN11', $salesU[4]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN12', $salesU[0]->sagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AO6', $salesU[6]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO7', $salesU[5]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO8', $salesU[2]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO9', $salesU[1]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO10', $salesU[3]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO11', $salesU[4]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO12', $salesU[0]->sseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AP6', $salesU[6]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP7', $salesU[5]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP8', $salesU[2]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP9', $salesU[1]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP10', $salesU[3]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP11', $salesU[4]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP12', $salesU[0]->soktober);
            $file->setActiveSheetIndex(2)->setCellValue('AQ6', $salesU[6]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ7', $salesU[5]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ8', $salesU[2]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ9', $salesU[1]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ10', $salesU[3]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ11', $salesU[4]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ12', $salesU[0]->snovember);
            $file->setActiveSheetIndex(2)->setCellValue('AR6', $salesU[6]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR7', $salesU[5]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR8', $salesU[2]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR9', $salesU[1]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR10', $salesU[3]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR11', $salesU[4]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR12', $salesU[0]->sdecember);
            $file->setActiveSheetIndex(2)->setCellValue('AS6', $salesU[6]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS7', $salesU[5]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS8', $salesU[2]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS9', $salesU[1]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS10', $salesU[3]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS11', $salesU[4]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS12', $salesU[0]->sjanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AT6', $salesU[6]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT7', $salesU[5]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT8', $salesU[2]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT9', $salesU[1]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT10', $salesU[3]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT11', $salesU[4]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT12', $salesU[0]->sfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AU6', $salesU[6]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU7', $salesU[5]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU8', $salesU[2]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU9', $salesU[1]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU10', $salesU[3]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU11', $salesU[4]->smaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU12', $salesU[0]->smaret);

            // // //body DM
            $file->setActiveSheetIndex(2)->setCellValue('U14', $dmB[8]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U15', $dmB[4]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U16', $dmB[5]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U17', $dmB[7]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U18', $dmB[6]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U19', $dmB[1]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U20', $dmB[3]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U21', $dmB[2]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('U22', $dmB[0]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('v14', $dmB[8]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v15', $dmB[4]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v16', $dmB[5]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v17', $dmB[7]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v18', $dmB[6]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v19', $dmB[1]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v20', $dmB[3]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v21', $dmB[2]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('v22', $dmB[0]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('W14', $dmB[8]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W15', $dmB[4]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W16', $dmB[5]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W17', $dmB[7]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W18', $dmB[6]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W19', $dmB[1]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W20', $dmB[3]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W21', $dmB[2]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('W22', $dmB[0]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('X14', $dmB[8]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X15', $dmB[4]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X16', $dmB[5]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X17', $dmB[7]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X18', $dmB[6]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X19', $dmB[1]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X20', $dmB[3]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X21', $dmB[2]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('X22', $dmB[0]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('Y14', $dmB[8]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y15', $dmB[4]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y16', $dmB[5]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y17', $dmB[7]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y18', $dmB[6]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y19', $dmB[1]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y20', $dmB[3]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y21', $dmB[2]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Y22', $dmB[0]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('Z14', $dmB[8]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z15', $dmB[4]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z16', $dmB[5]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z17', $dmB[7]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z18', $dmB[6]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z19', $dmB[1]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z20', $dmB[3]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z21', $dmB[2]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('Z22', $dmB[0]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AA14', $dmB[8]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA15', $dmB[4]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA16', $dmB[5]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA17', $dmB[7]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA18', $dmB[6]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA19', $dmB[1]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA20', $dmB[3]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA21', $dmB[2]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AA22', $dmB[0]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AB14', $dmB[8]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB15', $dmB[4]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB16', $dmB[5]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB17', $dmB[7]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB18', $dmB[6]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB19', $dmB[1]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB20', $dmB[3]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB21', $dmB[2]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AB22', $dmB[0]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AC14', $dmB[8]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC15', $dmB[4]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC16', $dmB[5]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC17', $dmB[7]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC18', $dmB[6]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC19', $dmB[1]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC20', $dmB[3]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC21', $dmB[2]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AC22', $dmB[0]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AD14', $dmB[8]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD15', $dmB[4]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD16', $dmB[5]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD17', $dmB[7]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD18', $dmB[6]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD19', $dmB[1]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD20', $dmB[3]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD21', $dmB[2]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AD22', $dmB[0]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AE14', $dmB[8]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE15', $dmB[4]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE16', $dmB[5]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE17', $dmB[7]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE18', $dmB[6]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE19', $dmB[1]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE20', $dmB[3]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE21', $dmB[2]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AE22', $dmB[0]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AF14', $dmB[8]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF15', $dmB[4]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF16', $dmB[5]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF17', $dmB[7]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF18', $dmB[6]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF19', $dmB[1]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF20', $dmB[3]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF21', $dmB[2]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AF22', $dmB[0]->dmaret);

            // // //dm unit
            $file->setActiveSheetIndex(2)->setCellValue('AJ14', $dmU[8]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ15', $dmU[4]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ16', $dmU[5]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ17', $dmU[7]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ18', $dmU[6]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ19', $dmU[1]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ20', $dmU[3]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ21', $dmU[2]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AJ22', $dmU[0]->dapril);
            $file->setActiveSheetIndex(2)->setCellValue('AK14', $dmU[8]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK15', $dmU[4]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK16', $dmU[5]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK17', $dmU[7]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK18', $dmU[6]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK19', $dmU[1]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK20', $dmU[3]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK21', $dmU[2]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AK22', $dmU[0]->dmei);
            $file->setActiveSheetIndex(2)->setCellValue('AL14', $dmU[8]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL15', $dmU[4]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL16', $dmU[5]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL17', $dmU[7]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL18', $dmU[6]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL19', $dmU[1]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL20', $dmU[3]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL21', $dmU[2]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AL22', $dmU[0]->djuni);
            $file->setActiveSheetIndex(2)->setCellValue('AM14', $dmU[8]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM15', $dmU[4]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM16', $dmU[5]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM17', $dmU[7]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM18', $dmU[6]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM19', $dmU[1]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM20', $dmU[3]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM21', $dmU[2]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AM22', $dmU[0]->djuli);
            $file->setActiveSheetIndex(2)->setCellValue('AN14', $dmU[8]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN15', $dmU[4]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN16', $dmU[5]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN17', $dmU[7]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN18', $dmU[6]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN19', $dmU[1]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN20', $dmU[3]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN21', $dmU[2]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AN22', $dmU[0]->dagustus);
            $file->setActiveSheetIndex(2)->setCellValue('AO14', $dmU[8]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO15', $dmU[4]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO16', $dmU[5]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO17', $dmU[7]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO18', $dmU[6]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO19', $dmU[1]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO20', $dmU[3]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO21', $dmU[2]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AO22', $dmU[0]->dseptember);
            $file->setActiveSheetIndex(2)->setCellValue('AP14', $dmU[8]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP15', $dmU[4]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP16', $dmU[5]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP17', $dmU[7]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP18', $dmU[6]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP19', $dmU[1]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP20', $dmU[3]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP21', $dmU[2]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AP22', $dmU[0]->doktober);
            $file->setActiveSheetIndex(2)->setCellValue('AQ14', $dmU[8]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ15', $dmU[4]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ16', $dmU[5]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ17', $dmU[7]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ18', $dmU[6]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ19', $dmU[1]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ20', $dmU[3]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ21', $dmU[2]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AQ22', $dmU[0]->dnovember);
            $file->setActiveSheetIndex(2)->setCellValue('AR14', $dmU[8]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR15', $dmU[4]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR16', $dmU[5]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR17', $dmU[7]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR18', $dmU[6]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR19', $dmU[1]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR20', $dmU[3]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR21', $dmU[2]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AR22', $dmU[0]->ddecember);
            $file->setActiveSheetIndex(2)->setCellValue('AS14', $dmU[8]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS15', $dmU[4]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS16', $dmU[5]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS17', $dmU[7]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS18', $dmU[6]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS19', $dmU[1]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS20', $dmU[3]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS21', $dmU[2]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AS22', $dmU[0]->djanuari);
            $file->setActiveSheetIndex(2)->setCellValue('AT14', $dmU[8]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT15', $dmU[4]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT16', $dmU[5]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT17', $dmU[7]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT18', $dmU[6]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT19', $dmU[1]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT20', $dmU[3]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT21', $dmU[2]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AT22', $dmU[0]->dfebruari);
            $file->setActiveSheetIndex(2)->setCellValue('AU14', $dmU[8]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU15', $dmU[4]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU16', $dmU[5]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU17', $dmU[7]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU18', $dmU[6]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU19', $dmU[1]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU20', $dmU[3]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU21', $dmU[2]->dmaret);
            $file->setActiveSheetIndex(2)->setCellValue('AU22', $dmU[0]->dmaret);

            // for ($a=0; $a < ; $a++) {
            //     // code...
            // }



        })->setFilename('result')->export('xlsx');
    }

    /**
     * generate csv file base on delimiter
     * @param  string $file
     * @return collection $data
     */
    private function getCsvFile($file)
    {
        $delimiters = [",", ";", "\t"];
        // $ValueBinder = new ImportBinder();

        Config::set('excel.csv.delimiter', ';');
        $datas = Excel::load($file, function ($reader) {
        })->get();

        dd($datas);
        // Excel::setValueBinder($ValueBinder)->

        return $datas;
    }

    private function getCsvFile2($file)
    {
        $delimiters = [",", ";", "\t"];

        // foreach ($delimiters as $delimiter) {


        // }
        Config::set('excel.csv.delimiter', ';');
        $datas = Excel::load($file, function ($reader) {
            $reader->select(array('budget_no', 'line_or_dept', 'profit_center', 'profit_center_code', 'cost_center', 'type', 'project_name', 'import_domestic', 'items_name', 'equipment', 'qty', 'curency', 'original_price', 'exchange_rate', 'price', 'sop', 'first_down_payment_term', 'first_down_payment_amount', 'final_payment_term', 'final_payment_amount', 'owner_asset', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'))->get();
        })->get();

        return $datas;
    }

    private function getCsvFile3($file)
    {
        $delimiters = [",", ";", "\t"];

        // foreach ($delimiters as $delimiter) {


        // }
        Config::set('excel.csv.delimiter', ';');
        $datas = Excel::load($file, function ($reader) {

            $reader->select(array('budget_no', 'group', 'line_or_dept', 'profit_center', 'profit_center_code', 'cost_center', 'account_code', 'project_name', 'equipment_name', 'importdomestic', 'qty', 'curr', 'price_per_qty', 'exchange_rate', 'budget_before_cr', 'cr', 'budget_after_cr', 'po', 'gr', 'sop', 'first_down_payment_term', 'first_down_payment_amount', 'final_payment_term', 'final_payment_amount', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'checking'))->get();
        })->get();

        return $datas;
    }

    /**
     * Parse file type xlsx
     *
     * @param Request $file
     * @return void
     */
    protected function parseXlsx($file, $name, $sheetNamesIndex = 0)
    {
        /** Jika bukan format csv */
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $loadedSheetNames[] = $spreadsheet->getSheetNames()[$sheetNamesIndex];

        $writer = new Csv($spreadsheet);

        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            $writer->setSheetIndex($sheetIndex);
            $writer->save(public_path('storage/uploads/' . $name . '.csv'));
        }

        /** Buat ulang file csv */
        return public_path('storage/uploads/' . $name . '.csv');
    }

    /**
     * Remove file after upload
     *
     * @return void
     */
    protected function deleteFiles($name): void
    {
        // unlink(public_path('storage/uploads/' . $name));
        unlink(public_path('storage/uploads/' . $name . '.csv'));
    }



    public function exportData1(Request $request)
    {

        ini_set('max_execution_time', 0);
        ob_start();

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load(public_path('files/TemplateExport.xlsx'));
        // Set document properties


        // dd($data_master_expense);
        $spreadsheet->getProperties()->setCreator('Aiia')
            ->setLastModifiedBy('Aiia')
            ->setTitle('Office 2021 XLSX Aiia Document')
            ->setSubject('Office 2021 XLSX Aiia Document')
            ->setDescription('Office 2021 XLSX Aiia Document.')
            ->setKeywords('Office 2021 XLSX Aiia Document')
            ->setCategory('Office 2021 XLSX Aiia Document');


        $master_code = MasterCode::all();

        $sales_code = SalesRb::select('acc_code', 'acc_name')
            ->groupBy('dept','acc_code')
            ->get();

        $material_code = DmaterialRb::select('acc_code', 'acc_name')
            ->groupBy('dept','acc_code')
            ->get();
        $expense_code = ExpenseRb::select('acc_code')
            ->groupBy('dept','acc_code')
            ->get();


        $sheet1 = $spreadsheet->getSheet(0);

        $sc = 6;
        $sales_code = array();
        $a = 0;
        foreach ($sheet1->getRowIterator() as $row) {

            $acc_group = $sheet1->getCell("A$sc")->getValue();
            $acc_code = $sheet1->getCell("B$sc")->getValue();
            $acc_name = $sheet1->getCell("C$sc")->getValue();
            $sales_code[$a]['acc_group'] = $acc_group;
            $sales_code[$a]['acc_code'] = $acc_code;
            $sales_code[$a]['acc_name'] = $acc_name;

            $sc++;
            $a++;
            if ($a === 8) {
                break;
            }
        }
        // dd($sales_code);
        $mc = 14;
        $material_code = array();
        $b = 0;
        foreach ($sheet1->getRowIterator() as $row) {

            $acc_group = $sheet1->getCell("A$mc")->getValue();
            $acc_code = $sheet1->getCell("B$mc")->getValue();
            $acc_name = $sheet1->getCell("C$mc")->getValue();
            $material_code[$b]['acc_group'] = $acc_group;
            $material_code[$b]['acc_code'] = $acc_code;
            $material_code[$b]['acc_name'] = $acc_name;

            $mc++;
            $b++;
            if ($b === 10) {
                break;
            }
        }

        $ec = 24;
        $expense_code = array();
        $c = 0;
        foreach ($sheet1->getRowIterator() as $row) {

            $acc_group = $sheet1->getCell("A$ec")->getValue();
            $acc_code = $sheet1->getCell("B$ec")->getValue();
            $acc_name = $sheet1->getCell("C$ec")->getValue();
            $expense_code[$c]['acc_group'] = $acc_group;
            $expense_code[$c]['acc_code'] = $acc_code;
            $expense_code[$c]['acc_name'] = ($acc_name == "                      Adjustment") ? str_replace(" ", "", $acc_name) : $acc_name;

            $ec++;
            $c++;
            if ($c === 364) {
                break;
            }
        }
        // dd($expense_code);

        // return $codesU;
        // sales body
        $sales_body = SalesRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('acc_name')
            ->get();
        // sales unit
        $sales_unit = SalesRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'unit')
            ->groupBy('acc_name')
            ->get();

        // sales electrik
        $sales_electrik = SalesRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'Electric')
            ->groupBy('acc_name')
            ->get();

        // sales all/company basis
        $sales_company_basis = SalesRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->groupBy('acc_name')
            ->get();


        // material
        $material_body = DmaterialRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('acc_name')
            ->get();
        $material_unit = DmaterialRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'unit')
            ->groupBy('acc_name')
            ->get();
        $material_electrik = DmaterialRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'Electric')
            ->groupBy('acc_name')
            ->get();
        $material_company_basis = DmaterialRb::select(
            'acc_name',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->groupBy('acc_name')
            ->get();

        // expense
        $expense_body = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('dept','acc_code')
            ->get();
        $expense_unit = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'unit')
            ->groupBy('dept','acc_code')
            ->get();
        $expense_electrik = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'electric')
            ->groupBy('dept','acc_code')
            ->get();
        $expense_company_basis = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->groupBy('dept','acc_code')
            ->get();



        // dd(json_encode($sales_company_basis));
        //   echo json_encode($sales_company_basis);
        //   die;
        // sales

        // $sheet = $spreadsheet->getActiveSheet();
        if (count((array) $sales_code) > 0) {

            $sales_code_count = count($sales_code) - 1;
            // dd($sales_code_count);
            $i = 0;
            $x = 6;
            $dsales = array();
            foreach ($sales_code as $key => $row) {
                // dd($row);
                if ($row['acc_code'] != "SALES") {
                    $dsales[$i]['acc_group'] = $row['acc_group'];
                    $dsales[$i]['acc_name'] = $row['acc_name'];
                    $dsales[$i]['acc_code'] = $row['acc_code'];
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . $x, $row['acc_group'])
                        ->setCellValue('B' . $x, $row['acc_code'])
                        ->setCellValue('C' . $x, $row['acc_name']);

                    $i++;
                    $x++;
                }
            }


            // dd($dsales);


            $total_all_body = 0;
            $t_april_b = 0;
            $t_mei_b = 0;
            $t_juni_b = 0;
            $t_juli_b = 0;
            $t_agustus_b = 0;
            $t_september_b = 0;
            $t_oktober_b = 0;
            $t_november_b = 0;
            $t_desember_b = 0;
            $t_januari_b = 0;
            $t_februari_b = 0;
            $t_maret_b = 0;
            $persen_arr_b = array();
            $b = 0;

            // dd($sales_body);
            foreach ($sales_body as $key => $value) {

                $key = array_search($value->acc_code, array_column($dsales, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                if ($key != false) {
                    $keyy = $key + 6;
                    // dd($keyy .$value);

                    $total_body = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('S' . $keyy, $total_body)
                        ->setCellValue('U' . $keyy, $value->sapril)
                        ->setCellValue('V' . $keyy, $value->smei)
                        ->setCellValue('W' . $keyy, $value->sjuni)
                        ->setCellValue('X' . $keyy, $value->sjuli)
                        ->setCellValue('Y' . $keyy, $value->sagustus)
                        ->setCellValue('Z' . $keyy, $value->sseptember)
                        ->setCellValue('AA' . $keyy, $value->soktober)
                        ->setCellValue('AB' . $keyy, $value->snovember)
                        ->setCellValue('AC' . $keyy, $value->sdesember)
                        ->setCellValue('AD' . $keyy, $value->sjanuari)
                        ->setCellValue('AE' . $keyy, $value->sfebruari)
                        ->setCellValue('AF' . $keyy, $value->smaret);

                    $total_all_body = $total_all_body + $total_body;

                    $t_april_b = $t_april_b + $value->sapril;
                    $t_mei_b = $t_mei_b + $value->smei;
                    $t_juni_b = $t_juni_b + $value->sjuni;
                    $t_juli_b = $t_juli_b + $value->sjuli;
                    $t_agustus_b = $t_agustus_b + $value->sagustus;
                    $t_september_b = $t_september_b + $value->sseptember;
                    $t_oktober_b = $t_oktober_b + $value->soktober;
                    $t_november_b = $t_november_b + $value->snovember;
                    $t_desember_b = $t_desember_b + $value->sdesember;
                    $t_januari_b = $t_januari_b + $value->sjanuari;
                    $t_februari_b = $t_februari_b + $value->sfebruari;
                    $t_maret_b = $t_maret_b + $value->smaret;

                    $persen_arr_b[$b]['acc_name'] = $value->acc_name;
                    $persen_arr_b[$b]['acc_code'] = $value->acc_code;
                    $persen_arr_b[$b]['total_body'] = $total_body;

                    $b++;
                }
            }

            // dd($persen_arr_b);

            // foreach ($persen_arr_b as $val) {
            //     // dd($val);
            //     $key = array_search($val['acc_code'], array_column($dsales, 'acc_code'));
            //     $keyy = $key + 6;
            //     $total_body_persen = $val['total_body'];
            //     $percent = $total_body_persen / $total_all_body;
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('T' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('T')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('R' .  ($sales_code_count + 6), $total_all_body)
            //     ->setCellValue('S' .  ($sales_code_count + 6), ($total_all_body > 0) ? 100 : '')
            //     ->setCellValue('T' .  ($sales_code_count + 6), $t_april_b)
            //     ->setCellValue('U' .  ($sales_code_count + 6), $t_mei_b)
            //     ->setCellValue('V' .  ($sales_code_count + 6), $t_juni_b)
            //     ->setCellValue('W' .  ($sales_code_count + 6), $t_juli_b)
            //     ->setCellValue('X' .  ($sales_code_count + 6), $t_agustus_b)
            //     ->setCellValue('Y' .  ($sales_code_count + 6), $t_september_b)
            //     ->setCellValue('Z' .  ($sales_code_count + 6), $t_oktober_b)
            //     ->setCellValue('AA' .  ($sales_code_count + 6), $t_november_b)
            //     ->setCellValue('AB' .  ($sales_code_count + 6), $t_desember_b)
            //     ->setCellValue('AC' .  ($sales_code_count + 6), $t_januari_b)
            //     ->setCellValue('AD' .  ($sales_code_count + 6), $t_februari_b)
            //     ->setCellValue('AE' .  ($sales_code_count + 6), $t_maret_b);



            $total_all_unit = 0;
            $t_april_u = 0;
            $t_mei_u = 0;
            $t_juni_u = 0;
            $t_juli_u = 0;
            $t_agustus_u = 0;
            $t_september_u = 0;
            $t_oktober_u = 0;
            $t_november_u = 0;
            $t_desember_u = 0;
            $t_januari_u = 0;
            $t_februari_u = 0;
            $t_maret_u = 0;
            $persen_arr_u = array();
            $u = 0;
            foreach ($sales_unit as $key => $value) {
                $key = array_search($value->acc_code, array_column($dsales, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + 6;
                $total_unit = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AH' . $keyy, $total_unit)
                    ->setCellValue('AJ' . $keyy, $value->sapril)
                    ->setCellValue('AK' . $keyy, $value->smei)
                    ->setCellValue('AL' . $keyy, $value->sjuni)
                    ->setCellValue('AM' . $keyy, $value->sjuli)
                    ->setCellValue('AN' . $keyy, $value->sagustus)
                    ->setCellValue('AO' . $keyy, $value->sseptember)
                    ->setCellValue('AP' . $keyy, $value->soktober)
                    ->setCellValue('AQ' . $keyy, $value->snovember)
                    ->setCellValue('AR' . $keyy, $value->sdesember)
                    ->setCellValue('AS' . $keyy, $value->sjanuari)
                    ->setCellValue('AT' . $keyy, $value->sfebruari)
                    ->setCellValue('AU' . $keyy, $value->smaret);

                $total_all_unit = $total_all_unit + $total_unit;

                $t_april_u = $t_april_u + $value->sapril;
                $t_mei_u = $t_mei_u + $value->smei;
                $t_juni_u = $t_juni_u + $value->sjuni;
                $t_juli_u = $t_juli_u + $value->sjuli;
                $t_agustus_u = $t_agustus_u + $value->sagustus;
                $t_september_u = $t_september_u + $value->sseptember;
                $t_oktober_u = $t_oktober_u + $value->soktober;
                $t_november_u = $t_november_u + $value->snovember;
                $t_desember_u = $t_desember_u + $value->sdesember;
                $t_januari_u = $t_januari_u + $value->sjanuari;
                $t_februari_u = $t_februari_u + $value->sfebruari;
                $t_maret_u = $t_maret_u + $value->smaret;

                $persen_arr_u[$u]['acc_name'] = $value->acc_name;
                $persen_arr_u[$u]['acc_code'] = $value->acc_code;
                $persen_arr_u[$u]['total_unit'] = $total_unit;
                $u++;
            }
            // foreach ($persen_arr_u as $val) {
            //     // dd($val['acc_name']);
            //     $key = array_search($val['acc_code'], array_column($dsales, 'acc_code'));
            //     $keyy = $key + 6;
            //     $total_unit_persen = $val['total_unit'];
            //     $percent = $total_unit_persen / $total_all_unit;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('AH' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('AH')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('AG' .  ($sales_code_count + 6), $total_all_unit)
            //     ->setCellValue('AH' .  ($sales_code_count + 6), ($total_all_unit > 0) ? 100 : '')
            //     ->setCellValue('AI' .  ($sales_code_count + 6), $t_april_u)
            //     ->setCellValue('AJ' .  ($sales_code_count + 6), $t_mei_u)
            //     ->setCellValue('AK' .  ($sales_code_count + 6), $t_juni_u)
            //     ->setCellValue('AL' .  ($sales_code_count + 6), $t_juli_u)
            //     ->setCellValue('AM' .  ($sales_code_count + 6), $t_agustus_u)
            //     ->setCellValue('AN' .  ($sales_code_count + 6), $t_september_u)
            //     ->setCellValue('AO' .  ($sales_code_count + 6), $t_oktober_u)
            //     ->setCellValue('AP' .  ($sales_code_count + 6), $t_november_u)
            //     ->setCellValue('AQ' .  ($sales_code_count + 6), $t_desember_u)
            //     ->setCellValue('AR' .  ($sales_code_count + 6), $t_januari_u)
            //     ->setCellValue('AS' .  ($sales_code_count + 6), $t_februari_u)
            //     ->setCellValue('AT' .  ($sales_code_count + 6), $t_maret_u);



            $total_all_electrik = 0;
            $t_april_e = 0;
            $t_mei_e = 0;
            $t_juni_e = 0;
            $t_juli_e = 0;
            $t_agustus_e = 0;
            $t_september_e = 0;
            $t_oktober_e = 0;
            $t_november_e = 0;
            $t_desember_e = 0;
            $t_januari_e = 0;
            $t_februari_e = 0;
            $t_maret_e = 0;
            $persen_arr_e = array();
            $e = 0;
            foreach ($sales_electrik as $key => $value) {
                $key = array_search($value->acc_code, array_column($dsales, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + 6;
                $total_electrik = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AW' . $keyy, $total_electrik)
                    ->setCellValue('AY' . $keyy, $value->sapril)
                    ->setCellValue('AZ' . $keyy, $value->smei)
                    ->setCellValue('AA' . $keyy, $value->sjuni)
                    ->setCellValue('BB' . $keyy, $value->sjuli)
                    ->setCellValue('BC' . $keyy, $value->sagustus)
                    ->setCellValue('BD' . $keyy, $value->sseptember)
                    ->setCellValue('BE' . $keyy, $value->soktober)
                    ->setCellValue('BF' . $keyy, $value->snovember)
                    ->setCellValue('BG' . $keyy, $value->sdesember)
                    ->setCellValue('BH' . $keyy, $value->sjanuari)
                    ->setCellValue('BI' . $keyy, $value->sfebruari)
                    ->setCellValue('BJ' . $keyy, $value->smaret);

                $total_all_electrik = $total_all_electrik + $total_electrik;

                $t_april_e = $t_april_e + $value->sapril;
                $t_mei_e = $t_mei_e + $value->smei;
                $t_juni_e = $t_juni_e + $value->sjuni;
                $t_juli_e = $t_juli_e + $value->sjuli;
                $t_agustus_e = $t_agustus_e + $value->sagustus;
                $t_september_e = $t_september_e + $value->sseptember;
                $t_oktober_e = $t_oktober_e + $value->soktober;
                $t_november_e = $t_november_e + $value->snovember;
                $t_desember_e = $t_desember_e + $value->sdesember;
                $t_januari_e = $t_januari_e + $value->sjanuari;
                $t_februari_e = $t_februari_e + $value->sfebruari;
                $t_maret_e = $t_maret_e + $value->smaret;

                $persen_arr_e[$e]['acc_name'] = $value->acc_name;
                $persen_arr_e[$e]['acc_code'] = $value->acc_code;
                $persen_arr_e[$e]['total_electrik'] = $total_electrik;
                $e++;
            }

            // foreach ($persen_arr_e as $val) {
            //     // dd($val['acc_name']);
            //     $key = array_search($val['acc_code'], array_column($dsales, 'acc_code'));
            //     $keyy = $key + 6;
            //     $total_electrik_persen = $val['total_electrik'];
            //     $percent = $total_electrik_persen / $total_all_electrik;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('AW' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('AW')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('AV' .  ($sales_code_count + 6), $total_all_electrik)
            //     ->setCellValue('AW' .  ($sales_code_count + 6), ($total_all_electrik > 0) ? 100 : '')
            //     ->setCellValue('AX' .  ($sales_code_count + 6), $t_april_e)
            //     ->setCellValue('AY' .  ($sales_code_count + 6), $t_mei_e)
            //     ->setCellValue('AZ' .  ($sales_code_count + 6), $t_juni_e)
            //     ->setCellValue('BA' .  ($sales_code_count + 6), $t_juli_e)
            //     ->setCellValue('BB' .  ($sales_code_count + 6), $t_agustus_e)
            //     ->setCellValue('BC' .  ($sales_code_count + 6), $t_september_e)
            //     ->setCellValue('BD' .  ($sales_code_count + 6), $t_oktober_e)
            //     ->setCellValue('BE' .  ($sales_code_count + 6), $t_november_e)
            //     ->setCellValue('BF' .  ($sales_code_count + 6), $t_desember_e)
            //     ->setCellValue('BG' .  ($sales_code_count + 6), $t_januari_e)
            //     ->setCellValue('BH' .  ($sales_code_count + 6), $t_februari_e)
            //     ->setCellValue('BI' .  ($sales_code_count + 6), $t_maret_e);




            // company basis
            $total_all_cb = 0;
            $t_april_cb = 0;
            $t_mei_cb = 0;
            $t_juni_cb = 0;
            $t_juli_cb = 0;
            $t_agustus_cb = 0;
            $t_september_cb = 0;
            $t_oktober_cb = 0;
            $t_november_cb = 0;
            $t_desember_cb = 0;
            $t_januari_cb = 0;
            $t_februari_cb = 0;
            $t_maret_cb = 0;
            $persen_arr_cb = array();
            $cb = 0;
            foreach ($sales_company_basis as $key => $value) {
                $key = array_search($value->acc_code, array_column($dsales, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + 6;
                $total_cb = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('D' . $keyy, $total_cb)
                    ->setCellValue('F' . $keyy, $value->sapril)
                    ->setCellValue('G' . $keyy, $value->smei)
                    ->setCellValue('H' . $keyy, $value->sjuni)
                    ->setCellValue('I' . $keyy, $value->sjuli)
                    ->setCellValue('J' . $keyy, $value->sagustus)
                    ->setCellValue('K' . $keyy, $value->sseptember)
                    ->setCellValue('L' . $keyy, $value->soktober)
                    ->setCellValue('M' . $keyy, $value->snovember)
                    ->setCellValue('N' . $keyy, $value->sdesember)
                    ->setCellValue('O' . $keyy, $value->sjanuari)
                    ->setCellValue('P' . $keyy, $value->sfebruari)
                    ->setCellValue('Q' . $keyy, $value->smaret);

                $total_all_cb = $total_all_cb + $total_cb;

                $t_april_cb = $t_april_cb + $value->sapril;
                $t_mei_cb = $t_mei_cb + $value->smei;
                $t_juni_cb = $t_juni_cb + $value->sjuni;
                $t_juli_cb = $t_juli_cb + $value->sjuli;
                $t_agustus_cb = $t_agustus_cb + $value->sagustus;
                $t_september_cb = $t_september_cb + $value->sseptember;
                $t_oktober_cb = $t_oktober_cb + $value->soktober;
                $t_november_cb = $t_november_cb + $value->snovember;
                $t_desember_cb = $t_desember_cb + $value->sdesember;
                $t_januari_cb = $t_januari_cb + $value->sjanuari;
                $t_februari_cb = $t_februari_cb + $value->sfebruari;
                $t_maret_cb = $t_maret_cb + $value->smaret;

                $persen_arr_cb[$cb]['acc_name'] = $value->acc_name;
                $persen_arr_cb[$cb]['acc_code'] = $value->acc_code;
                $persen_arr_cb[$cb]['total_cb'] = $total_cb;
                $cb++;
            }
            // dd($persen_arr_cb);

            // dd($persen_arr_cb);
            // foreach ($persen_arr_cb as $val) {

            //     $key = array_search($val['acc_code'], array_column($dsales, 'acc_code'));
            //     // dd($key);
            //     $keyy = $key + 6;
            //     $total_cb_persen = $val['total_cb'];
            //     $percent = $total_cb_persen / $total_all_cb;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     // dd($percent_friendly);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('D' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('C' .  ($sales_code_count + 6), $total_all_cb)
            //     ->setCellValue('D' .  ($sales_code_count + 6), ($total_all_cb > 0) ? 100 : '')
            //     ->setCellValue('E' .  ($sales_code_count + 6), $t_april_cb)
            //     ->setCellValue('F' .  ($sales_code_count + 6), $t_mei_cb)
            //     ->setCellValue('G' .  ($sales_code_count + 6), $t_juni_cb)
            //     ->setCellValue('H' .  ($sales_code_count + 6), $t_juli_cb)
            //     ->setCellValue('I' .  ($sales_code_count + 6), $t_agustus_cb)
            //     ->setCellValue('J' .  ($sales_code_count + 6), $t_september_cb)
            //     ->setCellValue('K' .  ($sales_code_count + 6), $t_oktober_cb)
            //     ->setCellValue('L' .  ($sales_code_count + 6), $t_november_cb)
            //     ->setCellValue('M' .  ($sales_code_count + 6), $t_desember_cb)
            //     ->setCellValue('N' .  ($sales_code_count + 6), $t_januari_cb)
            //     ->setCellValue('O' .  ($sales_code_count + 6), $t_februari_cb)
            //     ->setCellValue('P' .  ($sales_code_count + 6), $t_maret_cb);
        }


        // material
        // dd('saa');
        if (count((array) $material_code) > 0) {
            $material_code_count = count($material_code) - 1;
            $i = 0;
            $x = 6 + $sales_code_count + 1;
            $dmate = array();
            foreach ($material_code as $key => $row) {
                if ($row['acc_code'] != "MATERIAL") {
                    $dmate[$i]['acc_group'] = $row['acc_group'];
                    $dmate[$i]['acc_code'] = $row['acc_code'];
                    $dmate[$i]['acc_name'] = $row['acc_name'];
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . $x, $row['acc_group'])
                        ->setCellValue('B' . $x, $row['acc_code'])
                        ->setCellValue('C' . $x, $row['acc_name']);

                    $i++;
                    $x++;
                }
            }
            $count_sales_code_row = 6 + $sales_code_count + 1;




            $total_all_body = 0;
            $t_april_b = 0;
            $t_mei_b = 0;
            $t_juni_b = 0;
            $t_juli_b = 0;
            $t_agustus_b = 0;
            $t_september_b = 0;
            $t_oktober_b = 0;
            $t_november_b = 0;
            $t_desember_b = 0;
            $t_januari_b = 0;
            $t_februari_b = 0;
            $t_maret_b = 0;
            $persen_arr_b = array();
            $b = 0;
            foreach ($material_body as $key => $value) {
                // dd($value);
                $key = array_search($value->acc_code, array_column($dmate, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + $count_sales_code_row;

                $total_body = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('S' . $keyy, $total_body)
                    ->setCellValue('U' . $keyy, $value->sapril)
                    ->setCellValue('V' . $keyy, $value->smei)
                    ->setCellValue('W' . $keyy, $value->sjuni)
                    ->setCellValue('X' . $keyy, $value->sjuli)
                    ->setCellValue('Y' . $keyy, $value->sagustus)
                    ->setCellValue('Z' . $keyy, $value->sseptember)
                    ->setCellValue('AA' . $keyy, $value->soktober)
                    ->setCellValue('AB' . $keyy, $value->snovember)
                    ->setCellValue('AC' . $keyy, $value->sdesember)
                    ->setCellValue('AD' . $keyy, $value->sjanuari)
                    ->setCellValue('AE' . $keyy, $value->sfebruari)
                    ->setCellValue('AF' . $keyy, $value->smaret);

                $total_all_body = $total_all_body + $total_body;

                $t_april_b = $t_april_b + $value->sapril;
                $t_mei_b = $t_mei_b + $value->smei;
                $t_juni_b = $t_juni_b + $value->sjuni;
                $t_juli_b = $t_juli_b + $value->sjuli;
                $t_agustus_b = $t_agustus_b + $value->sagustus;
                $t_september_b = $t_september_b + $value->sseptember;
                $t_oktober_b = $t_oktober_b + $value->soktober;
                $t_november_b = $t_november_b + $value->snovember;
                $t_desember_b = $t_desember_b + $value->sdesember;
                $t_januari_b = $t_januari_b + $value->sjanuari;
                $t_februari_b = $t_februari_b + $value->sfebruari;
                $t_maret_b = $t_maret_b + $value->smaret;

                $persen_arr_b[$b]['acc_code'] = $value->acc_code;
                $persen_arr_b[$b]['acc_name'] = $value->acc_name;
                $persen_arr_b[$b]['total_body'] = $total_body;

                $b++;
            }

            // dd($persen_arr_b);

            // foreach ($persen_arr_b as $val) {
            //     // dd($val);
            //     $key = array_search($val['acc_name'], array_column($dmate, 'acc_name'));
            //     $keyy = $key + $count_sales_code_row;
            //     $total_body_persen = $val['total_body'];
            //     $percent = $total_body_persen / $total_all_body;
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('S' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('S')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('R' .  ($count_sales_code_row + $material_code_count), $total_all_body)
            //     ->setCellValue('S' .  ($count_sales_code_row + $material_code_count), ($total_all_body > 0) ? 100 : '')
            //     ->setCellValue('T' .  ($count_sales_code_row + $material_code_count), $t_april_b)
            //     ->setCellValue('U' .  ($count_sales_code_row + $material_code_count), $t_mei_b)
            //     ->setCellValue('V' .  ($count_sales_code_row + $material_code_count), $t_juni_b)
            //     ->setCellValue('W' .  ($count_sales_code_row + $material_code_count), $t_juli_b)
            //     ->setCellValue('X' .  ($count_sales_code_row + $material_code_count), $t_agustus_b)
            //     ->setCellValue('Y' .  ($count_sales_code_row + $material_code_count), $t_september_b)
            //     ->setCellValue('Z' .  ($count_sales_code_row + $material_code_count), $t_oktober_b)
            //     ->setCellValue('AA' .  ($count_sales_code_row + $material_code_count), $t_november_b)
            //     ->setCellValue('AB' .  ($count_sales_code_row + $material_code_count), $t_desember_b)
            //     ->setCellValue('AC' .  ($count_sales_code_row + $material_code_count), $t_januari_b)
            //     ->setCellValue('AD' .  ($count_sales_code_row + $material_code_count), $t_februari_b)
            //     ->setCellValue('AE' .  ($count_sales_code_row + $material_code_count), $t_maret_b);



            $total_all_unit = 0;
            $t_april_u = 0;
            $t_mei_u = 0;
            $t_juni_u = 0;
            $t_juli_u = 0;
            $t_agustus_u = 0;
            $t_september_u = 0;
            $t_oktober_u = 0;
            $t_november_u = 0;
            $t_desember_u = 0;
            $t_januari_u = 0;
            $t_februari_u = 0;
            $t_maret_u = 0;
            $persen_arr_u = array();
            $u = 0;
            foreach ($material_unit as $key => $value) {
                $key = array_search($value->acc_code, array_column($dmate, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + $count_sales_code_row;
                $total_unit = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AH' . $keyy, $total_unit)
                    ->setCellValue('AJ' . $keyy, $value->sapril)
                    ->setCellValue('AK' . $keyy, $value->smei)
                    ->setCellValue('AL' . $keyy, $value->sjuni)
                    ->setCellValue('AM' . $keyy, $value->sjuli)
                    ->setCellValue('AN' . $keyy, $value->sagustus)
                    ->setCellValue('AO' . $keyy, $value->sseptember)
                    ->setCellValue('AP' . $keyy, $value->soktober)
                    ->setCellValue('AQ' . $keyy, $value->snovember)
                    ->setCellValue('AR' . $keyy, $value->sdesember)
                    ->setCellValue('AS' . $keyy, $value->sjanuari)
                    ->setCellValue('AT' . $keyy, $value->sfebruari)
                    ->setCellValue('AU' . $keyy, $value->smaret);

                $total_all_unit = $total_all_unit + $total_unit;

                $t_april_u = $t_april_u + $value->sapril;
                $t_mei_u = $t_mei_u + $value->smei;
                $t_juni_u = $t_juni_u + $value->sjuni;
                $t_juli_u = $t_juli_u + $value->sjuli;
                $t_agustus_u = $t_agustus_u + $value->sagustus;
                $t_september_u = $t_september_u + $value->sseptember;
                $t_oktober_u = $t_oktober_u + $value->soktober;
                $t_november_u = $t_november_u + $value->snovember;
                $t_desember_u = $t_desember_u + $value->sdesember;
                $t_januari_u = $t_januari_u + $value->sjanuari;
                $t_februari_u = $t_februari_u + $value->sfebruari;
                $t_maret_u = $t_maret_u + $value->smaret;

                $persen_arr_u[$u]['acc_code'] = $value->acc_code;
                $persen_arr_u[$u]['acc_name'] = $value->acc_name;
                $persen_arr_u[$u]['total_unit'] = $total_unit;
                $u++;
            }
            // foreach ($persen_arr_u as $val) {
            //     // dd($val['acc_name']);
            //     $key = array_search($val['acc_name'], array_column($dmate, 'acc_name'));
            //     $keyy = $key + $count_sales_code_row;
            //     $total_unit_persen = $val['total_unit'];
            //     $percent = $total_unit_persen / $total_all_unit;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('AH' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('AH')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('AG' .  ($count_sales_code_row + $material_code_count), $total_all_unit)
            //     ->setCellValue('AH' .  ($count_sales_code_row + $material_code_count), ($total_all_unit > 0) ? 100 : '')
            //     ->setCellValue('AI' .  ($count_sales_code_row + $material_code_count), $t_april_u)
            //     ->setCellValue('AJ' .  ($count_sales_code_row + $material_code_count), $t_mei_u)
            //     ->setCellValue('AK' .  ($count_sales_code_row + $material_code_count), $t_juni_u)
            //     ->setCellValue('AL' .  ($count_sales_code_row + $material_code_count), $t_juli_u)
            //     ->setCellValue('AM' .  ($count_sales_code_row + $material_code_count), $t_agustus_u)
            //     ->setCellValue('AN' .  ($count_sales_code_row + $material_code_count), $t_september_u)
            //     ->setCellValue('AO' .  ($count_sales_code_row + $material_code_count), $t_oktober_u)
            //     ->setCellValue('AP' .  ($count_sales_code_row + $material_code_count), $t_november_u)
            //     ->setCellValue('AQ' .  ($count_sales_code_row + $material_code_count), $t_desember_u)
            //     ->setCellValue('AR' .  ($count_sales_code_row + $material_code_count), $t_januari_u)
            //     ->setCellValue('AS' .  ($count_sales_code_row + $material_code_count), $t_februari_u)
            //     ->setCellValue('AT' .  ($count_sales_code_row + $material_code_count), $t_maret_u);





            $total_all_electrik = 0;
            $t_april_e = 0;
            $t_mei_e = 0;
            $t_juni_e = 0;
            $t_juli_e = 0;
            $t_agustus_e = 0;
            $t_september_e = 0;
            $t_oktober_e = 0;
            $t_november_e = 0;
            $t_desember_e = 0;
            $t_januari_e = 0;
            $t_februari_e = 0;
            $t_maret_e = 0;
            $persen_arr_e = array();
            $e = 0;
            foreach ($material_electrik as $key => $value) {
                $key = array_search($value->acc_code, array_column($dmate, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + $count_sales_code_row;
                $total_electrik = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AW' . $keyy, $total_electrik)
                    ->setCellValue('AY' . $keyy, $value->sapril)
                    ->setCellValue('AZ' . $keyy, $value->smei)
                    ->setCellValue('BA' . $keyy, $value->sjuni)
                    ->setCellValue('BB' . $keyy, $value->sjuli)
                    ->setCellValue('BC' . $keyy, $value->sagustus)
                    ->setCellValue('BD' . $keyy, $value->sseptember)
                    ->setCellValue('BE' . $keyy, $value->soktober)
                    ->setCellValue('BF' . $keyy, $value->snovember)
                    ->setCellValue('BG' . $keyy, $value->sdesember)
                    ->setCellValue('BH' . $keyy, $value->sjanuari)
                    ->setCellValue('BI' . $keyy, $value->sfebruari)
                    ->setCellValue('BJ' . $keyy, $value->smaret);

                $total_all_electrik = $total_all_electrik + $total_electrik;

                $t_april_e = $t_april_e + $value->sapril;
                $t_mei_e = $t_mei_e + $value->smei;
                $t_juni_e = $t_juni_e + $value->sjuni;
                $t_juli_e = $t_juli_e + $value->sjuli;
                $t_agustus_e = $t_agustus_e + $value->sagustus;
                $t_september_e = $t_september_e + $value->sseptember;
                $t_oktober_e = $t_oktober_e + $value->soktober;
                $t_november_e = $t_november_e + $value->snovember;
                $t_desember_e = $t_desember_e + $value->sdesember;
                $t_januari_e = $t_januari_e + $value->sjanuari;
                $t_februari_e = $t_februari_e + $value->sfebruari;
                $t_maret_e = $t_maret_e + $value->smaret;

                $persen_arr_e[$e]['acc_code'] = $value->acc_code;
                $persen_arr_e[$e]['acc_name'] = $value->acc_name;
                $persen_arr_e[$e]['total_electrik'] = $total_electrik;
                $e++;
            }

            // foreach ($persen_arr_e as $val) {
            //     // dd($val['acc_name']);
            //     $key = array_search($val['acc_name'], array_column($dmate, 'acc_name'));
            //     $keyy = $key + $count_sales_code_row;
            //     $total_electrik_persen = $val['total_electrik'];
            //     $percent = $total_electrik_persen / $total_all_electrik;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('AW' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('AW')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('AV' .  ($count_sales_code_row + $material_code_count), $total_all_electrik)
            //     ->setCellValue('AW' .  ($count_sales_code_row + $material_code_count), ($total_all_electrik > 0) ? 100 : '')
            //     ->setCellValue('AX' .  ($count_sales_code_row + $material_code_count), $t_april_e)
            //     ->setCellValue('AY' .  ($count_sales_code_row + $material_code_count), $t_mei_e)
            //     ->setCellValue('AZ' .  ($count_sales_code_row + $material_code_count), $t_juni_e)
            //     ->setCellValue('BA' .  ($count_sales_code_row + $material_code_count), $t_juli_e)
            //     ->setCellValue('BB' .  ($count_sales_code_row + $material_code_count), $t_agustus_e)
            //     ->setCellValue('BC' .  ($count_sales_code_row + $material_code_count), $t_september_e)
            //     ->setCellValue('BD' .  ($count_sales_code_row + $material_code_count), $t_oktober_e)
            //     ->setCellValue('BE' .  ($count_sales_code_row + $material_code_count), $t_november_e)
            //     ->setCellValue('BF' .  ($count_sales_code_row + $material_code_count), $t_desember_e)
            //     ->setCellValue('BG' .  ($count_sales_code_row + $material_code_count), $t_januari_e)
            //     ->setCellValue('BH' .  ($count_sales_code_row + $material_code_count), $t_februari_e)
            //     ->setCellValue('BI' .  ($count_sales_code_row + $material_code_count), $t_maret_e);




            // company basis
            $total_all_cb = 0;
            $t_april_cb = 0;
            $t_mei_cb = 0;
            $t_juni_cb = 0;
            $t_juli_cb = 0;
            $t_agustus_cb = 0;
            $t_september_cb = 0;
            $t_oktober_cb = 0;
            $t_november_cb = 0;
            $t_desember_cb = 0;
            $t_januari_cb = 0;
            $t_februari_cb = 0;
            $t_maret_cb = 0;
            $persen_arr_cb = array();
            $cb = 0;
            foreach ($material_company_basis as $key => $value) {
                $key = array_search($value->acc_code, array_column($dmate, 'acc_code'));
                // dd($value->sapril.$value->acc_name);
                // dd(array_column($dsales, 'acc_code'));
                $keyy = $key + $count_sales_code_row;
                $total_cb = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('D' . $keyy, $total_cb)
                    ->setCellValue('F' . $keyy, $value->sapril)
                    ->setCellValue('G' . $keyy, $value->smei)
                    ->setCellValue('H' . $keyy, $value->sjuni)
                    ->setCellValue('I' . $keyy, $value->sjuli)
                    ->setCellValue('J' . $keyy, $value->sagustus)
                    ->setCellValue('K' . $keyy, $value->sseptember)
                    ->setCellValue('L' . $keyy, $value->soktober)
                    ->setCellValue('M' . $keyy, $value->snovember)
                    ->setCellValue('N' . $keyy, $value->sdesember)
                    ->setCellValue('O' . $keyy, $value->sjanuari)
                    ->setCellValue('P' . $keyy, $value->sfebruari)
                    ->setCellValue('Q' . $keyy, $value->smaret);

                $total_all_cb = $total_all_cb + $total_cb;

                $t_april_cb = $t_april_cb + $value->sapril;
                $t_mei_cb = $t_mei_cb + $value->smei;
                $t_juni_cb = $t_juni_cb + $value->sjuni;
                $t_juli_cb = $t_juli_cb + $value->sjuli;
                $t_agustus_cb = $t_agustus_cb + $value->sagustus;
                $t_september_cb = $t_september_cb + $value->sseptember;
                $t_oktober_cb = $t_oktober_cb + $value->soktober;
                $t_november_cb = $t_november_cb + $value->snovember;
                $t_desember_cb = $t_desember_cb + $value->sdesember;
                $t_januari_cb = $t_januari_cb + $value->sjanuari;
                $t_februari_cb = $t_februari_cb + $value->sfebruari;
                $t_maret_cb = $t_maret_cb + $value->smaret;

                $persen_arr_cb[$cb]['acc_code'] = $value->acc_code;
                $persen_arr_cb[$cb]['acc_name'] = $value->acc_name;
                $persen_arr_cb[$cb]['total_cb'] = $total_cb;
                $cb++;
            }
            // dd($persen_arr_cb);

            // dd($persen_arr_cb);
            // foreach ($persen_arr_cb as $val) {

            //     $key = array_search($val['acc_code'], array_column($dmate, 'acc_code'));
            //     // dd($key);
            //     $keyy = $key + $count_sales_code_row;
            //     $total_cb_persen = $val['total_cb'];
            //     $percent = $total_cb_persen / $total_all_cb;
            //     // dd($val['acc_name'].$percent);
            //     $percent_friendly = number_format($percent * 100, 2);
            //     // dd($percent_friendly);
            //     $spreadsheet->setActiveSheetIndex(0)
            //         ->setCellValue('D' . $keyy, $percent_friendly);
            // }
            // $sheet1->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // $spreadsheet->setActiveSheetIndex(0)
            //     ->setCellValue('C' .  ($count_sales_code_row + $material_code_count), $total_all_cb)
            //     ->setCellValue('D' .  ($count_sales_code_row + $material_code_count), ($total_all_cb > 0) ? 100 : '')
            //     ->setCellValue('E' .  ($count_sales_code_row + $material_code_count), $t_april_cb)
            //     ->setCellValue('F' .  ($count_sales_code_row + $material_code_count), $t_mei_cb)
            //     ->setCellValue('G' .  ($count_sales_code_row + $material_code_count), $t_juni_cb)
            //     ->setCellValue('H' .  ($count_sales_code_row + $material_code_count), $t_juli_cb)
            //     ->setCellValue('I' .  ($count_sales_code_row + $material_code_count), $t_agustus_cb)
            //     ->setCellValue('J' .  ($count_sales_code_row + $material_code_count), $t_september_cb)
            //     ->setCellValue('K' .  ($count_sales_code_row + $material_code_count), $t_oktober_cb)
            //     ->setCellValue('L' .  ($count_sales_code_row + $material_code_count), $t_november_cb)
            //     ->setCellValue('M' .  ($count_sales_code_row + $material_code_count), $t_desember_cb)
            //     ->setCellValue('N' .  ($count_sales_code_row + $material_code_count), $t_januari_cb)
            //     ->setCellValue('O' .  ($count_sales_code_row + $material_code_count), $t_februari_cb)
            //     ->setCellValue('P' .  ($count_sales_code_row + $material_code_count), $t_maret_cb);
        }

        if (count((array) $expense_code) > 0) {
            $expense_code_count = count($expense_code) - 1;
            $i = 0;
            $x = 6 + $sales_code_count + $material_code_count + 2;
            // dd($x);
            $dexpen = array();
            foreach ($expense_code as $key => $row) {
                // $acc = explode("_",$row->acc_code);
                // $acc_code = $acc[0];
                // $acc_name = $acc[1];
                $dexpen[$i]['acc_group'] = $row['acc_group'];
                $dexpen[$i]['acc_code'] = $row['acc_code'];
                $dexpen[$i]['acc_name'] = $row['acc_name'];
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $x, $row['acc_group'])
                    ->setCellValue('B' . $x, $row['acc_code'])
                    ->setCellValue('C' . $x, $row['acc_name']);

                $i++;
                $x++;
            }

            $count_material_code_row = 6 + $sales_code_count + $material_code_count + 2;


            $total_all_body = 0;
            $t_april_b = 0;
            $t_mei_b = 0;
            $t_juni_b = 0;
            $t_juli_b = 0;
            $t_agustus_b = 0;
            $t_september_b = 0;
            $t_oktober_b = 0;
            $t_november_b = 0;
            $t_desember_b = 0;
            $t_januari_b = 0;
            $t_februari_b = 0;
            $t_maret_b = 0;
            $persen_arr_b = array();
            $b = 0;

            foreach ($expense_body as $key => $value) {
                // dd($value);
                $acc = explode("_", $value->acc_code);
                $acc_code = $acc[0];
                $acc_name = $acc[1];
                $key = array_search($acc_name, array_column($dexpen, 'acc_name'));

                // dd($key);
                if ($key != false) {

                    // dd($value->sapril.$value->acc_name);
                    // dd(array_column($dsales, 'acc_code'));
                    $keyy = $key + $count_material_code_row;

                    $total_body = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('R' . $keyy, $total_body)
                        ->setCellValue('T' . $keyy, $value->sapril)
                        ->setCellValue('U' . $keyy, $value->smei)
                        ->setCellValue('V' . $keyy, $value->sjuni)
                        ->setCellValue('W' . $keyy, $value->sjuli)
                        ->setCellValue('X' . $keyy, $value->sagustus)
                        ->setCellValue('Y' . $keyy, $value->sseptember)
                        ->setCellValue('Z' . $keyy, $value->soktober)
                        ->setCellValue('AA' . $keyy, $value->snovember)
                        ->setCellValue('AB' . $keyy, $value->sdesember)
                        ->setCellValue('AC' . $keyy, $value->sjanuari)
                        ->setCellValue('AD' . $keyy, $value->sfebruari)
                        ->setCellValue('AE' . $keyy, $value->smaret);

                    $total_all_body = $total_all_body + $total_body;

                    $t_april_b = $t_april_b + $value->sapril;
                    $t_mei_b = $t_mei_b + $value->smei;
                    $t_juni_b = $t_juni_b + $value->sjuni;
                    $t_juli_b = $t_juli_b + $value->sjuli;
                    $t_agustus_b = $t_agustus_b + $value->sagustus;
                    $t_september_b = $t_september_b + $value->sseptember;
                    $t_oktober_b = $t_oktober_b + $value->soktober;
                    $t_november_b = $t_november_b + $value->snovember;
                    $t_desember_b = $t_desember_b + $value->sdesember;
                    $t_januari_b = $t_januari_b + $value->sjanuari;
                    $t_februari_b = $t_februari_b + $value->sfebruari;
                    $t_maret_b = $t_maret_b + $value->smaret;

                    $persen_arr_b[$b]['acc_name'] = $acc_name;
                    $persen_arr_b[$b]['total_body'] = $total_body;

                    $b++;
                }
            }

            // dd($persen_arr_b);

            foreach ($persen_arr_b as $val) {
                // dd($val);
                $key = array_search($val['acc_name'], array_column($dexpen, 'acc_name'));
                $keyy = $key + $count_material_code_row;
                $total_body_persen = $val['total_body'];
                $percent = $total_body_persen / $total_all_body;
                $percent_friendly = number_format($percent * 100, 2);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('S' . $keyy, $percent_friendly);
            }
            $sheet1->getStyle('S')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('R' . ($count_material_code_row + $expense_code_count), $total_all_body)
                ->setCellValue('S' . ($count_material_code_row + $expense_code_count), ($total_all_body > 0) ? 100 : '')
                ->setCellValue('T' . ($count_material_code_row + $expense_code_count), $t_april_b)
                ->setCellValue('U' . ($count_material_code_row + $expense_code_count), $t_mei_b)
                ->setCellValue('V' . ($count_material_code_row + $expense_code_count), $t_juni_b)
                ->setCellValue('W' . ($count_material_code_row + $expense_code_count), $t_juli_b)
                ->setCellValue('X' . ($count_material_code_row + $expense_code_count), $t_agustus_b)
                ->setCellValue('Y' . ($count_material_code_row + $expense_code_count), $t_september_b)
                ->setCellValue('Z' . ($count_material_code_row + $expense_code_count), $t_oktober_b)
                ->setCellValue('AA' . ($count_material_code_row + $expense_code_count), $t_november_b)
                ->setCellValue('AB' . ($count_material_code_row + $expense_code_count), $t_desember_b)
                ->setCellValue('AC' . ($count_material_code_row + $expense_code_count), $t_januari_b)
                ->setCellValue('AD' . ($count_material_code_row + $expense_code_count), $t_februari_b)
                ->setCellValue('AE' . ($count_material_code_row + $expense_code_count), $t_maret_b);


            $total_all_unit = 0;
            $t_april_u = 0;
            $t_mei_u = 0;
            $t_juni_u = 0;
            $t_juli_u = 0;
            $t_agustus_u = 0;
            $t_september_u = 0;
            $t_oktober_u = 0;
            $t_november_u = 0;
            $t_desember_u = 0;
            $t_januari_u = 0;
            $t_februari_u = 0;
            $t_maret_u = 0;
            $persen_arr_u = array();
            $u = 0;

            foreach ($expense_unit as $key => $value) {
                $acc = explode("_", $value->acc_code);
                $acc_code = $acc[0];
                $acc_name = $acc[1];
                $key = array_search($acc_name, array_column($dexpen, 'acc_name'));
                if ($key != false) {
                    // dd($value->sapril.$value->acc_name);
                    // dd(array_column($dsales, 'acc_code'));
                    $keyy = $key + $count_material_code_row;
                    $total_unit = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('AG' . $keyy, $total_unit)
                        ->setCellValue('AI' . $keyy, $value->sapril)
                        ->setCellValue('AJ' . $keyy, $value->smei)
                        ->setCellValue('AK' . $keyy, $value->sjuni)
                        ->setCellValue('AL' . $keyy, $value->sjuli)
                        ->setCellValue('AM' . $keyy, $value->sagustus)
                        ->setCellValue('AN' . $keyy, $value->sseptember)
                        ->setCellValue('AO' . $keyy, $value->soktober)
                        ->setCellValue('AP' . $keyy, $value->snovember)
                        ->setCellValue('AQ' . $keyy, $value->sdesember)
                        ->setCellValue('AR' . $keyy, $value->sjanuari)
                        ->setCellValue('AS' . $keyy, $value->sfebruari)
                        ->setCellValue('AT' . $keyy, $value->smaret);

                    $total_all_unit = $total_all_unit + $total_unit;

                    $t_april_u = $t_april_u + $value->sapril;
                    $t_mei_u = $t_mei_u + $value->smei;
                    $t_juni_u = $t_juni_u + $value->sjuni;
                    $t_juli_u = $t_juli_u + $value->sjuli;
                    $t_agustus_u = $t_agustus_u + $value->sagustus;
                    $t_september_u = $t_september_u + $value->sseptember;
                    $t_oktober_u = $t_oktober_u + $value->soktober;
                    $t_november_u = $t_november_u + $value->snovember;
                    $t_desember_u = $t_desember_u + $value->sdesember;
                    $t_januari_u = $t_januari_u + $value->sjanuari;
                    $t_februari_u = $t_februari_u + $value->sfebruari;
                    $t_maret_u = $t_maret_u + $value->smaret;

                    $persen_arr_u[$u]['acc_name'] = $acc_name;
                    $persen_arr_u[$u]['total_unit'] = $total_unit;
                    $u++;
                }
            }

            foreach ($persen_arr_u as $val) {
                // dd($val['acc_name']);
                $key = array_search($val['acc_name'], array_column($dexpen, 'acc_name'));
                $keyy = $key + $count_material_code_row;
                $total_unit_persen = $val['total_unit'];
                $percent = $total_unit_persen / $total_all_unit;
                // dd($val['acc_name'].$percent);
                $percent_friendly = number_format($percent * 100, 2);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AH' . $keyy, $percent_friendly);
            }

            $sheet1->getStyle('AH')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('AG' . ($count_material_code_row + $expense_code_count), $total_all_unit)
                ->setCellValue('AH' . ($count_material_code_row + $expense_code_count), ($total_all_unit > 0) ? 100 : '')
                ->setCellValue('AI' . ($count_material_code_row + $expense_code_count), $t_april_u)
                ->setCellValue('AJ' . ($count_material_code_row + $expense_code_count), $t_mei_u)
                ->setCellValue('AK' . ($count_material_code_row + $expense_code_count), $t_juni_u)
                ->setCellValue('AL' . ($count_material_code_row + $expense_code_count), $t_juli_u)
                ->setCellValue('AM' . ($count_material_code_row + $expense_code_count), $t_agustus_u)
                ->setCellValue('AN' . ($count_material_code_row + $expense_code_count), $t_september_u)
                ->setCellValue('AO' . ($count_material_code_row + $expense_code_count), $t_oktober_u)
                ->setCellValue('AP' . ($count_material_code_row + $expense_code_count), $t_november_u)
                ->setCellValue('AQ' . ($count_material_code_row + $expense_code_count), $t_desember_u)
                ->setCellValue('AR' . ($count_material_code_row + $expense_code_count), $t_januari_u)
                ->setCellValue('AS' . ($count_material_code_row + $expense_code_count), $t_februari_u)
                ->setCellValue('AT' . ($count_material_code_row + $expense_code_count), $t_maret_u);




            $total_all_electrik = 0;
            $t_april_e = 0;
            $t_mei_e = 0;
            $t_juni_e = 0;
            $t_juli_e = 0;
            $t_agustus_e = 0;
            $t_september_e = 0;
            $t_oktober_e = 0;
            $t_november_e = 0;
            $t_desember_e = 0;
            $t_januari_e = 0;
            $t_februari_e = 0;
            $t_maret_e = 0;
            $persen_arr_e = array();
            $e = 0;
            foreach ($expense_electrik as $key => $value) {
                $acc = explode("_", $value->acc_code);
                $acc_code = $acc[0];
                $acc_name = $acc[1];
                $key = array_search($acc_name, array_column($dexpen, 'acc_name'));
                if ($key != false) {
                    // dd($value->sapril.$value->acc_name);
                    // dd(array_column($dsales, 'acc_code'));
                    $keyy = $key + $count_material_code_row;
                    $total_electrik = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('AV' . $keyy, $total_electrik)
                        ->setCellValue('AX' . $keyy, $value->sapril)
                        ->setCellValue('AY' . $keyy, $value->smei)
                        ->setCellValue('AZ' . $keyy, $value->sjuni)
                        ->setCellValue('BA' . $keyy, $value->sjuli)
                        ->setCellValue('BB' . $keyy, $value->sagustus)
                        ->setCellValue('BC' . $keyy, $value->sseptember)
                        ->setCellValue('BD' . $keyy, $value->soktober)
                        ->setCellValue('BE' . $keyy, $value->snovember)
                        ->setCellValue('BF' . $keyy, $value->sdesember)
                        ->setCellValue('BG' . $keyy, $value->sjanuari)
                        ->setCellValue('BH' . $keyy, $value->sfebruari)
                        ->setCellValue('BI' . $keyy, $value->smaret);

                    $total_all_electrik = $total_all_electrik + $total_electrik;

                    $t_april_e = $t_april_e + $value->sapril;
                    $t_mei_e = $t_mei_e + $value->smei;
                    $t_juni_e = $t_juni_e + $value->sjuni;
                    $t_juli_e = $t_juli_e + $value->sjuli;
                    $t_agustus_e = $t_agustus_e + $value->sagustus;
                    $t_september_e = $t_september_e + $value->sseptember;
                    $t_oktober_e = $t_oktober_e + $value->soktober;
                    $t_november_e = $t_november_e + $value->snovember;
                    $t_desember_e = $t_desember_e + $value->sdesember;
                    $t_januari_e = $t_januari_e + $value->sjanuari;
                    $t_februari_e = $t_februari_e + $value->sfebruari;
                    $t_maret_e = $t_maret_e + $value->smaret;

                    $persen_arr_e[$e]['acc_name'] = $acc_name;
                    $persen_arr_e[$e]['total_electrik'] = $total_electrik;
                    $e++;
                }
            }

            foreach ($persen_arr_e as $val) {
                // dd($val['acc_name']);
                $key = array_search($val['acc_name'], array_column($dexpen, 'acc_name'));
                $keyy = $key + $count_material_code_row;
                $total_electrik_persen = $val['total_electrik'];
                $percent = $total_electrik_persen / $total_all_electrik;
                // dd($val['acc_name'].$percent);
                $percent_friendly = number_format($percent * 100, 2);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('AW' . $keyy, $percent_friendly);
            }
            $sheet1->getStyle('AW')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('AV' . ($count_material_code_row + $expense_code_count), $total_all_electrik)
                ->setCellValue('AW' . ($count_material_code_row + $expense_code_count), ($total_all_electrik > 0) ? 100 : '')
                ->setCellValue('AX' . ($count_material_code_row + $expense_code_count), $t_april_e)
                ->setCellValue('AY' . ($count_material_code_row + $expense_code_count), $t_mei_e)
                ->setCellValue('AZ' . ($count_material_code_row + $expense_code_count), $t_juni_e)
                ->setCellValue('BA' . ($count_material_code_row + $expense_code_count), $t_juli_e)
                ->setCellValue('BB' . ($count_material_code_row + $expense_code_count), $t_agustus_e)
                ->setCellValue('BC' . ($count_material_code_row + $expense_code_count), $t_september_e)
                ->setCellValue('BD' . ($count_material_code_row + $expense_code_count), $t_oktober_e)
                ->setCellValue('BE' . ($count_material_code_row + $expense_code_count), $t_november_e)
                ->setCellValue('BF' . ($count_material_code_row + $expense_code_count), $t_desember_e)
                ->setCellValue('BG' . ($count_material_code_row + $expense_code_count), $t_januari_e)
                ->setCellValue('BH' . ($count_material_code_row + $expense_code_count), $t_februari_e)
                ->setCellValue('BI' . ($count_material_code_row + $expense_code_count), $t_maret_e);



            $total_all_cb = 0;
            $t_mei_cb = 0;
            $t_juni_cb = 0;
            $t_juli_cb = 0;
            $t_agustus_cb = 0;
            $t_september_cb = 0;
            $t_oktober_cb = 0;
            $t_november_cb = 0;
            $t_desember_cb = 0;
            $t_januari_cb = 0;
            $t_februari_cb = 0;
            $t_maret_cb = 0;
            $persen_arr_cb = array();
            $cb = 0;

            foreach ($expense_company_basis as $key => $value) {
                $acc = explode("_", $value->acc_code);
                $acc_code = $acc[0];
                $acc_name = $acc[1];
                $key = array_search($acc_name, array_column($dexpen, 'acc_name'));
                if ($key != false) {
                    // dd($value->sapril.$value->acc_name);
                    // dd(array_column($dsales, 'acc_code'));
                    $keyy = $key + $count_material_code_row;
                    $total_cb = ($value->sapril + $value->smei + $value->sjuni + $value->sjuli + $value->sagustus + $value->sseptember + $value->soktober + $value->snovember + $value->sdesember + $value->sjanuari + $value->sfebruari + $value->smaret);
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('C' . $keyy, $total_cb)
                        ->setCellValue('E' . $keyy, $value->sapril)
                        ->setCellValue('F' . $keyy, $value->smei)
                        ->setCellValue('G' . $keyy, $value->sjuni)
                        ->setCellValue('H' . $keyy, $value->sjuli)
                        ->setCellValue('I' . $keyy, $value->sagustus)
                        ->setCellValue('J' . $keyy, $value->sseptember)
                        ->setCellValue('K' . $keyy, $value->soktober)
                        ->setCellValue('L' . $keyy, $value->snovember)
                        ->setCellValue('M' . $keyy, $value->sdesember)
                        ->setCellValue('N' . $keyy, $value->sjanuari)
                        ->setCellValue('O' . $keyy, $value->sfebruari)
                        ->setCellValue('P' . $keyy, $value->smaret);

                    $total_all_cb = $total_all_cb + $total_cb;

                    $t_april_cb = $t_april_cb + $value->sapril;
                    $t_mei_cb = $t_mei_cb + $value->smei;
                    $t_juni_cb = $t_juni_cb + $value->sjuni;
                    $t_juli_cb = $t_juli_cb + $value->sjuli;
                    $t_agustus_cb = $t_agustus_cb + $value->sagustus;
                    $t_september_cb = $t_september_cb + $value->sseptember;
                    $t_oktober_cb = $t_oktober_cb + $value->soktober;
                    $t_november_cb = $t_november_cb + $value->snovember;
                    $t_desember_cb = $t_desember_cb + $value->sdesember;
                    $t_januari_cb = $t_januari_cb + $value->sjanuari;
                    $t_februari_cb = $t_februari_cb + $value->sfebruari;
                    $t_maret_cb = $t_maret_cb + $value->smaret;

                    $persen_arr_cb[$cb]['acc_name'] = $acc_name;
                    $persen_arr_cb[$cb]['total_cb'] = $total_cb;
                    $cb++;
                }
            }

            // dd($persen_arr_cb);
            foreach ($persen_arr_cb as $val) {

                $key = array_search($val['acc_name'], array_column($dexpen, 'acc_name'));
                // dd($key);
                $keyy = $key + $count_material_code_row;
                $total_cb_persen = $val['total_cb'];
                $percent = $total_cb_persen / $total_all_cb;
                // dd($val['acc_name'].$percent);
                $percent_friendly = number_format($percent * 100, 2);
                // dd($percent_friendly);
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('D' . $keyy, $percent_friendly);
            }
            $sheet1->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('C' . ($count_material_code_row + $expense_code_count), $total_all_cb)
                ->setCellValue('D' . ($count_material_code_row + $expense_code_count), ($total_all_cb > 0) ? 100 : '')
                ->setCellValue('E' . ($count_material_code_row + $expense_code_count), $t_april_cb)
                ->setCellValue('F' . ($count_material_code_row + $expense_code_count), $t_mei_cb)
                ->setCellValue('G' . ($count_material_code_row + $expense_code_count), $t_juni_cb)
                ->setCellValue('H' . ($count_material_code_row + $expense_code_count), $t_juli_cb)
                ->setCellValue('I' . ($count_material_code_row + $expense_code_count), $t_agustus_cb)
                ->setCellValue('J' . ($count_material_code_row + $expense_code_count), $t_september_cb)
                ->setCellValue('K' . ($count_material_code_row + $expense_code_count), $t_oktober_cb)
                ->setCellValue('L' . ($count_material_code_row + $expense_code_count), $t_november_cb)
                ->setCellValue('M' . ($count_material_code_row + $expense_code_count), $t_desember_cb)
                ->setCellValue('N' . ($count_material_code_row + $expense_code_count), $t_januari_cb)
                ->setCellValue('O' . ($count_material_code_row + $expense_code_count), $t_februari_cb)
                ->setCellValue('P' . ($count_material_code_row + $expense_code_count), $t_maret_cb);
        }

        $filename = 'Export data' . date('d/m/Y');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'op' => 'ok',
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }


    public function exportData(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '256M');
        ob_start();
        
        // Load the spreadsheet
        $reader = IOFactory::createReader('Xlsx');
        // $reader->setReadDataOnly(true);
        // $spreadsheet = $reader->load(public_path('files/Summary_New_2024.xlsx'));
        $spreadsheet = $reader->load(public_path('files/TemplateExport.xlsx'));
        
        // dd($spreadsheet);

        // dd($data_master_expense);
        $spreadsheet->getProperties()->setCreator('Aiia')
            ->setLastModifiedBy('Aiia')
            ->setTitle('Office 2021 XLSX Aiia Document')
            ->setSubject('Office 2021 XLSX Aiia Document')
            ->setDescription('Office 2021 XLSX Aiia Document.')
            ->setKeywords('Office 2021 XLSX Aiia Document')
            ->setCategory('Office 2021 XLSX Aiia Document');

        $sheetdetail = $spreadsheet->getSheetByName('PnL Detail');
        $sheetbydept = $spreadsheet->getSheetByName('By Dept');

        $sc = 6;
        $code = array();
        $a = 0;
        foreach ($sheetdetail->getRowIterator() as $row) {

            $acc_group = $sheetdetail->getCell("A$sc")->getValue();
            $acc_code = $sheetdetail->getCell("B$sc")->getValue();
            $acc_name = $sheetdetail->getCell("C$sc")->getValue();
            $code[$a]['acc_group'] = $acc_group;
            $code[$a]['acc_code'] = $acc_code;
            if ($acc_name == "                      Adjustment") {
                $acc_name = str_replace(" ", "", $acc_name);
            }
            if ($acc_name == "                      Sub Total") {
                $acc_name = str_replace(" ", "", $acc_name);
            }
            $code[$a]['acc_name'] = $acc_name;

            $sc++;
            $a++;
            if ($a === 370) {
                break;
            }
        }


        $sc = 6;
        $codedept = array();
        $a = 0;
        foreach ($sheetbydept->getRowIterator() as $row) {
            $dept_code = $sheetbydept->getCell("C$sc")->getValue();
            $acc_code = $sheetbydept->getCell("F$sc")->getValue();
            $codedept[$a]['dept_code'] = $dept_code;
            $codedept[$a]['acc_code'] = $acc_code;
            $codedept[$a]['dept_acc'] = $dept_code .'-'.$acc_code;
            $sc++;
            $a++;
            if ($a === 238) {
                break;
            }
        }

        $salesElektrikDetail =  SalesRb::getSalesElektrikDetail();
        $materialElektrikDetail = DmaterialRb::getMaterialElektrikDetail();
        $expenseElektrikDetail = ExpenseRb::getExpenseElektrikDetail();
        $laborElektrikDetail = LaborRb::getLaborElektrikDetail();
 
        // dept
        $salesElektrikDept =  SalesRb::getSalesElektrikDept();
        $materialElektrikDept = DmaterialRb::getMaterialElektrikDept();
        $expenseElektrikDept = ExpenseRb::getExpenseElektrikDept();
        $laborElektrikDept = LaborRb::getLaborElektrikDept();
 


        $electrik = array();
        $e = 0;
        foreach ($salesElektrikDetail as $key => $val) {
            $electrik[$e]['acc_code'] = $val->acc_code;
            $electrik[$e]['acc_name'] = $val->acc_name;
            $electrik[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrik[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrik[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrik[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrik[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrik[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrik[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrik[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrik[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrik[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrik[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrik[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }
        foreach ($materialElektrikDetail as $key => $val) {
            $electrik[$e]['acc_code'] = $val->acc_code;
            $electrik[$e]['acc_name'] = $val->acc_name;
            $electrik[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrik[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrik[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrik[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrik[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrik[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrik[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrik[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrik[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrik[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrik[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrik[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }
        foreach ($expenseElektrikDetail as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $electrik[$e]['acc_code'] = $acc_code;
            $electrik[$e]['acc_name'] = $acc_name;
            $electrik[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrik[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrik[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrik[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrik[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrik[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrik[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrik[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrik[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrik[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrik[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrik[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }

        foreach ($laborElektrikDetail as $key => $val) {
            $electrik[$e]['acc_code'] = $val->acc_code;
            $electrik[$e]['acc_name'] = $val->acc_name;
            $electrik[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrik[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrik[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrik[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrik[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrik[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrik[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrik[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrik[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrik[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrik[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrik[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }


        
        $electrikdept = array();
        $e = 0;
        foreach ($salesElektrikDept as $key => $val) {
            $electrikdept[$e]['dept'] = $val->dept;
            $electrikdept[$e]['acc_code'] = $val->acc_code;
            $electrikdept[$e]['acc_name'] = $val->acc_name;
            $electrikdept[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrikdept[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrikdept[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrikdept[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrikdept[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrikdept[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrikdept[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrikdept[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrikdept[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrikdept[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrikdept[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrikdept[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }
        foreach ($materialElektrikDept as $key => $val) {
            $electrikdept[$e]['dept'] = $val->dept;
            $electrikdept[$e]['acc_code'] = $val->acc_code;
            $electrikdept[$e]['acc_name'] = $val->acc_name;
            $electrikdept[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrikdept[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrikdept[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrikdept[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrikdept[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrikdept[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrikdept[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrikdept[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrikdept[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrikdept[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrikdept[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrikdept[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }
        foreach ($expenseElektrikDept as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $electrikdept[$e]['acc_code'] = $acc_code;
            $electrikdept[$e]['acc_name'] = $acc_name;
            $electrikdept[$e]['dept'] = $val->dept;
            $electrikdept[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrikdept[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrikdept[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrikdept[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrikdept[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrikdept[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrikdept[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrikdept[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrikdept[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrikdept[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrikdept[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrikdept[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }

        foreach ($laborElektrikDept as $key => $val) {
            $electrikdept[$e]['dept'] = $val->dept;
            $electrikdept[$e]['acc_code'] = $val->acc_code;
            $electrikdept[$e]['acc_name'] = $val->acc_name;
            $electrikdept[$e]['sapril'] = str_replace(" ", "", $val->sapril);
            $electrikdept[$e]['smei'] = str_replace(" ", "", $val->smei);
            $electrikdept[$e]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $electrikdept[$e]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $electrikdept[$e]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $electrikdept[$e]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $electrikdept[$e]['soktober'] = str_replace(" ", "", $val->soktober);
            $electrikdept[$e]['snovember'] = str_replace(" ", "", $val->snovember);
            $electrikdept[$e]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $electrikdept[$e]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $electrikdept[$e]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $electrikdept[$e]['smaret'] = str_replace(" ", "", $val->smaret);
            $e++;
        }



        $salesUnitDetail =  SalesRb::getSalesUnitDetail();
        $materialUnitDetail = DmaterialRb::getMaterialUnitDetail();
        $expenseUnitDetail = ExpenseRb::getExpenseUnitDetail();
        $laborUnitDetail = LaborRb::getLaborUnitDetail();
 
        // dept
        $salesUnitDept =  SalesRb::getSalesUnitDept();
        $materialUnitDept = DmaterialRb::getMaterialUnitDept();
        $expenseUnitDept = ExpenseRb::getExpenseUnitDept();
        $laborUnitDept = LaborRb::getLaborUnitDept();
        

        $unit = array();
        $u = 0;
        foreach ($salesUnitDetail as $key => $val) {
            $unit[$u]['acc_code'] = $val->acc_code;
            $unit[$u]['acc_name'] = $val->acc_name;
            $unit[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unit[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unit[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unit[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unit[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unit[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unit[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unit[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unit[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unit[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unit[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unit[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }
        foreach ($materialUnitDetail as $key => $val) {
            $unit[$u]['acc_code'] = $val->acc_code;
            $unit[$u]['acc_name'] = $val->acc_name;
            $unit[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unit[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unit[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unit[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unit[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unit[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unit[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unit[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unit[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unit[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unit[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unit[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }
        foreach ($expenseUnitDetail as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $unit[$u]['acc_code'] = $acc_code;
            $unit[$u]['acc_name'] = $acc_name;
            $unit[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unit[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unit[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unit[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unit[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unit[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unit[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unit[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unit[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unit[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unit[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unit[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }

        foreach ($laborUnitDetail as $key => $val) {
            $unit[$u]['acc_code'] = $val->acc_code;
            $unit[$u]['acc_name'] = $val->acc_name;
            $unit[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unit[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unit[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unit[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unit[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unit[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unit[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unit[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unit[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unit[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unit[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unit[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }


        $unitdept = array();
        $u = 0;
        foreach ($salesUnitDept as $key => $val) {
            $unitdept[$u]['dept'] = $val->dept;
            $unitdept[$u]['acc_code'] = $val->acc_code;
            $unitdept[$u]['acc_name'] = $val->acc_name;
            $unitdept[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unitdept[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unitdept[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unitdept[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unitdept[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unitdept[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unitdept[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unitdept[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unitdept[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unitdept[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unitdept[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unitdept[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }
        foreach ($materialUnitDept as $key => $val) {
            $unitdept[$u]['dept'] = $val->dept;
            $unitdept[$u]['acc_code'] = $val->acc_code;
            $unitdept[$u]['acc_name'] = $val->acc_name;
            $unitdept[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unitdept[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unitdept[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unitdept[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unitdept[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unitdept[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unitdept[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unitdept[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unitdept[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unitdept[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unitdept[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unitdept[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }
        foreach ($expenseUnitDept as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $unitdept[$u]['acc_code'] = $acc_code;
            $unitdept[$u]['acc_name'] = $acc_name;
            $unitdept[$u]['dept'] = $val->dept;
            $unitdept[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unitdept[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unitdept[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unitdept[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unitdept[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unitdept[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unitdept[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unitdept[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unitdept[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unitdept[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unitdept[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unitdept[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }

        foreach ($laborUnitDept as $key => $val) {
            $unitdept[$u]['dept'] = $val->dept;
            $unitdept[$u]['acc_code'] = $val->acc_code;
            $unitdept[$u]['acc_name'] = $val->acc_name;
            $unitdept[$u]['sapril'] = str_replace(" ", "", $val->sapril);
            $unitdept[$u]['smei'] = str_replace(" ", "", $val->smei);
            $unitdept[$u]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $unitdept[$u]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $unitdept[$u]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $unitdept[$u]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $unitdept[$u]['soktober'] = str_replace(" ", "", $val->soktober);
            $unitdept[$u]['snovember'] = str_replace(" ", "", $val->snovember);
            $unitdept[$u]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $unitdept[$u]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $unitdept[$u]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $unitdept[$u]['smaret'] = str_replace(" ", "", $val->smaret);
            $u++;
        }


        $salesBodyDetail =  SalesRb::getSalesBodyDetail();
        $materialBodyDetail = DmaterialRb::getMaterialBodyDetail();
        $expenseBodyDetail = ExpenseRb::getExpenseBodyDetail();
        $laborBodyDetail = LaborRb::getLaborBodyDetail();
 
        // dept
        $salesBodyDept =  SalesRb::getSalesBodyDept();
        $materialBodyDept = DmaterialRb::getMaterialBodyDept();
        $expenseBodyDept = ExpenseRb::getExpenseBodyDept();
        $laborBodyDept = LaborRb::getLaborBodyDept();
        
       
        $body = array();
        $b = 0;
        foreach ($salesBodyDetail as $key => $val) {
            $body[$b]['acc_code'] = $val->acc_code;
            $body[$b]['acc_name'] = $val->acc_name;
            $body[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $body[$b]['smei'] = str_replace(" ", "", $val->smei);
            $body[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $body[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $body[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $body[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $body[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $body[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $body[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $body[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $body[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $body[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }


        foreach ($materialBodyDetail as $key => $val) {
            $body[$b]['acc_code'] = $val->acc_code;
            $body[$b]['acc_name'] = $val->acc_name;
            $body[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $body[$b]['smei'] = str_replace(" ", "", $val->smei);
            $body[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $body[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $body[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $body[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $body[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $body[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $body[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $body[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $body[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $body[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }
        foreach ($expenseBodyDetail as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $body[$b]['acc_code'] = $acc_code;
            $body[$b]['acc_name'] = $acc_name;
            $body[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $body[$b]['smei'] = str_replace(" ", "", $val->smei);
            $body[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $body[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $body[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $body[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $body[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $body[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $body[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $body[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $body[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $body[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }

        foreach ($laborBodyDetail as $key => $val) {
            $body[$b]['acc_code'] = $val->acc_code;
            $body[$b]['acc_name'] = $val->acc_name;
            $body[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $body[$b]['smei'] = str_replace(" ", "", $val->smei);
            $body[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $body[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $body[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $body[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $body[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $body[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $body[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $body[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $body[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $body[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }

        // dept
        $bodydept = array();
        $b = 0;
        foreach ($salesBodyDept as $key => $val) {
            $bodydept[$b]['dept'] = $val->dept;
            $bodydept[$b]['acc_code'] = $val->acc_code;
            $bodydept[$b]['acc_name'] = $val->acc_name;
            $bodydept[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $bodydept[$b]['smei'] = str_replace(" ", "", $val->smei);
            $bodydept[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $bodydept[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $bodydept[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $bodydept[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $bodydept[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $bodydept[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $bodydept[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $bodydept[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $bodydept[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $bodydept[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }


        foreach ($materialBodyDept as $key => $val) {
            $bodydept[$b]['dept'] = $val->dept;
            $bodydept[$b]['acc_code'] = $val->acc_code;
            $bodydept[$b]['acc_name'] = $val->acc_name;
            $bodydept[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $bodydept[$b]['smei'] = str_replace(" ", "", $val->smei);
            $bodydept[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $bodydept[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $bodydept[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $bodydept[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $bodydept[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $bodydept[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $bodydept[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $bodydept[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $bodydept[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $bodydept[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }
        foreach ($expenseBodyDept as $key => $val) {
            // dd($value);
            $acc = explode("_", $val->acc_code);
            $acc_code = $acc[0];
            $acc_name = $acc[1];
            $bodydept[$b]['acc_code'] = $acc_code;
            $bodydept[$b]['acc_name'] = $acc_name;
            $bodydept[$b]['dept'] = $val->dept;
            $bodydept[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $bodydept[$b]['smei'] = str_replace(" ", "", $val->smei);
            $bodydept[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $bodydept[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $bodydept[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $bodydept[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $bodydept[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $bodydept[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $bodydept[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $bodydept[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $bodydept[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $bodydept[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }

        foreach ($laborBodyDept as $key => $val) {
            $bodydept[$b]['dept'] = $val->dept;
            $bodydept[$b]['acc_code'] = $val->acc_code;
            $bodydept[$b]['acc_name'] = $val->acc_name;
            $bodydept[$b]['sapril'] = str_replace(" ", "", $val->sapril);
            $bodydept[$b]['smei'] = str_replace(" ", "", $val->smei);
            $bodydept[$b]['sjuni'] = str_replace(" ", "", $val->sjuni);
            $bodydept[$b]['sjuli'] = str_replace(" ", "", $val->sjuli);
            $bodydept[$b]['sagustus'] = str_replace(" ", "", $val->sagustus);
            $bodydept[$b]['sseptember'] = str_replace(" ", "", $val->sseptember);
            $bodydept[$b]['soktober'] = str_replace(" ", "", $val->soktober);
            $bodydept[$b]['snovember'] = str_replace(" ", "", $val->snovember);
            $bodydept[$b]['sdecember'] = str_replace(" ", "", $val->sdecember);
            $bodydept[$b]['sjanuari'] = str_replace(" ", "", $val->sjanuari);
            $bodydept[$b]['sfebruari'] = str_replace(" ", "", $val->sfebruari);
            $bodydept[$b]['smaret'] = str_replace(" ", "", $val->smaret);
            $b++;
        }
        // dd($code);
        // mapping set PnL Detail
        if (count((array) $code) > 0) {
            $i = 0;
            $x = 6;
            $dcode = array();
            foreach ($code as $key => $row) {
                // dd($row);
                $dcode[$i]['acc_group'] = $row['acc_group'];
                $dcode[$i]['acc_name'] = $row['acc_name'];
                $dcode[$i]['acc_code'] = (string) $row['acc_code'];
                $sheetdetail->setCellValue('A' . $x, $row['acc_group'])
                    ->setCellValue('B' . $x, $row['acc_code'])
                    ->setCellValue('C' . $x, $row['acc_name']);

                $i++;
                $x++;
            }


            

            $bb = 0;
            // dd($body);
            foreach ($body as $key => $value) {

                $keyb = array_search((string) $value['acc_code'], array_column($dcode, 'acc_code'));


                $keyyb = $keyb + 6;

                $sheetdetail->setCellValue('U' . $keyyb, $value['sapril'])
                    ->setCellValue('V' . $keyyb, $value['smei'])
                    ->setCellValue('W' . $keyyb, $value['sjuni'])
                    ->setCellValue('X' . $keyyb, $value['sjuli'])
                    ->setCellValue('Y' . $keyyb, $value['sagustus'])
                    ->setCellValue('Z' . $keyyb, $value['sseptember'])
                    ->setCellValue('AA' . $keyyb, $value['soktober'])
                    ->setCellValue('AB' . $keyyb, $value['snovember'])
                    ->setCellValue('AC' . $keyyb, $value['sdecember'])
                    ->setCellValue('AD' . $keyyb, $value['sjanuari'])
                    ->setCellValue('AE' . $keyyb, $value['sfebruari'])
                    ->setCellValue('AF' . $keyyb, $value['smaret']);

                $bb++;
            }



            $uu = 0;
            foreach ($unit as $key => $value) {
                $keyu = array_search((string) $value['acc_code'], array_column($dcode, 'acc_code'));

                $keyyu = $keyu + 6;
                $sheetdetail->setCellValue('AJ' . $keyyu, $value['sapril'])
                    ->setCellValue('AK' . $keyyu, $value['smei'])
                    ->setCellValue('AL' . $keyyu, $value['sjuni'])
                    ->setCellValue('AM' . $keyyu, $value['sjuli'])
                    ->setCellValue('AN' . $keyyu, $value['sagustus'])
                    ->setCellValue('AO' . $keyyu, $value['sseptember'])
                    ->setCellValue('AP' . $keyyu, $value['soktober'])
                    ->setCellValue('AQ' . $keyyu, $value['snovember'])
                    ->setCellValue('AR' . $keyyu, $value['sdecember'])
                    ->setCellValue('AS' . $keyyu, $value['sjanuari'])
                    ->setCellValue('AT' . $keyyu, $value['sfebruari'])
                    ->setCellValue('AU' . $keyyu, $value['smaret']);
                $uu++;
            }



            $ee = 0;
            foreach ($electrik as $key => $value) {
                $keye = array_search((string) $value['acc_code'], array_column($dcode, 'acc_code'));

                $keyye = $keye + 6;
                $sheetdetail->setCellValue('AY' . $keyye, $value['sapril'])
                    ->setCellValue('AZ' . $keyye, $value['smei'])
                    ->setCellValue('BA' . $keyye, $value['sjuni'])
                    ->setCellValue('BB' . $keyye, $value['sjuli'])
                    ->setCellValue('BC' . $keyye, $value['sagustus'])
                    ->setCellValue('BD' . $keyye, $value['sseptember'])
                    ->setCellValue('BE' . $keyye, $value['soktober'])
                    ->setCellValue('BF' . $keyye, $value['snovember'])
                    ->setCellValue('BG' . $keyye, $value['sdecember'])
                    ->setCellValue('BH' . $keyye, $value['sjanuari'])
                    ->setCellValue('BI' . $keyye, $value['sfebruari'])
                    ->setCellValue('BJ' . $keyye, $value['smaret']);


                $ee++;
            }
            // dd($body);
        }

        // mapping shet By Dept
        if (count((array) $codedept) > 0) {
            $i = 0;
            $x = 6;
            $dcodedept = array();
            foreach ($codedept as $key => $row) {
                // dd($row);
                $dcodedept[$i]['dept_code'] = $row['dept_code'];
                $dcodedept[$i]['acc_code'] = (string) $row['acc_code'];

                $dcodedept[$i]['dept_acc'] = $row['dept_acc'];
                $sheetbydept->setCellValue('C' . $x, $row['dept_code'])
                    ->setCellValue('F' . $x, $row['acc_code']);

                $i++;
                $x++;
            }
            // dd(json_encode($dcodedept));
            
          
            $sumDataBody = [];
            foreach ($bodydept as $itembody) {
                $cekCode = substr($itembody['acc_code'], 0, 5);
                $codePrefix = ($cekCode == '51191') ? $cekCode : substr($itembody['acc_code'], 0, 4);
                $deptPrefix = $itembody['dept'];
                $deptAccPrefix = $deptPrefix .'-'.$codePrefix;
                if (!isset($sumDataBody[$deptAccPrefix])) {
                    $sumDataBody[$deptAccPrefix] = [
                        'dept_acc' => $deptAccPrefix,
                    ];
                }

                foreach ($itembody as $key => $value) {
                    if ($key !== 'dept_acc') {
                        if (!isset($sumDataBody[$deptAccPrefix][$key])) {
                            $sumDataBody[$deptAccPrefix][$key] = 0;
                        }
                        $sumDataBody[$deptAccPrefix][$key] += (float)$value;
                    }
                }
            }
            // dd(json_encode($dcodedept));
            // dd(json_encode($sumDataBody));
            $bb = 0;
            foreach ($sumDataBody as $key => $value) {
                $keyb = array_search((string) $value['dept_acc'], array_column($dcodedept, 'dept_acc'));
                
                if ($keyb != false) {
                    $keyyb = $keyb + 6;
                    # code...
                    $sheetbydept->setCellValue('R' . $keyyb, $value['sapril'])
                        ->setCellValue('S' . $keyyb, $value['smei'])
                        ->setCellValue('T' . $keyyb, $value['sjuni'])
                        ->setCellValue('U' . $keyyb, $value['sjuli'])
                        ->setCellValue('V' . $keyyb, $value['sagustus'])
                        ->setCellValue('W' . $keyyb, $value['sseptember'])
                        ->setCellValue('X' . $keyyb, $value['soktober'])
                        ->setCellValue('Y' . $keyyb, $value['snovember'])
                        ->setCellValue('Z' . $keyyb, $value['sdecember'])
                        ->setCellValue('AA' . $keyyb, $value['sjanuari'])
                        ->setCellValue('AB' . $keyyb, $value['sfebruari'])
                        ->setCellValue('AC' . $keyyb, $value['smaret']);
                }
                $bb++;
            }


            
            $sumDataUnit = [];

            foreach ($unitdept as $itemunit) {
                $cekCode = substr($itemunit['acc_code'], 0, 5);
                $codePrefix = ($cekCode == '51191') ? $cekCode : substr($itemunit['acc_code'], 0, 4);
                $deptPrefix = $itemunit['dept'];
                $deptAccPrefix = $deptPrefix .'-'.$codePrefix;
                if (!isset($sumDataUnit[$deptAccPrefix])) {
                    $sumDataUnit[$deptAccPrefix] = [
                        'dept_acc' => $deptAccPrefix,
                    ];
                }

                foreach ($itemunit as $key => $value) {
                    if ($key !== 'dept_acc') {
                        if (!isset($sumDataUnit[$deptAccPrefix][$key])) {
                            $sumDataUnit[$deptAccPrefix][$key] = 0;
                        }
                        $sumDataUnit[$deptAccPrefix][$key] += (float)$value;
                    }
                }
            }
            $uu = 0;
            foreach ($sumDataUnit as $key => $value) {

                $keyu = array_search((string) $value['dept_acc'], array_column($dcodedept, 'dept_acc'));
                
                if ($keyu != false) {
                    $keyyu = $keyu + 6;
                
                $sheetbydept->setCellValue('AE' . $keyyu, $value['sapril'])
                    ->setCellValue('AF' . $keyyu, $value['smei'])
                    ->setCellValue('AG' . $keyyu, $value['sjuni'])
                    ->setCellValue('AH' . $keyyu, $value['sjuli'])
                    ->setCellValue('AI' . $keyyu, $value['sagustus'])
                    ->setCellValue('AJ' . $keyyu, $value['sseptember'])
                    ->setCellValue('AK' . $keyyu, $value['soktober'])
                    ->setCellValue('AL' . $keyyu, $value['snovember'])
                    ->setCellValue('AM' . $keyyu, $value['sdecember'])
                    ->setCellValue('AN' . $keyyu, $value['sjanuari'])
                    ->setCellValue('AO' . $keyyu, $value['sfebruari'])
                    ->setCellValue('AP' . $keyyu, $value['smaret']);
                    # code...
                }
                $uu++;
            }

            $sumDataElectrik = [];

            foreach ($electrikdept as $itemelectrik) {
                $cekCode = substr($itemelectrik['acc_code'], 0, 5);
                $codePrefix = ($cekCode == '51191') ? $cekCode : substr($itemelectrik['acc_code'], 0, 4);
                $deptPrefix = $itemelectrik['dept'];
                $deptAccPrefix = $deptPrefix .'-'.$codePrefix;
                if (!isset($sumDataElectrik[$deptAccPrefix])) {
                    $sumDataElectrik[$deptAccPrefix] = [
                        'dept_acc' => $deptAccPrefix,
                    ];
                }

                foreach ($itemelectrik as $key => $value) {
                    if ($key !== 'dept_acc') {
                        if (!isset($sumDataElectrik[$deptAccPrefix][$key])) {
                            $sumDataElectrik[$deptAccPrefix][$key] = 0;
                        }
                        $sumDataElectrik[$deptAccPrefix][$key] += (float)$value;
                    }
                }
            }
            $ee = 0;
            foreach ($sumDataElectrik as $key => $value) {

                $keye = array_search((string) $value['dept_acc'], array_column($dcodedept, 'dept_acc'));
                if ($keye != false) {
                    $keyye = $keye + 6;
                    $sheetbydept->setCellValue('AR' . $keyye, $value['sapril'])
                        ->setCellValue('AS' . $keyye, $value['smei'])
                        ->setCellValue('AT' . $keyye, $value['sjuni'])
                        ->setCellValue('AU' . $keyye, $value['sjuli'])
                        ->setCellValue('AV' . $keyye, $value['sagustus'])
                        ->setCellValue('AW' . $keyye, $value['sseptember'])
                        ->setCellValue('AX' . $keyye, $value['soktober'])
                        ->setCellValue('AY' . $keyye, $value['snovember'])
                        ->setCellValue('AZ' . $keyye, $value['sdecember'])
                        ->setCellValue('BA' . $keyye, $value['sjanuari'])
                        ->setCellValue('BB' . $keyye, $value['sfebruari'])
                        ->setCellValue('BC' . $keyye, $value['smaret']);
                }
                $ee++;
            }
            $semua = [
                ['body'=> $sumDataBody,
                    'unit'=>$sumDataUnit,
                    'elektrik'=>$sumDataElectrik,
                ]
            ];
            // dd(json_encode($semua));
        }



        $filename = 'Export Request Budget' . date('d/m/Y');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'op' => 'ok',
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
        // dd($code);
    }

    public function exportDataCapex(Request $request)
    {
        ini_set('max_execution_time', 0);
        ob_start();

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load(public_path('files/TemplateCapexExport.xlsx'));
        // Set document properties


        // dd($data_master_expense);
        $spreadsheet->getProperties()->setCreator('Aiia')
            ->setLastModifiedBy('Aiia')
            ->setTitle('Office 2021 XLSX Aiia Document')
            ->setSubject('Office 2021 XLSX Aiia Document')
            ->setDescription('Office 2021 XLSX Aiia Document.')
            ->setKeywords('Office 2021 XLSX Aiia Document')
            ->setCategory('Office 2021 XLSX Aiia Document');

        $sheettype = $spreadsheet->getSheetByName('Summary CAPEX');
        $sheetdept = $spreadsheet->getSheetByName('Summary CAPEX By Dept');
        // dd($sheettype);
        $sc = 7;
        $type = array();
        $a = 0;
        foreach ($sheettype->getRowIterator() as $row) {

            $types = $sheettype->getCell("A$sc")->getValue();
            $type[$a]['type'] = $types;
            $sc++;
            $a++;
            if ($a === 7) {
                break;
            }
        }

        $sc = 6;
        $bydept = array();
        $a = 0;
        foreach ($sheetdept->getRowIterator() as $row) {

            $deptt = $sheetdept->getCell("B$sc")->getValue() ?? $sheetdept->getCell("B$sc")->getCalculatedValue();
            $typee = $sheetdept->getCell("D$sc")->getValue() ?? $sheetdept->getCell("D$sc")->getCalculatedValue();
            $break = $sheetdept->getCell("A$sc")->getValue() ?? $sheetdept->getCell("A$sc")->getCalculatedValue();
          
            if ($break == "") {
                break;
            }
            $bydept[$a]['dept_code'] = $deptt;
            $bydept[$a]['by_type'] = $typee;
            $sc++;
            $a++;

        }

        if (count((array) $bydept) > 0) {
            $i = 0;
            $x = 6;

            foreach ($bydept as $key => $row) {

                $deptcode = $row['dept_code'];
                $typename = $row['by_type'];

                $sheetdept->setCellValue('B' . $x, $deptcode);

                if ($deptcode != "" && $typename != "") {
                 

                    $AIIA = CapexRb::select(
                        'dept',
                        'type',
                        'owner_asset',
                        DB::raw('IFNULL(SUM(april), 0) as sapril'),
                        DB::raw('IFNULL(SUM(mei), 0) as smei'),
                        DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                        DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                        DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                        DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                        DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                        DB::raw('IFNULL(SUM(november), 0) as snovember'),
                        DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                        DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                        DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                        DB::raw('IFNULL(SUM(maret), 0) as smaret')
                    )
                        ->where('owner_asset', 'AIIA')
                        ->where('type', $typename)
                        ->where('dept', $deptcode)
                        ->groupBy('type', 'dept')
                        ->get();

                   
                    $sheetdept->setCellValue('L' . $x, $AIIA[0]['sapril'] ?? 0);
                    $sheetdept->setCellValue('M' . $x, $AIIA[0]['smei'] ?? 0);
                    $sheetdept->setCellValue('N' . $x, $AIIA[0]['sjuni'] ?? 0);
                    $sheetdept->setCellValue('O' . $x, $AIIA[0]['sjuli'] ?? 0);
                    $sheetdept->setCellValue('P' . $x, $AIIA[0]['saagustus'] ?? 0);
                    $sheetdept->setCellValue('Q' . $x, $AIIA[0]['sseptember'] ?? 0);
                    $sheetdept->setCellValue('R' . $x, $AIIA[0]['soktober'] ?? 0);
                    $sheetdept->setCellValue('S' . $x, $AIIA[0]['snovember'] ?? 0);
                    $sheetdept->setCellValue('T' . $x, $AIIA[0]['sdecember'] ?? 0);
                    $sheetdept->setCellValue('U' . $x, $AIIA[0]['sjanuari'] ?? 0);
                    $sheetdept->setCellValue('V' . $x, $AIIA[0]['sfebruari'] ?? 0);
                    $sheetdept->setCellValue('W' . $x, $AIIA[0]['smaret'] ?? 0);

                    $SUPPLIER = CapexRb::select(
                        'dept',
                        'type',
                        'owner_asset',
                        DB::raw('IFNULL(SUM(april), 0) as sapril'),
                        DB::raw('IFNULL(SUM(mei), 0) as smei'),
                        DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                        DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                        DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                        DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                        DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                        DB::raw('IFNULL(SUM(november), 0) as snovember'),
                        DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                        DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                        DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                        DB::raw('IFNULL(SUM(maret), 0) as smaret')
                    )
                        ->where('owner_asset', 'Supplier')
                        ->where('type', $typename)
                        ->where('dept', $deptcode)
                        ->groupBy('type', 'dept')
                        ->get();

                      
                    $sheetdept->setCellValue('Y' . $x, $SUPPLIER[0]['sapril'] ?? 0);
                    $sheetdept->setCellValue('Z' . $x, $SUPPLIER[0]['smei'] ?? 0);
                    $sheetdept->setCellValue('AA' . $x, $SUPPLIER[0]['sjuni'] ?? 0);
                    $sheetdept->setCellValue('AB' . $x, $SUPPLIER[0]['sjuli'] ?? 0);
                    $sheetdept->setCellValue('AC' . $x, $SUPPLIER[0]['saagustus'] ?? 0);
                    $sheetdept->setCellValue('AD' . $x, $SUPPLIER[0]['sseptember'] ?? 0);
                    $sheetdept->setCellValue('AE' . $x, $SUPPLIER[0]['soktober'] ?? 0);
                    $sheetdept->setCellValue('AF' . $x, $SUPPLIER[0]['snovember'] ?? 0);
                    $sheetdept->setCellValue('AG' . $x, $SUPPLIER[0]['sdecember'] ?? 0);
                    $sheetdept->setCellValue('AH' . $x, $SUPPLIER[0]['sjanuari'] ?? 0);
                    $sheetdept->setCellValue('AI' . $x, $SUPPLIER[0]['sfebruari'] ?? 0);
                    $sheetdept->setCellValue('AJ' . $x, $SUPPLIER[0]['smaret'] ?? 0);

                    $LAINNYA = CapexRb::select(
                        'dept',
                        'type',
                        'owner_asset',
                        DB::raw('IFNULL(SUM(april), 0) as sapril'),
                        DB::raw('IFNULL(SUM(mei), 0) as smei'),
                        DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                        DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                        DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                        DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                        DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                        DB::raw('IFNULL(SUM(november), 0) as snovember'),
                        DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                        DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                        DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                        DB::raw('IFNULL(SUM(maret), 0) as smaret')
                    )
                        ->where('owner_asset', 'Lainnya')
                        ->where('type', $typename)
                        ->where('dept', $deptcode)
                        ->groupBy('type', 'dept')
                        ->get();

                        // if(count($LAINNYA) >0){
                        //     dd($LAINNYA);
                        // }
                    $sheetdept->setCellValue('AL' . $x, $LAINNYA[0]['sapril'] ?? 0);
                    $sheetdept->setCellValue('AM' . $x, $LAINNYA[0]['smei'] ?? 0);
                    $sheetdept->setCellValue('AN' . $x, $LAINNYA[0]['sjuni'] ?? 0);
                    $sheetdept->setCellValue('AO' . $x, $LAINNYA[0]['sjuli'] ?? 0);
                    $sheetdept->setCellValue('AP' . $x, $LAINNYA[0]['saagustus'] ?? 0);
                    $sheetdept->setCellValue('AQ' . $x, $LAINNYA[0]['sseptember'] ?? 0);
                    $sheetdept->setCellValue('AR' . $x, $LAINNYA[0]['soktober'] ?? 0);
                    $sheetdept->setCellValue('AS' . $x, $LAINNYA[0]['snovember'] ?? 0);
                    $sheetdept->setCellValue('AT' . $x, $LAINNYA[0]['sdecember'] ?? 0);
                    $sheetdept->setCellValue('AU' . $x, $LAINNYA[0]['sjanuari'] ?? 0);
                    $sheetdept->setCellValue('AV' . $x, $LAINNYA[0]['sfebruari'] ?? 0);
                    $sheetdept->setCellValue('AW' . $x, $LAINNYA[0]['smaret'] ?? 0);
                }

                $i++;
                $x++;
            }
        }

        if (count((array) $type) > 0) {
            $i = 0;
            $x = 7;
            $dcodesheettype = array();
            foreach ($type as $key => $row) {
                // dd($row);
                $dcodesheettype[$i]['type'] = $row['type'];
                $sheettype->setCellValue('A' . $x, $row['type']);

                $AIIA = CapexRb::select(
                    'type',
                    'owner_asset',
                    DB::raw('IFNULL(SUM(april), 0) as sapril'),
                    DB::raw('IFNULL(SUM(mei), 0) as smei'),
                    DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                    DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                    DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                    DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                    DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                    DB::raw('IFNULL(SUM(november), 0) as snovember'),
                    DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                    DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                    DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                    DB::raw('IFNULL(SUM(maret), 0) as smaret')
                )
                    ->where('owner_asset', 'AIIA')
                    ->where('type', $row['type'])
                    ->groupBy('type')
                    ->get();
                // dd($AIIA[0]['smei']);

                $sheettype->setCellValue('I' . $x, $AIIA[0]['sapril'] ?? 0);
                $sheettype->setCellValue('J' . $x, $AIIA[0]['smei'] ?? 0);
                $sheettype->setCellValue('K' . $x, $AIIA[0]['sjuni'] ?? 0);
                $sheettype->setCellValue('L' . $x, $AIIA[0]['sjuli'] ?? 0);
                $sheettype->setCellValue('M' . $x, $AIIA[0]['sagustus'] ?? 0);
                $sheettype->setCellValue('N' . $x, $AIIA[0]['sseptember'] ?? 0);
                $sheettype->setCellValue('O' . $x, $AIIA[0]['soktober'] ?? 0);
                $sheettype->setCellValue('P' . $x, $AIIA[0]['snovember'] ?? 0);
                $sheettype->setCellValue('Q' . $x, $AIIA[0]['sdecember'] ?? 0);
                $sheettype->setCellValue('R' . $x, $AIIA[0]['sjanuari'] ?? 0);
                $sheettype->setCellValue('S' . $x, $AIIA[0]['sfebruari'] ?? 0);
                $sheettype->setCellValue('T' . $x, $AIIA[0]['smaret'] ?? 0);

                $SUPPLIER = CapexRb::select(
                    'type',
                    'owner_asset',
                    DB::raw('IFNULL(SUM(april), 0) as sapril'),
                    DB::raw('IFNULL(SUM(mei), 0) as smei'),
                    DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                    DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                    DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                    DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                    DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                    DB::raw('IFNULL(SUM(november), 0) as snovember'),
                    DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                    DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                    DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                    DB::raw('IFNULL(SUM(maret), 0) as smaret')
                )
                    ->where('owner_asset', 'Supplier')
                    ->where('type', $row['type'])
                    ->groupBy('type')
                    ->get();
                // dd($AIIA[0]['smei']);

                $sheettype->setCellValue('V' . $x, $SUPPLIER[0]['sapril'] ?? 0);
                $sheettype->setCellValue('W' . $x, $SUPPLIER[0]['smei'] ?? 0);
                $sheettype->setCellValue('X' . $x, $SUPPLIER[0]['sjuni'] ?? 0);
                $sheettype->setCellValue('Y' . $x, $SUPPLIER[0]['sjuli'] ?? 0);
                $sheettype->setCellValue('Z' . $x, $SUPPLIER[0]['sagustus'] ?? 0);
                $sheettype->setCellValue('AA' . $x, $SUPPLIER[0]['sseptember'] ?? 0);
                $sheettype->setCellValue('AB' . $x, $SUPPLIER[0]['soktober'] ?? 0);
                $sheettype->setCellValue('AC' . $x, $SUPPLIER[0]['snovember'] ?? 0);
                $sheettype->setCellValue('AD' . $x, $SUPPLIER[0]['sdecember'] ?? 0);
                $sheettype->setCellValue('AE' . $x, $SUPPLIER[0]['sjanuari'] ?? 0);
                $sheettype->setCellValue('AF' . $x, $SUPPLIER[0]['sfebruari'] ?? 0);
                $sheettype->setCellValue('AG' . $x, $SUPPLIER[0]['smaret'] ?? 0);


                $LAINNYA = CapexRb::select(
                    'type',
                    'owner_asset',
                    DB::raw('IFNULL(SUM(april), 0) as sapril'),
                    DB::raw('IFNULL(SUM(mei), 0) as smei'),
                    DB::raw('IFNULL(SUM(juni), 0) as sjuni'),
                    DB::raw('IFNULL(SUM(juli), 0) as sjuli'),
                    DB::raw('IFNULL(SUM(agustus), 0) as sagustus'),
                    DB::raw('IFNULL(SUM(september), 0) as sseptember'),
                    DB::raw('IFNULL(SUM(oktober), 0) as soktober'),
                    DB::raw('IFNULL(SUM(november), 0) as snovember'),
                    DB::raw('IFNULL(SUM(december), 0) as sdecember'),
                    DB::raw('IFNULL(SUM(januari), 0) as sjanuari'),
                    DB::raw('IFNULL(SUM(februari), 0) as sfebruari'),
                    DB::raw('IFNULL(SUM(maret), 0) as smaret')
                )
                    ->where('owner_asset', 'Lainnya')
                    ->where('type', $row['type'])
                    ->groupBy('type')
                    ->get();
                // dd($AIIA[0]['smei']);

                $sheettype->setCellValue('AI' . $x, $LAINNYA[0]['sapril'] ?? 0);
                $sheettype->setCellValue('AJ' . $x, $LAINNYA[0]['smei'] ?? 0);
                $sheettype->setCellValue('AK' . $x, $LAINNYA[0]['sjuni'] ?? 0);
                $sheettype->setCellValue('AL' . $x, $LAINNYA[0]['sjuli'] ?? 0);
                $sheettype->setCellValue('AM' . $x, $LAINNYA[0]['sagustus'] ?? 0);
                $sheettype->setCellValue('AN' . $x, $LAINNYA[0]['sseptember'] ?? 0);
                $sheettype->setCellValue('AO' . $x, $LAINNYA[0]['soktober'] ?? 0);
                $sheettype->setCellValue('AP' . $x, $LAINNYA[0]['snovember'] ?? 0);
                $sheettype->setCellValue('AQ' . $x, $LAINNYA[0]['sdecember'] ?? 0);
                $sheettype->setCellValue('AR' . $x, $LAINNYA[0]['sjanuari'] ?? 0);
                $sheettype->setCellValue('AS' . $x, $LAINNYA[0]['sfebruari'] ?? 0);
                $sheettype->setCellValue('AT' . $x, $LAINNYA[0]['smaret'] ?? 0);

                $i++;
                $x++;
            }





            // dd($body);
        }



        $filename = 'Export Request Budget ' . date('d/m/Y');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'op' => 'ok',
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
        // dd($code) ini coment;
    }
}
