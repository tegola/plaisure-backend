@extends('site.layout')

@section('body_class', 'page-detail')
@section('title', "{$venue->name} - Sala VLT, ricevitoria")

@section('content')

@include('site.venues._navbar')

<div style="background-color: #eee; white-space: nowrap; overflow: auto; margin-bottom: 20px;">
	@for ($i = 0; $i < 8; $i++)
		<img src="http://placehold.it/240" alt="">
	@endfor
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-8 col-md-offset-1 col-md-7">
			<h1>{{ $venue->name }}</h1>
			@if ($venue->categories->count())
				<p class="lead">
					@foreach ($venue->categories as $category)
						{{ $category->name }}
					@endforeach
				</p>
			@endif
			<p>{{ $venue->long_address }} &ndash; <strong>a soli {{ round($venue->distance) }} km da te!</strong></p>
			<ul class="list-inline">
				<li class="list-inline-item">{{ $venue->surface_size }} mq.</li>
				<li class="list-inline-item">Informazione 2</li>
				<li class="list-inline-item">Informazione 3</li>
			</ul>

			<hr>

			<div class="row">
				<div class="col-xs-6">
					<button class="btn btn-sm btn-secondary">Salva nei posti preferiti</button>
				</div>
				<div class="col-sm-offset-1 col-xs-5">
					<input type="text" class="form-control form-control-sm" value="{{ Request::url() }}">
					<button class="btn btn-sm btn-secondary">Condividi</button>
				</div>
			</div>
		</div>
		<div class="col-sm-4 col-md-3">
			<div class="card card-block">
				Qui va la mappa
			</div>

			@if ($nearby_venues->count())
				<h5>Altre attivit&agrave; vicino a questa</h5>
				@foreach ($nearby_venues as $nearby_venue)
					<a href="{{ route('site.detail', ['venue' => $nearby_venue]) }}">{{ $nearby_venue->name }}</a><br>
				@endforeach
			@endif

			<hr>

			<h5>È la tua attivit&agrave;? <a href="#">Rivendicala subito.</a></h5>
			<p>Migliora gratuitamente le informazioni in questa pagina e trova nuovi clienti.</p>
		</div>
	</div>
</div>
@endsection