@extends('layouts.master')

@section('title')
    Upload Expense
@endsection

@section('content')

@php($active = 'expense')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Upload Expense</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('expense.index') }}">Upload Expense</a></li>
                    <li class="active">
                        Upload Expense
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
                    <form action="{{ route('expense.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Expense File Input</label>
                                <input type="file" name="file" class="form-control" accept=".csv">
                                <label class="text-muted">*) File format .csv</label>
                                <br>
                                <a href="{{ url('files/EX-Upload.csv') }}" ><i class="mdi mdi-download"></i>  Format Expense</a>
                            </div>
                            <div class="form-group">
                                <label>
                                <input type="checkbox" name="overwrite"> Overwrite (BE CAREFUL! All table records DELETED!)
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                <input type="checkbox" name="revision"> Is This a budget revision ? (Please checked for YES)
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12 text-left">
                            <button type="submit" id="btn-import" class="btn btn-primary btn-bordered waves-effect waves-light" onclick="on_table_temporary()">Upload</button>
                            <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                  </div>
              </div>
          </div>
      </div>
</div>



@endsection

@push('js')
<script src="{{ url('assets/js/pages/expense-add-edit.js') }}"></script> 
@endpush