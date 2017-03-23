@extends('site.layout')

@section('body_class', 'page-detail')

@section('title')
	{{ $venue->name }}
	&ndash;
	{{ $venue->categories->count() ? "{$venue_category_string} a" : '' }}
	{{ $venue->address_city }}
@endsection

@section('scripts')
<script src="{{ mix('js/app/detail.js') }}"></script>
@endsection


@section('content')

@include('site.venues._navbar')

{{-- Header --}}
<div class="container mt-4 mb-3">
	<div class="row text-center text-md-left align-items-center">
		<div class="col-md col-md-auto">
			<img class="header-icon mb-2 md-md-0" src="{{ asset("img/avatars/{$venue->category_icon_name}") }}">
		</div>
		<div class="col-md pl-md-0">
			<h1 class="font-weight-bold">{{ $venue->name }}</h1>
			<p class="text-muted mb-4 mb-md-0">
				{{ $venue->categories->count() ? "{$venue_category_string} a" : '' }}
				{{ $venue->address_city }}
			</p>
		</div>
		<div class="col-md-5">
			<div class="row">
				<div class="col">
					<a class="btn btn-outline-neutral btn-block mb-2 mb-lg-0" href="{{ $venue->google_maps_url }}" target="_blank">Ottieni indicazioni</a>
				</div>
				<div class="col">
					<a class="btn btn-primary btn-block mb-2 mb-md-0" href="#">Salva</a>
				</div>
			</div>
		</div>
	</div>
</div>
<hr>

{{-- Content --}}
<div class="container">
	<div class="row">
		<div class="col-md-7">
			{{-- General info --}}
			<h4>Servizi</h4>
			<div class="row">
				<div class="col-md">
					<ul class="list-unstyled">
						<li>
							Dimensioni:
							<strong>{{ $venue->surface_size }} mq.</strong>
						</li>
						<li>
							Numero di macchine {{ $venue->hasFakeMachineNumber() ? '(stimato)' : '' }}:
							<strong>{{ $venue->estimated_machine_number }}</strong>
						</li>
						<li class="text-muted">
							Numero di VLT:
							Non disponibile
						</li>
						<li class="text-muted">
							Numero di AWP:
							Non disponibile
						</li>
						<li class="text-muted">
							Piattaforme disponibili:
							Non disponibile
						</li>
						<li class="text-muted">
							Posti auto:
							Non disponibile
						</li>
						<li class="text-muted">
							Parcheggio privato:
							Non disponibile
						</li>
					</ul>
				</div>
				<div class="col-md">
					<ul class="list-unstyled">
						<li class="text-muted">
							Bar:
							Non disponibile
						</li>
						<li class="text-muted">
							Ristorante:
							Non disponibile
						</li>
						<li class="text-muted">
							POS:
							Non disponibile
						</li>
						<li class="text-muted">
							Bancomat:
							Non disponibile
						</li>
						<li class="text-muted">
							Pay Per View:
							Non disponibile
						</li>
						<li class="text-muted">
							Wi-Fi:
							Non disponibile
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			{{-- Map --}}
			<div class="embed-responsive embed-responsive-21by9 mb-4">
				<gmap-map class="embed-responsive-item" :center="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }" :zoom="15" :options="mapOptions">
					<gmap-marker :position="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }">
				</gmap-map>
			</div>

			{{-- Claim --}}
			<h5>&Egrave; la tua attivit&agrave;?</h5>
			<p>Se sei proprietaro o gestore di questa attivit&agrave;, puoi rivendicarla gratuitamente e tenerla aggiornata costantemente, aggiungere foto, jackpot e tanto altro.</p>
			<p><a class="btn ì btn-outline-accent btn-block" href="#">Rivendica attivit&agrave;</a>

			<hr>

			{{-- Error --}}
			<h5>Hai trovato un errore?</h5>
			<p class="mb-0">Se l'indirizzo &egrave; errato, l'attivit&agrave; non esiste, o se ci sono foto offensive, puoi <a href="#">segnalare questa attivit&agrave;</a>.
		</div>
	</div>
</div>

@if ($nearby_venues->count())
	<div class="bg-faded pt-4 pb-0 my-5">
		<div class="container">
			<h4 class="mb-3">Attivit&agrave; nei dintorni</h4>
			<div class="row">
				@foreach ($nearby_venues as $nearby_venue)
					<div class="col-md card-group mb-4">
						<div class="card">
							<div class="card-block">
								<h5 class="font-weight-bold"><a href="{{ route('site.venues.detail', ['venue' => $nearby_venue]) }}">{{ $nearby_venue->name }}</a></h5>
								<p class="card-text">{{ $nearby_venue->categories()->first()->name }}, {{ $nearby_venue->address_city }}</p>	
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif

@endsection