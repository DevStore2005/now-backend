@extends('restaurant_grocery.index')
@section('title', 'Profile')
@section('content')
    @restaurant
    @include('restaurant_grocery.includes.navigation', ['routes' => [['Profile' => route('restaurant.profileSetting')]]])
    @endrestaurant

    @grocer
    @include('restaurant_grocery.includes.navigation', ['routes' => [['Profile' => route('grocer.profileSetting')]]])
    @endgrocer
    <div class="content">
        <div class="container">
            <div class="row">
                @include('restaurant_grocery.includes.sidebar')

                <div class="col-md-7 col-lg-8 col-xl-9">
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
                    @restaurant
                    <form action="{{ route('restaurant.profileSetting') }}" method="post" enctype="multipart/form-data">
                        @endrestaurant

                        @grocer
                        <form action="{{ route('grocer.profileSetting') }}" method="post" enctype="multipart/form-data">
                            @endgrocer
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Basic Information</h4>
                                    <div class="row form-row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="change-avatar col-md-6">
                                                    <div class="profile-img">
                                                        <img src={{ $user->business_profile && $user->business_profile->profile_image ? $user->business_profile->profile_image : '/restaurant/assets/img/img.png' }}
                                                            onerror="this.src='/restaurant/assets/img/img.png';"
                                                            id="profilePreview" alt="User Image">
                                                    </div>
                                                    <div class="upload-img">
                                                        <div class="change-photo-btn">
                                                            <span><i class="fa fa-upload"></i> Upload Photo</span>
                                                            <input type="file" name="profile_image" id="profileImage"
                                                                class="upload">
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Allowed JPG, GIF or PNG. Max size of 2MB
                                                        </small>
                                                        Profile image
                                                    </div>
                                                </div>
                                                <div class="change-avatar col-md-6">
                                                    <div class="profile-img">
                                                        <img src={{ $user->business_profile && $user->business_profile->cover_image ? $user->business_profile->cover_image : '/restaurant/assets/img/img.png' }}
                                                            onerror="this.src='/restaurant/assets/img/img.png';"
                                                            id="coverPreview" alt="User Image">
                                                    </div>
                                                    <div class="upload-img">
                                                        <div class="change-photo-btn">
                                                            <span><i class="fa fa-upload"></i> Upload Photo</span>
                                                            <input type="file" name="cover_image" id="coverImage"
                                                                class="upload">
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Allowed JPG, GIF or PNG. Max size of 2MB
                                                        </small>
                                                        Cover Image
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" value="{{ @$user->email }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="first_name" value="{{ @$user->first_name }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="last_name" value="{{ @$user->last_name }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text" name="phone" value="{{ @$user->phone }}"
                                                    class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">About Me</h4>
                                    <div class="form-group mb-0">
                                        <label>Biography</label>
                                        <textarea class="form-control" name="about" value=''
                                            rows="5">{{ @$user->business_profile->about }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @php
                                $role = @$user->role;
                            @endphp
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        {{ $role == UserType::GROCERY_OWNER ? 'Store' : 'Restaurant' }} Info</h4>
                                    <div class="row form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $role == UserType::GROCERY_OWNER ? 'Store' : 'Restaurant' }}
                                                    Name</label>
                                                <input type="text" name="name"
                                                    value="{{ @$user->business_profile->name }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $role == UserType::GROCERY_OWNER ? 'Store' : 'Restaurant' }}
                                                    Website</label>
                                                <input type="text" name="website"
                                                    value="{{ @$user->business_profile->website }}"
                                                    class="form-control">
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $role == UserType::GROCERY_OWNER ? 'Store' : 'Restaurant' }}
                                                    Phone</label>
                                                <input type="text" name="business_phone"
                                                    value="{{ @$user->business_profile->business_phone }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        @restaurant
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="restaurant_type" class="form-control"
                                                    value={{ @$user->business_profile->restaurant_type }}>
                                                    <option value="" disabled="" selected="">Select type</option>
                                                    <option value={{ RestaurantType::VEG }}
                                                        {{ @$user->business_profile->restaurant_type == RestaurantType::VEG ? 'selected' : null }}>
                                                        {{ RestaurantType::$types[RestaurantType::VEG] }} </option>
                                                    <option value={{ RestaurantType::NON_VEG }}
                                                        {{ @$user->business_profile->restaurant_type == RestaurantType::NON_VEG ? 'selected' : null }}>
                                                        {{ RestaurantType::$types[RestaurantType::NON_VEG] }} </option>
                                                    <option value={{ RestaurantType::ALL }}
                                                        {{ @$user->business_profile->restaurant_type == RestaurantType::ALL ? 'selected' : null }}>
                                                        {{ RestaurantType::$types[RestaurantType::ALL] }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        @endrestaurant
                                    </div>
                                </div>
                            </div>


                            <div class="card contact-card">
                                <div class="card-body">
                                    <h4 class="card-title">Contact Details</h4>
                                    <div class="row form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input type="text" name="address"
                                                    value="{{ @$user->business_profile->address }}"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">City</label>
                                                <input type="text" name="city"
                                                    value="{{ @$user->business_profile->city }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">State / Province</label>
                                                <input type="text" name="state"
                                                    value="{{ @$user->business_profile->state }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Country</label>
                                                <input type="text" name="country"
                                                    value="{{ @$user->business_profile->country }}"
                                                    class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="submit-section submit-btn-bottom">
                                <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                            </div>
                        </form>

                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const previewImage = (id, url) => {
            if (id == 'profileImage') {
                $('#profilePreview').attr('src', url);
            }
            if (id == 'coverImage') {
                $('#coverPreview').attr('src', url);
            }
        }

        const imageHandleChange = function(e) {
            if (e.target.files.length) {
                let url = URL.createObjectURL(e.target.files[0]);
                previewImage(e.target.id, url);
                // $('#uploadPreview').attr('src', url);
            } else {
                previewImage(e.target.id, '/restaurant/assets/img/img.png');
                // $('#uploadPreview').attr('src', );
            }
        };
        $('#profileImage').change(imageHandleChange);
        $('#coverImage').change(imageHandleChange);
    </script>
@endsection
