@extends('layouts.master')

@section('title')
	Dashboard
@endsection

@section('content')

@php($active = 'Dashboard')

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
        <form class="form-inline" method="get" action="{{ url('dashboard/view/'.request()->get('group_type')) }}">
            <input type="text" name="plan_type" value="{{ request()->plan_type }}" hidden />
            <input type="text" name="division" value="{{ request()->division }}" hidden />
            <input type="text" name="department" value="{{ request()->department }}" hidden />
            <div class="col-md-12 m-b-10">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group m-r-10">
                            <div class="dropdown">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span id="plan-type">{{ !empty(request()->get('plan_type')) ? request()->get('plan_type') === 'rev' ? 'Revised Plan' : 'Original Plan' : 'Original Plan' }}</span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="#" class="change-plan" data-value="ori">Original Plan</a></li>
                                    <li><a href="#" class="change-plan" data-value="rev">Revised Plan</a></li>
                                </ul>
                            </div>
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
                    <div class="col-md-4 text-right">
                        <div class="form-group mr-10">
                            <button name="download" class="btn btn-success" type="submit"><i class="mdi mdi-download"></i>Download CSV</button>
                            <button name="download2" class="btn btn-success" type="submit"><i class="mdi mdi-download"></i>Download CSV 2</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    @if (!empty(request()->division))
                    {{ App\Division::getDivisionByCode(request()->division)->division_name }}
                    @elseif (!empty(request()->department))
                    {{ App\Department::getDepartmentByCode(request()->department)->department_name }}
                    @else
                    Choose Division
                    @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="{{ url('dashboard/view') }}">All</a></li>
                    @foreach ($divisions as $division)
                        <li><a href="{{ url('dashboard/view?division='.$division->division_code) }}">{{ $division->division_name }}</a></li>
                    @endforeach
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-submenu">
                        <a class="test" tabindex="-1" href="#">All Department <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-scroll">
                            @foreach ($departments as $department)
                                <li><a tabindex="-1" href="{{ url('dashboard/view?department='.$department->department_code) }}">{{ $department->department_name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </form>

		<div class="col-md-12">
			&nbsp;
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
                    <h3 class="panel-sub-title m-b-20" id="capex-plan">Capex Plan : IDR 0 Billion</h3>
					<canvas id="chart" style="height:260px"></canvas>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
                    <h3 class="panel-sub-title m-b-20" id="expense-plan">Expense Plan : IDR 0 Billion</h3>
					<canvas id="chart2" style="height:260px"></canvas>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
                    <h3 class="panel-sub-title m-b-20">Capex Budget Summary (FY:{{ Carbon\Carbon::parse($period_date)->format('Y') }})</h3>
					<canvas id="chart3" style="height:260px"></canvas>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
                    <h3 class="panel-sub-title m-b-20">Expense Budget Summary (FY:{{ Carbon\Carbon::parse($period_date)->format('Y') }})</h3>
					<canvas id="chart4" style="height:260px"></canvas>
				</div>
			</div>
		</div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/dashboard-new.js') }}"></script>
@endpush