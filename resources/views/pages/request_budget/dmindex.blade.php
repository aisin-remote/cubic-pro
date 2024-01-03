@extends('layouts.master')

@section('title')
    List of RB Direct Material
@endsection

@section('content')

@php($active = 'dm')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of RB Direct Material</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                         List of RB Direct Material
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-sm-4">
            <!-- @if (\Entrust::hasRole('budget'))
             <a href="{{ url('capex/create') }}" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Capex</a>
             <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
            @endif -->
        </div><!-- end col -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table m-0 table-colored table-inverse" id="table-dm">
                        <thead>
                            <tr>
                                <th>Dept</th>
                                <th>Account Code</th>
                                <th>Account Name</th>
                                <th>Group</th>
                                <th>Code</th>
                                <th>April-24</th>
                                <th>Mei-24</th>
                                <th>Juni-24</th>
                                <th>Juli-24</th>
                                <th>Agustus-24</th>
                                <th>September-24</th>
                                <th>Oktober-24</th>
                                <th>November-24</th>
                                <th>December-24</th>
                                <th>Januari-25</th>
                                <th>Februari-25</th>
                                <th>Maret-25</th>
                                <th>Fy 2024 1st</th>
                                <th>Fy 2024 2nd</th>
                                <th>Fy 2024 total</th>
                                @if (\Entrust::hasRole('budget'))
                                    <th style="width: 100px">Opsi</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<input type="hidden" id="is_budget" value="{{\Entrust::hasRole(['budget'])?'1':'0'}}">
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
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ url('assets/js/pages/dmrb.js') }}"></script>

@endpush
