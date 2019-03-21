@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('content')

@php($active = '')
<div class="container">
    <div class="row">
			  <div class="col-md-12">
				&nbsp;&nbsp;&nbsp;&nbsp;
			  </div>
			  <div class="col-md-12">
				<div align="center">
					Steps of Approval Procedures : <br><br>
					<img src="{{ asset('assets/images/prosedur.png') }}" class="img-responsive" alt="prosedur">
				</div>
				<div align="center">
					Standard of Asset Numbering : <br>
					<a href="#" >
						<img src="{{ asset('assets/images/std_asset.png') }}" class="img-responsive" alt="prosedur">
					</a>
				</div>
			  </div>
	</div>
</div>
@endsection
