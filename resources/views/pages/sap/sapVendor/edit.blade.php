@extends('layouts.master')

@section('title')
    Edit Sap Vendor
@endsection

@section('content')

@php($active = 'vendor')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Sap Vendor</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('vendor.index') }}">Sap Vendor</a>
                    </li>
                    <li class="active">
                         Edit Sap Vendor
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
                    <form method="post" action="{{ route('vendor.update', $vendor->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Vendor Code<span class="text-danger">*</span></label>
                                <input type="text" name="vendor_code" class="form-control" placeholder="Vendor Code " required="required" value="{{ $vendor->vendor_code }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Vendor SName  <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_sname" class="form-control" placeholder="Vendor SName " required="required" value="{{ $vendor->vendor_sname }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Vendor Fname <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_fname" class="form-control" placeholder="Vendor Fname " required="required" value="{{ $vendor->vendor_fname }}">
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
<script src="{{ url('assets/js/pages/vendor-add-edit.js') }}"></script>
@endpush