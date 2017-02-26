@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $what ? "Risultati per {$what} a {$near}" : "Tutti i risultati vicino {$near}")

@section('scripts')
<script src="{{ mix('js/app/explore.js') }}"></script>
@endsection

@section('content')

@include('site.venues._navbar', ['fluid' => 'true'])

<div class="navbar align-items-start">
	<div class="form-group">
		<label>Tipo</label>
		<select class="form-control" name="category">
			<option value="">Tutti</option>
			@foreach($categories as $category)
				<option value="{{ $category->id }}">{{ $category->name }}</option>
			@endforeach
		</select>
	</div>
</div>

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
				vicino <strong>@{{ near }}</strong>
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
							<li class="list-inline-item">
								<a class="text-muted" href="javascript:void(0)" @click="toggleFavorite(venue)">
								@include('site.icons.icon', ['name' => 'heart-outline'])
								Aggiungi ai preferiti
								</a>
							</li>
						</ul>
						<hr class="mb-0">
					</div>
				</div>

				<div v-if="venues.next_page_url" class="venue">
					<div class="venue-body">
						<button class="btn btn-outline-primary" @click="loadMore">Carica altri risultati&hellip;</button>
					</div>
				</div>
			</template>
		</div>
	</div>
	<gmap-map class="map" :center="mapCenter" :zoom="mapZoom" :options="mapOptions">
		<gmap-marker v-for="venue in venues.data" :position="{ lat: venue.geo_latitude, lng: venue.geo_longitude }" @click="select(venue)">
			<gmap-info-window :opened="venue == currentVenue" v-cloak>
				<div class="venue-infowindow">
					<img class="venue-infowindow-icon" :src="'/img/avatars/' + venue.category_icon_name">

					<h5 class="mt-2 mb-1"><strong><a :href="'/venues/' + venue.id">@{{ venue.name }}</a></strong></h5>
					<p v-if="venue.categories.length" class="my-0 small text-uppercase text-muted">@{{ venue.categories[0].name }}</p>

					<p class="my-2">@{{ venue.short_address }}</p>

					<a class="btn btn-sm btn-outline-primary" :href="'/venues/' + venue.id">Dettagli</a>
				</div>
			</gmap-info-window>
		</gmap-marker>
	</gmap-map>
</div>

@endsection