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
			<div class="venue-list py-2">
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
					Il numero di risultati è stato limitato automaticamente. Fai zoom sulla zona di tuo interesse per visualizzare più dettagli.
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
								<ul class="list-inline mb-0 d-none d-md-inline-block">
									<li class="list-inline-item mr-3">
										<a class="font-weight-bold" href="javascript:void(0)" @click="select(venue)">Mostra sulla mappa</a>
									</li>
								</ul>
								<hr class="mb-0">
							</div>
						</div>
					</template>
				</div>
			</div>
			<div class="map-container">
				<pg-map class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
					<pg-map-marker v-for="venue in venues" :key="venue.id" :position="mapMarkerPosition(venue)" :icon="mapMarkerIcon(venue)" @click="select(venue)">
						<pg-map-info-window v-cloak :opened="venue.id == selectedVenueId" @closeclick="select(null)">
							<div class="map-infowindow">
								<img class="map-infowindow-icon" :src="'/img/avatars/' + venue.category_icon_name">
								<div>
									<h5 class="mb-0 font-weight-bold">
										<a :href="'/venues/' + venue.id">@{{ venue.name }}</a>
									</h5>
									<p v-if="venue.categories.length" class="my-0 small text-uppercase text-muted">@{{ venue.categories[0].name }}</p>
									<p class="mb-0">@{{ venue.short_address }}</p>
								</div>
							</div>
						</pg-map-info-window>
					</pg-map-marker>
				</pg-map>
				<button v-show="mapNeedsRefresh" class="btn map-btn map-refresh-btn" @click="load" title="Cerca in questa zona" aria-label="Cerca in questa zona" data-toggle="tooltip" data-placement="right">
					<pg-icon icon="refresh"></pg-icon>
				</button>
				{{--
				<button class="btn btn-sm map-btn map-location-btn" @click="load" title="Usa la tua posizione" aria-label="Usa la tua posizione" data-toggle="tooltip" data-placement="right">
					<pg-icon icon="location-outline"></pg-icon>
				</button>
				--}}
			</div>
		</div>
	</div>
</pg-explore-page>
@endsection