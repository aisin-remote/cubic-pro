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
        
            <div class="form-group">
                <div class="col-md-2">
                    <div class="form-group">
                        <input name="fiscal_year" id="tanggal" class="form-control datepicker-year" required="required" placeholder="yyyy" aria-invalid="false" value="{{ date('Y') }}">
                    </div>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-primary btn-bordered waves-effect waves-light" type="button" onclick="filter()">Filter</button>
                </div>

                <div class="col-md-1">
                        <button type="button" class="btn btn-primary btn-bordered waves-effect waves-light" onclick="unduh_excel();">Unduh Excel</button>
                </div>
            </div>
        
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
                        <a href="#material_product" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Material (IDR)</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#material_sales" data-toggle="tab" aria-expanded="false">
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

                <div id="loading" style="display:none" class="text-muted">Loading...</div>
                <div class="tab-content">
                    <div class="tab-pane active" id="sales_amount">
                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                <table class="table  table-bordered" id="sales_data">
                                    <thead>
                                        <tr style="text-align:center">
                                            <th><span class="text-uppercase">SALES AMOUNT</span></th>
                                            <th><span class="text-uppercase">Product Code</span></th>
                                            <th style="text-align:center">April</th>
                                            <th style="text-align:center">May</th>
                                            <th style="text-align:center">June</th>
                                            <th style="text-align:center">July</th>
                                            <th style="text-align:center">Aug</th>
                                            <th style="text-align:center">Sep</th>
                                            <th style="text-align:center">Oct</th>
                                            <th style="text-align:center">Nov</th>
                                            <th style="text-align:center">Dec</th>
                                            <th style="text-align:center">Jan</th>
                                            <th style="text-align:center">Feb</th>
                                            <th style="text-align:center">Mar</th>
                                            <th style="text-align:center">Total</th>
                                        </tr>
                                    </thead>
                                </table>                  
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="material_product">
                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                <table class="table  table-bordered" id="material">
                                    <thead>
                                        <tr style="text-align:center">
                                            <th><span class="text-uppercase">TOTAL MATERIAL</span></th>
                                            <th><span class="text-uppercase">Product Code</span></th>
                                            <th style="text-align:center">April</th>
                                            <th style="text-align:center">May</th>
                                            <th style="text-align:center">June</th>
                                            <th style="text-align:center">July</th>
                                            <th style="text-align:center">Aug</th>
                                            <th style="text-align:center">Sep</th>
                                            <th style="text-align:center">Oct</th>
                                            <th style="text-align:center">Nov</th>
                                            <th style="text-align:center">Dec</th>
                                            <th style="text-align:center">Jan</th>
                                            <th style="text-align:center">Feb</th>
                                            <th style="text-align:center">Mar</th>
                                            <th style="text-align:center">Total</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                    </div>

                    <div class="tab-pane" id="material_sales">
                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                <table class="table  table-bordered table-responsive" id="material_sales_data">
                                    <thead>
                                        <tr style="text-align:center">
                                            <th><span class="text-uppercase">PRODUCT</span></th>
                                            <th><span class="text-uppercase">Product Code</span></th>
                                            <th style="text-align:center">April</th>
                                            <th style="text-align:center">May</th>
                                            <th style="text-align:center">June</th>
                                            <th style="text-align:center">July</th>
                                            <th style="text-align:center">Aug</th>
                                            <th style="text-align:center">Sep</th>
                                            <th style="text-align:center">Oct</th>
                                            <th style="text-align:center">Nov</th>
                                            <th style="text-align:center">Dec</th>
                                            <th style="text-align:center">Jan</th>
                                            <th style="text-align:center">Feb</th>
                                            <th style="text-align:center">Mar</th>
                                            <th style="text-align:center">Total</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="group">
                        <div id="group-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>  
    </div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/output_master.js') }}"></script>
<script src="{{ url('assets/js/pages/output_master_data.js') }}"></script>
@endpush
