@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $query)

@section('content')
<pg-explore-page inline-template>
	<div class="page-content">
		<pg-navbar
			variant="dark slim"
			:placeholder="placeholder"
			:query="query"
			:auto-submit="false"
			@place-changed="onPlaceChanged">
			<template slot="right">
				<button class="btn navbar__location-btn" :disabled="userLocation ? true : false" title="Usa la tua posizione" aria-label="Usa la tua posizione" @click="findUserLocation">
					<pg-icon :icon="locationButtonIcon" :spinning="locating"></pg-icon>
				</button>
			</template>
		</pg-navbar>

		{{-- Filters --}}
		<div class="filters">
			<div class="d-flex">
				<a v-if="$mq.constrained" class="filter-button filters__toggle-button" href="#" :title="showMap ? 'Mostra lista' : 'Mostra mappa'" @click="currentView = currentView == 'map' ? 'list' : 'map'">
					<pg-icon :icon="showMap ? 'list' : 'map'"></pg-icon>
				</a>
				<pg-filter-button label="Distanza" :text="radiusFilterText()" @close="onFilterClose" v-if="showRadiusFilter">
					<pg-pane class="filter-button-pane">
						<pg-filter-button-item
							v-for="radius in radiuses"
							icon="bullet"
							:key="radius"
							:checked="isFilterItemSelected('radius', radius)"
							@click="onRadiusSelect(radius)">
							@{{ radius }} km
						</pg-filter-button-item>
					</pg-pane>
				</pg-filter-button>
				<pg-filter-button label="Tipo" :text="categoryFilterText()" @close="onFilterClose">
					<pg-pane class="filter-button-pane">
						<pg-filter-button-item
							v-for="category in categories"
							:key="category.id"
							:checked="isFilterItemSelected('category', category.id)"
							@click="onCategorySelect(category)">
							@{{ category.name }}
						</pg-filter-button-item>
					</pg-pane>
				</pg-filter-button>
				{{--
				<pg-filter-button label="Servizi disponibili" :text="amenitiesFilterText()" placeholder="Scegli..."  @close="onFilterClose">
					<pg-pane class="filter-button-pane">
						<pg-filter-button-item
							v-for="amenity in amenities"
							:key="amenity.machine_name"
							:checked="isFilterItemSelected('amenity', amenity.machine_name)"
							@click="onAmenitySelect(amenity)">
							@{{ amenity.name }}
						</pg-filter-button-item>
					</pg-pane>
				</pg-filter-button>
				--}}
			</div>
			<div v-if="venues.length" class="text-muted px-3 align-self-center text-nowrap">
				@{{ venues.length }}<template v-if="hasMorePages">+</template>
				@{{ venues.length | singularOrPlural('risultato', 'risultati') }}
			</div>
		</div>

		<div class="wrapper">
			<div class="venue-list px-0 col col-md-8 col-lg-6 col-xl-5" v-if="showList">
				{{-- Loader --}}
				<div v-if="loading" class="list-group-item venue-list-placeholder-item text-muted" v-cloak>
					<pg-icon icon="circle-outline-notch" spinning></pg-icon>
					<h4 class="mb-0">Caricamento&hellip;</h4>
				</div>
				<template v-else v-cloak>
					{{-- Empty list --}}
					<div v-if="!venues.length" class="list-group-item venue-list-placeholder-item text-muted">
						<pg-icon icon="search" class="pg-icon--3x"></pg-icon>
						<h4 class="mt-3">Nessuna attività trovata</h4>
						<p>Cerca in un altra zona o modifica i criteri di rcerca.</p>
					</div>

					{{-- Venue list --}}
					<pg-venue-list-item
						v-if="venues.length"
						v-for="venue in venues"
						:venue="venue"
						:highlighted="highlightedVenueId == venue.id"
						:selected="selectedVenueId == venue.id"
						:key="venue.id"
						@mouseover="highlight(venue)"
						@mouseout="highlight()"
						@click="select(venue)">
					</pg-venue-list-item>

					{{-- Limited results --}}
					<div v-if="hasMorePages" class="list-group-item text-muted text-center border-0 mt-0 mb-5">
						<div class="h1">&hellip;</div>
						<p>Il numero di risultati è stato limitato automaticamente. Cerca una zona specifica per visualizzare più dettagli.</p>
					</div>
				</template>
			</div>

			<pg-map v-if="showMap" class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
				<pg-map-marker v-if="userLocation" :position="userLocation" icon="/img/map/pin-user.svg" title="La tua posizione"></pg-map-marker>
				<pg-map-marker v-for="(venue, index) in venues" :key="venue.id" :position="{ lat: venue.geo_latitude,
				lng: venue.geo_longitude }" :icon="mapMarkerIcon(venue, index)" @click="select(venue)">
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
				<template slot="visible">
					{{-- Refresh buttons --}}
					<button
						ref="refreshBtn"
						v-show="$mq.comfortable && mapNeedsRefresh"
						class="btn map-btn map-refresh-btn"
						title="Cerca in questa zona"
						aria-label="Cerca in questa zona"
						data-toggle="tooltip"
						data-placement="right"
						v-cloak
						@click="onSearchBoundsClick">
						<pg-icon icon="refresh"></pg-icon>
					</button>
					<div class="container-fluid map-floating-controls" v-if="$mq.constrained && mapNeedsRefresh" v-cloak>
						<button class="btn btn-accent btn-block" @click="onSearchBoundsClick">Cerca qui</button>
					</div>
				</template>
			</pg-map>
		</div>
	</div>
</pg-explore-page>
@endsection