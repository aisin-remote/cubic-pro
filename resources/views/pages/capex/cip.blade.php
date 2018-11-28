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
            <div class="card-box">
                <h5><strong>Select parameters :</strong></h5>
                <div class="radio radio-info radio-inline">
                    <input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
                    <label for="inlineRadio1"> CIP still open </label>
                </div>
                <div class="radio radio-inline">
                    <input type="radio" id="inlineRadio2" value="option2" name="radioInline" checked="">
                    <label for="inlineRadio2"> CIP already closed </label>
                </div>
                <br>
                <br>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <label class="control-label">Budget No :<span class="text-danger">*</span></label>
                    <select name="budget_no" class="select2" data-placeholder="Select Budget" required="required">
                        <option></option>
                    
                    </select>
               </div> 
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <label class="control-label">Asset No : <span class="text-danger">*</span></label>
                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" readonly="readonly" rows="5"></inout>
                    <span class="help-block"></span>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <label class="control-label">Settlement Name : <span class="text-danger">*</span></label>
                    <inout type="text" name="budget_name" placeholder="Budget Name" class="form-control tinymce" required="required" rows="5"></inout>
                    <span class="help-block"></span>
                </div>
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


