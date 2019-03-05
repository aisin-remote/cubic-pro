@extends('layouts.master')

@section('title')
	Dashboard
@endsection

@section('content')

@php($active = 'dashboard')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        Dashboard
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">

        <div class="col-xs-12 text-right">
            <div class="row">
                <div class="col-md-4 pull-right">
                    <div class="form-group">
                        <label class="control-label">Filter berdasarkan tanggal</label>
                        <input type="text" name="date_filter" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control datepicker">
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box widget-box-two widget-two-primary">
                <i class="mdi mdi-anchor widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Parkir Mobil"></p>
                    <h3><span id="total-car-park"></span></h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box widget-box-two widget-two-default">
                <i class="mdi mdi-car widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Parkir Mobil"></p>
                    <h3><span id="total-car-park"></span></h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box widget-box-two widget-two-warning">
                <i class="mdi mdi-motorbike widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Parkir Mobil"></p>
                    <h3><span id="total-car-park"></span></h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box widget-box-two widget-two-success">
                <i class="mdi mdi-car-wash widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Parkir Mobil"></p>
                    <h3><span id="total-car-park"></span></h3>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    
    </div>

</div> <!-- container -->

@endsection

@push('js')
@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif
<script src="{{ url('assets/js/pages/dashboard.js') }}"></script>
@endpush
