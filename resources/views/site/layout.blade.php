<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>
			@hasSection('title')
				@yield('title') - {{ config('app.name') }}
			@else
				{{ config('app.name') }}
			@endif
		</title>

		<script src="https://use.typekit.net/qwv3xzz.js"></script>
		<script>try{Typekit.load({ async: true });}catch(e){}</script>

		<link rel="stylesheet" href="{{ mix('css/app.css') }}">
		@yield('stylesheets')
	</head>
	<body class="page @yield('body_class')">
		<div id="app">
			@yield('content')
		</div>

		@include('site.components.footer')

		@include('site.icons.defs')

		@include('scripts')
		@yield('scripts')
		<script src="{{ mix('js/app.js') }}"></script>

		@if(env('GOOGLE_ANALYTICS_CODE'))
			@include('site.components.google-analytics', ['code' => env('GOOGLE_ANALYTICS_CODE')])
		@endif
	</body>
</html>