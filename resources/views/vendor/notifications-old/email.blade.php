<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href="{{ public_path('css/mail.css') }}">
</head>
<body>
	<div class="body">
		{{-- Logo --}}
		<a href="{{ url('/') }}">{{ config('app.name') }}</a>

		{{-- Greeting --}}
		<h1>
			@if (!empty($greeting))
				{{ $greeting }}
			@else
				@if ($level == 'error')
					{{ __('emails.common.greeting_error') }}
				@else
					{{ __('emails.common.greeting_success') }}
				@endif
			@endif
		</h1>

		{{-- Intro --}}
		@foreach ($introLines as $line)
			<p>{{ $line }}</p>
		@endforeach

		<!-- Action Button -->
		@if (isset($actionText))
			@php
				switch ($level) {
					case 'success': $actionColor = 'btn-success'; break;
					case 'error': $actionColor = 'btn-danger'; break;
					default: $actionColor = 'btn-primary';
				}
			@endphp
			<a href="{{ $actionUrl }}" class="btn {{ $actionColor }}">{{ $actionText }}</a>
		@endif

		<!-- Outro -->
		@foreach ($outroLines as $line)
			<p>{{ $line }}</p>
		@endforeach

		<!-- Salutation -->
		<p>
			@if (!empty($salutation))
				{{ $salutation }}
			@else
				{{ __('emails.common.salutation') }},<br>
				{{ __('emails.common.salutation_name', ['name' => config('app.name')]) }}
			@endif
		</p>

		<!-- Sub Copy -->
		@if (isset($actionText))
			<p class="small">{{ __('emails.common.trouble', ['action' => $actionText]) }}</p>
			<p class="small">
				<a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
			</p>
		@endif

		{{-- Footer --}}
		<p class="small">
			&copy; {{ date('Y') }}
			<a href="{{ url('/') }}">{{ config('app.name') }}</a>.
			{{ __('emails.common.rights') }}
		</p>
	</div>
</body>
</html>