<script>
import { stringify } from 'qs';
import _extend from 'lodash/extend';
import _debounce from 'lodash/debounce';
import singularOrPlural from 'prontogioco/utilities/singular-or-plural';

import { Map as PgMap, Marker as PgMapMarker, InfoWindow as PgMapInfoWindow } from 'vue2-google-maps';
import BTooltip from 'bootstrap-vue/es/components/tooltip/tooltip';

import PgButton from 'prontogioco/app/components/button';
import PgPane from 'prontogioco/app/components/pane';
import PgFilterButton from './filter-button';
import PgFilterButtonItem from './filter-button-item';
import PgVenueListItem from './list-item';

import headful from 'prontogioco/app/plugins/headful';
import { DEFAULT_COORDS, SEARCH_RADIUSES } from 'prontogioco/constants';

export default {
	name: 'PgExplorePage',

	components: {
		PgMap,
		PgMapMarker,
		PgMapInfoWindow,
		BTooltip,
		PgButton,
		PgPane,
		PgFilterButton,
		PgFilterButtonItem,
		PgVenueListItem
	},

	filters: {
		singularOrPlural
	},

	data() {
		const queryParams = this.$route.query;

		// Prepare map center
		let mapCenter = _extend({}, DEFAULT_COORDS)
		if (['c_lat', 'c_lng'].every(key => key in queryParams)) {
			mapCenter = {
				lat: parseFloat(queryParams.c_lat),
				lng: parseFloat(queryParams.c_lng)
			};
		}

		// Prepare map bounds
		let mapBounds = null;
		if (['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'].every(key => key in queryParams)) {
			mapBounds = {
				north: parseFloat(queryParams.ne_lat),
				east: parseFloat(queryParams.ne_lng),
				south: parseFloat(queryParams.sw_lat),
				west: parseFloat(queryParams.sw_lng)
			}
		}

		// Prepare default search params
		const searchParams = _extend({
			radius: SEARCH_RADIUSES[0],
			categories: []
		}, queryParams);

		return {
			radiuses: SEARCH_RADIUSES,
			categories: [],
			// amenities: [],

			loading: false,
			locating: false,
			userLocation: null,

			searchMode: 'center', // bounds, center
			searchParams,
			placeholder: undefined,
			query: queryParams.query,
			venues: [],
			currentView: 'list',
			highlightedVenueId: null,
			selectedVenueId: null,

			mapNeedsRefresh: false,
			mapBoundsEventEnabled: false,
			mapCenter,
			mapBounds,
			mapOptions: {
				gestureHandling: 'greedy',
				fullscreenControl: false,
				mapTypeControl: false,
				streetViewControl: false,
				zoomControl: this.$mq.comfortable,
				zoomControlOptions: {
					position: 1 //google.maps.ControlPosition.TOP_LEFT
				},
				styles: [
					{ // Hide points of interest
						'featureType': 'poi',
						'stylers': [{ 'visibility': 'off' }]
					}
				]
			}
		};
	},

	computed: {
		hasMorePages() {
			return this.venues ? this.venues.length >= 100 : false;
		},
		showRadiusFilter() {
			return this.searchMode == 'center';
		},
		showList() {
			return this.$mq.comfortable || this.currentView == 'list';
		},
		showMap() {
			return this.$mq.comfortable || this.currentView == 'map';
		}
	},

	methods: {
		loadData() {
			this.loading = true;

			return this.$axios.get('/venues/explore/data').then(response => {
				// Fill data
				this.categories = response.data.categories;
				// this.amenities = response.data.amenities;

				// Fill categories in search params
				if (!this.searchParams.categories.length) {
					this.searchParams.categories = this.categories.map(category => category.id);
				}

				// Stop loading
				this.loading = false;
			});
		},

		// Location search ----------------------------------------------------
		onPlaceChanged(place) {
			if (!place) return;

			const bounds = place.geometry && place.geometry.viewport ? place.geometry.viewport : null;
			const center = bounds && bounds.getCenter() ? bounds.getCenter() : null;

			// Change search mode
			this.searchMode = 'center';

			// Update view
			this.mapNeedsRefresh = false;
			this.placeholder = undefined;

			if (place.vicinity && place.name != place.vicinity) {
				this.query = `${place.name}, ${place.vicinity}`;
			} else {
				this.query = place.name;
			}

			// Move map, but disable map bounds tracking first
			if (bounds && this.$refs.map) {
				this.mapBoundsEventEnabled = false;
				this.$refs.map.fitBounds(bounds);
			}

			// Update search params
			_extend(this.searchParams, {
				query: this.query,
				c_lat: center ? center.lat() : null,
				c_lng: center ? center.lng() : null,
				ne_lat: null,
				ne_lng: null,
				sw_lat: null,
				sw_lng: null
			});

			// Load venues
			this.search();
		},

		// User location ------------------------------------------------------
		findUserLocation() {
			this.locating = true;

			navigator.geolocation.getCurrentPosition(this.onUserLocationFound, this.onUserLocationNotFound, {
				timeout: 10 * 1000, // 10 secs
				maximumAge: 5 * 60 * 1000 // last 5 minutes
			});
		},

		onUserLocationFound(position) {
			const { latitude, longitude } = position.coords;

			// Change search mode
			this.searchMode = 'center';

			// Update view
			this.mapNeedsRefresh = false;
			this.locating = false;
			this.userLocation = {
				lat: latitude,
				lng: longitude
			};
			this.query = '';
			this.placeholder = '(Vicino a te)';

			// Move map center, but disable map bounds tracking first
			this.mapBoundsEventEnabled = false;
			this.mapBounds = null;
			this.mapCenter = {
				lat: latitude,
				lng: longitude
			};
			if (this.$refs.map) this.$refs.map.panTo(this.mapCenter);

			// Update search params
			_extend(this.searchParams, {
				query: '',
				c_lat: latitude,
				c_lng: longitude,
				ne_lat: null,
				ne_lng: null,
				sw_lat: null,
				sw_lng: null
			});

			// Load venues
			this.search();
		},

		onUserLocationNotFound() {
			this.locating = false;
			this.userLocation = null;
			alert('Non è stato possibile trovare la tua posizione.');
		},

		// Filters ------------------------------------------------------------
		radiusFilterText() {
			return `${this.searchParams.radius} km`;
		},

		categoryFilterText() {
			let ids = this.searchParams.categories;

			// All categories
			if (ids.length == this.categories.length) return 'Tutti';

			// One category
			if (ids.length == 1) {
				return this.categories.find(item => {
					return item.id == ids[0];
				}).name;
			} 

			// More than one categories
			if (ids.length > 1) {
				return `${ids.length} selezionati`;
			}
		},

		/*
		amenitiesFilterText() {
			const machineNames = this.searchParams.amenities;

			if (!machineNames.length) return null;

			if (machineNames.length == 1) {
				return this.amenities.find(item => {
					return item.machine_name == machineNames[0];
				}).name;
			} else {
				return [
					machineNames.length,
					this.$options.filters.singularOrPlural(machineNames.length, 'selezionato', 'selezionati')
				].join(' ');
			}
		},
		*/

		isFilterItemSelected(type, key) {
			switch (type) {
				case 'radius': return this.searchParams.radius && this.searchParams.radius == key;
				case 'category': return this.searchParams.categories && this.searchParams.categories.indexOf(key) !== -1;
				// case 'amenity': return this.searchParams.amenities.indexOf(key) !== -1;
			}
		},

		onRadiusSelect(radius) {
			this.searchParams.radius = radius;
			this.$forceUpdate();
		},

		onCategorySelect(category) {
			const ids = this.searchParams.categories;
			const index = ids.indexOf(category.id);
			const allIds = this.categories.map(category => category.id);

			if (index !== -1) {
				ids.splice(index, 1);
				if (!ids.length) ids.push(...allIds); // Never empty
			} else {
				ids.push(category.id);
			}
		},

		/*
		onAmenitySelect(amenity) {
			const names = this.searchParams.amenities;
			const index = names.indexOf(amenity.machine_name);

			if (index !== -1) {
				names.splice(index, 1);
			} else {
				names.push(amenity.machine_name);
			}
		},
		*/

		onFilterClose() {
			// TODO: Determine whether a reload is needed
			this.search();
		},

		// Map ----------------------------------------------------------------
		onMapBoundsChange: _debounce(function(bounds) { // Fat arrow functions do not work with debounce
			// Store bounds
			this.mapBounds = bounds;

			// Stop if map bounds event is not enabled
			if (!this.mapBoundsEventEnabled) {
				this.mapBoundsEventEnabled = true;
				return;
			}

			// Mark for map refresh
			this.mapNeedsRefresh = true;
		}, 200),

		venueFirstCategoryMachineName(venue) {
			if (!venue.categories || !venue.categories.length) return null;

			return venue.categories[0].machine_name;
		},

		mapMarkerIcon(venue, index) {
			const variant = venue.id == this.selectedVenueId || venue.id == this.highlightedVenueId ? 'inverse' : 'normal';
			const firstCategoryMachineName = this.venueFirstCategoryMachineName(venue);
			const glyph = index < 25 && firstCategoryMachineName ? firstCategoryMachineName : 'collapsed';

			return `/img/map/pin-${variant}-${glyph}.svg`;
		},

		onSearchBoundsClick() {
			// Change search mode
			this.searchMode = 'bounds';

			// Update view
			this.mapNeedsRefresh = false;
			this.query = null;
			this.placeholder = "(All'interno della mappa)";

			// Update search params
			const c = this.mapBounds.getCenter();
			const ne = this.mapBounds.getNorthEast();
			const sw = this.mapBounds.getSouthWest();

			_extend(this.searchParams, {
				query: '',
				c_lat: c.lat(),
				c_lng: c.lng(),
				ne_lat: ne.lat(),
				ne_lng: ne.lng(),
				sw_lat: sw.lat(),
				sw_lng: sw.lng()
			});

			this.search();
		},

		// Search -------------------------------------------------------------
		search() {
			// Load venues
			this.loading = true;

			this.$axios.post('/venues/explore/search', this.searchParams)
				.then(response => {
					this.venues = response.data;
					this.loading = false;
				});

			// Update URL
			this.$router.replace({
				query: this.searchParams
			});

			// Update title
			headful({
				title: this.query
			});
		},

		// List support -------------------------------------------------------
		highlight(venue) {
			// Disabled when map is not visible
			if (!this.showMap) return;

			this.highlightedVenueId = venue ? venue.id : null;
		},

		select(venue) {
			// Disabled when map is not visible
			if (!this.showMap) return;

			// Always hide if no venue is passed
			if (!venue) {
				this.selectedVenueId = null;
				return;
			}

			// Select/deselect
			this.selectedVenueId = this.selectedVenueId != venue.id ? venue.id : null;
		}
	},

	mounted() {
		// Load initial data then search
		this.loadData().then(this.search);
	}
};
</script>

<template>
	<div class="pg-explore-page">
		<pg-navbar
			fluid
			slim
			variant="dark"
			:placeholder="placeholder"
			:query="query"
			:auto-submit="false"
			@place-changed="onPlaceChanged">
			<template slot="right">
				<pg-button
					variant="naked"
					class="navbar__location-btn"
					title="Usa la tua posizione"
					aria-label="Usa la tua posizione"
					:loading="locating"
					:icon="userLocation ? 'location' : 'location-outline'"
					v-if="$root.hasGeolocation"
					@click="findUserLocation"
				/>
			</template>
		</pg-navbar>

		<!-- Filters -->
		<div class="filters">
			<div class="d-flex">
				<a v-if="$mq.constrained" class="filter-button filters__toggle-button" href="#" :title="showMap ? 'Mostra lista' : 'Mostra mappa'" @click="currentView = currentView == 'map' ? 'list' : 'map'">
					<pg-icon :icon="showMap ? 'list' : 'map'" />
				</a>
				<pg-filter-button label="Distanza" :text="radiusFilterText()" @close="onFilterClose" v-if="showRadiusFilter">
					<pg-pane class="filter-button-pane">
						<pg-filter-button-item
							v-for="radius in radiuses"
							icon="bullet"
							:key="radius"
							:checked="isFilterItemSelected('radius', radius)"
							@click="onRadiusSelect(radius)">
							{{ radius }} km
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
							{{ category.name }}
						</pg-filter-button-item>
					</pg-pane>
				</pg-filter-button>
				<!--
				<pg-filter-button label="Servizi disponibili" :text="amenitiesFilterText()" placeholder="Scegli..."  @close="onFilterClose">
					<pg-pane class="filter-button-pane">
						<pg-filter-button-item
							v-for="amenity in amenities"
							:key="amenity.machine_name"
							:checked="isFilterItemSelected('amenity', amenity.machine_name)"
							@click="onAmenitySelect(amenity)">
							{{ amenity.name }}
						</pg-filter-button-item>
					</pg-pane>
				</pg-filter-button>
				-->
			</div>
			<div v-if="venues.length" class="text-muted px-3 align-self-center text-nowrap">
				{{ venues.length }}<template v-if="hasMorePages">+</template>
				{{ venues.length | singularOrPlural('risultato', 'risultati') }}
			</div>
		</div>

		<div class="wrapper">
			<div class="venue-list px-0 col col-md-7 col-lg-6 col-xl-5" v-if="showList">
				<!-- Loader -->
				<div v-if="loading" class="list-group-item venue-list-placeholder-item text-muted" v-cloak>
					<pg-icon icon="circle-outline-notch" spinning />
					<h4 class="mb-0">Caricamento&hellip;</h4>
				</div>
				<template v-else v-cloak>
					<!-- Empty list -->
					<div v-if="!venues.length" class="list-group-item venue-list-placeholder-item text-muted">
						<pg-icon icon="search" class="pg-icon--3x" />
						<h4 class="mt-3">Nessuna attività trovata</h4>
						<p>Cerca in un altra zona o modifica i criteri di rcerca.</p>
					</div>

					<!-- Venue list -->
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

					<!-- Limited results -->
					<div v-if="hasMorePages" class="list-group-item text-muted text-center border-0 mt-0 mb-5">
						<div class="h1">&hellip;</div>
						<p>Il numero di risultati è stato limitato automaticamente. Cerca una zona specifica per visualizzare più dettagli.</p>
					</div>
				</template>
			</div>

			<pg-map v-if="showMap" class="map" ref="map" :center="mapCenter" :zoom="13" :bounds="mapBounds" :options="mapOptions" @bounds_changed="onMapBoundsChange">
				<pg-map-marker v-if="userLocation" :position="userLocation" icon="/img/map/pin-user.svg" title="La tua posizione" />
				<pg-map-marker v-for="(venue, index) in venues" :key="venue.id" :position="venue.coords" :icon="mapMarkerIcon(venue, index)" @click="select(venue)">
					<pg-map-info-window v-cloak :opened="venue.id == selectedVenueId" @closeclick="select(null)">
						<div class="map-infowindow">
							<img class="map-infowindow-icon" :src="`/img/avatars/${venueFirstCategoryMachineName(venue)}.svg`">
							<div>
								<h5 class="mb-0 font-weight-bold">
									<router-link :to="`/venues/${venue.id}`">{{ venue.name }}</router-link>
								</h5>
								<p v-if="venue.categories && venue.categories.length" class="mt-1 mb-0 small text-uppercase text-muted">{{ venue.categories[0].name }}</p>
								<p class="mt-1 mb-0">{{ venue.address.short }}</p>
							</div>
						</div>
					</pg-map-info-window>
				</pg-map-marker>
				<template slot="visible">
					<!-- Refresh buttons -->
					<template v-if="$mq.comfortable && mapNeedsRefresh">
						<button
							id="desktop-refresh-btn"
							class="btn map-btn map-refresh-btn"
							aria-label="Cerca in questa zona"
							@click="onSearchBoundsClick">
							<pg-icon icon="refresh"></pg-icon>
						</button>
						<b-tooltip
							target="desktop-refresh-btn"
							placement="right"
							triggers=""
							show>
							Cerca in questa zona
						</b-tooltip>
					</template>
					<div class="container-fluid map-floating-controls" v-if="$mq.constrained && mapNeedsRefresh" v-cloak>
						<button class="btn btn-accent btn-block" @click="onSearchBoundsClick">Cerca qui</button>
					</div>
				</template>
			</pg-map>
		</div>
	</div>
</template>