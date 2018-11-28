@extends('layouts.master')

@section('title')
    Add Purchase Request Item.
@endsection

@section('content')

@php($active = 'expense')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Add Purchase Request Item.</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('approval-expense.create') }}">Create Expense Approval Sheet</a></li>
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
                    <form id="form-add-edit" action="{{ route('approval-expense.store') }}" method="post">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Budget No<span class="text-danger">*</span></label>
                                <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required">
                                    <option></option>
                                    
                                </select>
                                <span class="help-block"></span>
                           </div>

                           <div class="form-group">
                                <label class="control-label">Project Name/Purpose <span class="text-danger">*</span></label>
                                <input type="type" name="settlement_date" placeholder="Project Name/Purpose" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>  
                                
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">G/L Group<span class="text-danger">*</span></label>
                                <select name="sap_gl_group" class="select2" data-placeholder="Select G/L Account Group" required="required">
                                    <option></option>
                                    
                                </select>
                                <span class="help-block"></span>
                           </div>
                            
                            <div class="form-group">
                                <label class="control-label">G/L Account<span class="text-danger">*</span></label>
                                <select name="sap_gl_account" class="select2" data-placeholder="Select G/L Account" required="required">
                                    <option></option>
                                    
                                </select>
                                <span class="help-block"></span>
                           </div>      
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Budget Description <span class="text-danger">*</span></label>
                                <textarea type="text" name="budget_description" placeholder="Budget Description" class="form-control tinymce" required="required" rows="5"></textarea>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Item Category <span class="text-danger">*</span></label>
                                <div class="checkbox checkbox-primary">
                                    <label><input type="checkbox" value="">Non Chemical</label><br>
                                    <label><input type="checkbox" value="">Chemical</label><br>
                                </div>
                            </div>        
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">SAP Cost Center<span class="text-danger">*</span></label>
                                <select name="sap_cos_center" class="select2" data-placeholder="Select SAP Cost Center" required="required">
                                    <option></option>
                                    
                                </select>
                                <span class="help-block"></span>
                           </div>
                            
                            <div class="form-group">
                                <label class="control-label">Purchase Request Item Detail <span class="text-danger">*</span></label>
                                <textarea type="text" name="remarks" placeholder="Purchase Request Item Detail" class="form-control tinymce" required="required" rows="5"></textarea>
                                <span class="help-block"></span>
                           </div>      
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Unit <span class="text-danger">*</span></label>
                                <select name="sap_uom" class="select2" data-placeholder="Select Unit Of Measeure" required="required">
                                    <option></option>
                                    
                                </select>
                                <span class="help-block"></span>
                           </div>
                            
                            <div class="form-group">
                                <label class="control-label">Item Specs <span class="text-danger">*</span></label>
                                <input type="text" name="pr_specs" placeholder="Item Specs" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>      
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Last Budget Remains <span class="text-danger">*</span></label>
                                <input type="number" name="budget_remaining_log" placeholder="Last Budget Remains" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>   
                           <div class="form-group">
                                <label class="control-label">Max Budget Reservation <span class="text-danger">*</span></label>
                                <input type="number" name="price_remaining" placeholder="Max Budget Reservation" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>      
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Amount on Quotation (IDR) <span class="text-danger">*</span></label>
                                <input type="number" name="price_actual" placeholder="Amount on Quotation (IDR)" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <div class="col-sm-4">
                                </div>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                      <label><input type="checkbox" value id="foreign_currency" name="foreign_currency" onclick="foreignCurrency(this)">Foreign Currency</label>
                                    </div>
                                </div>
                            </div>   
                           <div class="form-group">
                                <label class="control-label">Actual GR <span class="text-danger">*</span></label>
                                <input type="number" name="plan_gr" placeholder="Actual GR" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>      
                        </div>
                        <div class="form-group" id="hide12">

                            <label for="budget_plan" class="col-sm-4 control-label">Amount Foreign Currency
                            </label>
                      
                            <div class="col-sm-3">
                                <select class="form-control" name="currency" id="currency">
                                      <option value="">-Currency-</option>
                                      <option value="USD">USD</option>
                                      <option value="JPY">JPY</option>
                                      <option value="THB">THB</option>
                                </select>
                            </div>

                            <div class="col-sm-5">
                                <input type="number" class="form-control" id="price_to_download" name="price_to_download" placeholder="Amount Foreign Currency">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-inverse btn-bordered waves-effect waves-light m-b-20" id="btn-save"> Simpan</a>
                            <button type="button" class="btn btn-default btn-bordered waves-effect waves-light m-b-20" id="btn-reset"> Reset</a>
                        </div>
                         
                        <div class="col-md-12 text-right">
                            <hr>
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
<script src="{{ url('assets/js/pages/bom_semi-add-edit.js') }}"></script>

@endpush