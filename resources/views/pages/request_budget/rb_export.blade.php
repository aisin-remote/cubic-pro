@extends('layouts.master')

@section('title')
    Export Template PNL
@endsection

@section('content')

@php($active = 'rb_export')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Export Data Request Budget</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('masterprice.index') }}">Export Data Request Budget</a></li>
                    <li class="active">
                        Export Data Request Budget
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
     <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Export Data Request Budget</label>
                                <br>
                                <label class="text-muted">*) File format .xlsx</label>
                                
                        </div>
                        
                        <div class="col-md-12 text-left">
                            <a href="{{ route('rb.exporttemplate') }}" class="btn btn-success" id="btn-export">Export To Excel</a>
                        </div>
                    </form>
                  </div>
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

@endpush