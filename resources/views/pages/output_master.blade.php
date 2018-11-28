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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach (App\System::configMultiply('product_code') as $code)

                                    @php($sales_amount[$code['id']]['apr'] = App\SalesData::sumSales('apr', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['may'] = App\SalesData::sumSales('may', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['june'] = App\SalesData::sumSales('june', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['july'] = App\SalesData::sumSales('july', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['august'] = App\SalesData::sumSales('august', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['sep'] = App\SalesData::sumSales('sep', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['okt'] = App\SalesData::sumSales('okt', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['nov'] = App\SalesData::sumSales('nov', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['dec'] = App\SalesData::sumSales('dec', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['jan'] = App\SalesData::sumSales('jan', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['feb'] = App\SalesData::sumSales('feb', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['march'] = App\SalesData::sumSales('march', $fiscal_year, $code['id']))
                                    @php($sales_amount[$code['id']]['total'] = App\SalesData::sumSalesTotal1( $fiscal_year, $code['id']))


                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('apr', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('may', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('june', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('july', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('august',$fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('sep', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('okt', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('nov', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('dec', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('jan', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('feb', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSales('march', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumSalesTotal1( $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @php($sales_total[$code['id']]['apr'] = App\SalesData::sumSalesTotal('apr', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['may'] = App\SalesData::sumSalesTotal('may', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['june'] = App\SalesData::sumSalesTotal('june', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['july'] = App\SalesData::sumSalesTotal('july', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['august'] = App\SalesData::sumSalesTotal('august', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['sep'] = App\SalesData::sumSalesTotal('sep', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['okt'] = App\SalesData::sumSalesTotal('okt', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['nov'] = App\SalesData::sumSalesTotal('nov', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['dec'] = App\SalesData::sumSalesTotal('dec', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['jan'] = App\SalesData::sumSalesTotal('jan', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['feb'] = App\SalesData::sumSalesTotal('feb', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['march'] = App\SalesData::sumSalesTotal('march', $fiscal_year, $code['id']))
                                        @php($sales_total[$code['id']]['total'] = App\SalesData::sumSalesTotal2( $fiscal_year, $code['id']))
                                        <th colspan="2" class="text-right">Total</th>
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
                                        <td>{{ number_format(App\SalesData::sumSalesTotal2( $fiscal_year, $code['id']),0, ',' , '.') }}</td>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="material">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered" style="width: 1027px;">
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

                                    @php ($total[$code['id']]['apr'] = App\SalesData::sumTotalMaterial('apr', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['may'] = App\SalesData::sumTotalMaterial('may', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['june'] = App\SalesData::sumTotalMaterial('june', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['july'] = App\SalesData::sumTotalMaterial('july', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['august'] = App\SalesData::sumTotalMaterial('august', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['sep'] = App\SalesData::sumTotalMaterial('sep', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['okt'] = App\SalesData::sumTotalMaterial('okt', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['nov'] = App\SalesData::sumTotalMaterial('nov', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['dec'] = App\SalesData::sumTotalMaterial('dec', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['jan'] = App\SalesData::sumTotalMaterial('jan', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['feb'] = App\SalesData::sumTotalMaterial('feb', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['march'] = App\SalesData::sumTotalMaterial('march', $fiscal_year, $code['id']))

                                    @php ($total[$code['id']]['total'] = App\SalesData::SumTotalMaterial1( $fiscal_year, $code['id']))

                                    <tr>
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('apr', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('may', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('june', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('july', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('august', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('sep', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('okt', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('nov', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('dec', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('jan', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('feb', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::sumTotalMaterial('march', $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumTotalMaterial1( $fiscal_year, $code['id']),0, ',' , '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                        <td>{{ number_format($total[$code['id']]['apr'] / max($sales_amount[$code['id']]['apr'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['may'] / max($sales_amount[$code['id']]['may'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['june'] / max($sales_amount[$code['id']]['june'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['july'] / max($sales_amount[$code['id']]['july'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['august'] / max($sales_amount[$code['id']]['august'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['sep'] / max($sales_amount[$code['id']]['sep'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['okt'] / max($sales_amount[$code['id']]['okt'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['nov'] / max($sales_amount[$code['id']]['nov'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['dec'] / max($sales_amount[$code['id']]['dec'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['jan'] / max($sales_amount[$code['id']]['jan'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['feb'] / max($sales_amount[$code['id']]['feb'], 1),2) }}%</td>
                                        <td>{{ number_format($total[$code['id']]['march'] / max($sales_amount[$code['id']]['march'], 1),2) }}%</td>                    
                                        <td>{{ number_format($total[$code['id']]['total'] / max($sales_amount[$code['id']]['total'], 1),2) }}%</td>                    
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th>{{ number_format(collect($total)->sum('apr') / max($sales_total[$code['id']]['apr'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('may') / max($sales_total[$code['id']]['may'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('june') / max($sales_total[$code['id']]['june'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('july') / max($sales_total[$code['id']]['july'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('august') / max($sales_total[$code['id']]['august'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('sep') / max($sales_total[$code['id']]['sep'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('okt') / max($sales_total[$code['id']]['okt'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('nov') / max($sales_total[$code['id']]['nov'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('dec') / max($sales_total[$code['id']]['dec'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('jan') / max($sales_total[$code['id']]['jan'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('feb') / max($sales_total[$code['id']]['feb'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('march') / max($sales_total[$code['id']]['march'],1),2) }}%</th>
                                        <th>{{ number_format(collect($total)->sum('total') / max($sales_total[$code['id']]['total'],1),2) }}%</th>
                                        
                                        
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="group">
                        <div class="col-md-12 table-responsive">
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
                                        <th>Peb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                         @php ($total[$code['id']]['apr'] = App\SalesData::SumPlasticMaterial('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumPlasticMaterial('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumPlasticMaterial('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumPlasticMaterial('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumPlasticMaterial('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumPlasticMaterial('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumPlasticMaterial('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumPlasticMaterial('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumPlasticMaterial('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumPlasticMaterial('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumPlasticMaterial('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumPlasticMaterial('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumPlasticMaterialTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterial('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumPlasticMaterialTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumIngotMaterial('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumIngotMaterial('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumIngotMaterial('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumIngotMaterial('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumIngotMaterial('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumIngotMaterial('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumIngotMaterial('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumIngotMaterial('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumIngotMaterial('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumIngotMaterial('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumIngotMaterial('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumIngotMaterial('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumIngotMaterialTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterial('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumIngotMaterialTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumCKD('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumCKD('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumCKD('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumCKD('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumCKD('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumCKD('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumCKD('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumCKD('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumCKD('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumCKD('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumCKD('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumCKD('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumCKDTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKD('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumCKDImportDuty('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumCKDImportDuty('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumCKDImportDuty('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumCKDImportDuty('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumCKDImportDuty('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumCKDImportDuty('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumCKDImportDuty('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumCKDImportDuty('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumCKDImportDuty('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumCKDImportDuty('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumCKDImportDuty('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumCKDImportDuty('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumCKDImportDutyTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDuty('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumCKDImportDutyTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumImportPart('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumImportPart('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumImportPart('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumImportPart('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumImportPart('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumImportPart('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumImportPart('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumImportPart('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumImportPart('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumImportPart('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumImportPart('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumImportPart('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumImportPartTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPart('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportPartTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumImportDuty('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumImportDuty('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumImportDuty('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumImportDuty('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumImportDuty('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumImportDuty('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumImportDuty('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumImportDuty('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumImportDuty('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumImportDuty('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumImportDuty('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumImportDuty('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumImportDutyTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDuty('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumImportDutyTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumInklaringCkd('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumInklaringCkd('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumInklaringCkd('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumInklaringCkd('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumInklaringCkd('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumInklaringCkd('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumInklaringCkd('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumInklaringCkd('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumInklaringCkd('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumInklaringCkd('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumInklaringCkd('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumInklaringCkd('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumInklaringCkdTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkd('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringCkdTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumInklaringImportPart('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumInklaringImportPart('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumInklaringImportPart('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumInklaringImportPart('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumInklaringImportPart('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumInklaringImportPart('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumInklaringImportPart('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumInklaringImportPart('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumInklaringImportPart('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumInklaringImportPart('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumInklaringImportPart('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumInklaringImportPart('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumInklaringImportPartTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPart('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumInklaringImportPartTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive">
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
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\System::configMultiply('product_code') as $code)
                                    <tr>
                                        @php ($total[$code['id']]['apr'] = App\SalesData::SumLocalPart('apr', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['may'] = App\SalesData::SumLocalPart('may', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['june'] = App\SalesData::SumLocalPart('june', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['july'] = App\SalesData::SumLocalPart('july', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['august'] = App\SalesData::SumLocalPart('august', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['sep'] = App\SalesData::SumLocalPart('sep', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['okt'] = App\SalesData::SumLocalPart('okt', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['nov'] = App\SalesData::SumLocalPart('nov', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['dec'] = App\SalesData::SumLocalPart('dec', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['jan'] = App\SalesData::SumLocalPart('jan', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['feb'] = App\SalesData::SumLocalPart('feb', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['march'] = App\SalesData::SumLocalPart('march', $fiscal_year, $code['id']))

                                        @php ($total[$code['id']]['total'] = App\SalesData::SumLocalPartTotal1( $fiscal_year, $code['id']))
                                        <td>{{ $code['text'] }}</td>
                                        <td>{{ $code['id'] }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('apr', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('may', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('june', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('july', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('august', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('sep', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('okt', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('nov', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('dec', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('jan', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('feb', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPart('march', $fiscal_year, $code['id']), 0,',','.') }}</td>
                                        <td>{{ number_format(App\SalesData::SumLocalPartTotal1( $fiscal_year, $code['id']), 0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
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
                                </tfoot>
                            </table>
                        </div>
                    </div>    
                </div>
            </div>
        </div>  
    </div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/output_master.js') }}"></script>
@endpush
