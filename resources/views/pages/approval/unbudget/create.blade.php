@extends('layouts.master')

@section('title')
    Add Purchase Request Item.
@endsection

@section('content')

@php($active = 'unbudget')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Add Purchase Request Item.</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('approval-unbudget.create') }}">Create Unbudget Approval Sheet</a></li>
                    <li class="active">
                        Add Purchase Request Item.
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
                    <form id="form-add-edit" action="{{route('approval_unbudget.store')}}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Type <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="type" id="type-1" value="1" checked="">
                                                    <label for="type-1">
                                                        Capex
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="type" id="type-0" value="0" checked="">
                                                    <label for="type-0">
                                                        Expense
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   </div>  
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Item Category <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_category" id="asset_category-1" value="1" checked="">
                                                    <label for="asset_category-1">
                                                        Non Chemical
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_category" id="asset_category-0" value="0" checked="">
                                                    <label for="asset_category-0">
                                                        Chemical
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   </div>  
                                </div>
                            </div>
                        </div>
                               
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <label class="control-label">Asset Type<span class="text-danger">*</span></label>
                                <select name="sap_asset_id" class="select2" data-placeholder="Select Asset Type" required="required">
                                    <option></option>
                                        @foreach ($sap_assets as $sap_asset)
                                        <option value="{{ $sap_asset->id }}">{{ $sap_asset->asset_type }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">G/L Group<span class="text-danger">*</span></label>
                                <select name="sap_gl_account_id" class="select2" data-placeholder="Select G/L Group" required="required">
                                    <option></option>
                                        @foreach ($sap_gl_account as $sap_gl_account)
                                        <option value="{{ $sap_gl_account->id }}">{{ $sap_gl_account->gl_gname }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Project Name/Purpose <span class="text-danger">*</span></label>
                                <input type="type" name="project_name" placeholder="Project Name/Purpose" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>      
                            
                            <div class="form-group">
                                <label class="control-label">Purchase Request Item Detail <span class="text-danger">*</span></label>
                                <textarea type="text" name="remarks" placeholder="Purchase Request Item Detail" class="form-control tinymce" required="required" rows="5"></textarea>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Unit <span class="text-danger">*</span></label>
                                <select name="sap_uom_id" class="select2" data-placeholder="Select Unit Of Measeure" required="required">
                                    <option></option>
                                        <option></option>
                                        @foreach ($sap_uoms as $sap_uom)
                                        <option value="{{ $sap_uom->id }}">{{ $sap_uom->uom_code }} - {{ $sap_uom->uom_fname }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div> 
                           <div class="form-group">
                                <label class="control-label">Actual GR <span class="text-danger">*</span></label>
                                <input  name="plan_gr" placeholder="Actual GR" class="form-control datepicker" required="required" value="{{ Carbon\Carbon::now()->format('M-D-Y') }}"></input>
                                <span class="help-block"></span>
                           </div>  
                          
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Asset Code<span class="text-danger">*</span></label>
                                <input type="text" name="asset_code" placeholder="Asset Code" class="form-control tinymce" required="required" rows="5" readonly="readonly"></input>
                                <span class="help-block"></span>
                           </div>  


                           <div class="form-group">
                                <label class="control-label">G/L Account<span class="text-danger">*</span></label>
                                <input type="text" name="gl_fname" placeholder="Sap GL Name" class="form-control tinymce" required="required" rows="5" readonly="readonly"></input>
                                <span class="help-block"></span>
                           </div> 
                            <div class="form-group">
                                <label class="control-label">SAP Cost Center<span class="text-danger">*</span></label>
                                <select name="sap_cos_center" class="select2" data-placeholder="Select SAP Cost Center" required="required">
                                    <option></option>
                                        @foreach ($sap_costs as $sap_cost)
                                        <option value="{{ $sap_cost->id }}">{{ $sap_cost->cc_code }} - {{ $sap_cost->cc_fname }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Item Specs <span class="text-danger">*</span></label>
                                <input type="number" name="pr_specs" placeholder="Item Specs" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>


                           <div class="form-group">
                                <label class="control-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="actual_qty" placeholder="Quantity Actual" class="form-control tinymce" required="required" ></input>
                                <span class="help-block"></span>
                           </div> 
                            <div class="form-group">
                                <label class="control-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price_actual" placeholder="Price Actual" class="form-control tinymce" required="required"  ></input>
                                <span class="help-block"></span>
                           </div>

                           <div class="form-group">
                                
                                <div class="checkbox">
                                    <input id="checkbox0" type="checkbox" name="foreign_currency" onclick="foreignCurrency(this)">
                                    <label for="checkbox0">
                                        Foreign Currency
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="form-group" id="hide12" style="display: none">
                                        
                                        <div class="col-sm-6">
                                            <select class="select2" name="currency" id="currency" data-placeholder="Select currency">
                                                  <option value=""></option>
                                                  <option value="USD">USD</option>
                                                  <option value="JPY">JPY</option>
                                                  <option value="THB">THB</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control tinymce"name="price_to_download" placeholder="Amount Foreign Currency" required="required">
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        
                        <div class="col-md-12 text-right m-t-20">
                            
                             <div class="modal-footer">
                                    <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>

                                <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Save</button>
                            </div>

                        </div>
                         
                        <div class="clearfix"></div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection

@push('js')
<script src="{{ url('assets/js/pages/approval-expense-add-edit.js') }}"></script>

@endpush