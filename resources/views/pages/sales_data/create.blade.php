@extends('layouts.master')

@section('title')
	Create Sales Data
@endsection

@section('content')

@php($active = 'salesdata')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Sales Data</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('salesdata.index') }}">Upload Sales Data</a></li>
                    <li class="active">
                        Create Sales Data
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <form id="form-add-edit" action="{{ route('salesdata.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <div class="card-box">
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
                        <label class="control-label">Market<span class="text-danger">*</span></label>
                        <input type="text" name="market" placeholder="Market" class="form-control tinymce" required="required">
                        <span class="help-block"></span>
                   </div>      
                </div>
                <div class="col-md-6">
                 <div class="form-group">
                      <label class="control-label">Customer Code<span class="text-danger">*</span></label>
                      <select name="supplier_id" class="select2" data-placeholder="Select Customer Code" required="required">
                          <option></option>
                          @foreach ($customers as $customer)
                          <option value="{{ $customer->id }}">{{ $customer->customer_code }} - {{ $customer->customer_name }}</option>
                          @endforeach
                      </select>
                      <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">Fiscal Year<span class="text-danger">*</span></label>
                    <input type="number" name="fiscal_year" placeholder="Fiscal Year" class="form-control tinymce" required="required" rows="5"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">January QTY</label>
                    <input type="number" name="jan_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 <div class="form-group">
                    <label class="control-label">February QTY</label>
                    <input type="number" name="feb_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">January Amount</label>
                    <input type="number" name="jan_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">February Amount</label>
                    <input type="number" name="feb_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">March QTY</label>
                    <input type="number" name="mar_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 <div class="form-group">
                    <label class="control-label">April QTY</label>
                    <input type="number" name="apr_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 
              </div>
              <div class="col-md-6">
                 
                 <div class="form-group">
                    <label class="control-label">March Amount</label>
                    <input type="number" name="mar_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">April Amount</label>
                    <input type="number" name="apr_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">May QTY</label>
                    <input type="number" name="may_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 <div class="form-group">
                    <label class="control-label">June QTY</label>
                    <input type="number" name="june_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>  
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">May Amount</label>
                    <input type="number" name="may_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">June Amount</label>
                    <input type="number" name="june_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">July QTY</label>
                    <input type="number" name="july_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">August QTY</label>
                    <input type="number" name="august_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>  
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">July Amount</label>
                    <input type="number" name="july_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">August Amount</label>
                    <input type="number" name="august_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">September QTY</label>
                    <input type="number" name="sep_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">October QTY</label>
                    <input type="number" name="okt_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">September Amount</label>
                    <input type="number" name="sep_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>

                 <div class="form-group">
                    <label class="control-label">October Amount</label>
                    <input type="number" name="okt_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">November QTY</label>
                    <input type="number" name="nov_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 <div class="form-group">
                    <label class="control-label">December QTY</label>
                    <input type="number" name="des_qty" placeholder="QTY" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 
              </div>
              <div class="col-md-6">
                 <div class="form-group">
                    <label class="control-label">November Amount</label>
                    <input type="number" name="nov_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
                 <div class="form-group">
                    <label class="control-label">December Amount</label>
                    <input type="number" name="des_amount" placeholder="Amount" class="form-control tinymce" required="required" rows="5" value="0"></input>
                    <span class="help-block"></span>
                 </div>
              </div>
            </div>

            <div class="col-md-12 text-right">
                <hr>

                <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>
                <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button>

            </div>

            <div class="clearfix"></div>
        </form>
    </div>

</div>



@endsection

@push('js')
<script src="{{ url('assets/js/pages/salesdata-add-edit.js') }}"></script>
@endpush