@component('mail::message')

#{{ config('app.name') }}!

forgot your password? your OTP is #{{ $otp->otp }}.

Thanks,<br>
{{ config('app.name') }}
@endcomponent