@extends('site.layout')

@section('body_class', 'page-detail')

@section('title')
	{{ $venue->name }}
	&ndash;
	{{ $venue->categories->count() ? "{$venue_category_string} a" : '' }}
	{{ $venue->address_city }}
@endsection

@section('content')
<pg-venue-detail-page inline-template>
	<div>
		@include('site.components.navbar')

		{{-- Header --}}
		<div class="header">
			<div class="container">
				{{-- Gallery --}}
				<div class="header-gallery">
					<div class="header-gallery-bg">
						<div class="header-photo"></div>
						<div class="header-photo"></div>
						<div class="header-photo"></div>
						<div class="header-photo"></div>
						<div class="header-photo"></div>
						<div class="header-photo"></div>
					</div>
					<a href="{{ route('site.promote') }}" class="header-photo header-photo-add">
						<pg-icon icon="plus"></pg-icon>
						<span class="header-photo-add-text">Aggiungi foto</span>
					</a>
				</div>

				{{-- Title --}}
				<h2 class="header-title">{{ $venue->name }}</h2>
				<ul class="list-inline header-subtitle">
					<li class="list-inline-item">
						{{ $venue->categories->count() ? "{$venue_category_string} a" : '' }}
						{{ $venue->address_city }}
					</li>
					{{-- <li class="list-inline-item header-subtitle2">
						<strong>Probabilmente aperto</strong>
					</li> --}}
				</ul>
			</div>
		</div>

		<div class="container">

			<div class="row">
				<div class="col-lg-8">
					{{-- Contact card for small screens --}}
					@include('site.venues.contact-card', [
						'venue' => $venue,
						'class' => 'd-lg-none'
					])
					
					{{-- Jackpots --}}
					<div class="row my-5 pt-2">
						<div class="col-md-4">
							<div class="jackpot mb-3 mb-md-0">
								<img class="jackpot-icon" src="{{ asset('img/detail/jackpot-1.svg') }}">
								<div>
									<div class="jackpot-name">Jackpot 1</div>
									<div class="jackpot-value">€ 0,00</div>
									<div><a href="{{ route('site.promote') }}">modifica</a></div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="jackpot mb-3 mb-md-0">
								<img class="jackpot-icon" src="{{ asset('img/detail/jackpot-2.svg') }}">
								<div>
									<div class="jackpot-name">Jackpot 2</div>
									<div class="jackpot-value">€ 0,00</div>
									<div><a href="{{ route('site.promote') }}">modifica</a></div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="jackpot">
								<img class="jackpot-icon" src="{{ asset('img/detail/jackpot-3.svg') }}">
								<div>
									<div class="jackpot-name">Jackpot 3</div>
									<div class="jackpot-value">€ 0,00</div>
									<div><a href="{{ route('site.promote') }}">modifica</a></div>
								</div>
							</div>
						</div>
					</div>

					<hr>

					{{-- Services --}}
					<div class="my-5">
						<h4>Servizi</h4>
						<div class="row">
							<div class="col-md">
								<ul class="list-unstyled mb-0 mb-md-3">
									<li class="service-list-item">
										Dimensioni:
										<strong>{{ $venue->surface_size }} mq.</strong>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									@if($venue->machine_count)
										<li class="service-list-item">
											Numero di macchine:
											<strong>{{ $venue->machine_count }}</strong>
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										</li>
									@elseif($venue->estimated_machine_count)
										<li class="service-list-item">
											Numero di macchine (stimato):
											<strong>{{ $venue->estimated_machine_count }}</strong>
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										</li>
									@endif
									<li class="service-list-item">
										Numero di VLT:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Numero di AWP:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Piattaforme disponibili:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Posti auto:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Parcheggio privato:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
								</ul>
							</div>
							<div class="col-md">
								<ul class="list-unstyled">
									<li class="service-list-item">
										Bar:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Ristorante:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										POS:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Bancomat:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Pay Per View:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
									<li class="service-list-item">
										Wi-Fi:
										<span class="text-muted">sconosciuto</span>
										<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

					{{-- Promote --}}
					<div class="card bg-light my-4 text-center">
						<div class="card-body">
							<h4 class="card-title">È la tua attività?</h4>
							<p class="card-text">Se sei proprietaro o gestore di questa attività, puoi rivendicarla gratuitamente e tenerla aggiornata, aggiungere foto, jackpot e tanto altro. <a href="{{ route('site.promote') }}">Ulteriori informazioni&hellip;</a></p>
							<p class="card-text"><a class="btn btn-primary" href="mailto:{{ config('constants.email.venues') }}?subject={{ rawurlencode("Rivendicazione attività: {$venue->name} (identificativo: {$venue->id})") }}">Rivendica attività</a></p>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					
					{{-- Contact card for big screens --}}
					@include('site.venues.contact-card', [
						'venue' => $venue,
						'class' => 'd-none d-lg-block'
					])

					{{-- Nearby venues --}}
					@if ($nearby_venues && $nearby_venues->count())
						<div class="my-5">
							<h5 class="mb-3">Attività nei dintorni</h5>
							<ul class="list-unstyled">
								@foreach ($nearby_venues as $nearby_venue)
									@php
										$img = $nearby_venue->first_category_short_name ?: 'collapsed';
									@endphp
									<li class="d-flex align-items-start">
										<img class="mr-3" src="{{ asset("img/map/pin-normal-{$img}.svg")}}">
										<p>
											<strong><a href="{{ route('site.venues.detail', ['venue' => $nearby_venue]) }}">{{ $nearby_venue->name }}</a></strong><br>
											<span class="initialism text-muted">{{ $nearby_venue->categories()->first()->name }}</span><br>
											{{ $nearby_venue->short_address }}
										</p>
									</li>
								@endforeach
							</ul>
						</div>
					@endif

					{{-- Report --}}
					<div class="my-4">
						<h5>Hai trovato un errore?</h5>
						<p>Se l'indirizzo o i dati sono errati, l'attività non esiste più, o se ci sono foto offensive, puoi <a href="mailto:{{ config('constants.email.report') }}?subject={{ rawurlencode("Segnalazione errore: {$venue->name} (identificativo: {$venue->id})") }}">segnalare questa attività</a>.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</pg-venue-detail-page>
@endsection