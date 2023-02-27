@extends('layouts.master')

@section('title')
	Edit SAP Gl Account
@endsection

@section('content')

@php($active = 'gl_account')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit SAP Gl Account</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('gl_account.index') }}">SAP Gl Account</a>
                    </li>
                    <li class="active">
                         Edit SAP Gl Account
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
                    <form method="post" action="{{ route('gl_account.update', $gl_account->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">CC GCode <span class="text-danger">*</span></label>
                                <input type="text" name="gl_gcode" class="form-control" placeholder="CC GCode " required="required" value="{{ $gl_account->gl_gcode }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">CC GName <span class="text-danger">*</span></label>
                                <input type="text" name="gl_gname" class="form-control" placeholder="CC GName" required="required" value="{{ $gl_account->gl_gname }}">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc AName</label>
                                <input type="text" name="gl_aname" class="form-control" placeholder="Cc SName" value="{{ $gl_account->gl_aname }}">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Cc ACode</label>
                                <input type="text" name="gl_acode" class="form-control" placeholder="Cc SCode" value="{{ $gl_account->gl_acode }}">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cc Dep Key</label>
                                <input type="text" name="dep_key" class="form-control" placeholder="Cc Dep Key" value="{{ $gl_account->dep_key }}">
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
<script src="{{ url('assets/js/pages/gk_account-add-edit.js') }}"></script>
@endpush