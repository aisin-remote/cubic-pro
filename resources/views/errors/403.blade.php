@extends('layouts.master')

@section('title')
    Error
@endsection

@section('content')

@php($active = '')
<div class="container">
    <div class="row">
	  <div class="col-md-12">
	  <h3>Error 403 - Forbidden</h3>
	  <p>You do not have permission to access this page</p>
	  </div>
	</div>
</div>
@endsection