@extends('restaurant_grocery.index')
@section('content')
    @php
    $USER_ROLE = Auth::user()->role;
    @endphp
    @restaurant
@section('title', 'Update Food')
@include('restaurant_grocery.includes.navigation', ['routes' => [['Update Food' => route('restaurant.editFood',
$product->id)]]])
@endrestaurant

@grocer
@section('title', 'Update Product')
@include('restaurant_grocery.includes.navigation', ['routes' => [['Update Product' => route('grocer.editProduct',
$product->id)]]])
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
                        <form action="{{ route('restaurant.editFood', $product->id) }}" method="post"
                            enctype="multipart/form-data">
                            @endrestaurant

                            @grocer
                            <form action="{{ route('grocer.profileSetting', $product->id) }}" method="post"
                                enctype="multipart/form-data">
                                @endgrocer
                                @csrf
                                <div class="row form-row">
                                    <div class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 6 : 4 }}">
                                        <div class="form-group">
                                            <label>Product Name</label>
                                            <input type="text" name="name" value="{{ $product->name }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    @if ($USER_ROLE == UserType::GROCERY_OWNER)
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <div>
                                                    <input type="number" class="form-control"
                                                        value="{{ $product->quantity }}" name="quantity">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @restaurant
                                    <div class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 6 : 4 }}">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category_id" class="form-control">
                                                <option value="" disabled="" selected="">Choose Service</option>
                                                @foreach ($categories as $category)
                                                    @if ($category->id == $product->category_id)
                                                        <option value="{!! $category->id !!}" selected>
                                                            {!! $category->name !!}</option>
                                                    @else
                                                        <option value="{!! $category->id !!}">{!! $category->name !!}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endrestaurant
                                    <div class="col-12 col-md-{{ $USER_ROLE === UserType::GROCERY_OWNER ? 6 : 4 }}">
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="text" class="form-control" value="{{ $product->price }}"
                                                name="price">
                                        </div>
                                    </div>

                                    <div class="col-{{ $USER_ROLE == UserType::GROCERY_OWNER ? 8 : 12 }}">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input type="text" name="description" value="{!! $product->description !!}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="change-avatar">
                                                <div class="profile-img">
                                                    <img src={{ $product->image ? $product->image : '/restaurant/assets/img/img.png' }}
                                                        onerror="this.src='/restaurant/assets/img/img.png';"
                                                        id="uploadPreview" alt="User Image">
                                                </div>
                                                <div class="upload-img">
                                                    <div class="change-photo-btn">
                                                        <span><i class="fa fa-image"></i> Upload Photo</span>
                                                        <input type="file" name="image" id="image"
                                                            class="upload">
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
                                            <button type="submit" class="btn btn-primary submit-btn">Update
                                                Changes</button>
                                        </div>
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
        const {
            files
        } = e.target;
        if (files.length) {
            let url = URL.createObjectURL(files[0]);
            $('#uploadPreview').attr('src', url);
        } else {
            $('#uploadPreview').attr('src', '/restaurant/assets/img/img.png');
        }
    };
    $('#image').change(imageHandleChange);
</script>
@endsection
