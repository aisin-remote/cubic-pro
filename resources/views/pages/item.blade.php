@extends('layouts.master')

@section('title')
    Master Item
@endsection

@section('content')

@php($active = 'item')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Master Item</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Item
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-4">
             <a href="{{ route('item.create') }}" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Item</a>
        </div><!-- end col -->
        <div class="col-xs-12 text-right">
            <button class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" onclick="on_import()"><i class="mdi mdi-upload"></i> Import</button>
            <a href="{{ route('item.export') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-download"></i> Eksport</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-item">
                    <thead>
                        <tr>
                            <th>Item Category</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Spesification</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>UoM</th>
                            <th>Supplier</th>
                            <th>Lead Times</th>
                            <th>Remarks</th>
                            <th style="width: 100px">Opsi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal for question -->
<div class="modal fade in" tabindex="-1" role="dialog" id="modal-delete-confirm">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Warning!</h4>
            </div>
            <div class="modal-body">The data you choose will be deleted, are you sure?</div>
            <div class="modal-footer">
                <button type="submit" id="btn-confirm" class="btn btn-danger btn-bordered waves-effect waves-light">Delete</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for import -->
<div class="modal fade in" tabindex="-1" role="dialog" id="modal-import">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Import Data</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('item.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                            @csrf
                            <div class="form-group">
                                <label class="control-label">Select File</label>
                                <input type="file" name="file" class="form-control" accept=".csv">
                                <label class="text-muted">*) File format .csv</label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-import" class="btn btn-primary btn-bordered waves-effect waves-light" onclick="on_table_temporary()">Import</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Cancel</button>
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

<script src="{{ url('assets/js/pages/item.js') }}"></script>
@endpush