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

		<div class="footer">
			<div class="container text-center">
				<div class="row">
					<div class="col-md-8 order-md-2 text-center text-md-right">
						<ul class="list-inline mb-md-0">
							<li class="list-inline-item"><a href="{{ route('site.venues.explore') }}">Esplora</a></li>
							<li class="list-inline-item"><a href="{{ route('site.about.company') }}">Azienda</a></li>
							{{-- <li class="list-inline-item"><a href="{{ route('site.venues.claim') }}">Rivendica la tua attivit&agrave;</a></li> --}}
							<li class="list-inline-item"><a href="{{ route('site.play-responsibly.index') }}">Gioca responsabilmente</a></li>
							<li class="list-inline-item"><a href="{{ route('site.about.contact') }}">Contatti</a></li>
						</ul>
					</div>
					<div class="col-md-4 order-md-1 text-center">
						<div class="d-flex align-items-center justify-content-center justify-content-md-start">
							@include('site.vectors.logo', ['text' => false, 'class' => 'footer-logo mr-3'])
							<ul class="list-inline mb-0">
								<li class="list-inline-item">&copy; {{ date('Y') }} {{ config('constants.company') }}</li>
								<li class="list-inline-item">P. IVA {{ config('constants.partita_iva')}}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		@include('site.icons.defs')

		@include('scripts')
		@yield('scripts')
		<script src="{{ mix('js/app.js') }}"></script>
	</body>
</html>