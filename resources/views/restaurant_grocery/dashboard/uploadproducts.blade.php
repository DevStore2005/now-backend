@extends('restaurant_grocery.index')
@section('content')
    @php
    $USER_ROLE = Auth::user()->role;
    @endphp
    @restaurant
@section('title', 'Add Food')
@include('restaurant_grocery.includes.navigation', ['routes' => [['Add Food' => route('restaurant.uploadFood')]]])
@endrestaurant

@grocer
@section('title', 'Add Product')
@include('restaurant_grocery.includes.navigation', ['routes' => [['Add Product' => route('grocer.uploadProduct')]]])
@endgrocer
<div class="content">
    <div class="container">
        <div class="row">
            @include('restaurant_grocery.includes.sidebar')
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card">
                    <div class="card-body">
                        @if (session('success_message'))
                            <p class="alert alert-success">
                                {{ session('success_message') }}</p>
                        @endif
                        @restaurant
                        <form action="{{ route('restaurant.uploadFood') }}" method="post"
                            enctype="multipart/form-data">
                            @endrestaurant

                            @grocer
                            <form action="{{ route('grocer.uploadProduct') }}" method="post"
                                enctype="multipart/form-data">
                                @endgrocer
                                @csrf
                                <div class="row">
                                    <div class="col-12 text">
                                        @restaurant
                                        <div class="card-title"><b> Add Food </b> </div>
                                        @endrestaurant

                                        @grocer
                                        <div class="card-title"><b> Add Product </b></div>
                                        @endgrocer
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 6 : 4 }}">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" required="">
                                        </div>
                                    </div>
                                    @if ($USER_ROLE == UserType::GROCERY_OWNER)
                                        <div
                                            class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 4 : 6 }}">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <div>
                                                    <input type="number" class="form-control" name="quantity"
                                                        required="">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @restaurant
                                    <div class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 6 : 4 }}">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category_id" class="form-control">
                                                <option value="" disabled="" selected="">Choose category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{!! $category->id !!}">{!! $category->name !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endrestaurant
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="text" class="form-control" name="price" required="">
                                        </div>
                                    </div>

                                    <div class="col-{{ $USER_ROLE == UserType::GROCERY_OWNER ? 8 : 12 }}">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input type="text" name="description" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="change-avatar">
                                                <div class="profile-img">
                                                    <img src="/restaurant/assets/img/img.png" id="uploadPreview" alt="">
                                                </div>
                                                <div class="upload-img">
                                                    <div class="change-photo-btn">
                                                        <span><i class="fa fa-upload"></i> Upload Photo</span>
                                                        <input type="file" name="image" id="image"
                                                            class="upload" required="">
                                                    </div>
                                                    <small class="form-text text-muted">Allowed JPG, GIF or PNG. Max
                                                        size
                                                        of 2MB</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="submit-section" style="position: absolute; bottom: 0;right:0;">
                                            <button type="submit" class="btn btn-primary submit-btn">Save
                                                Changes</button>
                                        </div>
                                    </div>
                            </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const imageHandleChange = function(e) {
        if (e.target.files.length) {
            let url = URL.createObjectURL(e.target.files[0]);
            $('#uploadPreview').attr('src', url);
        } else {
            $('#uploadPreview').attr('src', '/restaurant/assets/img/img.png');
        }
    };
    $('#image').change(imageHandleChange);
</script>
@endsection
