<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>{{ config('constants.name') }} - @yield('title')</title>

		<link rel="stylesheet" href="{{ mix('css/app.css') }}">
		@yield('stylesheets')
	</head>
	<body class="page @yield('body_class')">
		<div class="page-content">
			@yield('content')
		</div>

		<div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-8 push-md-4 text-center text-md-right">
						<ul class="list-inline">
							{{-- FIXME: Il link "Esplora" deve passare la città, altrimenti si viene ridirezionati alla home --}}
							<li class="list-inline-item"><a href="{{ route('site.venues.explore') }}">Esplora</a></li>
							<li class="list-inline-item"><a href="{{ route('site.about.company') }}">Azienda</a></li>
							<li class="list-inline-item"><a href="{{ route('site.venues.claim') }}">Rivendica la tua attivit&agrave;</a></li>
							<li class="list-inline-item"><a href="#">Gioca responsabilmente</a></li>
							<li class="list-inline-item"><a href="{{ route('site.about.contact') }}">Contatti</a></li>
						</ul>
					</div>
					<div class="col-md-4 pull-md-8 text-center text-md-left">
						<ul class="list-inline">
							<li class="list-inline-item">&copy; {{ date('Y') }} {{ config('constants.company') }}</li>
							<li class="list-inline-item">P. IVA {{ config('constants.partita_iva')}}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		{{-- Icons --}}
		@include('site.icons.defs')

		{{-- Common Javascript view for values passed by Laravel --}}
		@include('scripts')

		{{-- FIXME: Pass region per site and language per user locale --}}
		<script src="{{ mix('js/app/main.js') }}"></script>
		@yield('scripts')

		<script src="https://use.typekit.net/qwv3xzz.js"></script>
		<script>try{Typekit.load({ async: true });}catch(e){}</script>
	</body>
</html>