<!DOCTYPE html>
<html lang="{{ Locale::getPrimaryLanguage(app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>{{ config('app.name') }} - Amministrazione - @yield('title')</title>

		<link rel="stylesheet" href="{{ mix('css/admin.css') }}">
	</head>
	<body>
		<div id="app">
			@include('admin.components.navbar')

			@yield('content')
		</div>

		@include('scripts')
		<script src="{{ mix('js/admin.js') }}"></script>
	</body>
</html>