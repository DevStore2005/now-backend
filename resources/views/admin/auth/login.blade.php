@extends('admin.guest_layout')
@section('title', '')
@section('content')
			<div class="basic_form login_form">
				<!-- <h1 id = "login_status">Login</h1> -->
				<div class="dis-center" style="margin-bottom: 10px;">
                    <img id="main-logo" class="d-inline-block align-top mr-1" src="/admin/assets/img/logo.svg" alt="Logo" style="width: 100px;height: auto;">
                </div>
				<h1 id ="login_status">Admin Login</h1>
				<div class="form_cnt"> 
                    <div class="form_bdy admin active_form">
						<form role="form" class="validateForm" method="post">
							@csrf
							<input type =hidden name='role'  value="">
							<input type="hidden" name="salt_key" value="">
							<div class="form-group">
								<label class="control-label"><strong>Email </strong> <span class="text-danger">*</span></label>
								<div class="basic_tpy_sec"><input  maxlength="100" type="text" required="required" id="email" name="email" class="form-control" placeholder="Email" /></div>
							</div>

							<div class="form-group">
								<label class="control-label"><strong>Password </strong><span class="text-danger">*</span></label>
								<div class="basic_tpy_sec"><input maxlength="100" type="password" required="required" id="password" name="password" class="form-control" placeholder="Password"></div>
							</div>

							<div class="login_btn">
								<button class="btn btn-success   login_btn" type="submit" >Sign in</button>
							</div>
                        	
						</form>

					</div>
					<br>
					<span>Forgot your Password?
						<strong>
							<a href="{!! route('admin.auth.forgotPassword') !!}" class="signup-link forgotPassword"> <span class="txt-label pl-2">Reset Here</span></a>
						</strong>
					</span>
				</div>
				
			</div>
@endsection