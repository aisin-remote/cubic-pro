@extends('layouts.master')

@section('title')
	Create Price Catalogue
@endsection

@section('content')

@php($active = 'price_catalogue')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Master Price Catalogue</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('price_catalogue.index') }}">Upload Master Price Catalogue</a></li>
                    <li class="active">
                        Create Master Price Catalogue
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
                    <form id="form-add-edit" action="{{ route('price_catalogue.store') }}" method="post">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Fiscal Year<span class="text-danger">*</span></label>
                                <input type="text" name="fiscal_year" placeholder="Fiscal Year" class="form-control tinymce" required="required">
                                <span class="help-block"></span>
                           </div>
                            <div class="form-group">
                                <label class="control-label">Supplier Code</label>
                                <select name="supplier_id" class="select2" data-placeholder="Select Supplier Code" required="required">
                                    <option></option>
                                    @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>

                           <div class="form-group">
                                <label class="control-label">Source<span class="text-danger">*</span></label>
                                <input type="text" name="source" placeholder="Source" class="form-control tinymce" required="required">
                                <span class="help-block"></span>
                           </div>

                           
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Part Number</label>
                                <select name="part_id" class="select2" data-placeholder="Select Part Number" required="required">
                                    <option></option>
                                    @foreach ($parts as $part)
                                    <option value="{{ $part->id }}">{{ $part->part_number }} - {{ $part->part_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           

                           <div class="form-group">
                                <label class="control-label">Price<span class="text-danger">*</span></label>
                                <input type="number" name="price" placeholder="Price" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
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



@endsection

@push('js')
<script src="{{ url('assets/js/pages/price_catalogue-add-edit.js') }}"></script>
@endpush