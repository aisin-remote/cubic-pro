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
                                                    <input type="radio" name="asset_kind" id="asset_o" value="Immediate Use" checked onclick="setReadOnlyInput();">
                                                    <label for="asset_kind-1">
                                                        Immediate Use
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_kind" id="asset_c" value="CIP" onclick="setReadOnlyInput();">
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
                                                    <input type="radio" name="asset_category" id="asset_category-1" value="Non Chemical" checked>
                                                    <label for="asset_category-1">
                                                        Non Chemical
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="asset_category" id="asset_category-0" value="Chemical" >
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
                                        <option value="{{ $sap_asset->asset_type }}">{{ $sap_asset->asset_type }}</option>
                                        @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Asset Code<span class="text-danger">*</span></label>
                                <!-- <input type="text" name="asset_code" placeholder="Asset Code" class="form-control" required="required" rows="5" readonly="readonly"> -->
                                <select name="sap_code_id" class="select2" data-placeholder="Select Asset Code" required="required">

                                </select>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Project Name/Purpose <span class="text-danger">*</span></label>
                                <input type="type" name="project_name" placeholder="Project Name/Purpose" class="form-control" required="required" rows="5">
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Settlement Date (for CIP) <span class="text-danger">*</span></label>
                                <input name="settlement_date" placeholder="Settlement Date (for CIP)" class="form-control datepicker" date="true" required="required" value="{{ date('d-M-Y') }}" rows="5" readonly="readonly">
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
                                <select name="remarks" class="select2" data-tags="true" data-placeholder="Item Detail">
                                    @if ($itemcart == 'non-catalog')
                                        <option></option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->item_description }}" total="0" item_id="{{$item->id}}" item_spec="{{$item->item_specification}}" actual_qty="1" uom_id="{{$item->uom_id}}">{{ $item->item_description }}</option>
                                        @endforeach

                                    @else
                                        @foreach ($carts as $cart)
                                        <option value="{{ $cart->item->item_description }}" total="{{$cart->total}}" item_id="{{$cart->item_id}}" item_spec="{{$cart->item->item_specification}}" actual_qty="{{$cart->qty}}" uom_id="{{$cart->item->uom_id}}">{{ $cart->item->item_description }}</option>
                                        @endforeach

                                    @endif
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
                                <textarea type="text" name="budget_description" placeholder="Budget Description" class="form-control" required="required" rows="5" readonly="readonly"></textarea>
                                <span class="help-block"></span>
                           </div>



                            <div class="form-group">
                                <label class="control-label">Item Specs <span class="text-danger">*</span></label>
                                <input type="text" name="pr_specs" placeholder="Item Specs" class="form-control" required="required" rows="5">
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Max Budget Reservation <span class="text-danger">*</span></label>
                                <input  type="text" name="price_remaining" placeholder="0.00" class="form-control autonumeric text-right" required="required" rows="5" readonly="readonly">
                                <span class="help-block"></span>
                           </div>

                            <div class="form-group">
                                <label class="control-label">Last Budget Remains <span class="text-danger">*</span></label>
                                <input type="text" name="budget_remaining_log" placeholder="0.00" class="form-control autonumeric text-right" required="required" rows="5" readonly="readonly">
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">GR Estimation<span class="text-danger">*</span></label>
                                <input  name="plan_gr" placeholder="GR Estimation" class="form-control datepicker" required="required" value="{{ date('d-M-Y') }}" date="true">
                                <span class="help-block"></span>
                           </div>

                            <div class="form-group">
                                <label class="control-label">Amount on Quotation (IDR)<span class="text-danger">*</span></label>

                                <input type="text" name="price_actual" placeholder="Amount on Quotation (IDR)" class="form-control autonumeric text-right" required="required" number="true" rows="5" {{ $itemcart == 'non-catalog' ?  '' : 'readonly=readonly' }} >
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
                                            <input type="number" class="form-control"name="price_to_download" placeholder="Amount Foreign Currency" required="required">
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
@if ($itemcart != 'non-catalog')
<script>
    $(function() {
        var actual_qty = $('select[name="remarks"]').find('option:selected').attr('actual_qty');
        var uom_id 	= $('select[name="remarks"]').find('option:selected').attr('uom_id');
        var item_spec  = $('select[name="remarks"]').find('option:selected').attr('item_spec');
        var total 		= $('select[name="remarks"]').find('option:selected').attr('total');
        $('input[name="actual_qty"]').val(actual_qty);
        $('select[name="sap_uom_id"]').select2("val", uom_id);
        $('input[name="pr_specs"]').val(item_spec);
        $('input[name="price_actual"]').autoNumeric('set', total);
    });
</script>
@endif
@endpush