@php
    $links = App\Models\Link::whereIn('page', [
        'TERMS',
        'PRIVACY',
        'FAQ',
        'ABOUT'
    ])->get([
        'page',
        'url',
        'name'
    ]);
@endphp

<h1 style="margin-bottom: 50px; font-size: 24px">The Farenow Team</b><h2/>
{{--<hr class="full"/>--}}
<div class="sub-copy">
    <a href="https://farenow.com">
        <img style="width: 170px; height: 70px" src="{{ config('app.url') . '/api/logo-icon' }}" class="logo-icon" alt="Farenow">
    </a>
    <div>
        <strong style="color: #0a0a0a">Questions? Visit the Help Center</strong><br/>
        @foreach ($links as $link)
            <a href="{!! $link->url !!}">{!! $link->name !!}</a><br/>
        @endforeach
        <p>
            We are here to help if you need it. Visit the Help Center for more info or contact us.
            If you have any questions, please contact us at <a style="color: #1178df; text-decoration: underline" href="mailto:support@farenow.com.">support@farenow.com.</a>
            The {!! config('app.name') !!} team.
            This message was mailed from <a style="color: #1178df; text-decoration: underline" href="mailto:{!! config('mail.from.address') !!}">{!! config('mail.from.address') !!}</a> by {!! config('app.name') !!}.
        </p>
    </div>
</div>
