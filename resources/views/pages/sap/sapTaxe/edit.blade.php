@extends('layouts.master')

@section('title')
	Edit Sap Taxe
@endsection

@section('content')

@php($active = 'taxe')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Sap Taxe</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('taxe.index') }}">Sap Taxe</a>
                    </li>
                    <li class="active">
                         Edit Sap Taxe
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
                    <form method="post" action="{{ route('taxe.update', $taxe->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Taxe Code <span class="text-danger">*</span></label>
                                <input type="text" name="tax_code" class="form-control" placeholder="Taxe Code " required="required" value="{{ $taxe->tax_code }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Taxe Name  <span class="text-danger">*</span></label>
                                <input type="text" name="tax_name" class="form-control" placeholder="Taxe Name " required="required" value="{{ $taxe->tax_name }}">
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
<script src="{{ url('assets/js/pages/taxe-add-edit.js') }}"></script>
@endpush