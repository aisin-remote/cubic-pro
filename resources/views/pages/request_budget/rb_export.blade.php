@extends('layouts.master')

@section('title')
    Export Template PNL
@endsection

@section('content')

@php($active = 'rb_export')

<style>
  .bs-example {
            margin: 20px;
        }

</style>
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
                            
                        
                        <div class="col-md-12 text-left">
                            <!-- <a href="{{ route('rb.exporttemplate') }}" class="btn btn-success" id="btn-export">Export To Excel</a> -->
                        
                            <div class="pesan" id="pesan_daftar"></div>
                                 <div class="sebentar_daftar" id="sebentar_daftar"></div>

                                    
                            <div id="process_daftar" style="display:none;">
                                <div class="progress mt-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                             <div class="panel panel-default">
                                    <div class="bs-example">
                                    <div class="container">

                                                
                                        <div class="row">
                                            <div class="col-lg-12 bg-light text-center">
                                                    Export Data Request Budget
                                                <p><small>*) File format .xlsx</small></p>
                                                <button class="btn btn-info btn-lg" id="export"> <i class="glyphicon glyphicon-save-file"></i> Export To Excel </button>
                                            </div>
                                        </div>
                                    </div>
                            
                                </div>
                            </div>
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
<script>

$(document).ready(function() {
     
     $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });

     $('#export').on('click', function(){
        $.ajax({                   
                type: "POST", 
                url: "{{route('rb.exporting-data')}}",
                dataType:'json', 
                beforeSend: function() {
                    $('#sebentar_daftar').html("<div class='alert alert-warning mb-3' role='alert'>Processing...</div>");
                    $('#process_daftar').css('display', 'block');

                    $("#export").attr('disabled', true);
                },
                success: function(data) {   
                    var percentage = 0;

                    var timer = setInterval(function() {
                        percentage = percentage + 20;
                        progress_bar_process_daftar(percentage, timer, data);
                    }, 1000);

                },
                error:function(err)
                    {
                        $('#sebentar_daftar').html("");
                        $('#pesan_daftar').html('');
                        $("#export").removeAttr('disabled');
                        $('#process_daftar').css('display', 'none');
                        $('.progress-bar').css('width', '0%');
                    }
                    
            }); 
     })


 })


 function progress_bar_process_daftar(percentage, timer, data) {
         $('.progress-bar').css('width', percentage + '%');
         if (percentage > 100) {
             clearInterval(timer);
             $("#export").removeAttr('disabled');
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