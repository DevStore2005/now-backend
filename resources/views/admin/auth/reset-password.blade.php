@extends('admin.guest_layout')
@section('title', '')
@section('content')
    <div class="basic_form login_form">
        <!-- <h1 id = "login_status">Login</h1> -->
        <div class="dis-center" style="margin-bottom: 10px;">
            <img id="main-logo" class="d-inline-block align-top mr-1" src="/admin/assets/img/logo.svg" alt="Logo" style="width: 100px;height: auto;">
        </div>
        <h1 id ="login_status">Forgot Passsword</h1>
        <div class="form_cnt"> 
            <div class="form_bdy admin active_form">
                <form role="form" class="validateForm" action="{!! route('password.update') !!}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="form-group">
                        <label class="control-label"><strong>Password </strong> <span class="text-danger">*</span></label>
                        <div class="basic_tpy_sec"><input  maxlength="100" type="password" required="required" id="password" name="password" class="form-control" placeholder="password" /></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><strong>Confirm Password </strong> <span class="text-danger">*</span></label>
                        <div class="basic_tpy_sec"><input  maxlength="100" type="password_confirmation" required="required" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="password confirmation" /></div>
                    </div>
                    <div class="login_btn">
                        <button class="btn btn-success  login_btn" type="submit" >Reset password</button>
                    </div>
                </form>
            </div>
            <br>
            <span>
                <strong>
                    <a href="{!! route('admin.auth.login') !!}" class="signup-link forgotPassword"> <span class="txt-label pl-2">Login</span></a>
                </strong>
            </span>
        </div>
    </div>
@endsection