@extends('layouts.master')

@section('title')
    Upload Sales Data
@endsection

@section('content')

@php($active = 'salesdata')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Upload Sales Data</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Upload Sales Data
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-4">
             <a href="{{ route('salesdata.create') }}" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Sales Data</a>
        </div><!-- end col -->
        <div class="col-xs-12 text-right">
            <button class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" onclick="on_import()"><i class="mdi mdi-upload"></i> Import</button>
            <a href="{{ route('salesdata.export') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-download"></i> Eksport</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 table-responsive" >
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse table-responsive" id="table-salesdata">
                    <thead>
                        <tr>
                            <th>Fiscal Year</th>
                            <th>Part Number</th>
                            <th>Customer Code</th>
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
                <h4 class="modal-title">Apakah anda yakin?</h4>
            </div>
            <div class="modal-body">Data yang dipilih akan dihapus, apakah anda yakin?</div>
            <div class="modal-footer">
                <button type="submit" id="btn-confirm" class="btn btn-danger btn-bordered waves-effect waves-light">Hapus</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
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
                        <form action="{{ route('salesdata.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                            @csrf
                            <div class="form-group">
                                <label class="control-label">Pilih File</label>
                                <input type="file" name="file" class="form-control" accept=".csv">
                                <label class="text-muted">*) File format .csv</label>
                                <center><a href="{{ route('salesdata.template') }}" ><i class="mdi mdi-download"></i> Format Sales Data .csv</a></center>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-import" class="btn btn-primary btn-bordered waves-effect waves-light">Import</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
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