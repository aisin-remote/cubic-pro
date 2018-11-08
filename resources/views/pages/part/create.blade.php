@extends('layouts.master')

@section('title')
	Create Part
@endsection

@section('content')

@php($active = 'Part')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Part</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('part.index') }}">Part</a>
                    </li>
                    <li class="active">
                        Create Part
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
                    <form method="post" action="{{ route('part.store') }}" id="form-add-edit">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" name="part_number" class="form-control" placeholder="Part Number" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Part Name <span class="text-danger">*</span></label>
                                <input type="text" name="part_name" class="form-control" placeholder="Part Name" required="required">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">UoM<span class="text-danger">*</span></label>
                                <select name="uom" class="select2" data-placeholder="Unit Of Material" required="required">
                                    <option></option>
                                    @foreach ($uom as $uom)
                                    <option value="{{ $uom['id'] }}">{{ $uom['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Plant</label>
                                <select name="plant" class="select2" data-placeholder="Plant">
                                    <option></option>
                                    @foreach ($plant as $plant)
                                    <option value="{{ $plant['id'] }}">{{ $plant['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Part Category</label>
                                <select name="category_part" class="select2" data-placeholder="Part Category">
                                    <option></option>
                                    @foreach ($category_part as $category_part)
                                    <option value="{{ $category_part['id'] }}">{{ $category_part['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Product Code</label>
                                <select name="product_code" class="select2" data-placeholder="Product Code" >
                                    <option></option>
                                    @foreach ($product_code as $product_code)
                                    <option value="{{ $product_code['id'] }}">{{ $product_code['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Category Finish Good</label>
                                <select name="category_fg" class="select2" data-placeholder="Category Finish Good">
                                    <option></option>
                                    @foreach ($category_fg as $category_fg)
                                    <option value="{{ $category_fg['id'] }}">{{ $category_fg['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Assy Part</label>
                                <select name="assy_part" class="select2" data-placeholder="Assy Part" >
                                    <option></option>
                                    @foreach ($assy_part as $assy_part)
                                    <option value="{{ $assy_part['id'] }}">{{ $assy_part['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Group Material</label>
                                <select name="group_material" class="select2" data-placeholder="Group Material" >
                                    <option></option>
                                    @foreach ($group_material as $group_material)
                                    <option value="{{ $group_material['id'] }}">{{ $group_material['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
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

@endsection

@push('js')
<script src="{{ url('assets/js/pages/part-add-edit.js') }}"></script>
@endpush