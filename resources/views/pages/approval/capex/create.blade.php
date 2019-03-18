@extends('layouts.master')

@section('title')
    Add Purchase Request Item.
@endsection

@section('content')

@php($active = 'capex')

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Add Purchase Request Item.</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            
                            <a href="{{ route('approval-capex.create') }}">Create Capex Approval Sheet</a></li>
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
                    <form id="form-add-edit" action="{{route('approval_capex.store')}}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Budget No<span class="text-danger">*</span></label>
                                <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required">
                                    <option></option>
                                        @foreach ($capexs as $capex)
                                        <option value="{{ $capex->id }}">{{ $capex->budget_no }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div> 
                           
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Asset Kind <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_kind" id=asset_o" value="Immediate Use" checked="" onclick="setReadOnlyInput();">
                                                    <label for="asset_kind-1">
                                                        Immediate Use
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_kind" id="asset_c" value="CIP" checked="" onclick="setReadOnlyInput();">
                                                    <label for="asset_kind-0">
                                                        CIP (Construction In Process)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   </div>  
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Asset Category <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_category" id="asset_category-1" value="Non Chemical" checked="">
                                                    <label for="asset_category-1">
                                                        Non Chemical
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_category" id="asset_category-0" value="Chemical" checked="">
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
                                <label class="control-label">Asset Code<span class="text-danger">*</span></label>
                                <input type="text" name="asset_code" placeholder="Asset Code" class="form-control tinymce" required="required" rows="5" readonly="readonly"></input>
                                <span class="help-block"></span>
                           </div>       
                           <div class="form-group">
                                <label class="control-label">Project Name/Purpose <span class="text-danger">*</span></label>
                                <input type="type" name="project_name" placeholder="Project Name/Purpose" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>                   
                           <div class="form-group">
                                <label class="control-label">Settlement Date (for CIP) <span class="text-danger">*</span></label>
                                <input name="settlement_date" placeholder="Settlement Date (for CIP)" class="form-control datepicker" date="true" required="required" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" rows="5"></input>
                                <span class="help-block"></span><!-- Carbon\Carbon::now()->format('M-D-Y') -->
                           </div>

                            <div class="form-group">
                                <label class="control-label">SAP Cost Center<span class="text-danger">*</span></label>
                                <select name="sap_cost_center_id" class="select2" data-placeholder="Select SAP Cost Center" required="required">
                                    <option></option>
                                        @foreach ($sap_costs as $sap_cost)
                                        <option value="{{ $sap_cost->id }}">{{ $sap_cost->cc_code }} - {{ $sap_cost->cc_fname }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                            
                            <div class="form-group">
                                <label class="control-label">Purchase Request Item Detail <span class="text-danger">*</span></label>
								<select name="remarks" data-placeholder="Item Detail" required="required">
                                    <option></option>
                                        @foreach ($carts as $cart)
                                        <option value="{{ $cart->item_id }}" item_id="{{$cart->item_id}}" actual_qty="{{$cart->qty}}">{{ $cart->item->item_description }}</option>
                                        @endforeach
                                </select>	
								<input type="hidden" name="actual_qty">
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
                        
                        </div>

                       
                        <div class="col-md-6">
                           

                           <div class="form-group">
                                <label class="control-label">Budget Description <span class="text-danger">*</span></label>
                                <textarea type="text" name="budget_description" placeholder="Budget Description" class="form-control tinymce" required="required" rows="5" readonly="readonly"></textarea>
                                <span class="help-block"></span>
                           </div>  
                            
                                
                        
                            <div class="form-group">
                                <label class="control-label">Item Specs <span class="text-danger">*</span></label>
                                <input type="text" name="pr_specs" placeholder="Item Specs" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Max Budget Reservation <span class="text-danger">*</span></label>
                                <input type="number" name="price_remaining" placeholder="Max Budget Reservation" class="form-control tinymce" required="required" rows="5" readonly="readonly"></input>
                                <span class="help-block"></span>
                           </div>    
                        
                            <div class="form-group">
                                <label class="control-label">Last Budget Remains <span class="text-danger">*</span></label>
                                <input type="number" name="budget_remaining_log" placeholder="Last Budget Remains" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Actual GR <span class="text-danger">*</span></label>
                                <input  name="plan_gr" placeholder="Actual GR" class="form-control datepicker" required="required" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" date="true"></input>
                                <span class="help-block"></span>
                           </div>

                               
                       
                            <div class="form-group">
                                <label class="control-label">Amount on Quotation (IDR) <span class="text-danger">*</span></label>
                                <input type="number" name="price_actual" placeholder="Amount on Quotation (IDR)" class="form-control tinymce" required="required" number="true" rows="5"></input>
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
                                    <div class="form-group" id="hide12" style="display: none;">
										<div class="col-sm-12">
											<label class="control-label">Foreign Currency <span class="text-danger">*</span></label>
										</div>
                                        <div class="col-sm-6">
                                            <select class="select2" name="currency" id="currency" data-placeholder="Select currency" required="required">
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
<script src="{{ url('assets/js/pages/approval-capex-add-edit.js') }}"></script>

@endpush