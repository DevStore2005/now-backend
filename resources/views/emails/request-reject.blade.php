@component('mail::message')
@if ($type == 'User')
**{!! $provider->first_name !!}** has rejected your request id **#{!! $serviceRequest->id !!}**

Please try again or book other provider.
@if ($refund)

Your payment has been refunded. will be credited to your account within 5-10 business days.
Please check your transaction history for more details.
@endif
@elseif($type == 'Provider')
You have rejected the request id **#{!! $serviceRequest->id !!}** from **{!! $user->first_name !!}**
@endif

Thanks,
{{ config('app.name') }}
@endcomponent