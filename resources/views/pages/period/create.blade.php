@extends('layouts.master')

@section('title')
	Create Period
@endsection

@section('content')

@php($active = 'period')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Period</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('period.index') }}">Period</a>
                    </li>
                    <li class="active">
                        Create Period
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
                    <form method="post" action="{{ route('period.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Name" required="required">
                                <span class="help-block"></span>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Value <span class="text-danger">*</span></label>
                                <input type="text" name="value" class="form-control" placeholder="Value" required="required">
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
<script src="{{ url('assets/js/pages/period-add-edit.js') }}"></script>
@endpush