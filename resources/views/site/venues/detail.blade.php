@extends('site.layout')

@section('body_class', 'page-detail')
@section('title', "{$venue->name} - Sala VLT, ricevitoria")

@section('content')

@include('site.venues._navbar')

<div class="container m-t-2">
	<div class="row">
		<div class="col-md-9">

			<div class="card">
				<div class="row">
					<div class="col-lg-7 col-xl-8">
						<div class="card-block">
							<h1>{{ $venue->name }}</h1>
							@if ($venue->categories->count())
								<p class="lead text-muted">
									@foreach ($venue->categories as $category)
										{{ $category->name }}
									@endforeach
								</p>
							@endif
							<p>
								{{ $venue->long_address }}
								@if ($venue->distance)
									&ndash; <strong>a soli {{ round($venue->distance) }} km da te!</strong>
								@endif
							</p>
							<ul class="list-inline">
								<li class="list-inline-item">
									{{ $venue->surface_size }} mq.
								</li>
								<li class="list-inline-item">
									{{ $venue->estimated_machine_number }} macchine
									@if ($venue->hasFakeMachineNumber())
										<span class="label label-default" data-toggle="tooltip" title="Il numero di macchine è stimato in base alle dimensioni dei locali">Numero stimato</span>
									@endif
								</li>
							</ul>
							<div class="row">
								<div class="col-xs-5">
									<button class="btn btn-sm btn-secondary">Salva</button>
								</div>
								<div class="col-xs-7">
									<span class="input-group">
										<input type="text" class="form-control form-control-sm" value="{{ Request::url() }}">
										<span class="input-group-btn">
											<button class="btn btn-sm btn-secondary">Condividi</button>
										</span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-xl-4">
						<div class="map" data-lat="{{ $venue->geo_latitude }}" data-lng="{{ $venue->geo_longitude }}"></div>
					</div>
				</div>
				<hr class="m-y-0">
				<div class="card-block" style="white-space: nowrap; overflow: auto;">
					@for ($i = 0; $i < 8; $i++)
						<img src="http://placehold.it/200" alt="">
					@endfor
				</div>
				<hr class="m-y-0">
				<div class="card-block">
					<div class="row">
						<div class="col-xs-6">
							Numero di VLT<br>
							Numero di AWP<br>
							Piattaforme disponibili<br>
							Posti parcheggio<br>
							Parcheggio privato<br>
							Bar<br>
							Ristorante<br>
							POS<br>
							Bancomat<br>
							Pay per view<br>
							Wi-Fi
						</div>
						<div class="col-xs-6">
							Orari di apertura
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-3">

			<div class="row">
				<div class="col-sm-6 col-md-12">
					<h5>&Egrave; la tua attivit&agrave;?</h5>
					<p>Migliora gratuitamente le informazioni in questa pagina e trova nuovi clienti.</p>
					<p><a class="btn btn-sm btn-secondary" href="#">Rivendica attivit&agrave;</a>
				</div>
				@if ($nearby_venues->count())
					<div class="col-sm-6 col-md-12">
						<hr class="hidden-sm-down m-b-2">
						<h5>Attivit&agrave; vicine</h5>
						<ul class="list-unstyled">
							@foreach ($nearby_venues as $nearby_venue)
								<li>
									<strong><a href="{{ route('site.detail', ['venue' => $nearby_venue]) }}">{{ $nearby_venue->name }}</a></strong><br>
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