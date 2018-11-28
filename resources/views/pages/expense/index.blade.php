@extends('layouts.master')

@section('title')
    List of Expense
@endsection

@section('content')

@php($active = 'expense')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of Expense</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                         List of Expense
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-sm-4">
             <a href="{{ route('expense.create') }}" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Expense</a>
        </div><!-- end col -->
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-expense">
                    <thead>
                        <tr>
                            <th>Bdgt. Number</th>
                            <th width="10">Description</th>
                            <th>Qty Plan</th>
                            <th>Qty Used</th>
                            <th>Qty Remain</th>
                            <th>Bdgt Plan</th>
                            <th>Bdgt Used</th>
                            <th>Bdgt Remain</th>
                            <th>Plan GR</th>
                            <th>Status</th>
                            <th>Closing</th>
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

<script src="{{ url('assets/js/pages/expense.js') }}"></script>

@endpush