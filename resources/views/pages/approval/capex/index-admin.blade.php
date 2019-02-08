@extends('layouts.master')

@section('title')
    List of Capex Approval Sheet
@endsection

@section('content')

@php($active = 'capex')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of Capex Approval Sheet</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                         List of Capex Approval Sheet
                    </li>
                    <li>
                        <!-- Added by Ferry, July 6th 2015 for special input budget officer to catch up -->
                        @if (\Entrust::can('approval_create'))
                            <a href="{{ url('approval/create/cx') }}" class="btn btn-primary" id="create">Create Capex Approval</a>
                        @endif
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
                <table class="table m-0 table-colored table-inverse" id="table-approval-capex">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Approval Number</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Overbudget Info</th>
                            <th style="width: 100px">Opsi</th>
                        </tr>
                    </thead>
                </table>
            </div>  
        </div>
    </div>

</div>

<!-- Modal for question -->
<div class="modal fade in" tabindex="-1" role="dialog" id="modal-delete-confirm">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Apakah anda yakin?</h4>
            </div>
            <div class="modal-body">Data yang dipilih akan dihapus, apakah anda yakin?</div>
            <div class="modal-footer">
                <button type="submit" id="btn-confirm" class="btn btn-danger btn-bordered waves-effect waves-light">Hapus</button>
                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
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

<script src="{{ url('assets/js/pages/list-approval-capex.js') }}"></script>
 <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

@endpush