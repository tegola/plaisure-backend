<script>
import _extend from 'lodash/extend';
import * as geocoder from 'prontogioco/utilities/geocoder';
import PgButton from 'prontogioco/app/components/button';
import PgPlaceTextbox from 'prontogioco/app/components/place-textbox';
import { Map as PgMap } from 'vue2-google-maps';
import { DEFAULT_COORDS } from 'prontogioco/constants';

const placeholder = 'Inserisci la tua città...';
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
			placeholder: placeholder,
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
			this.placeholder = '(Vicino a te)';

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
			alert('Non è stato possibile trovare la tua posizione.');
		},

		onPlaceChanged(place) {
			// Reset user location indicator
			this.userLocationFound = false;
			this.placeholder = placeholder;

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
			})
		});
	}
};
</script>

<template>
<div class="pg-home-page">
	<pg-navbar variant="dark" />
	<div class="hero">
		<pg-map class="map" v-bind="mapProps" />
		<!--
		<nav class="navbar navbar-transparent navbar-expand-md">
			<div class="container justify-content-center">
				<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('app.name') }}">
					LOGO QUI
				</a>
				<div>
					@if (Auth::guest())
						<a class="btn btn-inverse-neutral" href="{{ url('/login') }}">Accedi</a>
						<a class="btn btn-primary" href="{{ url('/register') }}">Iscriviti</a>
					@else
						<span class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" type="button" id="navbar-user-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-user-button">
								<a class="dropdown-item" href="{{ route('site.user') }}">
									<strong>
										{{ Auth::user()->name }}
										{{ Gate::allows('administer') ? '(amministratore)' : '' }}
									</strong><br>
									<span class="text-muted">Visualizza il tuo profilo</span>
								</a>
								@if(Gate::allows('administer'))
									<a class="dropdown-item" href="{{ route('admin.home') }}">
										Vai all'amministrazione
									</a>
								@endif
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-user-logout-form').submit();">Esci</a>
								<form id="navbar-user-logout-form" action="{{ url('/logout') }}" method="POST" hidden>
									{{ csrf_field() }}
								</form>
							</div>
						</span>
					@endif
				</div>
			</div>
		</nav>
		-->

		<div class="container hero-content">
			<div class="text-center">
				<pg-logo class="logo" />
				<div class="row">
					<div class="col-lg-8 mx-lg-auto">
						<h1>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h1>
						<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 mx-lg-auto">
					<div class="form-row">
						<div class="col-sm-8">
							<div class="form-group position-relative">
								<label class="sr-only">Cerca</label>
								<pg-place-textbox
									class="form-control form-control-lg search-form-control search-query-control"
									autofocus
									:placeholder="placeholder"
									:place="query"
									:value="query"
									:options="placeTextboxOptions"
									@place-changed="onPlaceChanged"
								/>
								<div
									v-if="hasGeolocation"
									class="search-locate-btn-wrapper"
									v-b-tooltip
									title="Usa la tua posizione">
									<pg-button
										size="lg"
										variant="naked"
										class="search-locate-btn"
										:icon="userLocationFound ? 'location' : 'location-outline'"
										tabindex="-1"
										:loading="locating"
										:disabled="userLocationFound"
										@click="findUserLocation"
									/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<pg-button
									block
									variant="accent"
									size="lg"
									class="search-submit-btn"
									:disabled="!canSubmit"
									icon="search"
									@click="submit">
									Cerca
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
						<p class="card-text">Ti senti avventuroso?</p>
						<h4 class="card-title">Esplora la tua zona</h4>
					</div>
				</router-link>
			</div>
			<div class="mb-3 mb-md-0 col-md">
				<router-link class="card h-100" to="/promote">
					<div class="card-body">
						<div><img src="/img/home/venue.svg"></div>
						<p class="card-text">Sei nel campo?</p>
						<h4 class="card-title">Promuovi la tua attivit&agrave;</h4>
					</div>
				</router-link>
			</div>
			<div class="mb-3 mb-md-0 col-md">
				<router-link class="card h-100" to="/play-responsibly">
					<div class="card-body">
						<div><img src="/img/home/machine.svg"></div>
						<p class="card-text">Non esagerare</p>
						<h4 class="card-title">Gioca responsabilmente</h4>
					</div>
				</router-link>
			</div>
		</div>
	</div>

	<pg-page-footer />
</div>
</template>