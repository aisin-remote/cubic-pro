@extends('layouts.master')

@section('title')
    Upload Expense
@endsection

@section('content')
    @php($active = 'rb_expense')

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Upload Request Budget Expense</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li><a href="{{ route('masterprice.index') }}">Upload Request Budget Expense</a></li>
                        <li class="active">
                            Upload Request Budget Expense
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
                        <form method="post" enctype="multipart/form-data" id="form-import">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">RB Expense File Input</label>
                                    <input type="file" id="file" name="file" class="form-control"
                                        accept=".csv,.xlsx,.xls,.xlsm">
                                    <label class="text-muted">*) File format .csv,.xlsx,.xls,.xlsm</label>
                                    <br>
                                    <a href="{{ url('files/Template_Expense_New_2024_New.xlsm') }}"><i
                                            class="mdi mdi-download"></i>
                                        Format RB Expense
                                    </a>
                                    <!-- <a href="{{ url('files/Template_Expense_body') }}" ><i class="mdi mdi-download"></i>  Format RB Expense Body</a -->
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
                                <button type="button" id="btn-import"
                                    class="btn btn-primary btn-bordered waves-effect waves-light">Upload</button>
                                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light"
                                    data-dismiss="modal">Cancel</button>
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
            show_notification("{{ session('title') }}", "{{ session('type') }}", "{{ session('message') }}");
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

        $('#btn-import').click(function() {
            var files = $('#file')[0].files;
            if (files.length <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Files upload tidak boleh di kosongkan!',
                })

                return;
            }
            var fd = new FormData();

            // Append data 
            fd.append('file', files[0]);


            $.ajax({
                type: "POST",
                url: "{{ route('exps.importcek') }}",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: fd,
                beforeSend: function() {
                    $('#btn-import').html('loading...')
                    $('#btn-import').attr('disabled', true)
                },
                success: function(data) {
                    $('#btn-import').html('Upload')
                    $('#btn-import').removeAttr('disabled')
                    if (data.success) {

                        if (data.total != 0) {
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Total : " + data.total,
                                text: 'Data masih kosong',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                window.location.reload(true);
                            })
                        }
                    } else {
                        var pes = 'Data masih ada decimal (koma) value, cek kembali.!'
                        if (data.pesan != '') {
                            pes = data.pesan
                        }
                        Swal.fire({
                            icon: 'error',
                            title: "Total : " + data.total,
                            text: pes,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            window.location.reload(true);
                        })
                    }

                },
                error: function(err) {
                    alert("error cek request" + err.responseText)
                    window.location.reload(true);
                    $('#btn-import').html('Upload')
                    $('#btn-import').removeAttr('disabled')
                }

            });


        })

    });

    function upload() {
        var files = $('#file')[0].files;
        if (files.length > 0) {
            var fd = new FormData();

            // Append data 
            fd.append('file', files[0]);


            $.ajax({
                type: "POST",
                url: "{{ route('exps.import') }}",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: fd,
                beforeSend: function() {
                    $('#btn-import').html('loading...')
                    $('#btn-import').attr('disabled', true)
                },
                success: function(data) {
                    $('#btn-import').html('Upload')
                    $('#btn-import').removeAttr('disabled')
                    show_notification(data.title, data.type, data.message)

                    $('#form-import')[0].reset()
                },
                error: function(err) {
                    $('#btn-import').html('Upload')
                    $('#btn-import').removeAttr('disabled')
                    console.log(err)
                }

            });
        }

    }
</script>
