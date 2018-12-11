@extends('layouts.master')

@section('title')
	Create SAP Cost Center
@endsection

@section('content')

@php($active = 'cost_center')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create SAP Cost Center</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('cost_center.index') }}">SAP Cost Center</a>
                    </li>
                    <li class="active">
                        Create SAP Cost Center
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
                    <form method="post" action="{{ route('cost_center.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">CC Code <span class="text-danger">*</span></label>
                                <input type="text" name="cc_code" class="form-control" placeholder="CC Code " required="required" >
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">CC SName <span class="text-danger">*</span></label>
                                <input type="text" name="cc_sname" class="form-control" placeholder="CC SName" required="required" >
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc FName</label>
                                <input type="text" name="cc_fname" class="form-control" placeholder="Cc FName" required="required">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Cc GCode</label>
                                <input type="text" name="cc_gcode" class="form-control" placeholder="Cc GCode" required="required">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc GText</label>
                                <textarea type="text" name="cc_gtext" class="form-control" placeholder="Cc GText" required="required"></textarea>
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
<script src="{{ url('assets/js/pages/cost_center-add-edit.js') }}"></script>
@endpush