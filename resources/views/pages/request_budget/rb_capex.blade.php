@extends('layouts.master')

@section('title')
    Upload Capex
@endsection

@section('content')

@php($active = 'rb_capex')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Upload Request Budget Capex</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('masterprice.index') }}">Upload Request Budget Capex</a></li>
                    <li class="active">
                        Upload Request Budget Capex
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
                    <form  method="post" enctype="multipart/form-data" id="form-import">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">RB capex File Input</label>
                                <input type="file" id="file" name="file" class="form-control" accept=".csv,.xlsx,.xls">
                                <label class="text-muted">*) File format .csv,.xlsx,.xls</label>
                                <br>
                                <a href="{{ url('files\Template_Capex.xlsx') }}" ><i class="mdi mdi-download"></i>  Format RB Capex &emsp;</a>
                                <!-- <a href="{{ url('files/Template_Capex_body') }}" ><i class="mdi mdi-download"></i>  Format RB Capex Body</a> -->
                            </div>
                         
                        </div>

                        <div class="col-md-12 text-left">
                            <button type="button" id="btn-import" class="btn btn-primary btn-bordered waves-effect waves-light" onclick="on_table_temporary()">Upload</button>
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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

$(document).ready(function() {
     
     $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });

     $('#btn-import').click(function(){
        var files = $('#file')[0].files;
        console.log(files)
        if(files.length > 0){
        var fd = new FormData();

        // Append data 
        fd.append('file',files[0]);


        $.ajax({                   
                type: "POST", 
                url: "{{route('cpx.importcek')}}",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: fd,
                success: function(data) {   
                  if(data.total != 0){
                    Swal.fire({
                    title: 'Are you sure?',
                    text: "Total : " + data.total,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, upload it!'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        upload()
                    }
                    })
                  }

                },
                error:function(err)
                    {
                       alert("error cek request")
                    }
                    
            }); 

        }
     })

});

function upload(){
    var files = $('#file')[0].files;
        if(files.length > 0){
        var fd = new FormData();

        // Append data 
        fd.append('file',files[0]);


        $.ajax({                   
                type: "POST", 
                url: "{{route('cpx.import')}}",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: fd,
                success: function(data) {   
                    
                    show_notification(data.title,data.type,data.message)

                    $('#form-import')[0].reset()
                },
                error:function(err)
                    {
                        
                       console.log(err)
                    }
                    
            }); 
        }

}

    
</script>