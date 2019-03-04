@extends('layouts.master')

@section('title')
   List of Capex Moving (Used <=0) To Archive For Revision
@endsection

@section('content')

@php($active = 'archive')

<div class="container">
	
		<div class="row">
			<div class="col-xs-12">
				<div class="page-title-box">
					<h4 class="page-title"> {{$title}}</h4>
					<ol class="breadcrumb p-0 m-0">
						<li class="active">
							 <div class="btn-group pull-right">
								@if ($src_dest == 'src')
									<button type="submit" class="btn btn-warning" onclick="moveArchive()">Move To Archive!</button>
									<a href="{{ url('expense/archive/list') }}" id="back" class="btn btn-info">Go to Archive</a>
								@else
									<button type="submit" class="btn btn-warning" onclick="undoArchive()">Undo to Archive</button>
									<a href="{{ url('expense/archive') }}" id="back" class="btn btn-info">Back to Source</a>
								@endif
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
<input type="hidden" id="src_dest" value="{{$src_dest}}" />
@endsection

@push('js')
<script src="{{ url('assets/js/pages/expense-archive.js') }}"></script>
@endpush
