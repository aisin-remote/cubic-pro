@extends('layouts.master')

@section('title')
	Create Expense
@endsection

@section('content')

@php($active = 'expense')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Create Expense</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li><a href="{{ route('expense.index') }}">List Of Expense</a></li>
                    <li class="active">
                        Create Expense
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
     <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <form id="form-add-edit" action="{{ route('expense.store') }}" method="post">
                        @csrf
                        <div class="col-md-6">

                           <div class="form-group">
                                <label class="control-label">Departement</label>
                                <select name="department_id" class="select2" data-placeholder="Select Department Code" required="required">
                                    <option></option>
                                    @foreach ($department as $departement)
                                    <option value="{{ $departement->id }}">{{ $departement->departement_code }} - {{ $departement->department_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                           </div>   
                           <div class="form-group">
                                <label class="control-label">Budget Number<span class="text-danger">*</span></label>
                                <input type="text" name="budget_no" placeholder="Budget Number" class="form-control tinymce" required="required">
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Quality<span class="text-danger">*</span></label>
                                <input type="text" name="qty_plan" placeholder="Quality" class="form-control tinymce" required="required">
                                <span class="help-block"></span>
                           </div>
                           
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                                <label class="control-label">Description<span class="text-danger">*</span></label>
                                <input type="text" name="description" placeholder="Description" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                           <div class="form-group">
                                <label class="control-label">Budget Plan<span class="text-danger">*</span></label>
                                <input type="number" name="budget_plan" placeholder="Budget Plan" class="form-control tinymce" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                                <label class="control-label">Plant GR<span class="text-danger">*</span></label>
                                <input type="text" name="plan_gr" placeholder="Plant GR" class="form-control datepicker" required="required" rows="5"></input>
                                <span class="help-block"></span>
                           </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <hr>

                            <button class="btn btn-default btn-bordered waves-effect waves-light" type="reset">Reset</button>
                            <button class="btn btn-primary btn-bordered waves-effect waves-light" type="submit">Simpan</button>

                        </div>
                    </form>
                  </div>
              </div>
          </div>
      </div>
</div>



@endsection

@push('js')
<script src="{{ url('assets/js/pages/expense-add-edit.js') }}"></script>
@endpush