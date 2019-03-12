@extends('layouts.master')

@section('title')
   Fiscal Year Closing Activity
@endsection

@section('content')

@php($active = 'closing')

<div class="container">
	<div class="row">
			<div class="col-xs-12">
				<div class="page-title-box">
					<h4 class="page-title"> Fiscal Year Closing Activity</h4>
					<ol class="breadcrumb p-0 m-0">
						<li class="active">
							
						</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="form-group">
					<div class="col-md-12">
						<label>
						  <input type="checkbox" name="is_confirmed" id="is_confirmed"> Current Fiscal Year Will Be Closed (BE CAREFUL! All related data will be closed and archived!)
						</label>
						&nbsp;&nbsp;
						<button type="button" class="btn btn-primary" id="btnclosing">Close Fiscal Year : {{$fyear_open}}</button>
						<input type="hidden" id="fyear_open" value="{{$fyear_open}}">
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

@endsection
@push('css')
	<style>
	
	</style>
@endpush
@push('js')

	<script type="text/javascript">
	$(document).ready(function(){
		$('#btnclosing').click(function(){
			// $('#fyear_open').val('');
			 var fyear_open = $('#fyear_open').val();
			 if(fyear_open != ""){
				cancelClosing();
			 }else{
				show_notification('Error','error',"Period data is not active"); 
			 }
		});
	});
		function cancelClosing()
		{
			if (is_confirmed.checked) {
				var reconfirmed = confirm('Are you sure to close current fiscal year ?');

				if (reconfirmed) {
					
					$('#btnclosing').attr('disabled',true);
					$.getJSON(SITE_URL+"/fyear/doClosing",{},function(data){
						if (data.error) {
							show_notification('Error','error',data.error);
							$('#btnclosing').attr('disabled',true);
						}
						else {
							show_notification('Success','success',data.success);
							$('#btnclosing').attr('disabled',true);
						}
					});
				}
				
			}
			else {
				show_notification('Error','error','Please click the checkbox for confirmation!');
			}
		}
	</script>
@endpush