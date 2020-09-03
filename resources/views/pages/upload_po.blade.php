@extends('layouts.master')

@section('title')
    Input Purchase Order
@endsection

@section('content')

@php($active = 'input_po')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Input Purchase Order</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Input Purchase Order
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-9">
            <form class="form-inline">
                <div class="form-group m-r-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control" name="pr_receive" placeholder="Filter PR Receive" id="pr-receive" value="">
                    </div>
                </div>
                <div class="form-group mr-10">
                    <button class="btn btn-primary" type="button" id="btn-filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="btn-reset">Reset</button>
                </div>
            </form>
        </div>
        <div class="col-md-3 text-right">
			<a href="{{ route('upload_po.export') }}" class="btn btn-custom btn-bordered waves-effect waves-light m-b-20" id="btn-download"><i class="mdi mdi-download"></i> Download</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-upload_po">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Approval Number</th>
                            <th>Description</th>
                            <th>User Create PR Date</th>
                            <th>PR Receive</th>
                            <th>PO Number</th>
                            <th>PO Date</th>
                            <th>Quotation</th>
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
                        <form action="{{ route('upload_po.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                            @csrf
                            <div class="form-group">
                                <label class="control-label">Select File</label>
                                <input type="file" name="file" class="form-control" accept=".csv">
                                <center><a href="{{ route('upload_po.template') }}" ><i class="mdi mdi-download"></i> Template Upload PO.csv</a></center>
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

<script src="{{ url('assets/js/pages/upload_po.js') }}"></script>
@endpush