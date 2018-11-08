@extends('layouts.master')

@section('title')
	Tambah Pertanyaan dan Jawaban Frekuensi
@endsection

@section('content')

@php($active = 'faq')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Tambah Pertanyaan dan Jawaban Frekuensi</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('faq.index') }}">Pertanyaan dan Jawaban Frekuensi</a></li>
                    <li class="active">
                        Tambah Pertanyaan dan Jawaban Frekuensi
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <form id="form-add-edit" action="{{ route('faq.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <div class="card-box">
                   <div class="form-group">
                        <label class="control-label">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" name="question" placeholder="Pertanyaan" class="form-control input-lg" required="required">
                        <span class="help-block"></span>
                   </div>

                   <div class="form-group">
                        <label class="control-label">Jawaban <span class="text-danger">*</span></label>
                        <textarea name="answer" placeholder="Jawaban" class="form-control tinymce" required="required" rows="5"></textarea>
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
<script src="{{ url('assets/js/pages/faq-add-edit.js') }}"></script>
@endpush