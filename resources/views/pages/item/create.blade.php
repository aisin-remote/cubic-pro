@extends('layouts.master')

@section('title')
	Create Item
@endsection

@section('content')

@php($active = 'Item')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Item</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('item.index') }}">Item</a>
                    </li>
                    <li class="active">
                        Create Item
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
                @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    <form method="post" action="{{ route('item.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Item Category<span class="text-danger">*</span></label>
                                <select name="item_category_id" class="select2" data-placeholder="Item Category">
                                    <option></option>
                                    @foreach ($item_category as $item_category)
                                    <option value="{{ $item_category['id'] }}">{{ $item_category->category_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">Item Code<span class="text-danger">*</span></label>
                                <input type="text" name="item_code" class="form-control" placeholder="Item Code" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Description<span class="text-danger">*</span></label>
                                <input type="text" name="item_description" class="form-control" placeholder="Item Description" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Spesification<span class="text-danger">*</span></label>
                                <input type="text" name="item_specification" class="form-control" placeholder="Item Specification" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Brand<span class="text-danger">*</span></label>
                                <input type="text" name="item_brand" class="form-control" placeholder="Item Brand" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Price<span class="text-danger">*</span></label>
                                <input type="text" name="item_price" class="form-control autonumeric text-right" placeholder="Item Price" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">UoM<span class="text-danger">*</span></label>
                                <select name="uom_id" class="select2" data-placeholder="Unit Of material">
                                    <option></option>
                                    @foreach ($uom as $uom)
                                    <option value="{{ $uom['id'] }}">{{ $uom->uom_fname }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Supplier<span class="text-danger">*</span></label>
                                <select name="supplier_id" class="select2" data-placeholder="Supplier" >
                                    <option></option>
                                    @foreach ($supplier as $supplier)
                                    <option value="{{ $supplier['id'] }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Lead Times<span class="text-danger">*</span></label>
                                <input type="text" name="lead_times" class="form-control" placeholder="Lead Times" required="required">
                                <span class="help-block"></span>
                            </div>
                              
                            <div class="form-group">
                                <label class="control-label">Remarks</label>
                                <textarea rows="5" placeholder="Remarks" name="remarks" class="form-control"></textarea>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Tag</label>
                                <select name="tags[]" class="select2" multiple="true" data-tags="true" data-placeholder="Select or type tags">
                                    @foreach ($tags as $tag) 
                                        <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
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

                            <div class="form-group">
                                <label class="control-label">File</label>
                                <div class="input-group">
                                    <input type="file" name="feature_file" class="form-control" accept=".pdf">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Save</button>

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