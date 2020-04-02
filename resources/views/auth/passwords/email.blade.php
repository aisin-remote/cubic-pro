@extends('layouts.app')

@section('title')
    Reset Password
@endsection

@section('content')
<div class="col-sm-12">

    <div class="wrapper-page">

        <div class="m-t-40 account-pages">
            <div class="text-center account-logo-box" style="background-color: #fff">
                <h2 class="text-uppercase">
                </h2>
            </div>
            <div class="account-content">

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>Oh snap!</strong>{{ session('error') }}
                </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="post">
                    {{ csrf_field() }}
                    <div class="text-center"  style="background: #fff !important">

                        <!-- Image logo -->
                        <a href="{{ url('/') }}" class="logo">
                            <span>
                                <img src="{{ url('assets/images/logo.png') }}" alt="" style="width: 180px; height: 60px;">
                            </span>
                            <i>
                                <img src="{{ url('assets/images/logo_sm.png') }}" alt="" height="28">
                            </i>
                        </a>
                    </div><br>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} row">
                        <div class="col-xs-12">
                            <input id="email" type="email" class="form-control {{ $errors->has('email') ? ' has-error' : '' }}" name="email" value="{{ old('email') }}" required placeholder="E-mail">
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group account-btn text-center m-t-10 row">
                        <div class="col-xs-12">
                            <button class="btn w-md btn-bordered btn-danger waves-effect waves-light" type="submit">Send Password Reset Link</button>
                        </div>
                    </div><br>

                    <div class="form-group text-center m-t-30">
                        <div class="col-sm-12">
                            <a href="{{ route('login') }}" class="text-muted"><i class="fa fa-sign-in m-r-5"></i> Login </a>
                        </div>
                    </div>

                </form>

                <div class="clearfix"></div>

            </div>
        </div>
        <!-- end card-box-->

    </div>
    <!-- end wrapper -->

</div>

@endsection
