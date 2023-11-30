@php
    $socials = App\Models\Link::whereNotNull('type')->get();
@endphp

<h2>Stay Connected</h2>
@foreach ($socials as $social)
    <a href="{{ $social->url }}" target="_blank" style="display: inline-block; margin-right: 10px;">
        <img src="{{ config('app.url') . '/api/img?img=admin/assets/img/social/'.$social->type.'.png' }}"
             alt="{{ $social->name }}" width="30" height="30">
    </a>
@endforeach
