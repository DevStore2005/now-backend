<header class="header">
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="navbar-header">
            <a id="mobile_btn" href="javascript:void(0);">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
            @restaurant
            <a href="{{ route('restaurant.index') }}" class="navbar-brand logo">
            @endrestaurant
            @grocer
            <a href="{{ route('grocer.index') }}" class="navbar-brand logo">
            @endgrocer
            @guest
                <a href="{{ "#" }}" class="navbar-brand logo">
            @endguest
                <span>Farenow</span>
                {{-- <img src="/restaurant/assets/img/logo.svg" class="img-fluid" alt="Logo"> --}}
            </a>
        </div>
        <div class="main-menu-wrapper ml-auto">
            <div class="menu-header">
                <a href="index.html" class="menu-logo">
                    {{-- <img src="{{ url('restaurant/assets/img/logo.svg') }}" class="img-fluid" alt="Logo"> --}}
                </a>
                <a id="menu_close" class="menu-close" href="javascript:void(0);">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <ul class="main-nav">




            </ul>
        </div>
        @if (empty(Auth::user()))
            <ul class="nav header-navbar-rht">
                <li class="nav-item">
                    <a class="nav-link" href="{{ $subdomain == 'grocer' ? route('grocer.login') : route('restaurant.login')}}">Login </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link header-login" href="{{ $subdomain == 'grocer' ? route('grocer.signup') : route('restaurant.signup') }}">Signup </a>
                </li>
            </ul>
        @endif
    </nav>
</header>
