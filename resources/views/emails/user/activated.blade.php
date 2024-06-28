@component('mail::message')
# Sample App

Hi {{ $user->name }},

Welcome to the Sample App! Click on the link below to activate your account:

@component('mail::button', ['url' => route('account.activate', ['token' => $user->activation_token, 'email' => $user->email])])
Activate
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
