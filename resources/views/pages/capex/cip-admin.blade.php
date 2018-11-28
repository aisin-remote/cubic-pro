@extends('layouts.master')

@section('title')
    Cip Administrator
@endsection

@section('content')

@php($active = 'capex')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Cip Administrator</h4>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    
                    @csrf

                    <div class="col-lg-6">
                        <div class="portlet">
                            <div class="portlet-heading portlet-default">
                                <h3 class="portlet-title text-dark">
                                    Convert CIP From Immediate Use
                                </h3>
                                
                                <div class="portlet-widgets">
                                    <button id="btn_convert" type="submit" class="btn btn-primary" onclick="convertCIP($('#budget_no').val());">Convert to CIP</button>
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-default" class="" aria-expanded="true"><i class="ion-minus-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="bg-default" class="panel-collapse collapse in" aria-expanded="true" style="">
                               <div class="portlet-body">
                                    <label class="control-label">Budget No<span class="text-danger">*</span></label>
                                    <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required">
                                        <option></option>
                                    
                                    </select>
                                <span class="help-block"></span>
                               </div> 
                                <div class="portlet-body">
                                    <label class="control-label">Budget Name <span class="text-danger">*</span></label>
                                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" readonly="readonly" rows="5"></inout>
                                    <span class="help-block"></span>
                                </div>
                                <div class="portlet-body">
                                    <label class="control-label">Settlement Date <span class="text-danger">*</span></label>
                                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" rows="5"></inout>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="portlet">
                            <div class="portlet-heading portlet-default">
                                <h3 class="portlet-title text-dark">
                                    Extend CIP Settlement Date
                                </h3>
                                
                                <div class="portlet-widgets">
                                    <button id="btn_resettle" type="submit" class="btn btn-primary" onclick="resettleCIP($('#budget_no_cip').val());">Resettle Date</button>
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-default" class="" aria-expanded="true"><i class="ion-minus-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="bg-default" class="panel-collapse collapse in" aria-expanded="true" style="">
                               <div class="portlet-body">
                                    <label class="control-label">Budget No<span class="text-danger">*</span></label>
                                    <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required">
                                        <option></option>
                                    
                                    </select>
                               </div> 
                                <div class="portlet-body">
                                    <label class="control-label">Budget Name <span class="text-danger">*</span></label>
                                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" readonly="readonly" rows="5"></inout>
                                    <span class="help-block"></span>
                                </div>
                                <div class="portlet-body">
                                    <label class="control-label">New Settlement Date <span class="text-danger">*</span></label>
                                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" rows="5"></inout>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@push('js')
<script src="{{ url('assets/js/pages/cip-admin-add-edit.js') }}"></script>

@endpush