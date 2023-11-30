<div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar" id="theiaStickySidebar">

    <div class="profile-sidebar">
        <div class="widget-profile pro-widget-content">
            <div class="profile-info-widget">
                <a href="#" class="booking-res-img">
                    @php
                        $user = Auth::user();
                    @endphp
                    <img src={{ $user->business_profile && $user->business_profile->profile_image ? $user->business_profile->profile_image : '/restaurant/assets/img/customers/customer.jpg' }}
                        alt="User Image">
                </a>
                <div class="profile-det-info">
                    <h3> {{ Auth::user()->first_name }}</h3>
                    <div class="patient-details">
                        <h5 class="mb-0">Thai Cousine</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-widget">
            <nav class="dashboard-menu">
                <ul>
                    @include('restaurant_grocery.includes.nav')
                </ul>
            </nav>
        </div>
    </div>

</div>
