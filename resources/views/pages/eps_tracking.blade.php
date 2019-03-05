@extends('layouts.master')

@section('title')
	EPS Tracking
@endsection

@section('content')

@php($active = 'eps_tracking')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">EPS Tracking</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        EPS Tracking
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
                <table class="table m-0 table-colored table-inverse" id="table-eps_tracking">
                    <thead>
                        <tr>
                            <th>Fiscal Year</th>
                            <th>Approval Number</th>
                            <th>Budget Type</th>
                            <th>Budget Number</th>
                            <th>Description</th>
                            <th>Specification</th>
                            <th>Qty</th>
                            <th>UoM</th>
                            <th>Actual Price Purchasing</th>
                            <th>Status Approval</th>
                            <th>Status GR</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')

@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif

<script src="{{ url('assets/js/pages/eps_tracking.js') }}"></script>
@endpush