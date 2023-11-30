@extends('restaurant_grocery.index')
@section('title', 'Dashboard')
@section('content')
@restaurant
@include('restaurant_grocery.includes.navigation', ['routes' => [['Dashboard' => route('restaurant.dashboard')]]])
@endrestaurant
@grocer
@include('restaurant_grocery.includes.navigation', ['routes' => [['Dashboard' => route('grocer.dashboard')]]])
@endgrocer
    <div class="content">
        <div class="container">
            <div class="row">
                @include('restaurant_grocery.includes.sidebar')
                <div class="col-md-7 col-lg-8 col-xl-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card dash-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="icon-box">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    @restaurant
                                                    <h6>Total Foods</h6>
                                                    @endrestaurant
                                                    @grocer
                                                    <h6>Total Products</h6>
                                                    @endgrocer
                                                    {{-- {{ dd($order) }} --}}
                                                    <h3>{{ $total_product }}</h3>
                                                    <p class="text-muted">Till Today</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="icon-box">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Today Customer</h6>
                                                    <h3>160</h3>
                                                    <p class="text-muted">06, Nov 2021</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget">
                                                <div class="icon-box">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Bookings</h6>
                                                    <h3>85</h3>
                                                    <p class="text-muted">06, Apr 2021</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-4">Customer Bookings</h4>
                            <div class="appointment-tab">

                                {{-- <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#upcoming-appointments"
                                            data-toggle="tab">Upcoming</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#today-appointments" data-toggle="tab">Today</a>
                                    </li>
                                </ul> --}}

                                <div class="tab-content">

                                    <div class="tab-pane show active" id="upcoming-appointments">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="display table table-striped table-hover table-center mb-0">
                                                        @if (isset($orders) && count($orders) > 0)
                                                        <thead>
                                                            <tr>
                                                                <th>#order no</th>
                                                                @restaurant
                                                                    <th>Food</th>
                                                                @endrestaurant
                                                                @grocer
                                                                    <th>Product</th>
                                                                @endgrocer
                                                                <th class="text-center">Quantity</th>
                                                                <th class="text-center">Total Price</th>
                                                                {{-- <th></th> --}}
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($orders as $order)
                                                            <tr>
                                                                <td>{{ $order->id }}</td>
                                                                @restaurant
                                                                    <td>{{ $order->food->name }}</td>
                                                                @endrestaurant
                                                                @grocer
                                                                <td>{{ $order->product->name }}</td>
                                                                @endgrocer
                                                                <td class="text-center">{{ $order->quantity }}</td>
                                                                <td class="text-center">{{ $order->total_amount }}</td>
                                                                {{-- <td> --}}
                                                                    {{-- <a href="{{ route('restaurant.order.show', $item->id) }}"
                                                                        class="btn btn-primary btn-sm">View</a> --}}
                                                                {{-- </td> --}}
                                                            @endforeach
                                                            {{-- <tr>
                                                                <td> --}}
                                                                    {{-- <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Marion Hicks
                                                                            <span>#CT0016</span></a>
                                                                    </h2> --}}
                                                                {{-- </td>
                                                                <td>11 Nov 2021 <span class="d-block text-info">10.00
                                                                        AM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$150</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr> --}}
                                                            {{-- <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer1.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Charlene Reed
                                                                            <span>#CT0001</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>3 Nov 2021 <span class="d-block text-info">11.00
                                                                        AM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$200</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer2.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Travis Trimble
                                                                            <span>#CT0002</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>1 Nov 2021 <span class="d-block text-info">1.00
                                                                        PM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$75</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer3.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Carl Kelly
                                                                            <span>#CT0003</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>30 Oct 2021 <span class="d-block text-info">9.00
                                                                        AM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$100</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer4.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Michelle Fairfax
                                                                            <span>#CT0004</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>28 Oct 2021 <span class="d-block text-info">6.00
                                                                        PM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$350</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer5.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Gina Moore
                                                                            <span>#CT0005</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>27 Oct 2021 <span class="d-block text-info">8.00
                                                                        AM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$250</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr> --}}
                                                        </tbody>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- <div class="tab-pane" id="today-appointments">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer Name</th>
                                                                <th>Booked Date</th>
                                                                <th>Type</th>
                                                                <th class="text-center">Paid Amount</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer6.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Elsie Gilley
                                                                            <span>#CT0006</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">6.00
                                                                        PM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$300</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer7.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Joan Gardner
                                                                            <span>#CT0006</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">5.00
                                                                        PM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$100</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer8.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Daniel Griffing
                                                                            <span>#CT0007</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">3.00
                                                                        PM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$75</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer9.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Walter Roberson
                                                                            <span>#CT0008</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">1.00
                                                                        PM</span></td>
                                                                <td>Old Customer</td>
                                                                <td class="text-center">$350</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer10.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Robert Rhodes
                                                                            <span>#CT0010</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">10.00
                                                                        AM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$175</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h2 class="table-avatar">
                                                                        <a href="customer-profile.html"
                                                                            class="avatar avatar-sm mr-2"><img
                                                                                class="avatar-img rounded-circle"
                                                                                src="/restaurant/assets/img/customers/customer11.jpg"
                                                                                alt="User Image"></a>
                                                                        <a href="customer-profile.html">Harry Williams
                                                                            <span>#CT0011</span></a>
                                                                    </h2>
                                                                </td>
                                                                <td>14 Nov 2021 <span class="d-block text-info">11.00
                                                                        AM</span></td>
                                                                <td>New Customer</td>
                                                                <td class="text-center">$450</td>
                                                                <td class="text-right">
                                                                    <div class="table-action">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-info-light">
                                                                            <i class="far fa-eye"></i> View
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm bg-danger-light">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
 
