@component('mail::message')

{!! $provider->first_name !!} has accepted your request id {!! $serviceRequest->id !!}.

When provider starts the service, we will notify you.

Thanks,
{{ config('app.name') }}
@endcomponent