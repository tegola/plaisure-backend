@extends('mail/layout')

@section('content')
	<p><strong>{{ __('emails.common.greeting_name', ['name' => $notifiable->name]) }}</strong></p>
	<p>{{ __('emails.reset_password.intro') }}</p>

	@component('mail/components/action-button', ['url' => $url])
		{{ __('emails.reset_password.action') }}
	@endcomponent

	<p>{{ __('emails.reset_password.outro') }}</p>

	@include('mail/components/salutation')

	@include('mail/components/action-footer', [
		'action' => __('emails.reset_password.action'),
		'url' => $url
	])
@endsection