@extends('restaurant_grocery.index')
@section('title', 'login')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="account-content">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-7 col-lg-6 login-left">
                                <img src="/restaurant/assets/img/login-banner.png" class="img-fluid"
                                    alt="Dreams Hotel Login">
                            </div>
                            <div class="col-md-12 col-lg-6 login-right">
                                <div class="login-header">
                                    @if (session('message'))
                                        <p class="alert alert-primary">
                                            {{ session('message') }}
                                        </p>
                                    @endif
                                    @if (Session::has('success_message'))
                                        <p class="alert alert-success">{{ Session::get('success_message') }}</p>
                                    @endif

                                    @if (Session::has('error_message'))
                                        <p class="alert alert-danger">{{ Session::get('error_message') }}</p>
                                    @endif
                                    <h3>Login</h3>
                                </div>
                                <form action="{{ $subdomain == 'grocer' ? route('grocer.login') : route('restaurant.login') }}" method="post">
                                    @csrf

                                    {{-- <div class="form-group">
                                        <select name="role" class="form-control" required>
                                            <option selected value="" disabled>Please Select Role</option>
                                            <option value={{ UserType::RESTAURANT_OWNER }}>Restaurant</option>
                                            <option value={{ UserType::GROCERY_OWNER }}>Grocery</option>
                                        </select>
                                    </div> --}}
                                    {{-- @if($subdomain == 'grocer')
                                    <input type="text" name="role" value="{{ UserType::RESTAURANT_OWNER }}" hidden>
                                    @elseif($subdomain == 'restaurant')
                                    <input type="text" name="role" value="{{ UserType::RESTAURANT_OWNER }}" hidden>
                                    @endif --}}
                                    <div class="form-group form-focus">
                                        <input type="email" name="email" class="form-control floating" required>
                                        <label class="focus-label">Email</label>
                                    </div>
                                    <div class="form-group form-focus">
                                        <input type="password" name="password" class="form-control floating" required>
                                        <label class="focus-label">Password</label>
                                    </div>
                                    <div class="text-right">
                                        <!-- <a class="forgot-link" href="forgot-password.html">Forgot Password ?</a> -->
                                    </div>
                                    <button class="btn btn-primary btn-block btn-lg login-btn" type="submit">Login</button>
                                    <div class="login-or">
                                        <span class="or-line"></span>
                                        <span class="span-or">or</span>
                                    </div>

                                    <div class="text-center dont-have">Don’t have an account? <a
                                            href="{{ $subdomain == 'grocer' ? route('grocer.signup') : route('restaurant.signup') }}">Register</a></div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
