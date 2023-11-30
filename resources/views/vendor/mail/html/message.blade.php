@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    {{-- @isset($subcopy) --}}
        @slot('subcopy')
            @component('mail::subcopy')
            @endcomponent
        @endslot
    {{-- @endisset --}}

    @slot('social')
        @component('mail::social')
            # Stay Connected
        @endcomponent
    @endslot

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
