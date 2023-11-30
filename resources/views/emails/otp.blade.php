@component('mail::message')

# Email Verification OTP

# We are Welcome to {{ config('app.name') }}!

please verify your email, your OTP is #{{ $otp->otp }}.

pleae don't share this OTP with anyone.

Thanks,<br>
{{ config('app.name') }}
@endcomponent