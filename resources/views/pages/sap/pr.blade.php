@extends('layouts.master')

@section('title')
    Add Purchase Request Item.
@endsection

@section('content')

@php($active = 'unbudget')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">PR Convert to SAP PO.</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('approval-unbudget.create') }}">Link To SAP</a></li>
                    <li class="active">
                        PR Convert to SAP PO
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
					<div class="col-md-12">
						<div class="pull-right">
							@if (\Entrust::can('approval-create'))
								<a href="{{ url('approval/create/cx') }}" class="btn btn-primary" id="create">Create Capex Approval</a>
							@endif
							
						</div>
					</div>
					<div class="col-md-12">
						<div>
							<h5><strong>Select parameters :</strong></h5>
							
							<input type="radio" class="is_download" id="ready_download" name="is_downloaded" value="1"/> Ready Download (Approved by Dir) 
							<br>
							<input type="radio" class="is_download" id="already_download" name="is_downloaded" value="2"/> Already Download
							<br>
							<input type="radio" class="is_download" id="cant_download" name="is_downloaded" value="3"/> Can't Download (Not yet approved by Dir)

							<div class="clearfix"></div>
							<br>
						</div>
						<table id="data_table" class="table table-bordered responsive-utilities jambo_table">
							<thead>
								<tr>
									<th>Department</th>
									<th>Approval Number</th>
									<th>Total</th>
									<th>Status</th>
									<th>Overbudget Info</th>
									<th width="105">Action</th>
								</tr>
							</thead>
						</table>
					</div>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/pr_convert_po.js') }}"></script>

@endpush