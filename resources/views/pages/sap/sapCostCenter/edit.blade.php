@extends('layouts.master')

@section('title')
	Edit Sap Cost Center
@endsection

@section('content')

@php($active = 'cost_center')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Sap Cost Center</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('cost_center.index') }}">Sap Cost Center</a>
                    </li>
                    <li class="active">
                         Edit Sap Cost Center
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
                    <form method="post" action="{{ route('cost_center.update', $cost->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">CC Code <span class="text-danger">*</span></label>
                                <input type="text" name="cc_code" class="form-control" placeholder="CC Code " required="required" value="{{ $cost->cc_code }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">CC SName <span class="text-danger">*</span></label>
                                <input type="text" name="cc_sname" class="form-control" placeholder="CC SName" required="required" value="{{ $cost->cc_sname }}">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc FName</label>
                                <input type="text" name="cc_fname" class="form-control" placeholder="Cc FName" value="{{ $cost->cc_fname }}">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Cc GCode</label>
                                <input type="text" name="cc_gcode" class="form-control" placeholder="Cc GCode" value="{{ $cost->cc_gcode }}">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc GText</label>
                                <input type="text" name="cc_gtext" class="form-control" placeholder="Cc GText" value="{{ $cost->cc_gtext }}">
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
<script src="{{ url('assets/js/pages/cost_center-add-edit.js') }}"></script>
@endpush