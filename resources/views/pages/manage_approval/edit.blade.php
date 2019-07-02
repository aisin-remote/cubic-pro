@extends('layouts.master')
@section('title')
	Edit Manage Approval
@endsection

@section('content')

@php($active = 'manage_approval')
	
	<div class="container">

        <div class="row">
			<div class="col-xs-12">
				<div class="page-title-box">
                    <h4 class="page-title">Edit Manage Approval</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('manage_approval.index') }}">Manage Approval</a>
                        </li>
                        <li class="active">
                            Edit Manage Approval
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
                    <form id="form-add-edit" action="{{ route('manage_approval.update', $approval->id) }}" method="post">
                        @csrf
						@method('put')
						<input type="hidden" name="approval_id" value="{{$approval->id}}">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Department <span class="text-danger">*</span></label>
								<select class="form-control" name="department">
										@foreach($department as $dept)
                                            <option value="{{$dept->id}}" {{ $dept->department_code == $approval->department? 'selected=selected' : '' }}>{{$dept->department_name}}</option>
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
                                                    <input type="radio" name="is_seq" id="is-seq-1" value="1" {{$approval->is_seq?'checked="checked"':''}} >
                                                    <label for="is-seq-1">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_seq" id="is-seq-0" value="0" {{$approval->is_seq?'':'checked="checked"'}} >
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
                                                    <input type="radio" name="is_must_all" id="is-must-all-1" value="1" {{$approval->is_must_all?'checked="checked"':''}}>
                                                    <label for="is-must-all-1">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input type="radio" name="is_must_all" id="is-must-all-0" value="0" {{$approval->is_must_all?'':'checked="checked"'}}>
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
										<th style="width:20px">Opsi</th>
                                        <th style="width:150px">Level</th>
                                        <th style="width: 250px">User</th>
                                    </tr>
                                </thead>
                                <tbody>
									@foreach($approval_dtl as $i => $app_dtl)
                                    <tr>
										<td style="width:50px" class="text-center">
										
                                            <button type="button" class="btn btn-danger btn-xs removeRow"><i class="fa fa-times"></i></button>
										
										</td>
                                        <td>
                                            <div class="form-group">
												@if($app_dtl->level == 1)
													Budgeting
												@elseif($app_dtl->level == 2)
												    Department Head
												@elseif($app_dtl->level == 3)
													General Manager
												@elseif($app_dtl->level == 4)
													Director
												@elseif($app_dtl->level == 5)
													Purchasing
												@elseif($app_dtl->level == 6)
													Accounting
												@endif
                                                <input type="hidden" class="form-control" name="level[]" value="{{$app_dtl->level}}">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <select class="select2 form-control" name="user[]" data-placeholder="Choose User">
                                                    <option></option>
                                                    @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{$app_dtl->user_id == $user->id?'selected':''}}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
									@endforeach
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
    <script src="{{ url('assets/js/pages/manage_approval-edit.js') }}"></script>
@endpush