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
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label"><b>Budget No</b><span class="text-danger"></span></label>
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b>Budget Description</b><span class="text-danger"></span></label>
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b>Budget Plan | Remaining</b><span class="text-danger"></span></label>
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b>Budget Reserved | Used</b><span class="text-danger"></span></label>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label"><b><span class="text-danger"> : </span></label>
					{{$capex->budget_no}}</b>
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b><span class="text-danger"> : </span></label>
					{{$capex->equipment_name}}</b>

					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b><span class="text-danger"> : </span></label>
					{{number_format($capex->budget_plan).' | '.number_format($capex->budget_remaining)}}</b>
					<span class="help-block"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><b><span class="text-danger"> : </span></label>
					{{number_format($capex->budget_reserved).' | '.number_format($capex->budget_used)}}</b>
					<span class="help-block"></span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="data_table" class="table table-colored table-colored table-inverse responsive-utilities">
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
						<td><a href= "{{ url('approval/cx/'.$ap->approval->approval_number) }}" >{{$ap->approval->approval_number}}</a></td>
						<td>{{$ap->project_name}}</td>
						<td>{{number_format($ap->budget_reserved)}}</td>
						<td>{{number_format($ap->actual_price_user)}}</td>
						<td>{{$ap->actual_qty}}</td>
						<td @if($ap->isOver) class="bg-danger" @else class="bg-success" @endif style="color:#fff">{{ $ap->isOver ? "Over Budget" : "Under Budget" }}</td>
						@if($ap->approval->status == 0)
						<td>User Created</td>
						@elseif($ap->approval->status == 1)
						<td>Validasi Budget</td>
						@elseif($ap->approval->status == 2)
						<td>Approved by Dept. Head</td>
						@elseif($ap->approval->status == 3)
						<td>Approved by GM</td>
						@elseif($ap->approval->status == 4)
						<td>Approved by Director</td>
						@elseif($ap->approval->status == -1)
						<td>Canceled on Quotation Validation</td>
						@elseif($ap->approval->status == -2)
						<td>Canceled Dept. Head Approval</td>
						@else
						<td>Canceled on Group Manager Approval</td>
						@endif
						<td>{{ date('d-M-Y',strtotime($ap->actual_gr))}}</td>
					</tr>
					@endforeach
				</table>
			</div>
		</div>
</div>
@endsection
                    <!-- /Content of Items Shown -->

<!-- End of v3.1 by Ferry, 20150903, Integrate framework -->

