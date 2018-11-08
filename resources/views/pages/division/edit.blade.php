@extends('layouts.master')

@section('title')
	Division
@endsection

@section('content')

@php($active = 'division')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Division</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('division.index') }}">Division</a>
                    </li>
                    <li class="active">
                        Edit Division
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
                    <form method="post" action="{{ route('division.update', $division->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Division Code <span class="text-danger">*</span></label>
                                <input type="text" name="division_code" class="form-control" placeholder="Division Code" required="required" value="{{ $division->division_code }}">
                                <span class="help-block"></span>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Division Name</label>
                                <input type="text" name="division_name" class="form-control" placeholder="Division Name" required="required" value="{{ $division->division_name }}">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Director</label>
                                <select name="dir_key" class="select2" data-placeholder="Direction" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($dir_keys as $dir_key)
                                    <option value="{{ $dir_key['id'] }}" {{ $dir_key['id'] == $dir_key['text'] ? 'selected=selected' : '' }}>{{ $dir_key['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Update</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/division-add-edit.js') }}"></script>
@endpush