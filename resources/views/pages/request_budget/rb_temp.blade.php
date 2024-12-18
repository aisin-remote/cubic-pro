@extends('layouts.master')

@section('title')
    Temp
@endsection

@section('content')

@php($active = 'rb_temp')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Trial</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('masterprice.index') }}">Trial</a></li>
                    <li class="active">
                        Testing template
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
                    <form action="{{ route('temp.import') }}" method="post" enctype="multipart/form-data" id="form-import">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Testing Template</label>
                                <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls">
                                <label class="text-muted">*) File format .csv,.xlsx,.xls</label>
                                <br>
                                <!-- <a href="{{ url('files/Template_Direct_Material.xlsx') }}" ><i class="mdi mdi-download"></i>  Format RB Direct Material Unit &emsp;</a>
                                <a href="{{ url('files/Template_Direct_Material_body') }}" ><i class="mdi mdi-download"></i>  Format RB Direct Material Body</a> -->
                            </div>
                            <!-- <div class="form-group">
                                <label>
                                <input type="checkbox" name="overwrite"> Overwrite (BE CAREFUL! All table records DELETED!)
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                <input type="checkbox" name="revision"> Is This a budget revision ? (Please checked for YES)
                                </label>
                            </div> -->
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

@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif

@endpush
