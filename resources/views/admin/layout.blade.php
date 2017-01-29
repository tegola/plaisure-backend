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
		<nav class="navbar navbar-toggleable-sm navbar-light bg-faded mb-3">
			<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a class="navbar-brand" href="{{ route('admin.home') }}">{{ config('constants.name') }} - Amministrazione</a>

			<div class="collapse navbar-collapse" id="navbar-collapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="{{ route('admin.venues.upload') }}">Carica esercizi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('admin.venues.maintain') }}">Manutenzione esercizi</a>
					</li>
				</ul>
			</div>
		</nav>

		@yield('content')

		<div class="container">
			<hr>
			<p>&copy; {{ date('Y') }} {{ config('constants.company') }}</p>
		</div>

		{{-- FIXME: Pass region per site and language per user locale --}}
		<script src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.google_maps_api_key') }}&language=it&region=IT" defer></script> 
		<script src="{{ asset('js/admin.js') }}"></script>
	</body>
</html>