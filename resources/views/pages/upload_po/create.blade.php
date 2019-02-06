@extends('layouts.master')

@section('title')
	Create Data PO
@endsection

@section('content')

@php($active = 'Upload PO')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create PO Data</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('upload_po.index') }}">PO Data</a>
                    </li>
                    <li class="active">
                        Create PO Data
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <form method="post" action="{{ route('upload_po.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Approval Number<span class="text-danger">*</span></label>
                                <input type="text" name="approval_number" class="form-control" placeholder="Approval Number" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">PO Number</label>
                                <input type="text" name="po_number" class="form-control" placeholder="PO Number" required="required">
                            </div>

                            <div class="form-group">
                                <label class="control-label">PO Date</label>
                                <input type="text" class="form-control datepicker" placeholder="yyyy-mm-dd" name="po_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/upload_po-add-edit.js') }}"></script>
@endpush