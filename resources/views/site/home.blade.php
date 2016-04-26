@extends('site.layout')

@section('title', 'Homepage')
@section('body_class', 'page-home')

@section('content')

<nav class="navbar navbar-full navbar-fixed-top navbar-white">
	<div class="container p-x-0">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<div class="pull-xs-right">
			<span class="navbar-badge">
				<span class="label navbar-badge-label" aria-hidden="true">18+</span>
				<span class="navbar-badge-text">Il gioco &egrave; vietato<br>ai minori di 18 anni</span>
			</span>
			<span class="hidden-sm-down">
				<a class="btn btn-sm btn-secondary-outline" href="#">Accedi</a>
				<a class="btn btn-sm btn-primary" href="#">Iscriviti</a>
			</span>
		</div>
	</div>
</nav>

<div class="content">
	<div class="content-inner">
		<div class="map"></div>

		<div class="container">
			<div class="row">
				<div class="col-lg-offset-1 col-lg-10">
					<div class="card">
						<div class="card-block hero-card-block">
							<div class="row">
								<div class="col-xs-offset-1 col-xs-10 col-md-7">
									<h2>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h2>
									<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
									<div class="m-t-1 hidden-md-up">
										<a class="btn btn-primary" href="#">Iscriviti</a>
										<a class="btn btn-secondary-outline" href="#">Accedi</a>
									</div>
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
								<div class="col-xs-offset-1 col-xs-10">

									<div class="row">
										<div class="col-md-4 col-xl-5">
											<div class="form-group">
												<label class="initialism" for="form_query"><strong>Cosa</strong></label><br>
												<input type="text" class="form-control form-control-lg" name="query" value="{{ $query }}" placeholder="Sto cercando&hellip;" id="form_query">
												<div>Es. VLT, Bingo, Ricevitoria.</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label class="initialism" for="form_near"><strong>Dove</strong></label><br>
												<input type="text" class="form-control form-control-lg" name="near" value="{{ $near }}" placeholder="Citt&agrave;" id="form_near">
											</div>
										</div>
										<div class="col-md-3 col-xl-2">
											<div class="form-group">
												<label class="hidden-sm-down">&nbsp;</label>
												<button type="submit" class="btn btn-lg btn-block search-btn">
													<span class="icon icon-search"></span>
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