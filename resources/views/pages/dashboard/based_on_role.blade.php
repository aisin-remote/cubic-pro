@extends('layouts.master')

@section('title')
	Dashboard
@endsection

@section('content')
@php
if ($dashboardType == 'division') {
    $title = auth()->user()->division->division_name . ' Division';
    $departments = json_encode(auth()->user()->division->department->pluck('department_code')->toArray());
} else {
    $title = auth()->user()->department->department_name . ' Department';
    $departments = json_encode([auth()->user()->department->department_code]);
}

if ($codeDept = request()->department) {
    $title = App\Department::getDepartmentByCode($codeDept)->department_name . ' Department';
}

$active = 'Dashboard';
@endphp

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Realtime Dashboard</h4>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <form class="form-inline" id="form-filter" method="get">
            <input type="hidden" id="panel-title" value="{{ $title }}">
            <input type="hidden" id="departments" value="{{ $departments }}">
            <div class="col-md-12 m-b-10">
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group m-r-10">
                            <select class="form-control" id="plan-type">
                                <option value="0">Original Plan</option>
                                <option value="1">Revised Plan</option>
                            </select>
                        </div>
                        <div class="form-group m-r-10">
                            <select class="form-control" id="period">
                                @for($i = $first_period; $i <= $period_date; $i++)
                                <option value="{{ $i }}" @if($i == $period_date) selected @endif>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group m-r-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="interval" placeholder="Interval" id="interval" value="{{ !empty(request()->interval) ? request()->interval : \Carbon\Carbon::parse($period_date_from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period_date_to)->format('d/m/Y') }}">
                            </div>
                        </div>
                        <div class="form-group mr-10">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <div class="form-group mr-10">
                            <button class="btn btn-success" type="button" id="download-budget"><i class="mdi mdi-download"></i>List Budget</button>
                            <button class="btn btn-success" type="button" id="download-approval"><i class="mdi mdi-download"></i>List Approval</button>
                        </div>
                    </div>
                </div>
            </div>
            @if($dashboardType == 'division')
            <div class="col-md-12">
                <select class="form-control" id="department-code">
                    <option value="{{ $departments }}" data-title="{{ $title }}">All Department</option>
                    @foreach (auth()->user()->division->department as $department)
                    @php
                    $tempArray = [$department->department_code];
                    @endphp
                    <option value="{{ json_encode($tempArray) }}" data-title="{{ $department->department_name . ' DEPARTMENT' }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>

		<div class="col-md-12">
			&nbsp;
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ $title }}</h3>
				</div>
				<div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="panel-sub-title m-b-20" id="capex-plan">Capex Plan : IDR <span id="capex-plan">0</span> Billion</h3>
                        </div>
                        <div class="col-sm-12">
                            <canvas id="capex-used"></canvas>
                            <div id="no-data-capex-used" style="display: none">Budget is not available</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ $title }}</h3>
				</div>
				<div class="panel-body">
					<div class="row">
                        <div class="col-sm-12">
                            <h3 class="panel-sub-title m-b-20" id="expense-plan">Expense Plan : IDR <span>0</span> Billion</h3>
                        </div>
                        <div class="col-sm-12">
                            <canvas id="expense-used"></canvas>
                            <div id="no-data-expense-used" style="display: none">Budget is not available</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ $title }}</h3>
				</div>
				<div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="panel-sub-title m-b-20">Capex Budget Summary (FY:<span class="fy-active"></span>)</h3>
                        </div>
                        <div class="col-sm-12">
                            <canvas id="chart-summary-capex"></canvas>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ $title }}</h3>
				</div>
				<div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="panel-sub-title m-b-20">Expense Budget Summary (FY:<span class="fy-active"></span>)</h3>
                        </div>
                        <div class="col-sm-12">
                            <canvas id="chart-summary-expense"></canvas>
                        </div>
                    </div>
				</div>
			</div>
		</div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ asset('assets/plugins/chart.js/chart-label.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-role.js') }}"></script>
@endpush