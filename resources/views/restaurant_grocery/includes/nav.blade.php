@restaurant
<li>
    <a href="{{ route('restaurant.dashboard') }}">
        <i class="fas fa-columns"></i>
        <span>Dashboard</span>
    </a>
</li>
@endrestaurant

@grocer
<li>
    <a href="{{ route('grocer.dashboard') }}">
        <i class="fas fa-columns"></i>
        <span>Dashboard</span>
    </a>
</li>
@endgrocer

@if(auth()->check() && auth()->user()->business_profile)
    @restaurant
    <li>
        <a href="{{ route('restaurant.food.index') }}">
            <i class="fas fa-share-alt"></i>
            <span>Manage {{ auth()->user()->role == 'RESTAURANT_OWNER' ? 'Foods' : 'Products' }}</span>
        </a>
    </li>
    @endrestaurant
    
    @grocer
    <li>
        <a href="{{ route('grocer.product.index') }}">
            <i class="fas fa-share-alt"></i>
            <span>Manage {{ auth()->user()->role == 'RESTAURANT_OWNER' ? 'Foods' : 'Products' }}</span>
        </a>
    </li>
    @endgrocer
@else
@restaurant
    <li>
        <a href="{{ route('restaurant.profileSetting') }}">
            <i class="fas fa-share-alt"></i>
            <span>Please complete your profile</span>
        </a>
    </li>
@endrestaurant

@grocer
    <li>
        <a href="{{ route('grocer.profileSetting') }}">
            <i class="fas fa-share-alt"></i>
            <span>Please complete your profile</span>
        </a>
    </li>
@endgrocer
@endif

@restaurant
<li>
    <a href="{{ route('restaurant.profileSetting') }}">
        <i class="fas fa-user-cog"></i>
        <span>Profile Settings</span>
    </a>
</li>
@endrestaurant

@grocer
<li>
    <a href="{{ route('grocer.profileSetting') }}">
        <i class="fas fa-user-cog"></i>
        <span>Profile Settings</span>
    </a>
</li>
@endgrocer

@restaurant
<li>
    <a href="{{ route('restaurant.logout') }}">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</li>
@endrestaurant

@grocer
<li>
    <a href="{{ route('grocer.logout') }}">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</li>
@endgrocer