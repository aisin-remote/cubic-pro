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
            <div class="card-box table-responsive">
                <table class="table table-colored table-inverse table-responsive" id="table-eps_tracking">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle">Approval Number</th>
                            <th colspan="6" style="text-align:center">Status PR</th>
                            <th rowspan="2" style="vertical-align:middle">Item Number</th>
                            <th rowspan="2" style="vertical-align:middle">Name Of Good</th>
                            <th rowspan="2" style="vertical-align:middle">Quantity</th>
                            <th rowspan="2" style="vertical-align:middle">UoM</th>
                            <th rowspan="2" style="vertical-align:middle">Unit Price (Rp.)</th>
                            <th rowspan="2" style="vertical-align:middle">Supplier Name</th>
                            <th rowspan="2" style="vertical-align:middle">PO Date</th>
                            <th rowspan="2" style="vertical-align:middle">PO Number</th>
                        </tr>
                        <tr>
                            <th>User Create PR Date</th>
                            <th>Validation Budget</th>
                            <th>Approved By Dept. Head</th>
                            <th>Approved by GM</th>
                            <th>Approved By BOD</th>
                            <th>Receiving Date By Purch.</th>
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