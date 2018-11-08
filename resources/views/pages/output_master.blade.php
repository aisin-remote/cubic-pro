@extends('layouts.master')

@section('title')
  Upload BOM Finish Good
@endsection

@section('content')

@php($active = 'output_master')


<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Output Master</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Output Master
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <form  method="post">
            @csrf
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Fiscal Year</label>
                    <input type="text" name="fiscal_year" class="form-control datepicker" placeholder="Tahun" value="{{ Carbon\Carbon::now()->format('Y') }}">
                </div>
            </div>


            <div class="col-md-4 m-t-30">
                <button type="submit" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-search"></i> Cari</button>
            </div>
        </form>
    <!-- end row -->


    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Sales Amount</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::sumSales('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSales('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::sumSalesTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::sumSalesTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>

                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>

     <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Product</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>1,200,000</td>
                            <td>1,500,000</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>1,000,000</th>
                            <th>4,000,000</th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Product</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>1,200,000</td>
                            <td>1,500,000</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>1,000,000</th>
                            <th>4,000,000</th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Total Material</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>1,200,000</td>
                            <td>1,500,000</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>1,000,000</th>
                            <th>4,000,000</th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Plastic Material</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterial('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumPlasticMaterialTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Ingot Material</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterial('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumIngotMaterialTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">CKD</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumCKD('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKD('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumCKDTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">CKD Import Duty</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDuty('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumCKDImportDutyTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Import Part</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumImportPart('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPart('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumImportPartTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportPartTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Import Duty</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumImportDuty('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDuty('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumImportDutyTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumImportDutyTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Inklaring CKD</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkd('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringCkdTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">Inklaring Import Part</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPart('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumInklaringImportPartTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><span class="text-uppercase">local Part</span></th>
                            <th><span class="text-uppercase">Product Code</span></th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>feb</th>
                            <th>mar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\System::configMultiply('product_code') as $code)
                        <tr>
                            <td>{{ $code['text'] }}</td>
                            <td>{{ $code['id'] }}</td>
                            <td>{{ App\SalesData::SumLocalPart('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPart('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal1( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <td>{{ App\SalesData::SumLocalPartTotal('apr', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('may', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('june', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('july', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('august', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('sep', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('okt', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('nov', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('dec', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('jan', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('feb', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal('march', Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                            <td>{{ App\SalesData::SumLocalPartTotal2( Carbon\Carbon::now()->format('Y'), $code['id']) }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    


 

@endsection

@push('js')
<script src="{{ url('assets/js/pages/output_master.js') }}"></script>
@endpush
