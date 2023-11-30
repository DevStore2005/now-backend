@extends('restaurant_grocery.index')
@section('title', 'Signup')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <div class="account-content">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-6 col-lg-6 login-left">
                            <img src="/restaurant/assets/img/login-banner.png" class="img-fluid" alt="Dreams Hotel Register">
                        </div>
                        <div class="col-md-12 col-lg-6 login-right">
                            <div class="login-header">
                                @if (session('message'))
                                <p class="alert alert-primary">
                                    {{ session('message') }}
                                    </p>
                                    @endif
                                @if(Session::has('success_message'))
                                <p class="alert alert-success">{{ Session::get('success_message') }}</p>
                                @endif
                                
                                @if(Session::has('error_message'))
                                <p class="alert alert-danger">{{ Session::get('error_message') }}</p>
                                @endif
                                <h3> Register </h3>
                            </div>

                            <form action="{{ $subdomain == 'grocer' ? route('grocer.signup') : route('restaurant.signup')}}" method="post">
                                @csrf
                                <div class="form-group form-focus">
                                    <input type="text" name="phone" required="" class="form-control floating">
                                    <label class="focus-label">Phone</label>
                                </div>
                                <div class="form-group form-focus">
                                    <input type="email" name="email" required="" class="form-control floating">
                                    <label class="focus-label">Email</label>
                                </div>
                                <div class="form-group form-focus">
                                    <input type="password" name="password" required="" class="form-control floating">
                                    <label class="focus-label">Create Password</label>
                                </div>
                                
                                @if($subdomain == 'grocer')
                                    <input type="hidden" name="category" value="grocer">
                                @elseif($subdomain == 'restaurant')
                                    <input type="hidden" name="category" value="restaurant">
                                @endif
                                {{-- <div class="form-group form-focus">
                                    <select name="category" required="" class="form-control floating">
                                        <option value="restaurant">
                                            Restaurant
                                        </option>
                                        <option value="grocery"> Grocery Store</option>
                                    </select>
                                    <label class="focus-label">Select Category</label>
                                </div> --}}


                                <div class="text-right">
                                    <a class="forgot-link" href="{{ $subdomain == 'grocer' ? route('grocer.login') : route('restaurant.login') }}">Already have an
                                        account?</a>
                                </div>
                                <button class="btn btn-primary btn-block btn-lg login-btn" type="submit">Signup</button>
                                <div class="login-or">
                                    <span class="or-line"></span>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection