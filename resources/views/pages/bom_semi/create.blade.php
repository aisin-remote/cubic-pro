@extends('layouts.master')

@section('title')
    Upload BOM Semi Finish Good
@endsection

@section('content')

@php($active = 'bom_semi')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Create Bill Of Material (BOM) Semi Finish Good</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('bom.index') }}">Upload Bill Of Material (BOM) Semi Finish Good</a></li>
                    <li class="active">
                        Create Bill Of Material (BOM) Semi Finish Good
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
                    <form id="form-add-edit" action="{{ route('bom_semi.store') }}" method="post">
                        @csrf
                       <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Part Number<span class="text-danger">*</span></label>
                                <select name="part_id" class="select2" data-placeholder="Select Part Number" required="required">
                                    <option></option>
                                    @foreach ($parts as $part)
                                    <option value="{{ $part->id }}">{{ $part->part_number }} - {{ $part->part_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>

                           <div class="form-group">
                                <label class="control-label">Model<span class="text-danger">*</span></label>
                                <input type="text" name="model" placeholder="Model" class="form-control tinymce" required="required">
                                <span class="help-block"></span>
                           </div>     
                        </div>

                         <div class="col-md-6">
                           <div class="form-group">
                                <label class="control-label">Supplier Code<span class="text-danger">*</span></label>
                                <select name="supplier_id" class="select2" data-placeholder="Select Supplier Code" required="required">
                                    <option></option>
                                    @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>

                           <div class="form-group">
                                <label class="control-label">Reject Ratio <span class="text-danger">*</span></label>
                                <input type="text" name="reject_ratio" placeholder="Reject Ratio" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <hr>

                            <!-- <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button> -->

                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
             <button class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" onclick="on_details()"> Create BOM Detail Finish Good</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="details-table">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Supplier Name</th>
                            <th>Qty</th>
                            <th>Source</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Detail -->
<div class="modal fade in" tabindex="10" role="dialog" id="modal-details">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Create Detail Bom Finish Good</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-19">
                        <form method="post" enctype="multipart/form-data" id="form-details">
                            @csrf
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Part Number<span class="text-danger">*</span></label>
                                    <select name="part_id" class="select2" data-placeholder="Select Part Number" required="required">
                                        <option></option>
                                        @foreach ($parts as $part)
                                        <option value="{{ $part->id }}">{{ $part->part_number }} - {{ $part->part_name }}</option>
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
                                    <label class="control-label">Supplier Code<span class="text-danger">*</span></label>
                                    <select name="supplier_id" class="select2" data-placeholder="Select Supplier Code" required="required">
                                        <option></option>
                                        @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block"></span>
                               </div>

                               <div class="form-group">
                                    <label class="control-label">Qty <span class="text-danger">*</span></label>
                                    <input type="text" name="qty" placeholder="QTY" class="form-control number" required="required" rows="5"></input>
                                    <span class="help-block"></span>
                               </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btn-details-save" class="btn btn-primary btn-bordered waves-effect waves-light">Simpan</button>
                                <button type="button" class="btn btn-default btn-bordered waves-effect waves-light" data-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20" id="btn-save"> Simpan</a>
    <button type="button" class="btn btn-default btn-bordered waves-effect waves-light m-b-20" id="btn-reset"> Reset</a>
</div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/bom_semi-add-edit.js') }}"></script>

@endpush