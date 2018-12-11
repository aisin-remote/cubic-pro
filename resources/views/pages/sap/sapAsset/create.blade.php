@extends('layouts.master')

@section('title')
	Create SAP Asset
@endsection

@section('content')

@php($active = 'asset')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create SAP Asset</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('asset.index') }}">SAP Asset</a>
                    </li>
                    <li class="active">
                        Create SAP Asset
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
                    <form method="post" action="{{ route('asset.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Asset Code <span class="text-danger">*</span></label>
                                <input type="text" name="asset_code" class="form-control" placeholder="Asset Code" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Asset Name</label>
                                <input type="text" name="asset_name" class="form-control" placeholder="Asset Name">
                                <span class="help-block"></span>
                            </div>
                            
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Asset Type <span class="text-danger">*</span></label>
                                <input type="text" name="asset_type" class="form-control" placeholder="Asset Type" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Asset Class</label>
                                <input type="text" name="asset_class" class="form-control" placeholder="Asset Class">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Asset Content</label>
                                <input type="text" name="asset_content" class="form-control" placeholder="Asset Content" required="required" >
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Asset Account</label>
                                <input type="text" name="asset_account" class="form-control" placeholder="Asset Account" required="required">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Asset AccText</label>
                                <input type="text" name="asset_acctext" class="form-control" placeholder=" Asset AccText" required="required">
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
<script src="{{ url('assets/js/pages/asset-add-edit.js') }}"></script>
@endpush