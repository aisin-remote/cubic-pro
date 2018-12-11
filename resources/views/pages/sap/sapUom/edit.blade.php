@extends('layouts.master')

@section('title')
	Edit Sap Uom
@endsection

@section('content')

@php($active = 'uom')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Sap Uom</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('uom.index') }}">Sap Uom</a>
                    </li>
                    <li class="active">
                         Edit Sap Uom
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
                    <form method="post" action="{{ route('uom.update', $uom->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Uom Code<span class="text-danger">*</span></label>
                                <input type="text" name="uom_code" class="form-control" placeholder="Uom Code " required="required" value="{{ $uom->uom_code }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Uom SName  <span class="text-danger">*</span></label>
                                <input type="text" name="uom_sname" class="form-control" placeholder="Uom SName " required="required" value="{{ $uom->uom_sname }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Uom Fname <span class="text-danger">*</span></label>
                                <input type="text" name="uom_fname" class="form-control" placeholder="Uom Fname " required="required" value="{{ $uom->uom_fname }}">
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
<script src="{{ url('assets/js/pages/uom-add-edit.js') }}"></script>
@endpush