@extends('layouts.master')

@section('title')
	Item
@endsection

@section('content')

@php($active = 'item')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Item</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('item.index') }}">Item</a>
                    </li>
                    <li class="active">
                        Edit Item
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
                    <form method="post" action="{{ route('item.update', $item->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            
                            
                            <div class="form-group">
                                <label class="control-label">Item Code<span class="text-danger">*</span></label>
                                <input type="text" name="item_code" class="form-control" placeholder="Item Code" required="required" value="{{ $item->item_code }}">
                                <span class="help-block"></span>
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
    @include('pages.media.list')

@endsection

@push('js')
<script src="{{ url('assets/js/pages/item-add-edit.js') }}"></script>
<script src="{{ url('assets/js/pages/media.js') }}"></script>
@endpush