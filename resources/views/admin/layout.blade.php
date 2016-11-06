<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>{{ config('constants.name') }} - Amministrazione - @yield('title')</title>

		<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
	</head>
	<body>
		<nav class="navbar navbar-full navbar-light bg-faded mb-3">
			<div class="container">
				<a class="navbar-brand" href="{{ route('admin.home') }}">{{ config('constants.name') }} - Amministrazione</a>
				<div class="collapse navbar-toggleable-sm" id="navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.upload') }}">Carica esercizi</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.maintain') }}">Manutenzione esercizi</a>
						</li>
					</ul>
				</div>
				<button class="navbar-toggler hidden-md-up pull-xs-right" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Mostra/nascondi navigazione">&#9776;</button>
			</div>
		</nav>

		@yield('content')

		<div class="container">
			<hr>
			<p>&copy; {{ date('Y') }} {{ config('constants.company') }}</p>
		</div>

		<script src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.google_maps_api_key') }}" defer></script>
		<script src="{{ asset('js/admin.js') }}"></script>
	</body>
</html>