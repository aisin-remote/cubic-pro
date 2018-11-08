@extends('layouts.master')

@section('title')
    Data Temporary Master Price
@endsection

@section('content')

@php($active = 'temporary')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Data Temporary Master Price</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Data Temporary Upload Master Price
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
                <table class="table m-0 table-colored table-inverse" id="table-temporary-masterprice">
                    <thead>
                        <tr>
                            <!-- <th style="width: 50px"></th> -->
                            <th>Part Number</th>
                            <th>Supplier Code</th>
                            <th>Source</th>
                            <th>Price</th>
                            <!-- <th style="width: 100px">Opsi</th> -->
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <a href="{{ route('masterprice.temporary.save') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"> Simpan</a>
                <a href="{{ route('masterprice.temporary.cancel') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"> Reset</a>
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

<script src="{{ url('assets/js/pages/temporary_masterprice.js') }}"></script>
@endpush