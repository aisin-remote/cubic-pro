@extends('layouts.master')

@section('title')
	List Approval
@endsection

@section('content')

@php($active = 'approval_master')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">List Approval Capex {{ $approval_masters->approval_number }}</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('department.index') }}">Approval {{ $approval_masters->approval_number }}</a>
                    </li>
                    <li class="active">
                        Approval {{ $approval_masters->approval_number }}
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
                    <form method="post" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Approval Number<span class="text-danger"> : </span></label> {{ $approval_masters->approval_number }}
                                
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Department<span class="text-danger"> : </span></label>
                                 
                                {{ $approval_masters->departments->department_name }}     
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Division<span class="text-danger"> : </span></label> {{ $approval_masters->divisions->division_name }}
                                
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Total<span class="text-danger"> : </span></label> {{ $approval_masters->total }}
                                
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Status<span class="text-danger">  : </span></label> 
                                @if ($approval_masters->status == '0')
                                   User Created 
                                   @elseif ($approval_master->status  == '1')
                                   Validasi Budget
                                   @elseif ($approval_master->status  == '2')
                                   Approved by Dept. Head
                                   @elseif ($approval_master->status  == '3')
                                   Approved by GM
                                   @elseif ($approval_master->status  == '4')
                                   Approved by Director
                                   @elseif ($approval_master->status  == '-1')
                                   Canceled on Quotation Validation
                                   @elseif ($approval_master->status  == '-2')
                                   Canceled Dept. Head Approval
                                @endif
                                
                                <span class="help-block"></span>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
<div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="details-table">
                    <thead>
                        <tr>
                            <th>Budget Number</th>
                            <th>Project Name</th>
                            <th>Asset Kind</th>
                            <th>Asset Category</th>
                            <th>Unit</th>
                            <th>Asset Type</th>
                            <th>Sap Cost Center</th>
                            <th>Item Specs</th>
                            <th>Last Budget Remains</th>
                            <th>Max Budget Reservation</th>
                            <th>Amount on Quotation (IDR)</th>
                            <th>Actual GR</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/approval-capex-add-edit.js') }}"></script>
@endpush