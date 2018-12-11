@extends('layouts.master')

@section('title')
	Create SAP Number
@endsection

@section('content')

@php($active = 'number')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create SAP Number</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('number.index') }}">SAP Number</a>
                    </li>
                    <li class="active">
                        Create SAP Number
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
                    <form method="post" action="{{ route('number.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Number Type <span class="text-danger">*</span></label>
                                <input type="text" name="number_type" class="form-control" placeholder="Number Type " required="required" >
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Number Booked <span class="text-danger">*</span></label>
                                <input type="text" name="number_booked" class="form-control" placeholder="Number Booked" required="required" >
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Number Current</label>
                                <input type="text" name="number_current" class="form-control" placeholder="Number Current" required="required">
                                <span class="help-block"></span>
                            </div>
                            
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Save</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/number-add-edit.js') }}"></script>
@endpush