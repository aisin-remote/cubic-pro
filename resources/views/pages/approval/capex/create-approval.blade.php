@extends('layouts.master')

@section('title')
    Create Capex Approval Sheet
@endsection

@section('content')

@php($active = 'capex')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> Create Capex Approval Sheet</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                        @if (\Entrust::hasRole('user')) 
                         <a href="{{ route('approval-capex.create') }}" class="btn btn-primary btn-bordered waves-effect waves-light m-b-20"><i class="mdi mdi-plus"></i> Create Capex Approval Sheet</a>
                        
                    </li>
                    <li>
                        <form action="{{route('approval_capex.approval')}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success btn-bordered waves-effect waves-light m-b-20"> Submit Approval</a>
                        </form>
                        @endif
                    </li>
                </ol>
            </div>
        </div>
    </div>
    

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-approval-capex">
                    <thead>
                        <tr>
                            
                           <th>Budget No.</th>
                            <th>Project Name</th>
                            <th>Actual Qty</th>
                            <th>Actual Price</th>
                            <th>Actual GR</th>
                            <th>Asset Kind</th>         
                            <th>Settlement Date</th>    
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

<script src="{{ url('assets/js/pages/approval-capex.js') }}"></script>
<script>
    v_widget_type="chart_gold_antam";
    v_period=360;//hari
    v_width=400;
    v_height=300;
    he_org_show_chart(v_widget_type,v_period,v_width,v_height,'div_chart_antam');

    function he_org_show(v_widget_type,v_width,v_height,div)
    {
        document.getElementById(div).innerHTML="<iframe src='//harga-emas.org/widget/widget.php?v_widget_type="+v_widget_type+"&v_height="+v_height+"' width='"+v_width+"' height='"+v_height+"' style='border: solid 1px;'></iframe>";
    }
    function he_org_show_chart(v_widget_type,v_period,v_width,v_height,div)
    {
        document.getElementById(div).innerHTML="<iframe src='//harga-emas.org/widget/widget.php?v_widget_type="+v_widget_type+"&v_period="+v_period+"' width='"+v_width+"' height='"+v_height+"' style='border: solid 1px;'></iframe>";
    }
</script>


@endpush
