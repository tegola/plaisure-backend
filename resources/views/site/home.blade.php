@extends('site.layout')

@section('title', 'Homepage')
@section('body_class', 'page-home')

@section('content')
<div class="map"></div>

<nav class="navbar navbar-light bg-faded">
	<div class="container">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<div class="pull-xs-right">
			<span aria-hidden="true">18+</span> Il gioco è vietato ai minori di 18 anni
			<a class="btn btn-secondary-outline" href="#">Accedi</a>
			<a class="btn btn-primary" href="#">Iscriviti</a>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
		<div class="col-sm-offset-1 col-sm-10">
			<div class="box">
				<div class="row">
					<div class="col-xs-offset-1 col-xs-10 col-md-7">
						<h2>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e vinci!</h2>
						<p class="lead text-muted">Più di 5000 sale tra cui scegliere!</p>
					</div>
				</div>
				<div class="box-bottom">
					<form action="{{ route('site.explore') }}" method="get">
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
@endsection