@extends('layouts.master')

@section('title')
List of RB Capex
@endsection

@section('content')

@php($active = 'cpx')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of RB Capex</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        List of RB Capex
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
                    <div class="pesan" id="pesan_daftar"></div>
                    <div class="sebentar_daftar" id="sebentar_daftar"></div>


                    <div id="process_daftar" style="display:none;">
                        <div class="progress mt-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">

                                <select class="form-control" id="dept" name="dept" aria-label="Default select example">
                                    <option value="">--Pilih--</option>
                                    @foreach($dep_data as $val)
                                    <option value="{{$val->dept}}">{{$val->dept}}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="btn-download" class="btn btn-info btn-bordered waves-effect waves-light"> <i class="glyphicon glyphicon-save-file"></i> Download</button>
                            </div>
                        </div>
                    </div>

                    <table class="table m-0 table-colored table-inverse" id="table-cpx">
                        <thead>
                            <tr>
                                <th>Dept</th>
                                <th>Budget Number</th>
                                <th>Line or Dept</th>
                                <th>Profit Center</th>
                                <th>Profit Center Code</th>
                                <th>Cost Center</th>
                                <th>Type</th>
                                <th>Project Name</th>
                                <th>Import/Domestic</th>
                                <th>Item Name</th>
                                <th>Equipment</th>
                                <th>QTY</th>
                                <th>Curency</th>
                                <th>Original Price</th>
                                <th>Exchange Rate</th>
                                <th>Price</th>
                                <th>SOP</th>
                                <th>1st D Payment Term</th>
                                <th>1st D Payment Amount</th>
                                <th>Final Payment Term</th>
                                <th>Final Payment Amount</th>
                                <th>Owner Asset</th>
                                <th>april</th>
                                <th>mei</th>
                                <th>juni</th>
                                <th>juli</th>
                                <th>agustus</th>
                                <th>september</th>
                                <th>oktober</th>
                                <th>november</th>
                                <th>december</th>
                                <th>januari</th>
                                <th>februari</th>
                                <th>maret</th>
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
    show_notification("{{ session('title') }}", "{{ session('type') }}", "{{ session('message') }}");
</script>
@endif
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<!-- <script src="{{ url('assets/js/pages/cpxrb.js') }}"></script> -->
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var tSales = $('#table-cpx').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: SITE_URL + '/cpx/get_data',
            //      fnDrawCallback : function (oSettings) {
            //          budgetStatusStyler();
            //          budgetClosingStyler();
            //          budgetView();
            // if(is_budget==1){
            // 	xeditClasser();
            // 	initEditable();
            // 	initSelectable();
            // }
            //      },
            columns: [{
                    data: 'dept',
                    name: 'dept'
                },
                {
                    data: 'budget_no',
                    name: 'budget_no'
                },
                {
                    data: 'line',
                    name: 'line'
                },
                {
                    data: 'profit_center',
                    name: 'profit_center'
                },
                {
                    data: 'profit_center_code',
                    name: 'profit_center_code'
                },
                {
                    data: 'cost_center',
                    name: 'cost_center'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'project_name',
                    name: 'project_name'
                },
                {
                    data: 'import_domestic',
                    name: 'import_domestic'
                },
                {
                    data: 'items_name',
                    name: 'items_name'
                },
                {
                    data: 'equipment',
                    name: 'equipment'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'curency',
                    name: 'curency'
                },
                {
                    data: 'original_price',
                    name: 'original_price'
                },
                {
                    data: 'exchange_rate',
                    name: 'exchange_rate'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'sop',
                    name: 'sop'
                },
                {
                    data: 'first_dopayment_term',
                    name: 'first_dopayment_term'
                },
                {
                    data: 'first_dopayment_amount',
                    name: 'first_dopayment_amount'
                },
                {
                    data: 'final_payment_term',
                    name: 'final_payment_term'
                },
                {
                    data: 'final_payment_amount',
                    name: 'final_payment_amount'
                },
                {
                    data: 'owner_asset',
                    name: 'owner_asset'
                },
                {
                    data: 'april',
                    name: 'april'
                },
                {
                    data: 'mei',
                    name: 'mei'
                },
                {
                    data: 'juni',
                    name: 'juni'
                },
                {
                    data: 'juli',
                    name: 'juli'
                },
                {
                    data: 'agustus',
                    name: 'agustus'
                },
                {
                    data: 'september',
                    name: 'september'
                },
                {
                    data: 'oktober',
                    name: 'oktober'
                },
                {
                    data: 'november',
                    name: 'november'
                },
                {
                    data: 'december',
                    name: 'december'
                },
                {
                    data: 'januari',
                    name: 'januari'
                },
                {
                    data: 'februari',
                    name: 'februari'
                },
                {
                    data: 'maret',
                    name: 'maret'
                },
                {
                    data: null,
                    className: "center",
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (is_budget == 1) {
                            return '<button class="btn btn-danger btn-xs" data-toggle="tooltip"  title="Hapus" onclick="on_delete(' + data.id + ')"><i class="mdi mdi-close"></i></button> <form action="/capex/' + data.id + '" method="POST" id="form-delete-' + data.id + '" style="display:none"><input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="_method" value="DELETE"></form>'
                        } else {
                            return '';
                        }
                    }
                }
            ],
            drawCallback: function(d) {
                $('[data-toggle="popover"]').popover();
            }
        });

        $('#btn-confirm').click(function() {
            var sales_id = $(this).data('value');
            $('#form-delete-' + sales_id).submit();
        });

        $('#btn-download').click(function() {
            var dept = $('#dept').val()
            if (dept == "") {
                return false
            }
            $.ajax({
                type: "POST",
                url: "{{route('cpx.export')}}",
                data: {
                    dept: dept
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#sebentar_daftar').html("<div class='alert alert-warning mb-3' role='alert'>Processing dont close this page...!  <i class='fa fa-cloud-download'></i></div>");
                    $('#process_daftar').css('display', 'block');

                    $("#btn-download").attr('disabled', true);
                },
                success: function(data) {
                    var percentage = 0;

                    var timer = setInterval(function() {
                        percentage = percentage + 20;
                        progress_bar_process_daftar(percentage, timer, data);
                    }, 1000);

                },
                error: function(err) {
                    $('#sebentar_daftar').html("");
                    $('#pesan_daftar').html('');
                    $("#btn-download").removeAttr('disabled');
                    $('#process_daftar').css('display', 'none');
                    $('.progress-bar').css('width', '0%');
                }

            });
        })

    });

    function progress_bar_process_daftar(percentage, timer, data) {
        $('.progress-bar').css('width', percentage + '%');
        if (percentage > 100) {
            clearInterval(timer);
            $("#btn-download").removeAttr('disabled');
            $('#process_daftar').css('display', 'none');
            $('.progress-bar').css('width', '0%');

            $('#pesan_daftar').html("<div class='alert alert-success mb-3' role='alert'>Okey, Data excel berhasil di export</div>");
            $('#sebentar_daftar').html("");

            setTimeout(() => {
                $('#pesan_daftar').html('');
                try {

                    downloadfile(data);

                } catch (error) {
                    console.log(error)
                }
            }, 3000);

        }
    }

    function downloadfile(data) {
        return new Promise((resolve, reject) => {

            var $a = $("<a>");
            $a.attr("href", data.file);
            $("body").append($a);
            $a.attr("download", data.filename + ".xlsx");
            $a[0].click();
            $a[0].remove();
            resolve(true)

        });

    }
</script>
@endpush