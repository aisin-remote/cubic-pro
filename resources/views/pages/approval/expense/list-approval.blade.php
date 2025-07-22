@extends('layouts.master')

@section('title')
    List of Expense Approval Sheet
@endsection

@section('content')
    @php($active = 'expense')

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title"> List of Expense Approval Sheet</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li class="active">
                            List of Expense Approval Sheet
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-12">
                <div id="chart1" style="height:400px;margin:0 auto"></div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    {{-- <button id="btn-approve-selected" class="btn btn-success mb-2">Approve Selected</button> --}}
                    @if (auth()->user() && auth()->user()->role->pluck('name')->contains('department-head'))
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <button id="btn-approve-selected" class="btn btn-success">Approve Selected</button>
                            <span id="selected-total" class="ms-3 fw-bold text-primary">Total: Rp 0</span>
                        </div>
                    @endif

                    <table class="table m-0 table-colored table-inverse" id="table-list-approval-expense">
                        <thead>
                            <tr>
                                @if (auth()->user() && auth()->user()->role->pluck('name')->contains('department-head'))
                                    <th><input type="checkbox" id="select-all" /></th>
                                @endif
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
                    <button type="submit" id="btn-confirm"
                        class="btn btn-danger btn-bordered waves-effect waves-light">Hapus</button>
                    <button type="button" class="btn btn-default btn-bordered waves-effect waves-light"
                        data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.isDeptHead = @json(auth()->user() && auth()->user()->role->pluck('name')->contains('department-head') ? 'department-head' : '');
    </script>

    @if (session()->has('message'))
        <script type="text/javascript">
            show_notification("{{ session('title') }}", "{{ session('type') }}", "{{ session('message') }}");
        </script>
    @endif
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="{{ url('assets/js/pages/list-approval-expense-unvalidated.js') }}"></script>
@endpush
