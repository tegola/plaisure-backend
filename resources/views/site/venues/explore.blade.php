@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $near)

@section('content')
<pg-explore-page inline-template>
	<div class="page-content">
		@include('site.components.navbar', [
			'fluid' => 'true',
			'class' => 'navbar-dark navbar-slim',
			'vue_support' => true
		])

		<div class="wrapper">
			<div class="venue-list">
				<div class="container-fluid">
					@foreach($categories as $category)
						<label class="filter-tag">
							<input type="checkbox" class="filter-tag-input" name="categories[]" value="{{ $category->id }}" v-model="categories">
							<span class="filter-tag-token">{{ $category->name }}</span>
						</label>
					@endforeach
					<span v-if="venues.length > 0" class="ml-2 text-muted" v-cloak>
						@{{ venues.length }}<template v-if="hasMorePages">+</template>
						@{{ venues.length | singularOrPlural('risultato', 'risultati') }}
					</span>
				</div>

				<div v-if="hasMorePages" class="alert alert-info border-0 rounded-0">
					Il numero di risultati &egrave; stato limitato automaticamente. Fai zoom sulla zona interessata per visualizzare pi&ugrave; dettagli.
				</div>

				<div class="container-fluid">
					<div v-if="!venues.length" class="text-center" v-cloak>
						<h4>Nessun esercizio trovato</h4>
						<p class="text-muted">
							Prova a cercare in un'altra zona
							<template v-if="categories.length">
								<br>
								oppure <a href="javascript:void(0)" @click="resetCategories">mostra tutti i tipi di esercizi</a>
							</template>
						</p>
					</div>
					<template v-else>
						<div v-for="venue in venues" class="venue" @mouseover="highlight(venue)" @mouseout="highlight()">
							<img class="venue-icon" :src="'/img/avatars/' + venue.category_icon_name">

							<div class="venue-body">
								<h5 class="mb-1 font-weight-bold">
									<a class="text-inherit" :href="'/venues/' + venue.id">@{{ venue.name }}</a>
								</h5>
								<p v-if="venue.categories.length" class="small text-uppercase text-muted mb-1">@{{ venue.categories[0].name }}</p>
								<p>
									@{{ venue.short_address }}
									<template v-if="venue.distance"> - @{{ venue.distance | formatDistance }}</template>
								</p>
								<ul class="list-inline mb-0">
									<li class="list-inline-item mr-3">
										<a class="font-weight-bold" href="javascript:void(0)" @click="select(venue)">Mostra sulla mappa</a>
									</li>
									{{-- <li class="list-inline-item">
										<a class="text-accent font-weight-bold" href="javascript:void(0)" @click="toggleFavorite(venue)">
											@include('site.icons.icon', ['name' => 'heart-outline', 'class' => 'mr-2'])Aggiungi ai preferiti
										</a>
									</li> --}}
								</ul>
								<hr class="mb-0">
							</div>
						</div>
					</template>
				</div>
			</div>
			<div class="map-container">
				<pg-map class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
					<pg-map-marker v-for="venue in venues" :key="venue.id" :position="{ lat: venue.geo_latitude, lng: venue.geo_longitude }" :label="venue.id == highlightedVenueId ? '*' : null" @click="select(venue)">
						<pg-map-info-window v-cloak :opened="venue.id == selectedVenueId">
							<div class="venue-infowindow">
								<img class="venue-infowindow-icon" :src="'/img/avatars/' + venue.category_icon_name">

								<h5 class="mt-2 mb-1 font-weight-bold">
									<a class="text-inherit" :href="'/venues/' + venue.id">@{{ venue.name }}</a>
								</h5>
								<p v-if="venue.categories.length" class="my-0 small text-uppercase text-muted">@{{ venue.categories[0].name }}</p>

								<p class="my-2">@{{ venue.short_address }}</p>

								<a class="btn btn-sm btn-outline-primary" :href="'/venues/' + venue.id">Dettagli</a>
							</div>
						</pg-map-info-window>
					</pg-map-marker>
				</pg-map>
				<div class="map-controls">
					<button v-show="mapNeedsRefresh" class="btn btn-primary map-refresh-button" @click="load" data-toggle="tooltip" title="Cerca in questa zona" aria-label="Cerca in questa zona">
						<pg-icon icon="refresh"></pg-icon>
					</button>
					<label class="custom-control custom-checkbox map-follow-checkbox">
						<input type="checkbox" class="custom-control-input" v-model="followMap">
						<span class="custom-control-indicator"></span>
						<span class="custom-control-description text-muted">Aggiorna automaticamente</span>
					</label>
				</div>
			</div>
		</div>
	</div>
</pg-explore-page>
@endsection