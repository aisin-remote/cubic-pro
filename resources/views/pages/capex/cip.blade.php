@extends('layouts.master')

@section('title')
    List of Outstanding CIP
@endsection

@section('content')

@php($active = 'capex')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> List of Outstanding CIP</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li class="active">
                         List of Outstanding CIP
                    </li>
                </ol>
                
                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    <div class="row">
		<div class="col-md-12">
			
		</div>
        <div class="col-md-12">
            <div class="card-box">
				<div class="btn-group pull-right">
					<button id="btn_finish" class="btn btn-warning" onclick="finishCIP(budget_no);">Finish CIP</button>
				</div>
                <h5><strong>Select parameters :</strong></h5>
                <div class="radio radio-info radio-inline">
                    <input type="radio" value="o" name="radioInline" checked="checked" id="cip_open">
                    <label for="inlineRadio1"> CIP still open </label>
                </div>
                <div class="radio radio-inline">
                    <input type="radio" value="c" name="radioInline" id="cip_close">
                    <label for="inlineRadio2"> CIP already closed </label>
                </div>
                <br>
                <br>
                <div class="col-md-4 col-sm-12 col-xs-12" id="div_budget_no">
                    <label class="control-label">Budget No :<span class="text-danger">*</span></label>
                    <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required" id="opt_budget_no">
                        <option></option>
						@foreach($budget_nos as $budget_no)
							<option value="{{$budget_no->budget_no}}">{{$budget_no->budget_no}}</option>
						@endforeach
                    </select>
               </div> 
                <div class="col-md-4 col-sm-12 col-xs-12" id="div_asset_no">
                    <label class="control-label">Asset No : <span class="text-danger">*</span></label>
                    <input type="text" class="form-control tinymce" required="required" readonly="readonly" id="asset_no" />
                    <span class="help-block"></span>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12" id="div_settlement_name">
                    <label class="control-label">Settlement Name : <span class="text-danger">*</span></label>
                    <input type="text"  class="form-control tinymce" required="required" id="settlement_name" />
                    <span class="help-block"></span>
                </div>
				<input type="hidden" id="budgetno" value="{{$budgetno}}">
            </div>  
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <table class="table m-0 table-colored table-inverse" id="table-CIP-capex">
                    <thead>
                        <tr>
                            <th>Budget Number</th>
                            <th>Asset Number</th>
                            <th>CIP Number</th>
                            <th>Settlement Date</th>
                            <th>Settlement Asset Name</th>
                            <th>GR Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>  
        </div>
    </div>

</div>




@endsection

@push('js')
<script src="{{ url('assets/js/pages/cip.js') }}"></script>

@endpush


