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
        <form action="{{ route('output_master.index') }}" method="get">
            <div class="form-group">
                <div class="col-md-2">
                    <div class="form-group">
                        <input name="fiscal_year" id="tanggal" class="form-control datepicker-year" required="required" placeholder="yyyy" aria-invalid="false" value="{{ !empty(request()->fiscal_year) ? request()->fiscal_year : \Carbon\Carbon::now()->format('Y') }}">
                    </div>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Filter</button>
                </div>

                <div class="col-md-1">
                        <a class="btn btn-primary btn-bordered waves-effect waves-light" href="{{ route('output_master.download', request()->all()) }}">Unduh PDF</a>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                <ul class="nav nav-tabs tabs-bordered nav-justified">
                    <li class="active">
                        <a href="#sales_amount" data-toggle="tab" aria-expanded="true">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Output Sales Amount</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#material" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Material (IDR)</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#material-sales" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-envelope-o"></i></span>
                            <span class="hidden-xs">Material (%Sales)</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#group" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-cog"></i></span>
                            <span class="hidden-xs">Group</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="sales_amount">
                        <div class="col-md-12 table-responsive">
                            <table class="table  table-bordered">
                                <thead>
                                    <tr style="text-align:center">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach (App\System::configMultiply('product_code') as $code)

                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('apr', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('may', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('june', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('july', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('august',$fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('sep', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('okt', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('nov', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('dec', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('jan', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('feb', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('march', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumSales('total', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="text-align:right">
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('apr', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('may', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('june', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('july', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('august', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('sep', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('okt', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('nov', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('dec', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('jan', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('feb', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('march', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal('total', $fiscal_year, $code['id']),0,',','.') }}</td>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="material">
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)

                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('apr', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('may', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('june', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('july', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('august', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('sep', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('okt', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('nov', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('dec', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('jan', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('feb', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumTotalMaterial('march', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::SumTotalMaterial('total', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <td>{{ number_format(collect($total)->sum('apr'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('may'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('june'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('july'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('august'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('sep'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('okt'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('nov'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('dec'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('jan'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('feb'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('march'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('total'), 0,',','.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="material-sales">
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)

                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('apr', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('may', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('june', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('july', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('august', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('sep', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('okt', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('nov', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('dec', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('jan', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('feb', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('march', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercTotalMaterial('total', $fiscal_year, $code['id']),2, ',' , '.') }} %</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>                                        
                                        
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('apr', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('may', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('june', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('july', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('august', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('sep', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('okt', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('nov', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('dec', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('jan', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('feb', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('march', $fiscal_year),2, ',' , '.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::sumPercTotalMaterial('total', $fiscal_year),2,',','.') }} %</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="group">
                        @foreach (App\System::config('group_material') as $group_material) 
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="text-align:center">
                                        <th><span class="text-uppercase">{{ $group_material['text'] }}</span></th>
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
                                        <th>Peb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::materialGroup($group_material['text'], 'total', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="text-align:right">
                                        <th colspan="2" class="text-right">Total</th>
                                        <td>{{ number_format(collect($total)->sum('apr'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('may'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('june'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('july'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('august'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('sep'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('okt'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('nov'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('dec'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('jan'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('feb'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('march'), 0,',','.') }}</td>
                                        <td>{{ number_format(collect($total)->sum('total'), 0,',','.') }}</td>
                                    </tr>
                                    <tr style="text-align:right">
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'apr', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'may', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'june', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'july', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'august', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'sep', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'okt', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'nov', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'dec', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'jan', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'feb', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'march', $fiscal_year), 2,',','.') }} %</td>
                                        <td style="text-align:right">{{ number_format(App\SalesData::PercGroupMaterial($group_material['text'], 'total', $fiscal_year), 2,',','.') }} %</td>
                                    </tr>
                                </tfoot>
                            </table>
                        
                        </div>
                        @endforeach
                    </div>    
                </div>
            </div>
        </div>  
    </div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/output_master.js') }}"></script>
@endpush
