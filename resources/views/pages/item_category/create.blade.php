@extends('layouts.master')

@section('title')
	Create Item Category
@endsection

@section('content')

@php($active = 'item_category')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Item Category</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('item_category.index') }}">Item Category</a>
                    </li>
                    <li class="active">
                        Create Item Category
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <form method="post" action="{{ route('item_category.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Category Code <span class="text-danger">*</span></label>
                                <input type="text" name="category_code" class="form-control" placeholder="Category Code" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Category Name</label>
                                <input type="text" name="category_name" class="form-control" placeholder="Category Name" required="required">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Image</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Feature Image" readonly="readonly" name="feature_image">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-bordered waves-light waves-light btn-open-media" type="button">Browse</button>
                                    </span>
                                </div>
                            </div>
                              

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
    @include('pages.media.list')

@endsection

@push('js')
<script src="{{ url('assets/js/pages/item_category-add-edit.js') }}"></script>
<script src="{{ url('assets/js/pages/media.js') }}"></script>
@endpush