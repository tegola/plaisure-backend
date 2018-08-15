<script>
import _extend from 'lodash/extend';
import * as geocoder from 'prontogioco/utilities/geocoder';
import PgButton from 'prontogioco/app/components/button';
import PgPlaceTextbox from 'prontogioco/app/components/place-textbox';
import { Map as PgMap } from 'vue2-google-maps';
import { DEFAULT_COORDS } from 'prontogioco/constants';

const mapOptions = {
	disableDefaultUI: true,
	scrollwheel: false,
	draggable: false,
	disableDoubleClickZoom: true,
	styles: [
		{ // Remove color
			'stylers': [{ 'saturation': -100 }, { 'gamma': 0.5 }]
		},
		{ // Remove labels
			'elementType': 'labels',
			'stylers': [{ 'visibility': 'off' }]
		},
		{ // Less visible highways
			'featureType': 'road.highway',
			'stylers': [{ 'lightness': 50 }]
		},
		{ // Thinner roads
			'featureType': 'road',
			'elementType': 'geometry.stroke',
			'stylers': [{ 'weight': 0.3 }]
		}
	]
};

export default {
	name: 'PgHomePage',

	components: {
		PgButton,
		PgPlaceTextbox,
		PgMap
	},
	data() {
		return {
			query: null,
			placeholder: this.$t('pages.home.city_placeholder'),
			placeTextboxOptions: {
				types: ['geocode'] // Limit search to cities, addresses, etc.
			},
			locating: false,
			userLocationFound: false,
			searchParams: {
				query: null,
				c_lat: null,
				c_lng: null
			}
		};
	},

	computed: {
		hasGeolocation() {
			return this.$root.hasGeolocation;
		},

		mapProps() {
			let center = DEFAULT_COORDS;
			let zoom = 5;
			const options = mapOptions;

			if (this.searchParams.c_lat && this.searchParams.c_lng) {
				center = {
					lat: this.searchParams.c_lat,
					lng: this.searchParams.c_lng
				};
				zoom = 15;
			}

			return {
				center,
				zoom,
				options
			};
		},

		canSubmit() {
			return (this.searchParams.c_lat && this.searchParams.c_lng);
		}
	},

	mounted() {
		// If no location is set, find a generic one using IP info
		geocoder.geocodeByIp((error, location) => {
			if (!location || !location.latitude || !location.longitude || !location.city) return;

			this.query = location.city;

			_extend(this.searchParams, {
				query: location.city,
				c_lat: location.latitude,
				c_lng: location.longitude
			});
		});
	},

	methods: {
		findUserLocation() {
			this.locating = true;

			navigator.geolocation.getCurrentPosition(this.onUserLocationFound, this.onUserLocationNotFound, {
				timeout: 10 * 1000, // 10 secs
				maximumAge: 5 * 60 * 1000 // last 5 minutes
			});
		},

		onUserLocationFound(position) {
			const { latitude, longitude } = position.coords;

			this.locating = false;
			this.userLocationFound = true;

			// Update search params
			_extend(this.searchParams, {
				query: null,
				c_lat: latitude,
				c_lng: longitude
			});

			// Update view
			this.query = null;
			this.placeholder = ['(', this.$t('pages.home.location_placeholder'), ')'].join('');

			// Find city name
			geocoder.reverse(latitude, longitude, (error, location) => {
				if (error) return;

				let address = [];

				if (location.streetName) address.push(location.streetName);
				address.push(location.administrativeLevels.level3long);
				address = address.join(', ');

				this.query = address;
				this.searchParams.query = address;
			});
		},

		onUserLocationNotFound() {
			this.locating = false;
			this.userLocationFound = false;
			alert(this.$t('pages.home.location_error'));
		},

		onPlaceChanged(place) {
			// Reset user location indicator
			this.userLocationFound = false;
			this.placeholder = this.$t('pages.home.city_placeholder');

			// Reset search
			if (!place) {
				this.query = null;
				_extend(this.searchParams, {
					query: null,
					c_lat: null,
					c_lng: null
				});
				return;
			}

			// Update search params
			let query = place.name;
			if (place.vicinity && place.name != place.vicinity) {
				query = `${place.name}, ${place.vicinity}`;
			}

			const center = place.geometry.viewport.getCenter();

			this.query = query;

			_extend(this.searchParams, {
				query: query,
				c_lat: center.lat(),
				c_lng: center.lng()
			});
		},

		submit() {
			this.$router.push({
				name: 'venues.explore',
				query: this.searchParams
			});
		}
	}
};
</script>

<template>
	<div class="pg-home-page">
		<!-- <pg-navbar variant="dark" /> -->
		<div class="hero">
			<pg-map v-bind="mapProps" class="map" />

			<div class="container hero-content">
				<div class="text-center">
					<pg-logo class="logo" />
					<div class="row">
						<div class="col-lg-8 mx-lg-auto">
							<h1 v-html="$t('pages.home.title')" />
							<p v-html="$t('pages.home.subtitle')" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-8 mx-lg-auto">
						<div class="form-row">
							<div class="col-sm-8">
								<div class="form-group position-relative">
									<label class="sr-only">{{ $t('pages.home.search') }}</label>
									<pg-place-textbox
										:placeholder="placeholder"
										:place="query"
										:value="query"
										:options="placeTextboxOptions"
										class="form-control form-control-lg search-form-control search-query-control"
										autofocus
										@place-changed="onPlaceChanged"
									/>
									<div
										v-b-tooltip
										v-if="hasGeolocation"
										:title="$t('pages.home.location')"
										class="search-locate-btn-wrapper">
										<pg-button
											:icon="userLocationFound ? 'location' : 'location-outline'"
											:loading="locating"
											:disabled="userLocationFound"
											size="lg"
											variant="naked"
											class="search-locate-btn"
											tabindex="-1"
											@click="findUserLocation"
										/>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<pg-button
										:disabled="!canSubmit"
										block
										variant="accent"
										size="lg"
										class="search-submit-btn"
										icon="search"
										@click="submit">
										{{ $t('pages.home.submit') }}
									</pg-button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container text-center my-md-5 py-5">
			<div class="row justify-content-stretch">
				<div class="mb-3 mb-md-0 col-md">
					<router-link class="card h-100" to="/venues/explore">
						<div class="card-body">
							<div><img src="/img/home/map.svg"></div>
							<p class="card-text">{{ $t('pages.home.explore.intro') }}</p>
							<h4 class="card-title">{{ $t('pages.home.explore.title') }}</h4>
						</div>
					</router-link>
				</div>
				<div class="mb-3 mb-md-0 col-md">
					<router-link class="card h-100" to="/promote">
						<div class="card-body">
							<div><img src="/img/home/venue.svg"></div>
							<p class="card-text">{{ $t('pages.home.promote.intro') }}</p>
							<h4 class="card-title">{{ $t('pages.home.promote.title') }}</h4>
						</div>
					</router-link>
				</div>
				<div class="mb-3 mb-md-0 col-md">
					<router-link class="card h-100" to="/play-responsibly">
						<div class="card-body">
							<div><img src="/img/home/machine.svg"></div>
							<p class="card-text">{{ $t('pages.home.play_responsibly.intro') }}</p>
							<h4 class="card-title">{{ $t('pages.home.play_responsibly.title') }}</h4>
						</div>
					</router-link>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>