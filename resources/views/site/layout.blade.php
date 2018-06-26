<!DOCTYPE html>
<html lang="{{ Locale::getPrimaryLanguage(app()->getLocale()) }}">
	<head>
		{{-- Metas --}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		{{-- Verifications --}}
		<meta name="google-site-verification" content="KOtwsto-b3doO3NsrSsETD-ci_02n8wKCO0dzZfL_bk">
		<meta name="msvalidate.01" content="0F83EB755446F01A87E89E5439AB1573">

		{{-- Title --}}
		<title>
			@hasSection('title')
				@yield('title') - {{ config('app.name') }}
			@else
				{{ config('app.name') }}
			@endif
		</title>

		{{-- Description --}}
		@hasSection('description')
			<meta name="description" content="@yield('description')">
		@endif

		{{-- Typekit --}}
		<script src="https://use.typekit.net/qwv3xzz.js"></script>
		<script>try{Typekit.load({ async: true });}catch(e){}</script>

		{{-- Styles --}}
		<link rel="stylesheet" href="{{ mix('css/app.css') }}">

		{{-- Favicons --}}
		<link rel="apple-touch-icon" href="/img/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/16x16.png">
		<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#7dd194">

		@yield('head')
	</head>
	<body>
		<div id="app">
			<pg-app></pg-app>
			@yield('content')
		</div>

		@include('site.components.vectors')

		@include('scripts')
		<script src="{{ mix('js/app.js') }}"></script>
	</body>
</html>