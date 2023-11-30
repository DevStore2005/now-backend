@extends('restaurant_grocery.index')
@section('content')
@restaurant
    @section('title', 'Food List')
    @include('restaurant_grocery.includes.navigation', ['routes' => [['Foods' => route('restaurant.food.index')]]])
@endrestaurant

@grocer
    @section('title', 'Add Product')
    @include('restaurant_grocery.includes.navigation', ['routes' => [['Products' => route('grocer.product.index')]]])
@endgrocer
<div class="content">
    <div class="container">
        <div class="row">
            @include('restaurant_grocery.includes.sidebar')

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="row">
                    <div class="col-md-12">
                        @if (session('message'))
                            <p class="alert alert-warning">
                                {{ session('message') }}</p>
                        @endif
                        @restaurant
                        <h4 class="mb-4">Food<span>
                            <a href="{{ route('restaurant.uploadFood') }}">
                                <button class="float-right btn btn-sm btn-primary">+ Food</button>
                            </a></span>
                        </h4>
                        @endrestaurant

                        @grocer
                            <h4 class="mb-4">Product<span>
                                <a href="{{ route('grocer.uploadProduct') }}">
                                    <button class="float-right btn btn-sm btn-primary">+ Product</button>
                                </a></span>
                            </h4>
                        @endgrocer

                        <div class="appointment-tab">



                            <div class="tab-content">

                                <div class="tab-pane show active" id="upcoming-appointments">
                                    <div class="card card-table mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="display table table-striped table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Price</th>
                                                            <th class="text-center">Description</th>

                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($products as $product)
                                                            <tr>
                                                                <td>{{ $product->id }} </td>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2">
                                                                            <img
                                                                                class="avatar-img rounded-circle"
                                                                                src="{{ $product->product_image ? $product->product_image : '/restaurant/assets/img/customers/customer5.jpg' }}"
                                                                                alt="User Image"
                                                                            >
                                                                        </a>
                                                                        <a href="customer-profile.html">{{ $product->product_name }}
                                                                            <span>#{{ $product->id }}</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>{{ $product->price }} </td>
                                                                <td>{{ $product->description }}</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        @restaurant
                                                                        <a href="{{ route('restaurant.editFood', $product->id) }}"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>

                                                                        <a href="{{ route('restaurant.deleteFood', $product->id) }}"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Delete
                                                                        </a>
                                                                        @endrestaurant

                                                                        @grocer
                                                                        <a href="{{ route('grocer.editProduct', $product->id) }}"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>

                                                                        <a href="{{ route('grocer.deleteProduct', $product->id) }}"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Delete
                                                                        </a>
                                                                        @endgrocer
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
