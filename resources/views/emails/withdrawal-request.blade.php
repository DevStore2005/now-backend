@component('mail::message')
**{!! $provider->first_name. " " .$provider->last_name !!}** has requested a withdrawal of **${!! $amount !!}**.

provider has ${!! $provider->provider_profile->id !!}

{!! $description !!}

@component('mail::button', ['url' => route('admin.profiles.profile', ['user' =>$provider->id, 'request-amount' => $amount])])
View {!! $provider->first_name !!}'s Profile
@endcomponent
@endcomponent