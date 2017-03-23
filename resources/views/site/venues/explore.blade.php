@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $near)

@section('scripts')
<script src="{{ mix('js/app/explore.js') }}"></script>
@endsection

@section('content')

@include('site.venues._navbar', [
	'fluid' => 'true',
	'class' => 'navbar-dark navbar-slim',
	'vue_support' => true
])

<div class="filterbar d-flex align-items-center justify-content-between">
	<div class="form-inline d-flex align-items-center">
		<div class="form-group">
			<label class="mr-2">Tipo</label>
			<select class="form-control" name="category" :value="searchParams.category" @input="onCategoryChange">
				<option value="">Tutti</option>
				@foreach($categories as $category)
					<option value="{{ $category->id }}">{{ $category->name }}</option>
				@endforeach
			</select>
		</div>
		<div v-if="venues.length > 0" class="ml-2 text-muted" v-cloak>
			@{{ venues.length }}<template v-if="hasMorePages">+</template>
			@{{ venues.length | singularOrPlural('risultato', 'risultati') }}
		</div>
	</div>

	<div>
		<label class="custom-control custom-checkbox mb-0">
			<input type="checkbox" class="custom-control-input" v-model="followMap">
			<span class="custom-control-indicator"></span>
			<span class="custom-control-description text-muted">Cerca mentre sposto la mappa</span>
		</label>
		<button v-if="mapNeedsRefresh" class="btn btn-sm btn-primary" @click="load">Cerca in questa zona</button>
	</div>
</div>

<div class="wrapper">
	<div class="venue-list">
		<div class="container-fluid">
			<div v-if="hasMorePages" class="alert alert-info" v-cloak>
				Il numero di risultati &egrave; stato limitato automaticamente. Fai zoom sulla zona interessata per visualizzare pi&ugrave; dettagli.
			</div>
			<template v-if="!venues.length">
				Nessun risultato
			</template>
			<template v-else>
				<div v-for="venue in venues" class="venue" @mouseover="highlight(venue)" @mouseout="highlight()">
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
			</template>
		</div>
	</div>
	<gmap-map class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
		<gmap-marker v-for="venue in venues" :key="venue.id" :position="{ lat: venue.geo_latitude, lng: venue.geo_longitude }" :label="venue.id == highlightedVenueId ? '*' : null" @click="select(venue)">
			<gmap-info-window v-cloak :opened="venue.id == selectedVenueId">
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