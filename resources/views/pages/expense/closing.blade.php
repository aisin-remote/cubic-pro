@extends('layouts.master')

@section('title')
   List of Capex For Open / Close Action
@endsection

@section('content')

@php($active = 'capex')
<div class="container">
	
		<div class="row">
			<div class="col-xs-12">
				<div class="page-title-box">
					<h4 class="page-title"> List of Capex For Open / Close Action</h4>
					<ol class="breadcrumb p-0 m-0">
						<li class="active">
							 <div class="btn-group pull-right">
								<button type="submit" class="btn btn-info" onclick="closing(0)">Open Budget</button>
								<button type="submit" class="btn btn-warning" onclick="closing(1)">Close Budget</button>
							</div>
						</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
       <div class="row">
			<div class="col-md-12">
				<div class="card-box">
					<form>
						<table id="data_table" class="table table-bordered responsive-utilities jambo_table">
							<thead>
								<tr>
									<th>Bdgt. Number</th>
									<th>Equipment Name</th>
									<th>Bdgt. Plan</th>
									<th>Bdgt. Used</th>
									<th>Bdgt. Remaining</th>
									<th>Plan GR</th>
									<th>Status</th>
									<th>Closing</th>
									<th><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);"></th>
								</tr>
							</thead>
						</table>
					</form>
				</div>
			</div>
		</div>
</div>
@endsection

@push('js')
<script src="{{ url('assets/js/pages/expense-closing.js') }}"></script>
@endpush
                              
