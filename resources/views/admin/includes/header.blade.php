@php
    $active_countries = \App\Models\Country::query()->where('is_active', 1)->get();
@endphp

<div class="main-navbar sticky-top bg-white">
    <nav class="navbar align-items-stretch justify-content-stretch navbar-light flex-md-nowrap  p-0">
        <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
            <div class="alert alert-danger alert-dismissible is_bankdetails  fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{-- <strong>Please Fill Bank Details</strong><a href="https://farerun.com/admin/bankdetails">click here</a>. --}}
            </div>
            <!-- <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search"> -->
        </form>
        <span class="m-4 toggle-sidebar-btn d-md-none d-lg-none" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
              aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </span>
        <ul class="navbar-nav flex-row px-3">

            <li class="nav-item dropdown p-3">
                <form action="" id="country-form">
                    <div class="form-group" style="width: 200px">
                        <select id="country-select" name="locale" class="form-control" onchange="">
                            <option selected value="all">Choose Country</option>
                            <option value="all" {{ request()->query('locale') === 'all' ? 'selected' : '' }}>All
                            </option>
                            @foreach($active_countries as $country)
                                <option
                                    {{ request()->query('locale') === strtolower($country->iso2) ? 'selected' : '' }} value="{{ strtolower($country->iso2) }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
            <li class="nav-item dropdown p-3" style="text-align: left;">
                <a class="nav-link dropdown-toggle text-nowrap p-3" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons">
                        person
                    </i> Profile
                    <span class="d-none d-md-inline-block user_name"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="{!! route('admin.profile') !!}">
                        <i class="material-icons">
                            person
                        </i> Profile
                    </a>
                    {{-- <a class="dropdown-item" href="https://farerun.com/admin/password">
                    <i class="material-icons">lock</i> Change Password
                    </a> --}}
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{route('admin.logout')}}">
                        <i class="material-icons text-danger">&#xE879;</i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
</div>

<script>
    $(document).ready(function () {
        $('#country-select').change(function () {
            $("#country-form").submit();
            // var selectedCountry = $(this).val();
            // var currentUrl = window.location.href;
            // var baseUrl = currentUrl.split('/').slice(0, 3).join('/'); // Get the base URL
            // var newUrl = baseUrl + '/' + selectedCountry + currentUrl.substr(baseUrl.length);
            // // // Redirect to the new URL
            // window.location.href = newUrl;
        });
    });
</script>
