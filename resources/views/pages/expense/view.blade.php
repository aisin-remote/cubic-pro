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
				<div class="form-group">
					<label class="control-label">Budget No<span class="text-danger"> : </span></label>
					{{$capex->budget_no}}
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label">Budget Description<span class="text-danger"> : </span></label>
					{{$capex->equipment_name}}
						
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label">Budget Plan | Remaining<span class="text-danger"> : </span></label> 
					{{number_format($capex->budget_plan).' | '.number_format($capex->budget_remaining)}}
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label">Budget Reserved | Used<span class="text-danger"> : </span></label>
					{{number_format($capex->budget_reserved).' | '.number_format($capex->budget_used)}}
					<span class="help-block"></span>
				</div>
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
						@if($ap->status == 0)
						<td>User Created</td>
						@elseif($ap->status == 1)
						<td>Validasi Budget</td>	
						@elseif($ap->status == 2)
						<td>Approved by Dept. Head</td>
						@elseif($ap->status == 3)
						<td>Approved by GM</td>
						@elseif($ap->status == 4)
						<td>Approved by Director</td>
						@elseif($ap->status == -1)
						<td>Canceled on Quotation Validation</td>
						@elseif($ap->status == -2)
						<td>Canceled Dept. Head Approval</td>
						@else
						<td>Canceled on Group Manager Approval</td>	
						@endif
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

