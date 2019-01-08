@extends('layouts.master')

@section('title')
	Edit Period
@endsection

@section('content')

@php($active = 'period')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Period</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('period.index') }}">Period</a>
                    </li>
                    <li class="active">
                        Edit Period
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
                    <form method="post" action="{{ route('period.update', $period->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Name" required="required" value="{{ $period->name }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">Value <span class="text-danger">*</span></label>
                                <input type="text" name="value" class="form-control" placeholder="value" required="required" value="{{ $period->value }}">
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
<script src="{{ url('assets/js/pages/period-add-edit.js') }}"></script>
@endpush