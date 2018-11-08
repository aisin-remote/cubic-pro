@extends('layouts.master')

@section('title')
	Tambah Bantuan
@endsection

@section('content')

@php($active = 'help')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Tambah Bantuan</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('help.index') }}">Bantuan</a></li>
                    <li class="active">
                        Tambah Bantuan
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <form id="form-add-edit" action="{{ route('help.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <div class="card-box">
                   <div class="form-group">
                        <label class="control-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" placeholder="Judul" class="form-control input-lg" required="required">
                        <span class="help-block"></span>
                   </div>

                   <div class="form-group">
                        <label class="control-label">Isi <span class="text-danger">*</span></label>
                        <textarea name="description" placeholder="Isi" class="form-control tinymce" required="required" rows="5"></textarea>
                        <span class="help-block"></span>
                   </div>

                </div>
            </div>

            <div class="col-md-12 text-right">
                <hr>

                <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>
                <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button>

            </div>

            <div class="clearfix"></div>
        </form>
    </div>

</div>



@endsection

@push('js')
<script src="{{ url('assets/js/pages/help-add-edit.js') }}"></script>
@endpush