@component('mail::message')
# @if ($user)
    Hi {{ $user->first_name }}!
@endif

Welcome to FareNow, your go-to destination for all your outsourcing needs. We're excited to have you on board! With FareNow, you can easily find, hire, and pay local freelancers for a variety of tasks, such as cleaning, handyman work, moving, and more. Our platform makes it easy to compare prices, read reviews, and find the right service provider for your needs.
In addition to our standard booking options, we also offer the flexibility of hourly service and subscription services. With our hourly service, you can hire a freelancer for as many hours as you need, and with our subscription service, you can enjoy the convenience of having a regular service provider at your disposal and the added benefit of receiving significant discounts.
Thank you for signing up and we look forward to helping you get things done.
Start browsing and booking now and don't hesitate to contact us if you have any questions.

# @if ($user->provider_type == "INDIVIDUAL" && $user->role == "PROVIDER")
    Please submit your time slots to get approved by admin.
@endif

{{-- {{ config('app.name') }} --}}
@endcomponent