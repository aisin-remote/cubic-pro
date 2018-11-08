@extends('layouts.master')

@section('title')
	Supplier
@endsection

@section('content')

@php($active = 'supplier')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Edit Supplier</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="{{ route('supplier.index') }}">Supplier</a>
                    </li>
                    <li class="active">
                        Edit Supplier
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
                    <form method="post" action="{{ route('supplier.update', $supplier->id) }}" id="form-add-edit">
                        @csrf
                        @method('PUT')

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Supplier Code <span class="text-danger">*</span></label>
                                <input type="text" name="supplier_code" class="form-control" placeholder="Supplier Code" required="required" value="{{ $supplier->supplier_code }}">
                                <span class="help-block"></span>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Supplier Name</label>
                                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" required="required" value="{{ $supplier->supplier_name }}">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Address</label>
                                <textarea rows="3" placeholder="Supplier Address" name="supplier_address" class="form-control" value="{{ $supplier->supplier_address }}"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Phone</label>
                                <input type="text" name="supplier_phone" class="form-control" placeholder="Supplier Phone" value="{{ $supplier->supplier_phone }}">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input type="text" name="supplier_email" class="form-control" placeholder="Supplier Email" value="{{ $supplier->supplier_email }}">
                            </div>
                        
                            <div class="form-group">
                                <label class="control-label">Website</label>
                                <input type="text" name="supplier_website" class="form-control" placeholder="Supplier website" value="{{ $supplier->supplier_website }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">PIC Name</label>
                                <input type="text" name="supplier_pic_name" class="form-control" placeholder="Supplier PIC Name" value="{{ $supplier->supplier_pic_name }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">PIC Phone</label>
                                <input type="text" name="supplier_pic_phone" class="form-control" placeholder="Supplier PIC Phone" value="{{ $supplier->supplier_pic_phone }}">
                                <span class="help-block"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">PIC Email</label>
                                <input type="text" name="supplier_pic_email" class="form-control" placeholder="Supplier PIC Email" value="{{ $supplier->supplier_pic_email }}">
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

@endsection

@push('js')
<script src="{{ url('assets/js/pages/supplier-add-edit.js') }}"></script>
@endpush