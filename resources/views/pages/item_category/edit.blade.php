@extends('layouts.master')

@section('title')
	Item Category
@endsection

@section('content')

@php($active = 'item_category')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Item Category</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('item_category.index') }}">Item Category</a>
                    </li>
                    <li class="active">
                        Edit Item Category
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
                    <form method="post" action="{{ route('item_category.update', $item_category->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Category Code <span class="text-danger">*</span></label>
                                <input type="text" name="category_code" class="form-control" placeholder="Category Code" required="required" value="{{ $item_category->category_code }}">
                                <span class="help-block"></span>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Category Name</label>
                                <input type="text" name="category_name" class="form-control" placeholder="Category Name" required="required" value="{{ $item_category->category_name }}">
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Update</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ url('assets/js/pages/item_category-add-edit.js') }}"></script>
@endpush