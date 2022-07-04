<?php

namespace App\Http\Controllers;

use DB;
use file;
use Config;
use Response;
use App\Period;
use DataTables;
use App\CapexRb;
use App\SalesRb;
use App\ExpenseRb;
use Carbon\Carbon;
use App\MasterCode;
use App\DmaterialRb;
use App\Exports\RbExport;
use App\Imports\CapexImport;
use App\Imports\SalesImport;
// use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Helpers\ImportBinder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DirectMaterialImport;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class RequestController extends Controller
{
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
        $Sls = SalesRb::select('acc_code', 'acc_name', 'group', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret', 'fy_first', 'fy_second', 'fy_total')->get();
        // $sls = $slsx->get();
        return DataTables::of($Sls)->toJson();
        // ->editColumn("status", function ($capex) {
        //     // $expense->is_closed="ABS";
        //     if ($capex->status=='0'){
        //         return "Underbudget";
        //     }else{
        //         return "Overbudget";
        //     }
        // })
        // ->editColumn("is_closed", function ($capex) {
        //     // $expense->is_closed="ABS";
        //     if ($capex->is_closed=='0'){
        //         return "Open";

        //     }else{
        //         return "Closed";
        //     }
        // })
        // ->toJson();
    }

    public function slsimport(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $ext  = $file->getClientOriginalExtension();

        /** Upload file ke storage public */
        $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        if($ext !== 'csv') {
            $file = $this->parseXlsx($file, $name, 0);
        }

        Excel::import(new SalesImport, $file);

        $res = [
            'title'   => 'Sukses',
            'type'    => 'success',
            'message' => 'Data berhasil di Upload!'
        ];

        /** Hapus files */
        $this->deleteFiles($name);

        return redirect()->route('sales.view')->with($res);

        // $data = [];
        // if ($request->hasFile('file')) {
        //     $datas = $this->getCsvFile(public_path('storage/uploads/' . $name));

        //     if ($datas->first()->has('acc_code')) {
        //         foreach ($datas as $data) {

        //             // $salesrb = SalesRb::updateOrCreate(
        //             //     ['acc_name' => $data->acc_name, 'group' => $data->group],
        //             //     ['april' => $data->apr]
        //             //         );
        //             $cek = SalesRb::where('acc_name', $data->acc_name)->where('group', $data->group)->first();

        //             if ($cek) {
        //                 $salesrb = SalesRb::where('acc_name', $data->acc_name)->where('group', $data->group)
        //                     ->update([
        //                         'april' => $data->apr,
        //                         'mei'       => $data->may,
        //                         'juni'      => $data->jun,
        //                         'juli'      => $data->jul,
        //                         'agustus'   => $data->aug,
        //                         'september' => $data->sept,
        //                         'oktober'   => $data->oct,
        //                         'november'  => $data->nov,
        //                         'december'  => $data->dec,
        //                         'januari'   => $data->jan,
        //                         'februari'  => $data->feb,
        //                         'maret'     => $data->mar,
        //                         'fy_first'  => $data->fy_2022_1st,
        //                         'fy_second' => $data->fy_2022_2nd,
        //                         'fy_total'  => $data->fy_2022_total
        //                     ]);
        //             } else {

        //                 $salesrb                    = new SalesRb;
        //                 $salesrb->acc_code          = $data->acc_code;
        //                 $salesrb->acc_name          = $data->acc_name;
        //                 $salesrb->group             = $data->group;
        //                 $salesrb->april             = $data->apr;
        //                 $salesrb->mei               = $data->may;
        //                 $salesrb->juni              = $data->jun;
        //                 $salesrb->juli              = $data->jul;
        //                 $salesrb->agustus           = $data->aug;
        //                 $salesrb->september         = $data->sept;
        //                 $salesrb->oktober           = $data->oct;
        //                 $salesrb->november          = $data->nov;
        //                 $salesrb->december          = $data->dec;
        //                 $salesrb->januari           = $data->jan;
        //                 $salesrb->februari          = $data->feb;
        //                 $salesrb->maret             = $data->mar;
        //                 $salesrb->fy_first          = $data->fy_2022_1st;
        //                 $salesrb->fy_second         = $data->fy_2022_2nd;
        //                 $salesrb->fy_total          = $data->fy_2022_total;
        //                 $salesrb->save();
        //             }
        //             // else {

        //             //    return redirect()
        //             //            ->route('sales.view')
        //             //            ->with(
        //             //                [
        //             //                    'title' => 'Error',
        //             //                    'type' => 'error',
        //             //                    'message' => 'Bad Request, Gagal Upload!'
        //             //                ]
        //             //            );
        //             // }


        //         }

        //         $res = [
        //             'title'             => 'Sukses',
        //             'type'              => 'success',
        //             'message'           => 'Data berhasil di Upload!'
        //         ];
        //         Storage::delete('public/uploads/' . $name);
        //         return redirect()
        //             ->route('sales.view')
        //             ->with($res);
        //     } else {

        //         Storage::delete('public/uploads/' . $name);

        //         return redirect()
        //             ->route('sales.view')
        //             ->with(
        //                 [
        //                     'title' => 'Error',
        //                     'type' => 'error',
        //                     'message' => 'Format Buruk!'
        //                 ]
        //             );
        //     }
        // }
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
        $dm = DmaterialRb::select('acc_code', 'acc_name', 'group', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret', 'fy_first', 'fy_second', 'fy_total')->get();

        return DataTables::of($dm)->toJson();
    }

    public function materialimport(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $ext  = $file->getClientOriginalExtension();

        /** Upload file ke storage public */
        $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        if($ext !== 'csv') {
            $file = $this->parseXlsx($file, $name, 0);
        }

        Excel::import(new DirectMaterialImport, $file);

        $res = [
            'title'   => 'Sukses',
            'type'    => 'success',
            'message' => 'Data berhasil di Upload!'
        ];

        /** Hapus files */
        $this->deleteFiles($name);

        return redirect()->route('material.view')->with($res);

        // $data = [];
        // if ($request->hasFile('file')) {
        //     $datas = $this->getCsvFile(public_path('storage/uploads/' . $name . '.csv'));

        //     if ($datas->first()->has('acc_code')) {
        //         foreach ($datas as $data) {
        //             $cek = DmaterialRb::where('acc_name', $data->acc_name)->where('group', $data->group)->first();

        //             if ($cek) {
        //                 $materialrb = SalesRb::where('acc_name', $data->acc_name)->where('group', $data->group)
        //                     ->update([
        //                         'april' => $data->apr,
        //                         'mei'       => $data->may,
        //                         'juni'      => $data->jun,
        //                         'juli'      => $data->jul,
        //                         'agustus'   => $data->aug,
        //                         'september' => $data->sept,
        //                         'oktober'   => $data->oct,
        //                         'november'  => $data->nov,
        //                         'december'  => $data->dec,
        //                         'januari'   => $data->jan,
        //                         'februari'  => $data->feb,
        //                         'maret'     => $data->mar,
        //                         'fy_first'  => $data->fy_2022_1st,
        //                         'fy_second' => $data->fy_2022_2nd,
        //                         'fy_total'  => $data->fy_2022_total
        //                     ]);
        //             } else {
        //                 $materialrb                    = new DmaterialRb;
        //                 $materialrb->acc_code          = $data->acc_code;
        //                 $materialrb->acc_name          = $data->acc_name;
        //                 $materialrb->group             = $data->group;
        //                 $materialrb->april             = $data->apr;
        //                 $materialrb->mei               = $data->may;
        //                 $materialrb->juni              = $data->jun;
        //                 $materialrb->juli              = $data->jul;
        //                 $materialrb->agustus           = $data->aug;
        //                 $materialrb->september         = $data->sept;
        //                 $materialrb->oktober           = $data->oct;
        //                 $materialrb->november          = $data->nov;
        //                 $materialrb->december          = $data->dec;
        //                 $materialrb->januari           = $data->jan;
        //                 $materialrb->februari          = $data->feb;
        //                 $materialrb->maret             = $data->mar;
        //                 $materialrb->fy_first          = $data->fy_2022_1st;
        //                 $materialrb->fy_second         = $data->fy_2022_2nd;
        //                 $materialrb->fy_total          = $data->fy_2022_total;
        //                 $materialrb->save();
        //             }
        //             // else {

        //             //    return redirect()
        //             //            ->route('material.view')
        //             //            ->with(
        //             //                [
        //             //                    'title' => 'Error',
        //             //                    'type' => 'error',
        //             //                    'message' => 'Bad Request, Gagal Upload!'
        //             //                ]
        //             //            );

        //             // }

        //         }

        //         $res = [
        //             'title'             => 'Sukses',
        //             'type'              => 'success',
        //             'message'           => 'Data berhasil di Upload!'
        //         ];
        //         Storage::delete('public/uploads/' . $name);
        //         return redirect()
        //             ->route('material.view')
        //             ->with($res);
        //     } else {

        //         Storage::delete('public/uploads/' . $name);

        //         return redirect()
        //             ->route('material.view')
        //             ->with([
        //                 'title' => 'Error',
        //                 'type' => 'error',
        //                 'message' => 'Format Buruk!'
        //             ]);
        //     }
        // }
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
        return view('pages.request_budget.cpxindex');
    }

    public function getDataCPX(Request $request)
    {
        $cpx = CapexRb::select('budget_no', 'line', 'profit_center', 'profit_center_code', 'cost_center', 'type', 'project_name', 'import_domestic', 'items_name', 'equipment', 'qty', 'curency', 'original_price', 'exchange_rate', 'price', 'sop', 'first_dopayment_term', 'first_dopayment_amount', 'final_payment_term', 'final_payment_amount', 'owner_asset', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret')->get();
        // $sls = $slsx->get();
        return DataTables::of($cpx)->toJson();
    }

    public function capeximport(Request $request)
    {
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $ext  = $file->getClientOriginalExtension();

        /** Upload file ke storage public */
        $file->storeAs('public/uploads', $name);

        /** Jika bukan format csv */
        if($ext !== 'csv') {
            $file = $this->parseXlsx($file, $name, 0);
        }

        Excel::import(new CapexImport, $file);

        $res = [
            'title'   => 'Sukses',
            'type'    => 'success',
            'message' => 'Data berhasil di Upload!'
        ];

        /** Hapus files */
        $this->deleteFiles($name);

        return redirect()->route('capex.view')->with($res);

        // $data = [];
        // if ($request->hasFile('file')) {
        //     $datas = $this->getCsvFile2(public_path('storage/uploads/' . $name));

        //     if ($datas->first()->has('budget_no')) {
        //         foreach ($datas as $data) {

        //             $cek = CapexRb::where('budget_no', $data->budget_no)->where('line', $data->line_or_dept)->first();

        //             if (!empty($data->budget_no)) {

        //                 if ($cek) {

        //                     $capexrb = CapexRb::where('budget_no', $data->budget_no)->where('line', $data->line_or_dept)
        //                         ->update([
        //                             'profit_center' => $data->profit_center,
        //                             'profit_center_code'    => $data->profit_center_code,
        //                             'cost_center'           => $data->cost_center,
        //                             'type'                  => $data->type,
        //                             'project_name'          => $data->project_name,
        //                             'import_domestic'       => $data->importdomestic,
        //                             'items_name'            => $data->items_name,
        //                             'equipment'             => $data->equipment,
        //                             'qty'                   => $data->qty,
        //                             'curency'               => $data->curency,
        //                             'original_price'        => $data->original_price,
        //                             'exchange_rate'         => $data->exchange_rate,
        //                             'price'                 => $data->price,
        //                             'sop'                   => $data->sop,
        //                             'first_dopayment_term'  => $data->first_down_payment_term,
        //                             'first_dopayment_amount' => $data->first_down_payment_amount,
        //                             'final_payment_term'    => $data->final_payment_term,
        //                             'final_payment_amount'  => $data->final_payment_amount,
        //                             'owner_asset'           => $data->owner_asset,
        //                             'april'                 => $data->apr,
        //                             'mei'                   => $data->may,
        //                             'juni'                  => $data->jun,
        //                             'juli'                  => $data->jul,
        //                             'agustus'               => $data->aug,
        //                             'september'             => $data->sep,
        //                             'oktober'               => $data->oct,
        //                             'november'              => $data->nov,
        //                             'december'              => $data->dec,
        //                             'januari'               => $data->jan,
        //                             'februari'              => $data->feb,
        //                             'maret'                 => $data->mar
        //                         ]);
        //                 } else {

        //                     $capexrb                         = new CapexRb;
        //                     $capexrb->dept                   = $data->dept;
        //                     $capexrb->budget_no              = $data->budget_no;
        //                     $capexrb->line                   = $data->line_or_dept;
        //                     $capexrb->profit_center          = $data->profit_center;
        //                     $capexrb->profit_center_code     = $data->profit_center_code;
        //                     $capexrb->cost_center            = $data->cost_center;
        //                     $capexrb->type                   = $data->type;
        //                     $capexrb->project_name           = $data->project_name;
        //                     $capexrb->import_domestic        = $data->importdomestic;
        //                     $capexrb->items_name             = $data->items_name;
        //                     $capexrb->equipment              = $data->equipment;
        //                     $capexrb->qty                    = $data->qty;
        //                     $capexrb->curency                = $data->curency;
        //                     $capexrb->original_price         = $data->original_price;
        //                     $capexrb->exchange_rate          = $data->exchange_rate;
        //                     $capexrb->price                  = $data->price;
        //                     $capexrb->sop                    = $data->sop;
        //                     $capexrb->first_dopayment_term   = $data->first_down_payment_term;
        //                     $capexrb->first_dopayment_amount = $data->first_down_payment_amount;
        //                     $capexrb->final_payment_term     = $data->final_payment_term;
        //                     $capexrb->final_payment_amount   = $data->final_payment_amount;
        //                     $capexrb->owner_asset            = $data->owner_asset;
        //                     $capexrb->april                  = $data->apr;
        //                     $capexrb->mei                    = $data->may;
        //                     $capexrb->juni                   = $data->jun;
        //                     $capexrb->juli                   = $data->jul;
        //                     $capexrb->agustus                = $data->aug;
        //                     $capexrb->september              = $data->sep;
        //                     $capexrb->oktober                = $data->oct;
        //                     $capexrb->november               = $data->nov;
        //                     $capexrb->december               = $data->dec;
        //                     $capexrb->januari                = $data->jan;
        //                     $capexrb->februari               = $data->feb;
        //                     $capexrb->maret                  = $data->mar;
        //                     // $capexrb->fy                     = $data->fy_2022;
        //                     $capexrb->save();
        //                 }
        //                 // else {

        //                 //    return redirect()
        //                 //            ->route('capex.view')
        //                 //            ->with(
        //                 //                [
        //                 //                    'title' => 'Error',
        //                 //                    'type' => 'error',
        //                 //                    'message' => 'Bad Request, Gagal Upload!'
        //                 //                ]
        //                 //            );

        //                 // }

        //             }
        //         }

        //         $res = [
        //             'title'             => 'Sukses',
        //             'type'              => 'success',
        //             'message'           => 'Data berhasil di Upload!'
        //         ];
        //         Storage::delete('public/uploads/' . $name);
        //         return redirect()
        //             ->route('capex.view')
        //             ->with($res);
        //     } else {

        //         Storage::delete('public/uploads/' . $name);

        //         return redirect()
        //             ->route('capex.view')
        //             ->with(
        //                 [
        //                     'title' => 'Error',
        //                     'type' => 'error',
        //                     'message' => 'Format Buruk!'
        //                 ]
        //             );
        //     }
        // }
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
        return view('pages.request_budget.expindex');
    }

    public function getDataEXP(Request $request)
    {
        $exp = ExpenseRb::select('budget_no', 'group', 'line', 'profit_center', 'profit_center_code', 'cost_center', 'acc_code', 'project_name', 'equipment_name', 'import_domestic', 'qty', 'cur', 'price_per_qty', 'exchange_rate', 'budget_before', 'cr', 'budgt_aft_cr', 'po', 'gr', 'sop', 'first_dopayment_term', 'first_dopayment_amount', 'final_payment_term', 'final_payment_amount', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'december', 'januari', 'februari', 'maret')->get();
        // $sls = $slsx->get();
        return DataTables::of($exp)->toJson();
    }

    public function expenseimport(Request $request)
    {

        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $name);

        $data = [];
        if ($request->hasFile('file')) {
            $datas = $this->getCsvFile3(public_path('storage/uploads/' . $name));

            if ($datas->first()->has('budget_no')) {
                foreach ($datas as $data) {

                    $cek = ExpenseRb::where('budget_no', $data->budget_no)->where('group', $data->group)->where('line', $data->line_or_dept)->first();

                    if (!empty($data->budget_no)) {
                        if ($cek) {

                            $expenserb = ExpenseRb::where('budget_no', $data->budget_no)->where('group', $data->group)->where('line', $data->line_or_dept)
                                ->update([
                                    'profit_center' => $data->profit_center,
                                    'profit_center_code'    => $data->profit_center_code,
                                    'cost_center'           => $data->cost_center,
                                    'acc_code'              => $data->account_code,
                                    'project_name'          => $data->project_name,
                                    'equipment_name'        => $data->equipment_name,
                                    'import_domestic'       => $data->importdomestic,
                                    'qty'                   => $data->qty,
                                    'cur'                   => $data->curr,
                                    'price_per_qty'         => $data->price_per_qty,
                                    'exchange_rate'         => $data->exchange_rate,
                                    'budget_before'         => $data->budget_before_cr,
                                    'cr'                    => $data->cr,
                                    'budgt_aft_cr'          => $data->budget_after_cr,
                                    'po'                    => $data->po,
                                    'gr'                    => $data->gr,
                                    'sop'                   => $data->sop,
                                    'first_dopayment_term'  => $data->first_down_payment_term,
                                    'first_dopayment_amount' => $data->first_down_payment_amount,
                                    'final_payment_term'    => $data->final_payment_term,
                                    'final_payment_amount'  => $data->final_payment_amount,
                                    'april'                 => $data->apr,
                                    'mei'                   => $data->may,
                                    'juni'                  => $data->jun,
                                    'juli'                  => $data->jul,
                                    'agustus'               => $data->aug,
                                    'september'             => $data->sep,
                                    'oktober'               => $data->oct,
                                    'november'              => $data->nov,
                                    'december'              => $data->dec,
                                    'januari'               => $data->jan,
                                    'februari'              => $data->feb,
                                    'maret'                 => $data->mar,
                                    'checking'              => $data->checking
                                ]);
                        } else {

                            $expenserb                         = new ExpenseRb;
                            $expenserb->budget_no              = $data->budget_no;
                            $expenserb->group                  = $data->group;
                            $expenserb->line                   = $data->line_or_dept;
                            $expenserb->profit_center          = $data->profit_center;
                            $expenserb->profit_center_code     = $data->profit_center_code;
                            $expenserb->cost_center            = $data->cost_center;
                            $expenserb->acc_code               = $data->account_code;
                            $expenserb->project_name           = $data->project_name;
                            $expenserb->equipment_name         = $data->equipment_name;
                            $expenserb->import_domestic        = $data->importdomestic;
                            $expenserb->qty                    = $data->qty;
                            $expenserb->cur                    = $data->curr;
                            $expenserb->price_per_qty          = $data->price_per_qty;
                            $expenserb->exchange_rate          = $data->exchange_rate;
                            $expenserb->budget_before          = $data->budget_before_cr;
                            $expenserb->cr                     = $data->cr;
                            $expenserb->budgt_aft_cr           = $data->budget_after_cr;
                            $expenserb->po                     = $data->po;
                            $expenserb->gr                     = $data->gr;
                            $expenserb->sop                    = $data->sop;
                            $expenserb->first_dopayment_term   = $data->first_down_payment_term;
                            $expenserb->first_dopayment_amount = $data->first_down_payment_amount;
                            $expenserb->final_payment_term     = $data->final_payment_term;
                            $expenserb->final_payment_amount   = $data->final_payment_amount;
                            $expenserb->april                  = $data->apr;
                            $expenserb->mei                    = $data->may;
                            $expenserb->juni                   = $data->jun;
                            $expenserb->juli                   = $data->jul;
                            $expenserb->agustus                = $data->aug;
                            $expenserb->september              = $data->sep;
                            $expenserb->oktober                = $data->oct;
                            $expenserb->november               = $data->nov;
                            $expenserb->december               = $data->dec;
                            $expenserb->januari                = $data->jan;
                            $expenserb->februari               = $data->feb;
                            $expenserb->maret                  = $data->mar;
                            $expenserb->checking               = $data->checking;
                            $expenserb->save();
                        }
                        // else {

                        //    return redirect()
                        //            ->route('expense.view')
                        //            ->with(
                        //                [
                        //                    'title' => 'Error',
                        //                    'type' => 'error',
                        //                    'message' => 'Bad Request, Gagal Upload!'
                        //                ]
                        //            );

                        // }
                    }
                }
                $res = [
                    'title'             => 'Sukses',
                    'type'              => 'success',
                    'message'           => 'Data berhasil di Upload!'
                ];
                Storage::delete('public/uploads/' . $name);
                return redirect()
                    ->route('expense.view')
                    ->with($res);
            } else {

                Storage::delete('public/uploads/' . $name);

                return redirect()
                    ->route('expense.view')
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

    public function  draw($file, $data, $master_account_code, $start)
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
    public function exporttotemplate(RbExport $export)
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
            ->groupBy('acc_code')
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
            ->groupBy('acc_code')
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


        Excel::load('/public/files/AIIA-PNL.xlsx',  function ($file) use ($salesB, $salesU, $dmB, $dmU, $master_code, $codes, $codesU) {
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
        $datas = Excel::load($file, function ($reader) {})->get();

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

        foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
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
    protected function deleteFiles($name) : void
    {
        unlink(public_path('storage/uploads/' . $name));
        unlink(public_path('storage/uploads/' . $name . '.csv'));
    }
}
