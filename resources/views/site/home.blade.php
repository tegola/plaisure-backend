@extends('site.layout')

@section('title', 'Homepage')
@section('body_class', 'page-home')

@section('content')

<nav class="navbar navbar-full navbar-fixed-top navbar-white">
	<div class="container">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<div class="float-xs-right">
			<span class="navbar-badge">
				<span class="tag navbar-badge-tag" aria-hidden="true">18+</span>
				<span class="navbar-badge-text">Il gioco &egrave; vietato<br>ai minori di 18 anni</span>
			</span>
			@if (Auth::guest())
				<a class="btn btn-outline-secondary" href="{{ url('/login') }}">Accedi</a>
				<a class="btn btn-primary" href="{{ url('/register') }}">Registrati</a>
			@else
				<span class="dropdown open">
					<button class="btn btn-secondary dropdown-toggle" type="button" id="navbar-user-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-user-button">
						<a class="dropdown-item" href="{{ route('site.user') }}">
							<strong>{{ Auth::user()->name }}</strong><br>
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

<div class="content">
	<div class="content-inner">
		<div class="map"></div>

		<div class="container">
			<div class="row">
				<div class="offset-lg-1 col-lg-10">
					<div class="card">
						<div class="card-block hero-card-block">
							<div class="row">
								<div class="offset-xs-1 col-xs-10 col-md-7">
									<h2>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h2>
									<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
								</div>
								<div class="col-md-4">
									<div class="logo"></div>
								</div>
							</div>
						</div>
						<form class="card-block search-card-block form-search" action="{{ route('site.venues.explore') }}" method="get">
							<input type="hidden" name="lat">
							<input type="hidden" name="lng">
							<div class="row">
								<div class="offset-xs-1 col-xs-10">

									<div class="row">
										<div class="col-md-4">
											<div class="form-group dropdown">
												<label class="initialism"><strong>Trova</strong></label><br>
												<input type="text" class="form-control form-control-lg" name="what" placeholder="VLT, Bingo, Ricevitoria" autocomplete="off">
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group dropdown">
												<label class="initialism"><strong>Vicino a</strong></label><br>
												<div style="position: relative;">
													<input type="text" class="form-control form-control-lg search-near-control" name="near" value="{{ $near }}" placeholder="Citt&agrave;" autocomplete="off">
													<button type="button" class="btn btn-lg btn-link search-locate-btn" data-toggle="tooltip" title="Usa posizione esatta" aria-label="Usa posizione esatta" data-action="locate">@include('site.icons.icon', ['name' => 'location'])</button>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="initialism hidden-sm-down">&nbsp;</label>
												<button type="submit" class="btn btn-lg btn-block search-btn">
													@include('site.icons.icon', ['name' => 'search'])
													Cerca
												</button>
											</div>
										</div>
									</div>

								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
@endsection