<script>
import _extend from 'lodash/extend';
import _debounce from 'lodash/debounce';
import * as geocoder from 'prontogioco/utilities/geocoder';
import PgPlaceTextbox from 'prontogioco/app/components/place-textbox';
import InputTypeahead from 'prontogioco/app/components/input-typeahead';
import PgVenueSuggestionItem from 'prontogioco/app/components/venue-suggestion-item';
import { Map as PgMap } from 'vue2-google-maps';

const locationNotFoundMsg = 'Non è stato possibile trovare la tua posizione.';
const placeTextboxOptions = {
	types: ['geocode'] // Limit search to cities, addresses, etc.
};
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

import constants from 'prontogioco/constants';

export default {
	name: 'PgHomePage',

	components: {
		PgPlaceTextbox,
		PgMap,
		'pg-input-typeahead': _extend(InputTypeahead, {
			components: {
				PgVenueSuggestionItem
			}
		}),
	},

	data() {
		return {
			categories: [],
			searchQuery: '',
			searchSuggestions: [],
			placeQuery: null,
			placeTextboxOptions: placeTextboxOptions,
			isSearchingLocation: false,
			isLocationFound: false,
			mapOptions: mapOptions,
			searchCenter: {
				lat: null,
				lng: null
			}
		};
	},

	computed: {
		mapProps() {
			return {
				center: this.searchCenter.lat && this.searchCenter.lng ? this.searchCenter : this.DEFAULT_COORDS,
				zoom: this.searchCenter.lat && this.searchCenter.lng ? 15 : 5,
				options: mapOptions
			};
		},
		locateButtonIcon() {
			return this.isSearchingLocation ? 'circle-outline-notch' : this.isLocationFound ? 'location' : 'location-outline';
		},
		canSubmit() {
			return this.searchCenter.lat && this.searchCenter.lng ? true : false;
		}
	},

	methods: {
		locate() {
			this.isSearchingLocation = true;

			navigator.geolocation.getCurrentPosition(
				(position) => {
					this.searchCenter = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};

					// Find city name and use it to fill the City field
					geocoder.reverse(this.searchCenter.lat, this.searchCenter.lng, (error, location) => {
						this.isLocationFound = location ? true : false;
						this.isSearchingLocation = false;

						if (error) {
							alert(locationNotFoundMsg);
						} else {
							let address = [];

							if (location.streetName) address.push(location.streetName);
							address.push(location.administrativeLevels.level3long);

							this.placeQuery = address.join(', ');
							this.$refs.placeTextbox.$refs.input.$refs.input.value = this.placeQuery; // Needed, the previous line wasn't always working
						}
					});
				},
				() => {
					this.isSearchingLocation = false;
					this.isLocationFound = false;
					alert(locationNotFoundMsg);
				},
				{
					timeout: 10 * 1000, // 10 secs
					maximumAge: 5 * 60 * 1000 // last 5 minutes
				}
			);
		},

		onSearchInput(value) {
			// Always reset the category (it will be set back when selecting
			// a suggestion)
			this.categories = [];

			// Reset if empty
			if (!value) {
				this.searchQuery = null;
				this.searchSuggestions = [];
				return;
			}

			// Load suggestions
			this.loadSearchSuggestions(value);
		},

		loadSearchSuggestions: _debounce(function(value) {
			// Load suggestions and use them
			this.$axios.post('/suggestions', {
				query: value,
			}).then(response => {
				this.searchSuggestions = response.data;
			});
		}, 300),

		onSearchSuggestionSelect(item) {
			// If it's a venue, go to detail page,
			// otherwise store the category name and value
			if (item.type == 'venue') {
				location.href = item.url;
			} else if (item.type == 'category') {
				this.categories = [item.id];
				this.searchQuery = item.name;
			}
		},

		onPlaceChanged(place) {
			// Reset user location indicator
			this.isLocationFound = false;

			// Reset center if no place is set
			if (!place) {
				this.placeQuery = null;
				this.searchCenter = {
					lat: null,
					lng: null
				};
				return;
			}

			const center = place.geometry.viewport.getCenter();

			this.searchCenter = {
				lat: center.lat(),
				lng: center.lng()
			};

			if (place.vicinity && place.name != place.vicinity) {
				this.placeQuery = `${place.name}, ${place.vicinity}`;
			} else {
				this.placeQuery = place.name;
			}
		},

		onSubmit(e) {
			if (!this.canSubmit) e.preventDefault();
		}
	},

	beforeCreate() {
		_extend(this, constants);
	},

	mounted() {
		// If no location is set, find a generic one using IP info
		geocoder.geocodeByIp((error, location) => {
			if (!location || !location.latitude || !location.longitude || !location.city) return;

			this.placeQuery = location.city;
			this.searchCenter = {
				lat: location.latitude,
				lng: location.longitude
			};
		});
	}
};
</script>

<template>
<div class="pg-home-page">
	<div class="hero">
		<pg-map class="map" v-bind="mapProps"></pg-map>
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
				<pg-logo class="logo"></pg-logo>
				<div class="row">
					<div class="col-lg-8 ml-lg-auto mr-lg-auto">
						<h1>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h1>
						<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
					</div>
				</div>
			</div>

			<form class="form-search" action="/venues/explore" method="get" @submit="onSubmit">
				<input type="hidden" name="categories[]" v-model="categories" v-if="categories.length">
				<input type="hidden" name="c_lat" v-model="searchCenter.lat">
				<input type="hidden" name="c_lng" v-model="searchCenter.lng">
				<div class="row">
					<div class="ml-md-auto col-md-5 col-lg-4">
						<div class="form-group">
							<label class="initialism"><strong>Trova</strong></label><br>
							<pg-input-typeahead
								input-class="form-control form-control-lg search-form-control"
								name="what"
								placeholder="VLT, Bingo, Ricevitoria"
								autofocus
								v-model="searchQuery"
								:suggestions="searchSuggestions"
								item-component="pg-venue-suggestion-item"
								@input="onSearchInput"
								@select="onSearchSuggestionSelect">
							</pg-input-typeahead>
						</div>
					</div>
					<div class="col-md-5 col-lg-4 mr-md-auto mr-lg-0">
						<div class="form-group dropdown">
							<label class="initialism"><strong>Vicino a</strong></label><br>
							<div style="position: relative">
								<pg-place-textbox
									class="form-control form-control-lg search-form-control search-query-control"
									ref="placeTextbox"
									name="query"
									placeholder="Città"
									autofocus
									:place="placeQuery"
									:value="placeQuery"
									:disabled="isSearchingLocation"
									:options="placeTextboxOptions"
									@place-changed="onPlaceChanged">
								</pg-place-textbox>
								<button
									type="button"
									ref="locateButton"
									class="btn btn-lg btn-link search-locate-btn"
									data-toggle="tooltip"
									title="Usa la tua posizione"
									aria-label="Usa la tua posizione"
									tabindex="-1"
									:disabled="isSearchingLocation"
									v-if="$root.hasGeolocation"
									@click="locate">
									<pg-icon :icon="locateButtonIcon" :spinning="isSearchingLocation"></pg-icon>
								</button>
							</div>
						</div>
					</div>
					<div class="col-md-10 ml-md-auto mr-md-auto col-lg-2 ml-lg-0 mr-lg-auto">
						<div class="form-group">
							<label class="initialism d-none d-lg-inline-block">&nbsp;</label>
							<button type="submit" class="btn btn-lg btn-block btn-accent search-submit-btn" :disabled="!canSubmit">
								<pg-icon icon="search"></pg-icon>
								Cerca
							</button>
						</div>
					</div>
				</div>
			</form>
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