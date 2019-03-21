@extends('layouts.master')
@section('title')
	Tambah Manage Approval
@endsection

@section('content')

@php($active = 'manage_approval')
	
	<div class="container">

        <div class="row">
			<div class="col-xs-12">
				<div class="page-title-box">
                    <h4 class="page-title">Tambah Manage Approval</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('manage_approval.index') }}">Manage Approval</a>
                        </li>
                        <li class="active">
                            Tambah Manage Approval
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
                    <form id="form-add-edit" action="{{ route('manage_approval.store') }}" method="post">
                        @csrf
                       <div class="col-md-6">
							<div class="form-group">
                                <label class="control-label">Department <span class="text-danger">*</span></label>
								<select class="form-control select2" name="department" required="required">
										@foreach($department as $dept)
											<option value="{{$dept->department_code}}">{{'('.$dept->department_code.') - '.$dept->department_name}}</option>
										@endforeach
								</select>
                                <span class="help-block"></span>
                           </div>
                           
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Is Seq <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_seq" id="is-seq-1" value="1" checked="">
                                                    <label for="is-seq-1">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_seq" id="is-seq-0" value="0" checked="">
                                                    <label for="is-seq-0">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   </div>  
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Is Must All <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_must_all" id="is-must-all-1" value="1" checked="">
                                                    <label for="is-must-all-1">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_must_all" id="is-must-all-0" value="0" checked="">
                                                    <label for="is-must-all-0">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                   </div>  
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-md-12 text-right">
                            <hr>
                        </div>
                        <div class="col-sm-6">
                        <p>Approval Details</p>
                        </div>
                            <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-sm btn-primary btn-success" onclick="on_add()">Add</button>
                            </div>
                            <table class="table jambo_table table-bordered" id="table-details-appr">
                                <thead>
                                    <tr>
										<th style="width:50px"></th>
                                        <th style="width:150px">Level</th>
                                        <th style="width: 250px">User</th>
                                    </tr>
                                </thead>
                                <!-- <tbody>
                                    <tr class="text-center" id="empty-row">
                                        <td colspan="4">No data available.</td>
                                    </tr>
                                </tbody> -->
                                <tbody>
                                    <tr>
										<td></td>
                                        <td>
                                            <div class="form-group">
                                            <select name="level_approval[]" class="select2" data-placeholder="Level" data-allow-clear="true">
                                                <option></option>
                                                @foreach ($level_approval as $level_approvals)
                                                    <option value="{{ $level_approvals['id'] }}">{{ $level_approvals['text'] }}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <select class="select2 form-control" name="user[]" data-placeholder="Choose User" required="required">
                                                    <option></option>
                                                    @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
					
                        <div class="col-md-12">
                            <div class="pull-right">
                                <input type="submit" name="submit" value="Save" class="btn btn-sm btn-primary">
                                <input type="reset" name="reset" value="Reset" class="btn btn-sm btn-default">
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
    <script src="{{ url('assets/js/pages/manage_approval-create.js') }}"></script>
@endpush