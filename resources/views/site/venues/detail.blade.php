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

					{{-- Description --}}
					@if ($venue->description)
						<hr>

						<div class="my-5">
							<h4>Descrizione attività</h4>
							<p>{{ $venue->description }}</p>
						</div>
					@endif

					<hr>

					{{-- Details --}}
					<div class="my-5">
						<h4>Dettagli</h4>
						<div class="row">
							<div class="col-md">
								<ul class="list-unstyled mb-0 mb-md-3">
									@if ($venue->isInCategory('betting_agency'))
										<li class="detail-list-item">
											Concessionario:
											@if ($venue->concessionaire)
												<strong>{{ $venue->concessionaire->name }}</strong>
											@else
												<span class="text-muted">sconosciuto</span>
											@endif
											@if (!$venue->isManaged())
												<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
											@endif
										</li>
										<li class="detail-list-item">
											Scommesse sportive:
											@if ($venue->sports_betting)
												<strong class="text-success">Sì</strong>
											@else
												<span class="text-muted">No</span>
											@endif
											@if (!$venue->isManaged())
												<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
											@endif
										</li>
										<li class="detail-list-item">
											Scommesse virtuali:
											@if ($venue->virtual_betting)
												<strong class="text-success">Sì</strong>
											@else
												<span class="text-muted">No</span>
											@endif
											@if (!$venue->isManaged())
												<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
											@endif
										</li>
										<li class="detail-list-item">
											Scommesse ippiche:
											@if ($venue->horse_betting)
												<strong class="text-success">Sì</strong>
											@else
												<span class="text-muted">No</span>
											@endif
											@if (!$venue->isManaged())
												<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
											@endif
										</li>
									@endif
									<li class="detail-list-item">
										Roulette arcade:
										@if ($venue->arcade_roulette)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Casinò online:
										@if ($venue->url_online_casino)
											<a href="{{ $venue->url_online_casino}}" target="_blank">{{ $venue->url_online_casino}}</a>
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
								</ul>
							</div>
							<div class="col-md">
								<ul class="list-unstyled">
									<li class="detail-list-item">
										Dimensioni:
										@if ($venue->isManaged())
											<strong>{{ $venue->surface_size }} mq.</strong> {{-- Avoid showing it for unmanaged venues --}}
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Numero di VLT:
										@if ($venue->vlt_machine_count)
											<strong>{{ $venue->vlt_machine_count}}</strong>
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Numero di AWP:
										@if ($venue->awp_machine_count)
											<strong>{{ $venue->awp_machine_count}}</strong>
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Piattaforme VLT disponibili:
										@if ($venue->vltPlatforms->count())
											{{ $venue->vltPlatforms->pluck('name')->implode(', ') }}
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									@if ($venue->isInCategory('betting_agency'))
										<li class="detail-list-item">
											Posti a sedere:
											@if ($venue->seating_capacity)
												<strong>{{ $venue->seating_capacity}}</strong>
											@else
												<span class="text-muted">sconosciuto</span>
											@endif
											@if (!$venue->isManaged())
												<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
											@endif
										</li>
									@endif
									<li class="detail-list-item">
										Posti auto:
										@if ($venue->parking_capacity)
											<strong>{{ $venue->parking_capacity}}</strong>
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Pay per view disponibili:
										@if ($venue->payPerViewPlatforms->count())
											{{ $venue->payPerViewPlatforms->pluck('name')->implode(', ') }}
										@else
											<span class="text-muted">sconosciuto</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
								</ul>
							</div>
						</div>
					</div>

					<hr>

					{{-- Amenities --}}
					<div class="my-5">
						<h4>Servizi</h4>
						<div class="row">
							<div class="col-md">
								<ul class="list-unstyled mb-0 mb-md-3">
									<li class="detail-list-item">
										Totem Bancomat:
										@if ($venue->amenity_atm)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Bar:
										@if ($venue->amenity_bar)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Pay per view:
										@if ($venue->amenity_pay_per_view)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										POS:
										@if ($venue->amenity_pos)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Parcheggio privato:
										@if ($venue->amenity_private_parking)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
								</ul>
							</div>
							<div class="col-md">
								<ul class="list-unstyled">
									<li class="detail-list-item">
										Ristorante:
										@if ($venue->amenity_restaurant)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Servizio di sicurezza:
										@if ($venue->amenity_security)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Area fumatori:
										@if ($venue->amenity_smoking_area)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
									</li>
									<li class="detail-list-item">
										Wi-Fi:
										@if ($venue->amenity_wifi)
											<strong class="text-success">Sì</strong>
										@else
											<span class="text-muted">No</span>
										@endif
										@if (!$venue->isManaged())
											<a href="{{ route('site.promote') }}" class="ml-2">modifica</a>
										@endif
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
										$img = $nearby_venue->first_category_machine_name ?: 'collapsed';
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