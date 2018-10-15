<script>
import _extend from 'lodash/extend';
import _debounce from 'lodash/debounce';

import { Map as PgMap, Marker as PgMapMarker, InfoWindow as PgMapInfoWindow } from 'vue2-google-maps';
import BTooltip from 'bootstrap-vue/es/components/tooltip/tooltip';

import PgButton from 'prontogioco/app/components/button';
import PgVenueListItem from './list-item';
import PgFilterButton from './filter-button';


import { MAP_DEFAULT_CENTER, SEARCH_RADIUSES } from 'prontogioco/constants';

export default {
	name: 'PgExplorePage',

	components: {
		PgMap,
		PgMapMarker,
		PgMapInfoWindow,
		BTooltip,
		PgButton,
		PgVenueListItem,
		PgFilterButton
	},

	data() {
		const queryParams = this.$route.query;
		let searchMode = 'center';

		// Prepare map center
		let mapCenter = _extend({}, MAP_DEFAULT_CENTER);
		if (['c_lat', 'c_lng'].every(key => key in queryParams)) {
			mapCenter = {
				lat: parseFloat(queryParams.c_lat),
				lng: parseFloat(queryParams.c_lng)
			};
		}

		// Prepare map bounds and change search mode
		let mapBounds = null;
		if (['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'].every(key => key in queryParams)) {
			mapBounds = {
				north: parseFloat(queryParams.ne_lat),
				east: parseFloat(queryParams.ne_lng),
				south: parseFloat(queryParams.sw_lat),
				west: parseFloat(queryParams.sw_lng)
			};
			searchMode = 'bounds';
		}

		const mapZoom = parseInt(queryParams.zoom) || 13;

		// Prepare default search params
		const searchParams = _extend({
			radius: SEARCH_RADIUSES[0],
			categories: []
		}, queryParams);

		// Cleanup search params
		searchParams.categories = searchParams.categories
			.map(category => parseInt(category))
			.filter((category, index, arr) => arr.indexOf(category) === index);

		searchParams.radius = parseInt(searchParams.radius);

		// Prepare filter options
		const radiusOptions = SEARCH_RADIUSES.map(radius => ({
			value: radius,
			label: `${radius} km`
		}));

		return {
			categories: [],

			radiusOptions,
			categoryOptions: [],
			// amenityOptions: [],

			loading: false,
			locating: false,
			userLocation: null,

			searchMode, // bounds, center
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
			mapZoom,
			mapOptions: {
				gestureHandling: 'greedy',
				fullscreenControl: false,
				mapTypeControl: false,
				streetViewControl: false,
				zoomControl: this.$mq.comfortable,
				zoomControlOptions: {
					position: 1 // google.maps.ControlPosition.TOP_LEFT
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

	meta() {
		return {
			title: this.query || this.$t('pages.explore.meta_title')
		};
	},

	mounted() {
		// Load initial data then search
		this.loadData().then(this.search);
	},

	methods: {
		loadData() {
			this.loading = true;

			return this.$axios.get('/venues/explore/data').then(response => {
				// Fill data
				this.categories = response.data.categories;
				this.categoryOptions = response.data.categories.map(category => ({
					value: category.id,
					label: this.$t(`db.categories.${category.machine_name}`)
				}));
				/*
				this.amenityOptions = response.data.amenities.map(amenity => ({
					value: amenity.machine_name,
					label: this.$t(`db.amenities.${amenity.machine_name}`)
				}));
				*/

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
			this.placeholder = ['(', this.$t('pages.explore.placeholder.location '), ')'].join('');

			// Move map center, but disable map bounds tracking first
			this.mapBoundsEventEnabled = false;
			this.mapBounds = null;
			this.mapZoom = 13;
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
			alert(this.$t('pages.home.location_error'));
		},

		// Filters ------------------------------------------------------------
		onRadiusChange(value) {
			this.searchParams.radius = value;
			this.search();
		},

		onCategoryChange(value) {
			this.searchParams.categories = value.length ? value : this.categories.map(category => category.id);
			this.search();
		},

		/*
		onAmenityChange(value) {
			this.searchParams.amenities = value.length ? value : this.amenities.map(amenity => amenity.id);
			this.search();
		},
		*/

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
			this.placeholder = ['(', this.$t('pages.explore.placeholder.in_map '), ')'].join('');

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
	}
};
</script>

<template>
	<div class="pg-explore-page">
		<pg-navbar
			:placeholder="placeholder"
			:query="query"
			:auto-submit="false"
			variant="dark"
			@place-changed="onPlaceChanged">
			<template slot="right">
				<pg-button
					v-if="$root.hasGeolocation"
					:loading="locating"
					:icon="userLocation ? 'location' : 'location-outline'"
					:title="$t('pages.explore.location')"
					:aria-label="$t('pages.explore.location')"
					variant="naked"
					class="navbar__location-btn"
					@click="findUserLocation"
				/>
			</template>
		</pg-navbar>

		<!-- Filters -->
		<div class="navbar filter-navbar">
			<div class="d-flex">
				<pg-button
					v-if="$mq.constrained"
					:title="showMap ? $t('pages.explore.view.list') : $t('pages.explore.view.map')"
					variant="link"
					class="filter-button filter-button--toggle"
					@click="currentView = currentView == 'map' ? 'list' : 'map'">
					<pg-icon :icon="showMap ? 'list' : 'map'" />
				</pg-button>
				<pg-filter-button
					v-if="showRadiusFilter"
					:value="searchParams.radius"
					:options="radiusOptions"
					:label="$t('pages.explore.filters.radius_label')"
					@change="onRadiusChange"
				/>
				<pg-filter-button
					:value="searchParams.categories"
					:options="categoryOptions"
					:label="$t('pages.explore.filters.category_label')"
					multiple
					@change="onCategoryChange"
				/>
				<!--
				<pg-filter-button
					:value="searchParams.amenities"
					:options="amenityOptions"
					:label="$t('pages.explore.filters.amenity_label')"
					multiple
					@change="onAmenityChange"
				/>
				-->
			</div>
			<div v-if="venues.length" class="text-muted px-3 align-self-center text-nowrap small">
				{{
					$tc('pages.explore.results', venues.length, {
						count: hasMorePages ? `${venues.length}+` : venues.length
					})
				}}
			</div>
		</div>

		<div class="wrapper">
			<div v-if="showList" class="venue-list px-0 col col-md-7 col-lg-6 col-xl-5">
				<!-- Loader -->
				<div v-if="loading" v-cloak class="list-group-item venue-list-placeholder-item text-muted">
					<pg-icon icon="circle-outline-notch" spinning />
					<h4 class="mb-0">{{ $t('common.status.loading') }}&hellip;</h4>
				</div>
				<template v-else v-cloak>
					<!-- Empty list -->
					<div v-if="!venues.length" class="list-group-item venue-list-placeholder-item text-muted">
						<pg-icon icon="search" class="pg-icon--3x" />
						<h4 class="mt-3">{{ $t('pages.explore.no_items.title') }}</h4>
						<p>{{ $t('pages.explore.no_items.subtitle') }}</p>
					</div>

					<!-- Venue list -->
					<pg-venue-list-item
						v-for="venue in venues"
						:venue="venue"
						:highlighted="highlightedVenueId == venue.id"
						:selected="selectedVenueId == venue.id"
						:key="venue.id"
						@mouseover="highlight(venue)"
						@mouseout="highlight()"
						@click="select(venue)"
					/>

					<!-- Limited results -->
					<div v-if="hasMorePages" class="list-group-item text-muted text-center border-0 mt-0 mb-5">
						<div class="h1">&hellip;</div>
						<p>{{ $t('pages.explore.limited_results') }}</p>
					</div>
				</template>
			</div>

			<pg-map
				v-if="showMap"
				ref="map"
				:center="mapCenter"
				:zoom="mapZoom"
				:bounds="mapBounds"
				:options="mapOptions"
				class="map"
				@bounds_changed="onMapBoundsChange">
				<pg-map-marker v-if="userLocation" :position="userLocation" icon="/img/map/pin-user.svg" title="La tua posizione" />
				<pg-map-marker v-for="(venue, index) in venues" :key="venue.id" :position="venue.coords" :icon="mapMarkerIcon(venue, index)" @click="select(venue)">
					<pg-map-info-window v-cloak :opened="venue.id == selectedVenueId" @closeclick="select(null)">
						<div class="map-infowindow">
							<img :src="`/img/avatars/${venueFirstCategoryMachineName(venue)}.svg`" class="map-infowindow-icon">
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
						<pg-button
							id="desktop-refresh-btn"
							:aria-label="$t('pages.explore.search_area')"
							variant="accent"
							class="map-refresh-btn"
							icon="refresh"
							@click="onSearchBoundsClick"
						/>
						<b-tooltip
							target="desktop-refresh-btn"
							placement="right"
							triggers=""
							show>
							{{ $t('pages.explore.search_area') }}
						</b-tooltip>
					</template>
					<div v-if="$mq.constrained && mapNeedsRefresh" v-cloak class="container-fluid map-floating-controls">
						<pg-button variant="accent" block @click="onSearchBoundsClick">{{ $t('pages.explore.search_area') }}</pg-button>
					</div>
				</template>
			</pg-map>
		</div>
	</div>
</template>