@extends('layouts.master')

@section('title')
	GR Confirmation
@endsection

@section('content')

@php($active = 'gr_confirm')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create GR Confimration</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('gr_confirm.index') }}">Upload GR Confimration</a></li>
                    <li class="active">
                        Create GR Confimration
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
                    <form id="form-add-edit" action="{{ route('gr_confirm.store') }}" method="post">
                        @csrf
                       <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">PO Number<span class="text-danger">*</span></label>
                                <select name="po_number" class="select2" data-placeholder="Select PO Number" required="required">
                                    <option></option>
                                    @foreach ($po as $po)
                                    <option value="{{ $po->po_number }}">{{ $po->po_number }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Approval Number<span class="text-danger">*</span></label>
                                <input type="text" name="approval_number" class="form-control tinymce" readonly="readonly">
                                <span class="help-block"></span>
                           </div>    
                        </div>

                         <div class="col-md-6">
                           
                           <div class="form-group">
                                <label class="control-label">PIC</label>
                                <input type="text" name="user_name" class="form-control tinymce" readonly="readonly">
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Department</label>
                                <input type="text" name="department_name" class="form-control tinymce" readonly="readonly">
                                <span class="help-block"></span>
                           </div>
                        </div>
                        
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="details-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Specification</th>
                            <th>UoM</th>
                            <th>Qty Order</th>
                            <th>GR Number</th>
                            <th>GR Amount</th>
                            <th>GR Date</th>
                            <th>Notes</th>
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
                <h4 class="modal-title">Create Detail gr_confirm Finish Good</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-19">
                        <form method="post" enctype="multipart/form-data" id="form-details">
                            @csrf
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Part Number<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>

                               <div class="form-group">
                                    <label class="control-label">Part Name<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>

                               <div class="form-group">
                                    <label class="control-label">UoM<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>     
                               <div class="form-group">
                                    <label class="control-label">Qty Order<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>
                            </div>

                             <div class="col-md-6">
                               <div class="form-group">
                                    <label class="control-label">Qty Receive<span class="text-danger">*</span></label>
                                    <input type="text" name="qty_receive" placeholder="QTY Receive" class="form-control number" required="required" rows="5"></input>
                                    <span class="help-block"></span>
                               </div>
                               <div class="form-group">
                                    <label class="control-label">Qty Outstanding<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>
                               <div class="form-group">
                                    <label class="control-label">GR Number<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>
                               <div class="form-group">
                                    <label class="control-label">GR Amount<span class="text-danger">*</span></label>
                                    
                                    <span class="help-block"></span>
                               </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btn-details-save" class="btn btn-primary btn-bordered waves-effect waves-light">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20" id="btn-save"> Save</a>
    <button type="button" class="btn btn-default btn-bordered waves-effect waves-light m-b-20" id="btn-reset"> Reset</a>
</div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/gr_confirm-add-edit.js') }}"></script>

@endpush