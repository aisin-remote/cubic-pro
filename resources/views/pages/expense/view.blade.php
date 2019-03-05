@extends('layouts.master')

@section('title')
	Detail Capex
@endsection

@section('content')

@php($active = 'capex')
<div class="container">
	 <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of Detail Capex Allocation</h4>
                <ol class="breadcrumb p-0 m-0">
					<li class="active"></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="row">
			<div class="col-md-12">
				<table>
					<tr>
						<th>Budget No</th>
						<th>: {{$capex->budget_no}}</th>
					</tr>
					<tr>
						<th>Budget Description</th>
						<th>: {{$capex->equipment_name}}</th>
					</tr>
					<tr>
						<th>Budget Plan | Remaining</th>
						<th> : {{$capex->budget_plan.' | '.$capex->budget_remaining}}</th>
					</tr>
					<tr>
						<th>Budget Reserved | Used</th>
						<th> : {{$capex->budget_reserved.' | '.$capex->budget_used}}</th>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="data_table" class="table table-bordered responsive-utilities jambo_table">
					<thead>
						<tr>
							<th>Approval No</th>
							<th>Project Name</th>
							<th>Bdgt. Reserved</th>
							<th>Act. Price</th>
							<th>Act. Qty</th>
							<th>Bdgt. Status</th>
							<th>Approval Status</th>
							<th>Act. GR</th>
						</tr>
					</thead>
					@foreach($approval_details as $ap)
					<tr>
						<td>{{$ap->approval_number}}</td>
						<td>{{$ap->project_name}}</td>
						<td>{{$ap->budget_reserved}}</td>
						<td>{{$ap->actual_price_user}}</td>
						<td>{{$ap->actual_qty}}</td>
						<td>{{$capex->status == 0?'Available':'not Available'}}</td>
						<td>{{$ap->status}}</td>
						<td>{{$ap->actual_gr}}</td>
					</tr>
					@endforeach
				</table>
			</div>
		</div>
</div>
@endsection
                    <!-- /Content of Items Shown -->

<!-- End of v3.1 by Ferry, 20150903, Integrate framework -->

