@extends('site.layout')

@section('title', 'Homepage')
@section('body_class', 'page-home')

@section('content')

<nav class="navbar navbar-full navbar-fixed-top">
	<div class="container">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<div class="nav navbar-nav pull-xs-right">
			<div class="nav-item nav-link">
				<span aria-hidden="true">18+</span> Il gioco è vietato ai minori di 18 anni
			</div>
			<a class="nav-item btn btn-sm btn-secondary-outline" href="#">Accedi</a>
			<a class="nav-item btn btn-sm btn-primary" href="#">Iscriviti</a>
		</div>
	</div>
</nav>

<div class="content">
	<div class="content-inner">
		<div class="map"></div>
		<div class="map-overlay"></div>

		<div class="container">
			<div class="row">
				<div class="col-sm-offset-1 col-sm-10">
					<div class="card">
						<div class="card-block hero-card-block">
							<div class="row">
								<div class="col-xs-offset-1 col-xs-10 col-md-7">
									<h2>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e vinci!</h2>
									<p class="lead text-muted m-b-0">Più di 5000 sale tra cui scegliere!</p>
								</div>
								<div class="col-md-4">
									<div class="logo"></div>
								</div>
							</div>
						</div>
						<form class="card-block search-card-block" action="{{ route('site.explore') }}" method="get">
							<input type="hidden" name="lat">
							<input type="hidden" name="lng">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-4">
									<div class="form-group">
										<label class="initialism" for="form_query">Cosa</label><br>
										<input type="text" class="form-control form-control-lg" name="query" value="{{ $query }}" placeholder="Sto cercando&hellip;" id="form_query">
										<small>Es. VLT, Bingo, Ricevitoria.</small>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="initialism" for="form_near">Dove</label><br>
										<input type="text" class="form-control form-control-lg" name="near" value="{{ $near }}" placeholder="Citt&agrave;" id="form_near">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">&nbsp;</label>
										<button type="submit" class="btn btn-lg btn-block btn-primary btn-initialism">
											<span class="icon icon-search"></span>
											Cerca
										</button>
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