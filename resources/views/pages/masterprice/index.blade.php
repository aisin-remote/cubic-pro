@extends('layouts.master')

@section('title')
    Upload Master Price
@endsection

@section('content')

@php($active = 'masterprice')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Upload Master Price</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Upload Master Price
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-4">
             <a href="{{ route('masterprice.create') }}" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Master Price</a>
        </div><!-- end col -->
        <div class="col-xs-12 text-right">
            <button class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" onclick="on_import()"><i class="mdi mdi-upload"></i> Import</button>
            <a href="{{ route('masterprice.export') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-download"></i> Eksport</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-masterprice">
                    <thead>
                        <tr>
                            <!-- <th style="width: 50px"></th> -->
                            <th>Fiscal Year</th>
                            <th>Part Number</th>
                            <th>Supplier Code</th>
                            <th>Source</th>
                            <th>Price April</th>
                            <th>Price May</th>
                            <th>Price June</th>
                            <th>Price July</th>
                            <th>Price August</th>
                            <th>Price September</th>
                            <th>Price October</th>
                            <th>Price November</th>
                            <th>Price December</th>
                            <th>Price January</th>
                            <th>Price February</th>
                            <th>Price March</th>
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
                        <form action="{{ route('masterprice.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                            @csrf
                            <div class="form-group">
                                <label class="control-label">Pilih File</label>
                                <input type="file" name="file" class="form-control" accept=".csv">
                                <label class="text-muted">*) File format .csv</label>
                                <center><a href="{{ route('masterprice.template') }}" ><i class="mdi mdi-download"></i>  Format Master Price .csv</a></center>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-import" class="btn btn-primary btn-bordered waves-effect waves-light" onclick="on_table_temporary()">Import</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" tabindex="-1" role="dialog" id="modal-temporary">
    <!-- <div class="modal-dialog modal-sm"> -->
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Data Temporary Master Price</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        
                            <!-- <div class="card-box"> -->
                                <table class="table m-0 table-colored table-inverse" id="table-temporary-masterprice">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px"></th>
                                            <th>Part Number</th>
                                            <th>Supplier Code</th>
                                            <th>Source</th>
                                            <th>Price April</th>
                                            <th>Price May</th>
                                            <th>Price June</th>
                                            <th>Price July</th>
                                            <th>Price August</th>
                                            <th>Price September</th>
                                            <th>Price October</th>
                                            <th>Price November</th>
                                            <th>Price December</th>
                                            <th>Price January</th>
                                            <th>Price February</th>
                                            <th>Price March</th>
                                            <th style="width: 100px">Opsi</th>
                                        </tr>
                                    </thead>
                                </table>
                            <!-- </div> -->
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-bordered waves-effect waves-light">Simpan</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
            </div>
        </div>
    <!-- </div> -->
</div>

@endsection

@push('js')

@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif

<script src="{{ url('assets/js/pages/masterprice.js') }}"></script>

@endpush