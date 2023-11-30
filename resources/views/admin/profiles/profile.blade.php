@extends('admin.layout')
@section('title', 'Profile')
@section('content')
    @php
        $common = new \App\Http\Helpers\Common()
    @endphp
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left ml-2"> {!! $user->role ? $user->role : 'Unknown' !!}{!! " ($user->provider_type)" !!}</h6>
                    @if ($user->role === 'PROVIDER')
                        <a href="{{ route('admin.history.provider.services', ['provider_id' => $user->id]) }}">
                            <button type="button" class="btn btn-success pull-right">Service History</button>
                        </a>
                    @else
                        <a href="{{ route('admin.history.user.services', ['user_id' => $user->id]) }}">
                            <button type="button" class="btn btn-success pull-right">Service History</button>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($user->role === 'PROVIDER')
                            {{-- Show Provider profit --}}
                            <div class="col-12">
                                <div class="row">
                                    @foreach ($profit as $key => $item)
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <div class="card dashboard_card">
                                                <div class="card-header card-header-warning card-header-icon">
                                                    <div class="card-icon">
                                                        <i class="material-icons">paid</i>
                                                    </div>
                                                    <p class="card-category stats-small__label text-uppercase">{!!$key!!}</p>
                                                    <h3 class="card-title user_data">{{$common->defaultCurrencySymbol()}}{!!$item!!}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- <div class="col-4">
                                    </div>
                                    <div class="col-4">
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="card dashboard_card">
                                            <div class="card-header card-header-warning card-header-icon">
                                                <div class="card-icon">
                                                    <i class="material-icons">attach_money</i>
                                                </div>
                                                <p class="card-category stats-small__label text-uppercase">total
                                                    earning</p>
                                                <h3 class="card-title user_data">
                                                    {{$common->defaultCurrencySymbol()}}{!! isset($user->provider_profile) ? $user->provider_profile->total_earn : null !!}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="card dashboard_card">
                                            <div class="card-header card-header-warning card-header-icon">
                                                <div class="card-icon">
                                                    <i class="material-icons">attach_money</i>
                                                </div>
                                                <p class="card-category stats-small__label text-uppercase">total
                                                    commission</p>
                                                <h3 class="card-title user_data">
                                                    {{$common->defaultCurrencySymbol()}}{!! isset($user->provider_profile) ? $user->provider_profile->commission : null !!}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="card dashboard_card">
                                            <div class="card-header card-header-warning card-header-icon">
                                                <div class="card-icon">
                                                    <i class="material-icons">attach_money</i>
                                                </div>
                                                <p class="card-category stats-small__label text-uppercase">payable</p>
                                                <h3 class="card-title user_data">
                                                    @if (isset($user->provider_profile) && $user->provider_profile->earn)
                                                        <button class="btn btn-primary btn-sm mr-2"
                                                                onclick="showSwal({type:'passing-parameter-execute-cancel', text: '{{$common->defaultCurrencySymbol()}} <?php echo $user->provider_profile->earn.  ' Pay to '. $user->first_name?>', confirmButtonText: 'Yes, Pay', link: '<?php echo route('admin.transaction.pay', $user) ?>' })">
                                                            Pay
                                                        </button>{{$common->defaultCurrencySymbol()}}{!! $user->provider_profile->earn !!}
                                                    @else
                                                        {{$common->defaultCurrencySymbol()}}0
                                                    @endif
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary float-right" data-toggle="modal"
                                                data-target="#exampleModal">
                                            Add Credit
                                        </button>
                                        <button class="btn btn-primary float-right mr-2"><a
                                                href="{!! route('admin.history.index', ['type' => 'pay', 'provider_id' => $user->id]) !!}">Pay
                                                History</a></button>
                                        <button class="btn btn-primary float-right mr-2"><a
                                                href="{!! route('admin.history.index', ['type' => 'commission', 'provider_id' => $user->id]) !!}">Commission
                                                History</a></button>
                                        <button class="btn btn-primary float-right mr-2"><a
                                                href="{!! route('admin.history.index', ['type' => 'credit', 'provider_id' => $user->id]) !!}">Credit
                                                History</a></button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <h6 class="m-0 mb-1 pull-left ml-1">User Detail</h6>
                        </div>
                        <div class="col-12">
                            <form action="" method="post" class="form">
                                <fieldset>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="first_name"
                                                   placeholder="First Name"
                                                   value={!! isset($user) ? $user->first_name : @old('first_name') !!}>
                                        </div>
                                        <div class="col-3">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="last_name"
                                                   placeholder="Last Name"
                                                   value={!! isset($user) ? $user->last_name : @old('last_name') !!}>
                                        </div>
                                        <div class="col-3">
                                            <label>E-mail</label>
                                            <input type="text" class="form-control" name="email" placeholder="E-mail"
                                                   value={!! isset($user) ? $user->email : @old('email') !!}>
                                        </div>
                                        <div class="col-3">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" palaceholder="Phone"
                                                   value={!! isset($user) ? $user->phone : @old('phone') !!}>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label>Address</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Address"
                                                   disabled
                                                   readonly
                                                   value="{{ isset($user->addresses[0]) ? $user->addresses[0]->address : '' }}">
                                        </div>
                                        {{--                                        <div class="col-3">--}}
                                        {{--                                            <label>Zip Code</label>--}}
                                        {{--                                            <input type="text" class="form-control" name="zip_code"--}}
                                        {{--                                                   placeholder="Zip Code"--}}
                                        {{--                                                   value={!! isset($user) ? $user->zip_code : @old('zip_code') !!}>--}}
                                        {{--                                        </div>--}}
                                        <div class="col-3">
                                            <label>Role</label>
                                            <input type="text" class="form-control" name="role" placeholder="Role"
                                                   value={!! isset($user) ? $user->role : @old('role') !!}>
                                        </div>
                                        <div class="col-3">
                                            <label>Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option
                                                    value="1" {{ isset($user) && $user->status == 1 ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option
                                                    value="0" {{ isset($user) && $user->status == 0 ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            {{-- <input type="text" class="form-control" name="status"
                                                value={{ isset($user) ? $user->status : @old('status') }}> --}}
                                        </div>
                                        <div class="col-3">
                                            <label>Phone Verified</label>
                                            <select name="phone_verification" id="phone_verification"
                                                    class="form-control">
                                                <option
                                                    value="1" {{ isset($user) && $user->phone_verification == 1 ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option
                                                    value="0" {{ isset($user) && $user->phone_verification == 0 ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-3 mt-3">
                                            <label>Rating</label>
                                            <input type="text" class="form-control" name="rating" readonly
                                                   placeholder="rating"
                                                   value={{ (isset($user) ? $user->rating : @old('rating')) ?? 0}} >
                                        </div>
                                        @if($user->role === 'PROVIDER')
                                            <div class="col-3 mt-3">
                                                <label>Provider Type</label>
                                                <input type="text" class="form-control" name="provider_type" readonly
                                                       placeholder="Provider Type"
                                                       value={{ isset($user) ? $user->provider_type : @old('provider_type') }} >
                                            </div>
                                            <div class="col-3 mt-3">
                                                <label>Account Type</label>
                                                <select name="account_type" class="form-control">
                                                    <option
                                                        value="1" {{ isset($user) && $user->account_type == 'BASIC' ? 'selected' : '' }}>
                                                        Basic
                                                    </option>
                                                    <option
                                                        value="2" {{ isset($user) && $user->account_type == 'PREMIUM' ? 'selected' : '' }}>
                                                        Premium
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-3 mt-3">
                                                <label>Completed Jobs</label>
                                                <input type="text" class="form-control" name="completed_jobs" readonly
                                                       placeholder="Completed Jobs"
                                                       value="{{ isset($user) ? $user->provider_service_requests->count() : @old('completed_jobs') }}">
                                            </div>

                                        @endif
                                        <div class="col-3 mt-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option
                                                    value="ACTIVE" {{ isset($user) && $user->account_type == 'ACTIVE' ? 'selected' : '' }}>
                                                    ACTIVE
                                                </option>
                                                <option
                                                    value="PENDING" {{ isset($user) && $user->account_type == 'PENDING' ? 'selected' : '' }}>
                                                    PENDING
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-3 mt-3">
                                            <label>Country</label>
                                            <select name="country_id" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option
                                                        value="{{ $country->id }}" {{ isset($country) && $country->id == $user->country_id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label>Bio</label>
                                            <textarea type="text" class="form-control" placeholder="Bio..." rows="4"
                                                      name="bio">{{ isset($user) ? $user->bio : @old('bio') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="m-0 pull-left ml-2">Payment Detail</h6>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-3">
                                            <label>Stripe Id</label>
                                            <input type="text" class="form-control" name="stripe_id" disabled
                                                   placeholder="Stripe Id"
                                                   value="{{ isset($user->stripe_id) == true ? $user->stripe_id : '' }}">
                                        </div>
                                        <div class="col-3">
                                            <label>Card Brand</label>
                                            <input type="text" class="form-control" name="card_brand" disabled
                                                   placeholder="Card Brand"
                                                   value="{{ $user->card_brand !== null ? $user->card_brand : "" }}">
                                        </div>
                                        <div class="col-3">
                                            <label>Card Last Four Digits</label>
                                            <input type="text" class="form-control" name="card_last_four" disabled
                                                   placeholder="Card Last Four Digits"
                                                   value="{{ isset($user->card_last_four) == true  ? $user->card_last_four : '' }}">
                                        </div>
                                        <div class="col-3">
                                            <label>Trial Ends At</label>
                                            <input type="date" class="form-control" name="trial_ends_at"
                                                   placeholder="Trial Ends At"
                                                   value="{{ isset($user->trial_ends_at) == true ? $user->trial_ends_at : '' }}">
                                        </div>
                                    </div>
                                    @if ($user->role === 'RESTAURANT_OWNER' || $user->role === 'GROCERY_OWNER')
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="m-0 pull-left ml-1">Profile Details</h6>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-3">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="profile_name"
                                                       placeholder="Name"
                                                       value="{{ isset($user->business_profile->name) == true ? $user->business_profile->name: @old('profile_name') }}">
                                            </div>
                                            <div class="col-3">
                                                <label>Rating</label>
                                                <input type="text" class="form-control" name="rating"
                                                       value="{{ isset($user->business_profile->rating) == true ? $user->business_profile->rating: @old('rating') }}">
                                            </div>
                                            <div class="col-3">
                                                <label>Web Site</label>
                                                <input type="text" class="form-control" name="website"
                                                       value="{{ isset($user->business_profile->website) == true ? $user->business_profile->website: @old('website') }}">
                                            </div>
                                            <div class="col-3">
                                                <label>Phone</label>
                                                <input type="text" class="form-control" name="business_phone"
                                                       value="{{ isset($user->business_profile->business_phone) == true ? $user->business_profile->business_phone: @old('business_phone')}}">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-4">
                                                <label>City</label>
                                                <input type="text" class="form-control" name="city"
                                                       value="{{ isset($user->business_profile->city) == true ? $user->business_profile->city : @old('city') }}">
                                            </div>
                                            <div class="col-4">
                                                <label>State</label>
                                                <input type="text" class="form-control" name="state"
                                                       value="{{ isset($user->business_profile->state) == true ? $user->business_profile->state : @old('state') }}">
                                            </div>
                                            <div class="col-4">
                                                <label>Country</label>
                                                <input type="text" class="form-control" name="country"
                                                       value="{{ isset($user->business_profile->country) == true ? $user->business_profile->country: @old('country') }}">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <label>Address</label>
                                                <textarea type="text" class="form-control" name="address"
                                                >{{ isset($user->business_profile->address) == true ? $user->business_profile->address : @old('address') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <label>About</label>
                                                <textarea type="text" class="form-control" name="about"
                                                >{{ isset($user->business_profile->about) == true ? $user->business_profile->about : @old('about') }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($user->role === 'PROVIDER')
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="m-0 pull-left ml-1">Profile Details</h6>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-3">
                                                <label>Date of Birth</label>
                                                <input type="text" class="form-control" name="dob"
                                                       placeholder="Date of Birth"
                                                       value="{{ isset($user->provider_profile) == true ? $user->provider_profile->dob: @old('dob') }}">
                                            </div>
                                            <div class="col-3">
                                                <label>Business Name</label>
                                                <input type="text" class="form-control" name="business_name"
                                                       placeholder="Business Name"
                                                       value="{{ isset($user->provider_profile->business_name) == true ? $user->provider_profile->business_name: @old('business_name') }}">
                                            </div>
                                            <div class="col-3">
                                                <label>Founded Year</label>
                                                <input type="text" class="form-control" name="founded_year"
                                                       placeholder="Founded Year"
                                                       value="{{ isset($user->provider_profile->founded_year) == true ? $user->provider_profile->founded_year: @old('founded_year')}}">
                                            </div>
                                            <div class="col-3">
                                                <label>Total Employees</label>
                                                <input type="text" class="form-control" name="number_of_employees"
                                                       placeholder="Total Epmloyees"
                                                       value="{{ isset($user->provider_profile->number_of_employees) == true ? $user->provider_profile->number_of_employees: @old('number_of_employees')}}">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-4">
                                                <label>Suite Numer </label>
                                                <input type="text" class="form-control" name="suite_number"
                                                       placeholder="Suite Number"
                                                       value="{{ isset($user->provider_profile->suite_number) == true ? $user->provider_profile->suite_number : @old('suite_number') }}">
                                            </div>
                                            <div class="col-4">
                                                <label>State</label>
                                                <input type="text" class="form-control" name="state" palceholder="State"
                                                       value="{{ isset($user->provider_profile->state) == true ? $user->provider_profile->state : @old('state') }}">
                                            </div>
                                            <div class="col-4">
                                                <label>City</label>
                                                <input type="text" class="form-control" name="city" placeholder="City"
                                                       value="{{ isset($user->provider_profile->city) == true ? $user->provider_profile->city : @old('city') }}">
                                            </div>
                                            <div class="col-4 mt-3">
                                                <label>Hourly Rate</label>
                                                <input type="text" class="form-control" name="hourly_rate"
                                                       placeholder="Hourly Rate"
                                                       value="{{ isset($user->provider_profile->hourly_rate) == true ? $user->provider_profile->hourly_rate : @old('hourly_rate') }}">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <label>Street Address</label>
                                                <input type="text" class="form-control" name="street_address"
                                                       placehoder="Street Addess"
                                                       value="{{ isset($user->provider_profile->street_address) == true ? $user->provider_profile->street_address : @old('street_address') }}">
                                            </div>
                                        </div>
                                        <div class="row mt3 mx auto">
                                            <div class="col-md-12 mt-3">
                                                <h6 class="m-0 pull-left ml-2">Provider Documents</h6>
                                            </div>
                                            @if($user->medias->isEmpty())
                                                <div class="ml-3 col-md-12">
                                                    Not Found any documents
                                                </div>
                                            @else
                                                @foreach($user->medias as $key => $media)
                                                    @if($media->type == '2')
                                                        <div class="col-3">
                                                            <div class="card">
                                                                <div class="card-body media" data-url={{ $media->url }}>
                                                                    <a data-fancybox data-type="iframe"
                                                                       data-src="{{ $media->url }}" href="javascript:;">
                                                                        PDF File
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($media->type == '1')
                                                        <div class="col-3">
                                                            <div class="card">
                                                                <a href="{{ $media->url }}" data-fancybox="images">
                                                                    <div id="media" class="card-body media"
                                                                         data-url={{ $media->url }}>
                                                                        <img src="{{ $media->url }}"
                                                                             alt="{{ $media->name }}" class="img-fluid">
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if(!$user->portfolios->isEmpty())
                                                <div class="col-md-12 mt-3">
                                                    <h6 class="m-0 pull-left ml-2">Provider Portfolio</h6>
                                                    <button class="btn btn-success float-right"><a
                                                            href="{!! route('admin.portfolios.status', ['type' => 'all', 'id' => $user->id, 'status'=>"1"]) !!}">Approve
                                                            All</a></button>
                                                </div>
                                                @foreach($user->portfolios as $key => $portfolio)
                                                    <div class="col-3">
                                                        <div class="card">
                                                            <div id="portfolio" class="card-body media"
                                                                 data-url={{ $portfolio->image }}>
                                                                <img src="{{ $portfolio->image }}"
                                                                     alt="{{ $portfolio->image }}" data-toggle="modal"
                                                                     data-target="#exampleModalCenter"
                                                                     class="img-fluid">
                                                                <div
                                                                    class="portfolio-status-card {!! $portfolio->status == 1  ? "approved-image" : "not-approved-image"!!}">
                                                                    {!! $portfolio->status == '1' ? "Approved" : "Not Approved" !!}
                                                                </div>
                                                                <i class="fa fa-ellipsis-v float-right three-dots"
                                                                   data-toggle="dropdown" aria-expanded="false"></i>
                                                                <div class="dropdown-menu">
                                                                    @if ($portfolio->status)
                                                                        <a class="dropdown-item"
                                                                           href="{{route('admin.portfolios.status', ['type' => 'pericular', 'id' => $portfolio->id, 'status'=>'0'])}}">Rejected</a>
                                                                    @else
                                                                        <a class="dropdown-item"
                                                                           href="{{route('admin.portfolios.status', ['type' => 'pericular', 'id' => $portfolio->id, 'status'=>'1'])}}">Approved</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if(!$user->provider_services->isEmpty())
                                                <div class="col-md-12 mt-3">
                                                    <h6 class="m-0 pull-left ml-2">Provider Services</h6>
                                                </div>
                                                @foreach($user->provider_services as $key => $service)
                                                    <div class="col-3 mt-2">
                                                        <div class="card dashboard_card">
                                                            <div
                                                                class="card-header card-header-success card-header-icon">
                                                                <div class="card-icon">
                                                                    {{$service->service->name}}
                                                                </div>
                                                                <p class="card-category stats-small__label text-uppercase">
                                                                    Sub Service</p>
                                                                <h3 class="card-title user_data">{{$service->sub_service->name}}</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if(!$user->schedules->isEmpty())
                                                <div class="col-md-12 mt-3">
                                                    <h6 class="m-0 pull-left ml-2">Provider Schedule Details</h6>
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="display table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Day</th>
                                                            <th>From Time</th>
                                                            <th>To Time</th>
                                                            <th>Is Custom</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($user->schedules as $schedule)
                                                            <tr>
                                                                <td>{{$schedule->id}}</td>
                                                                <td>{{$schedule->day}}</td>
                                                                <td>{{$schedule->from_time}}</td>
                                                                <td>{{$schedule->to_time}}</td>
                                                                <td>{{$schedule->is_custom ? "Yes" : "No"}}</td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- @if($user->schedules->isEmpty())
                                                            <tr>
                                                                <td colspan="3">No Schedule Found</td>
                                                            </tr>
                                                        @else
                                                            @foreach($user->schedules as $schedule)
                                                            {{ dd($schedule) }}
                                                                <tr>
                                                                    <td>{{$provider_schedule->id}}</td>
                                                                    <td>{{$provider_schedule->date."/".$provider_schedule->month."/".$provider_schedule->year}}</td>
                                                                    <td>
                                                                        <div class="input-group-btn action_group">
                                                                            <li class="action_icon">
                                                                                <button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
                                                                            <ul class="dropdown-menu">
                                                                                <li>
                                                                                    <a href="javascript:void(0);" id="viewSchedule" class="viewSchedule" data-slots="{{ $provider_schedule->time_slots }}" data-toggle="modal" data-target="#slotsDetails">
                                                                                        <i class="material-icons">visibility</i>  View
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                            </li>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif --}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </fieldset>
                            </form>

                            @if($user->role === 'PROVIDER')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="m-0 pull-left ml-2">Service Area</h6>
                                    </div>
                                </div>
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <th scope="row">Zip Code</th>
                                        <td>Sates</td>
                                        <td>Country</td>
                                        <td>Place ID</td>
                                    </tr>
                                    @foreach($service_area as $zipcode)
                                        <tr>
                                            <td>{{ @$zipcode->code }}</td>
                                            <td>
                                                @foreach(@$zipcode->states as $state)
                                                    <span class="badge badge-info">{{ $state->name }}</span>
                                                @endforeach
                                            </td>
                                            <td> {{ @$zipcode->states[0]['country']['name'] }}</td>
                                            <td>
                                                @foreach(@$zipcode->service_areas as $area)
                                                    <span class="badge badge-info">{{ $area->place_id }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="m-0 pull-left ml-2">Address</h6>
                                    </div>
                                </div>
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <th scope="row">Type</th>
                                        <td>Address</td>
                                        <td>Flat No</td>
                                        <td>Zip Code</td>
                                        <td>State</td>
                                        <td>City</td>
                                    </tr>
                                    @foreach($user->addresses as $address)
                                        <tr>
                                            <td>{{ @$address->type }}</td>
                                            <td>{{ @$address->address }}</td>
                                            <td>{{ @$address->flat_no }}</td>
                                            <td>{{ @$address->zip_code }}</td>
                                            <td>{{ @$address->state }}</td>
                                            <td>{{ @$address->city }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" class="img-fluid d-none" id="providerImages">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="slotsDetails" tabindex="-1" role="dialog" aria-labelledby="slotsDetails"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Slots</th>
                        </tr>
                        </thead>
                        <tbody id="slots"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Credit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add_credit" action="{!! route("admin.users.credit",$user->id) !!}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="credit">Credit</label>
                            <input type="number" class="form-control" id="credit" name="credit"
                                   value="{{old('credit')}}" placeholder="Credits" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="credit-btn" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('.viewSchedule').on('click', function () {
            $('#slots').empty();
            var time_slots = $(this).data('slots');
            console.log(time_slots);
            if (time_slots && time_slots?.length > 0) {
                time_slots.forEach(element => {
                    $('#slots').append(`<tr><td>${element.id}</td><td>${element.start}</td></tr>`);
                });
            } else {
                $('#slots').append(`<tr><td colspan="2">No Slots Found</td></tr>`);
            }
        });

        $('#slotsDetails').on('hidden.bs.modal', function () {
            $('#slots').empty();
        });

        $('#add_credit').on('submit', function () {
            $('#add_credit').find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Adding...').attr('disabled', true);
        });
        // $('#fileClick').on('click', function() {


        // var url = $(this).data('url');
        // console.log(url);
        // let pdfDoc = null,
        // pageNum = 1,
        // pageIsRendering = false,
        // pageNumIsPending = null;

        // const scale = 1.5,
        // canvas = document.querySelector('#pdf-render'),
        // ctx = canvas.getContext('2d');

        // // Render the page
        // const renderPage = num => {
        // pageIsRendering = true;

        // // Get page
        // pdfDoc.getPage(num).then(page => {
        //     // Set scale
        //     const viewport = page.getViewport({ scale });
        //     canvas.height = viewport.height;
        //     canvas.width = viewport.width;

        //     const renderCtx = {
        //     canvasContext: ctx,
        //     viewport
        //     };

        //     page.render(renderCtx).promise.then(() => {
        //     pageIsRendering = false;

        //     if (pageNumIsPending !== null) {
        //         renderPage(pageNumIsPending);
        //         pageNumIsPending = null;
        //     }
        //     });

        //     // Output current page
        //     document.querySelector('#page-num').textContent = num;
        // });
        // };

        // // Check for pages rendering
        // const queueRenderPage = num => {
        // if (pageIsRendering) {
        //     pageNumIsPending = num;
        // } else {
        //     renderPage(num);
        // }
        // };

        // // Show Prev Page
        // const showPrevPage = () => {
        // if (pageNum <= 1) {
        //     return;
        // }
        // pageNum--;
        // queueRenderPage(pageNum);
        // };

        // // Show Next Page
        // const showNextPage = () => {
        // if (pageNum >= pdfDoc.numPages) {
        //     return;
        // }
        // pageNum++;
        // queueRenderPage(pageNum);
        // };

        // // Get Document
        // pdfjsLib
        // .getDocument(url)
        // .promise.then(pdfDoc_ => {
        //     pdfDoc = pdfDoc_;

        //     document.querySelector('#page-count').textContent = pdfDoc.numPages;

        //     renderPage(pageNum);
        // })
        // .catch(err => {

        //     // Display error
        //     const div = document.createElement('div');
        //     div.className = 'error';
        //     div.appendChild(document.createTextNode(err.message));
        //     document.querySelector('body').insertBefore(div, canvas);
        //     // Remove top bar
        //     document.querySelector('.top-bar').style.display = 'none';
        // });

        // // Button Events
        // document.querySelector('#prev-page').addEventListener('click', showPrevPage);
        // document.querySelector('#next-page').addEventListener('click', showNextPage);

        // url = `${APP_URL}${url}`;
        // $('.modal-body').find('img').attr('src', "");
        // $('#iframeFile').attr('src', url);
        // });

        $('.media').on('click', function () {
            var url = $(this).data('url');
            $('.modal-body').find('img').attr('src', url);
            $('.modal-body').find('img').removeClass('d-none');
        });
        $('.portfolio').on('click', function () {
            var url = $(this).data('url');
            $('.modal-body').find('img').attr('src', url);
            $('.modal-body').find('img').removeClass('d-none');
        });
    </script>
@endsection

@push('custom-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"
            integrity="sha512-qa1o08MA0596eSNsnkRv5vuGloSKUhY09O31MY2OJpODjUVlaL0GOJJcyt7J7Z61FiEgHMgBkH04ZJ+vcuLs/w=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('/js/sweet-alert.js') }}"></script>
@endpush
