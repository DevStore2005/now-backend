@component('mail::message')
# @if ($user)
    New User {{ $user->first_name }} {{ $user->last_name }} has registered.
    Name: {{ $user->first_name }} {{ $user->last_name }}
    Email: {{ $user->email }}
    Phone: {{ $user->phone }}
    Role ID: {{ Str::title($user->role) }}
@endif
{{ config('app.name') }}
@endcomponent