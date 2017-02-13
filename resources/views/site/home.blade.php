@extends('site.layout')

@section('title', 'Homepage')
@section('body_class', 'page-home')

@section('scripts')
<script src="{{ mix('js/app/home.js') }}"></script>
@endsection

@section('content')

<div class="hero">
	<gmap-map class="map" :center="mapCenter" :zoom="mapZoom" :options="mapOptions"></gmap-map>
	<nav class="navbar navbar-transparent">
		<div class="container d-flex justify-content-between align-items-center">
			<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('constants.name') }}">
				@include('site.vectors.logo', ['style' => 'dark', 'class' => 'navbar-logo'])
			</a>
			<div>
				<span class="navbar-age-warning">
					<span class="badge navbar-age-warning-badge" aria-hidden="true">18+</span>
					<span class="navbar-age-warning-text">Il gioco &egrave; vietato<br>ai minori di 18 anni</span>
				</span>
				@if (Auth::guest())
					<a class="btn btn-inverse-neutral" href="{{ url('/login') }}">Accedi</a>
					<a class="btn btn-primary" href="{{ url('/register') }}">Iscriviti</a>
				@else
					<span class="dropdown">
						<button class="btn btn-secondary dropdown-toggle" type="button" id="navbar-user-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-user-button">
							<a class="dropdown-item" href="{{ route('site.user') }}">
								<strong>
									{{ Auth::user()->name }}
									@if (Auth::user()->isAdmin())
										(admin)
									@endif
								</strong><br>
								<span class="text-muted">Visualizza il tuo profilo</span>
							</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-user-logout-form').submit();">Esci</a>
							<form id="navbar-user-logout-form" action="{{ url('/logout') }}" method="POST" hidden>
								{{ csrf_field() }}
							</form>
						</div>
					</span>
				@endif
			</div>
		</div>
	</nav>

	<div class="container hero-content">
		<div class="text-center">
			@include('site.vectors.logo', ['text' => false, 'class' => 'logo', 'style' => 'dark'])
			<div class="row">
				<div class="col-xs-12 offset-lg-2 col-lg-8">
					<h1>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h1>
					<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
				</div>
			</div>
		</div>
		<pg-search-form action="{{ route('site.venues.explore') }}" :lat="lat" :lng="lng" :what="what" :near="near" @locate="onLocationUpdate"></pg-search-form>
	</div>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
</div>
@endsection