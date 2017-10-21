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
			<div class="venue-list px-0 col col-md-6 col-lg-5 col-xl-4">
				{{-- Mobile title --}}
				<div v-if="venues.length" class="container-fluid d-md-none mt-4">
					<h4>
						@{{ venues.length }}<template v-if="hasMorePages">+</template>
						@{{ venues.length | singularOrPlural('risultato', 'risultati') }}
						vicino a @{{ searchParams.near }}
					</h4>
				</div>

				{{-- Desktop filters --}}
				<div class="list-group-item venue-list-filters">
					<div>
						@foreach($categories as $category)
							<label class="filter-tag">
								<input type="checkbox" class="filter-tag-input" name="categories[]" value="{{ $category->id }}" v-model="categories">
								<span class="filter-tag-token">{{ $category->name }}</span>
							</label>
						@endforeach
					</div>
					<div v-if="venues.length > 0" class="ml-2 text-muted text-nowrap" v-cloak>
						@{{ venues.length }}<template v-if="hasMorePages">+</template>
						@{{ venues.length | singularOrPlural('risultato', 'risultati') }}
					</div>
				</div>

				{{-- Limited results --}}
				<p v-if="hasMorePages" class="alert alert-info border-0 rounded-0">
					Il numero di risultati è stato limitato automaticamente. <span v-if="$mq.comfortable">Ingrandisci la zona di tuo interesse per visualizzare più dettagli.</span>
				</p>

				{{-- Venue list --}}
				<template v-if="venues.length">
					<div v-for="venue in venues" class="list-group-item venue-list-item" :class="{ 'active': selectedVenueId == venue.id }" @mouseover="highlight(venue)" @mouseout="highlight()" @click="select(venue)">
						<div class="d-flex w-100 align-items-start">
							<img class="venue-list-item-icon" :src="'/img/avatars/' + venue.first_category_machine_name + '.svg'">
							<div class="w-100">
								<div class="d-flex w-100 justify-content-between">
									<h5 class="mb-1 font-weight-bold">
										<a class="text-primary" :href="'/venues/' + venue.id">@{{ venue.name }}</a>
									</h5>
									<div class="text-muted ml-3 text-nowrap" v-if="venue.distance">@{{ venue.distance | formatDistance }}</div>
								</div>
								<p v-if="venue.categories.length" class="small text-uppercase text-muted mb-1">@{{ venue.categories[0].name }}</p>
								<p class="mb-0">@{{ venue.short_address }}</p>
							</div>
						</div>
					</div>
				</template>

				{{-- Empty list --}}
				<div v-else class="list-group-item venue-list-empty-item" v-cloak>
					<h4>Nessun esercizio trovato</h4>
					<p class="text-muted">
						Prova a cercare in un'altra zona
						<template v-if="categories.length">
							<br>
							oppure <a href="javascript:void(0)" @click="resetCategories">mostra tutti i tipi di esercizi</a>
						</template>
					</p>
				</div>
			</div>
			<div class="map-container" v-if="$mq.comfortable">
				<pg-map class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
					<pg-map-marker v-for="(venue, index) in venues" :key="venue.id" :position="mapMarkerPosition(venue)" :icon="mapMarkerIcon(venue, index)" @click="select(venue)">
						<pg-map-info-window v-cloak :opened="venue.id == selectedVenueId" @closeclick="select(null)">
							<div class="map-infowindow">
								<img class="map-infowindow-icon" :src="'/img/avatars/' + venue.first_category_machine_name + '.svg'">
								<div>
									<h5 class="mb-0 font-weight-bold">
										<a :href="'/venues/' + venue.id">@{{ venue.name }}</a>
									</h5>
									<p v-if="venue.categories.length" class="mt-1 mb-0 small text-uppercase text-muted">@{{ venue.categories[0].name }}</p>
									<p class="mt-1 mb-0">@{{ venue.short_address }}</p>
								</div>
							</div>
						</pg-map-info-window>
					</pg-map-marker>
				</pg-map>
				<button
					ref="refreshBtn"
					v-show="mapNeedsRefresh"
					class="btn map-btn map-refresh-btn"
					title="Cerca in questa zona"
					aria-label="Cerca in questa zona"
					data-toggle="tooltip"
					data-placement="right"
					v-cloak
					@click="load">
					@include ('site.icons.icon', ['name' => 'refresh'])
				</button>
				{{--
				<button class="btn btn-sm map-btn map-location-btn" @click="load" title="Usa la tua posizione" aria-label="Usa la tua posizione" data-toggle="tooltip" data-placement="right">
					@include ('site.icons.icon', ['name' => 'location-outline'])
				</button>
				--}}
			</div>
		</div>
	</div>
</pg-explore-page>
@endsection