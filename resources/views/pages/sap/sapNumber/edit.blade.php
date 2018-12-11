@extends('layouts.master')

@section('title')
	Edit Sap Number
@endsection

@section('content')

@php($active = 'number')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Sap Number</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('number.index') }}">Sap Number</a>
                    </li>
                    <li class="active">
                         Edit Sap Number
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
                    <form method="post" action="{{ route('number.update', $number->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Number Type <span class="text-danger">*</span></label>
                                <input type="text" name="number_type" class="form-control" placeholder="Number Type " required="required" value="{{ $number->number_type }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Number Booked <span class="text-danger">*</span></label>
                                <input type="text" name="number_booked" class="form-control" placeholder="Number Booked" required="required" value="{{ $number->number_booked }}">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Number Current</label>
                                <input type="text" name="number_current" class="form-control" placeholder="Number Current" value="{{ $number->number_current }}">
                                <span class="help-block"></span>
                            </div>
                            
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Save Changes</button>

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