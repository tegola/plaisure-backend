<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>{{ config('app.name') }} - Amministrazione - @yield('title')</title>

		<link rel="stylesheet" href="{{ mix('css/admin.css') }}">
	</head>
	<body>
		<div id="app">
			<nav class="navbar navbar-toggleable-md navbar-light bg-faded mb-3">
				<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Mostra menu di navigazione">
					<span class="navbar-toggler-icon"></span>
				</button>
				<a class="navbar-brand" href="{{ route('admin.home') }}">{{ config('app.name') }} - Amministrazione</a>

				<div class="collapse navbar-collapse" id="navbar-collapse">
					<ul class="navbar-nav">
						{{-- 
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.upload') }}">Carica esercizi</a>
						</li>
						--}}
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="http://example.com" id="navbar-venues-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Esercizi
							</a>
							<div class="dropdown-menu" aria-labelledby="navbar-venues-link">
								<a class="dropdown-item" href="{{ route('admin.venues.index') }}">Esercizi nel database</a>
								<a class="dropdown-item" href="#">Esercizi chiusi</a>
								<a class="dropdown-item" href="#">Nuovi esercizi</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="{{ route('admin.venues.upload') }}">Carica CSV</a>
								{{-- <a class="dropdown-item" href="{{ route('admin.venues.maintain') }}">Modalit&agrave; di manutenzione</a> --}}
							</div>
						</li>
					</ul>
				</div>
			</nav>

			@yield('content')

			<div class="container">
				<hr>
				<p>&copy; {{ date('Y') }} {{ config('constants.company') }}</p>
			</div>
		</div>

		@include('scripts')
		<script src="{{ mix('js/manifest.js') }}"></script>
		<script src="{{ mix('js/vendor.js') }}"></script>
		<script src="{{ mix('js/admin/main.js') }}"></script>
		@yield('scripts')
	</body>
</html>