@extends('site.layout')

@section('body_class', 'page-detail')
@section('title', "{$venue->name} - {$venue_category_string}")

@section('content')

@include('site.venues._navbar')

<div class="card mb-0">
	<div class="container mt-2 mb-1">
		<div class="row clearfix">
			<div class="col-md-8">
				<img class="result-icon" src="{{ asset("img/avatars/{$venue->category_icon_name}") }}">
				@if ($venue->categories->count())
					<p class="mb-0 initialism text-muted">
						{{ $venue_category_string }}
					</p>
				@endif
				<h1 class="mt-0">{{ $venue->name }}</h1>
				<p>
					{{ $venue->long_address }}
					@if ($venue->distance)
						&ndash; <strong>a soli {{ round($venue->distance) }} km da te!</strong>
					@endif
				</p>	
			</div>
			<div class="col-md-4 text-right">
				<p>
					<a class="btn btn-secondary" href="{{ $venue->google_maps_url }}" target="_blank">Ottieni indicazioni</a>
					<a class="btn btn-secondary" href="#">Salva</a>
				</p>
			</div>
		</div>
	</div>
</div>
<div class="card-block" style="white-space: nowrap; overflow: auto;">
	@for ($i = 0; $i < 8; $i++)
		<img src="http://placehold.it/160">
	@endfor
</div>

<div class="container">
	<div class="card">
		<div class="card-block row text-center">
			<div class="col-4">
				<strong>$$$</strong>
				<h4 class="font-weight-normal">&euro; 42.425,09</h4>
				<strong class="initialism text-muted">Mega Jackpot</strong>
			</div>
			<div class="col-4">
				<strong>$$</strong>
				<h4 class="font-weight-normal">&euro; 4.007,14</h4>
				<strong class="initialism text-muted">Super jackpot</strong>
			</div>
			<div class="col-4">
				<strong>$</strong>
				<h4 class="font-weight-normal">&euro; 5.348,23</h4>
				<strong class="initialism text-muted">Easy jackpot</strong>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-block">
			<div class="embed-responsive embed-responsive-21by9">
				<div class="map embed-responsive-item" data-lat="{{ $venue->geo_latitude }}" data-lng="{{ $venue->geo_longitude }}"></div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-block">
					<div class="row">
						<div class="col-md-4">
							<strong>Dimensioni</strong>
						</div>
						<div class="col-md-8">
							{{ $venue->surface_size }} mq.
						</div>
						<div class="col-md-4">
							<strong>Numero di macchine</strong>
						</div>
						<div class="col-md-8">
							{{ $venue->estimated_machine_number }} macchine
							@if ($venue->hasFakeMachineNumber())
								<span class="label label-default" data-toggle="tooltip" title="Il numero di macchine è stimato in base alle dimensioni dei locali">Numero stimato</span>
							@endif
						</div>
						<div class="col-md-4">
							<strong>Numero di VLT</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Numero di AWP</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Piattaforme disponibili</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Posti parcheggio</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Parcheggio privato</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Bar</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Ristorante</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>POS</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Bancomat</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Pay per view</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
						<div class="col-md-4">
							<strong>Wi-Fi</strong>
						</div>
						<div class="col-md-8">
							<span class="text-muted">Non disponibile</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card">
				<div class="card-block">
					<h5>&Egrave; la tua attivit&agrave;?</h5>
					<p>Se sei proprietaro o gestore di questa attività, puoi rivendicarla gratuitamente e tenerla aggiornata costantemente, aggiungere foto, jackpot e tanto altro.</p>
					<p class="mb-0"><a class="btn btn-sm btn-secondary" href="#">Rivendica attivit&agrave;</a>
				</div>
				<hr class="my-0">

				<div class="card-block">
					<h5>Hai trovato un errore?</h5>
					<p class="mb-0">Se l'indirizzo è errato, l'attività non esiste, o se ci sono foto offensive, puoi <a href="#">segnalare questa attività</a>.
				</div>

				@if ($nearby_venues->count())
					<hr class="my-0">
					<div class="card-block">
						<h5>Attivit&agrave; vicine</h5>
						<ul class="list-unstyled mb-0">
							@foreach ($nearby_venues as $nearby_venue)
								<li>
									<strong><a href="{{ route('site.venues.detail', ['venue' => $nearby_venue]) }}">{{ $nearby_venue->name }}</a></strong><br>
									<span class="text-muted">{{ $nearby_venue->address_city }}</span>
								</li>
							@endforeach
						</ul>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection