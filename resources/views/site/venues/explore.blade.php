@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $what ? "Risultati per {$what} a {$near}" : "Tutti i risultati a {$near}")

@section('scripts')
<script src="{{ asset('js/app/explore.js') }}"></script>
@endsection

@section('content')

@include('site.venues._navbarVue', ['fluid' => 'true'])

<div class="wrapper">
	<div class="venue-list">
		<div class="container-fluid">
			<h5>
				<template v-if="what">
					@{{ venues.length }} @{{ venues.length | singularOrPlural('risultato', 'risultati') }}
					per &ldquo;<strong>@{{ what }}</strong>&rdquo;
				</template>
				<template v-else>
					Tutti i risultati
				</template>
				vicino a <strong>@{{ near }}</strong>
			</h5>
			<hr>

			<template v-if="!venues.data.length">
				Nessun risultato
			</template>
			<template v-else>
				<div v-for="venue in venues.data" class="venue">
					<img class="venue-icon" :src="'/img/avatars/' + venue.category_icon_name">

					<div class="venue-body">
						<h5 class="mb-1">
							<strong><a :href="'/venues/' + venue.id">@{{ venue.name }}</a></strong>
						</h5>
						<p v-if="venue.categories.length" class="small text-uppercase text-muted">@{{ venue.categories[0].name }}</p>
						<p class="mb-0">
							<strong v-if="venue.distance">@{{ venue.distance | formatDistance }}</strong>
							@{{ venue.short_address }}
						</p>
						<ul class="list-inline mb-0 ">
							<li class="list-inline-item"><a class="text-muted" href="javascript:void(0)" @click="select(venue)">Mostra sulla mappa</a></li>
							<li class="list-inline-item text-muted">&middot;</li>
							<li class="list-inline-item"><a class="text-muted" href="javascript:void(0)" @click="toggleFavorite(venue)">Aggiungi ai preferiti</a></li>
						</ul>
						<hr class="mb-0">
					</div>
				</div>

				@if ($venues->hasMorePages())
					<div class="text-center">
						<button class="btn btn-outline-primary" @click="loadMore">Carica altri risultati&hellip;</button>
					</div>
				@endif
			</template>
		</div>
	</div>
	<gmap-map class="map" :center="center" :zoom="15" :options="mapOptions">
		<gmap-marker v-for="venue in venues.data" :position="{ lat: venue.geo_latitude, lng: venue.geo_longitude }" @click="select(venue)">
			<gmap-info-window :opened="venue == currentVenue" class="pippo">
				<div style="background-color: #f9f9f9">
					<h4>@{{venue.name}}</h4>
					<p v-if="venue.categories.length" class="small text-uppercase text-muted my-0">@{{ venue.categories[0].name }}</p>
					<img src="http://placehold.it/200x140"><br>
					<button class="btn btn-sm btn-secondary" @click="toggleFavorite(venue)">Aggiungi ai preferiti</button>
				</div>
			</gmap-info-window>
		</gmap-marker>
	</gmap-map>
</div>

@endsection