@extends('layouts.master')

@section('title')
    Data Temporary Sales Data
@endsection

@section('content')

@php($active = 'salesdata')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Data Temporary Sales Data</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Data Temporary Upload Sales Data
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-4">
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-temporary-salesdata">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Customer Code</th>
                            <th>Fiscal Year</th>
                            <th>Market</th>
                            <th>April Qty</th>
                            <th>April Amount</th>
                            <th>May Qty</th>
                            <th>May Amount</th>
                            <th>June Qty</th>
                            <th>June Amount</th>
                            <th>July Qty</th>
                            <th>July Amount</th>
                            <th>Aug Qty</th>
                            <th>Aug< Amount</th>
                            <th>Sep Qty</th>
                            <th>Sep Amount</th>
                            <th>Oct Qty</th>
                            <th>Oct Amount</th>
                            <th>Nov Qty</th>
                            <th>Nov Amount</th>
                            <th>Dec Qty</th>
                            <th>Dec Amount</th>
                            <th>Jan Qty</th>
                            <th>Jan Amount</th>
                            <th>feb Qty</th>
                            <th>feb Amount</th>
                            <th>mar Qty</th>
                            <th>mar Amount</th> 
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <a href="{{ route('salesdata.temporary.save') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"> Simpan</a>
                <a href="{{ route('salesdata.temporary.cancel') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"> Reset</a>
            </div>
        </div>
    </div>

</div>



@endsection

@push('js')

@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif

<script src="{{ url('assets/js/pages/salesdata.js') }}"></script>
@endpush