import Vue from 'vue';
import _ from 'lodash';
import $ from 'jquery';
import axios from 'axios';
import * as geocoder from '../utilities/geocoder';
import InputTypeahead from './components/input-typeahead.vue';
import VenueSuggestionItem from './components/venue-suggestion-item.vue';
import { Map } from 'vue2-google-maps'

const locationNotFoundMsg = 'Non è stato possibile trovare la tua posizione.';

Vue.component('pg-home-page', {
	components: {
		'pg-map': Map,
		'pg-input-typeahead': _.extend(InputTypeahead, {
			components: {
				'pg-venue-suggestion-item': VenueSuggestionItem
			}
		})
	},

	data() {
		return {
			category: null,
			venueQuery: '',
			venueSuggestions: [],
			locationQuery: '',
			locationAutocompleteOptions: {
				types: ['geocode'] // Limit search to cities, addresses, etc.
			},
			isSearchingLocation: false,
			isLocationFound: false,
			center: {
				lat: pg.config.defaultMapCenter.lat,
				lng: pg.config.defaultMapCenter.lng
			},
			ne: {
				lat: null,
				lng: null
			},
			sw: {
				lat: null,
				lng: null
			},
			mapOptions: {
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
			}
		};
	},

	computed: {
		zoom() {
			return this.center.lat && this.center.lng ? 15 : 5;
		},
		locateButtonIcon() {
			return this.isSearchingLocation ? 'circle-outline-notch' : this.isLocationFound ? 'location' : 'location-outline';
		},
		isLocateButtonDisabled() {
			return this.isSearchingLocation || this.isLocationFound;
		},
		isSubmitButtonDisabled() {
			// FIXME: Same as onSubmit down below?
			return !this.center.lat || !this.center.lng;
		}
	},

	methods: {
		locate() {
			this.isSearchingLocation = true;

			navigator.geolocation.getCurrentPosition(
				(position) => {
					this.center = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};

					// Find city name and use it to fill the City field
					geocoder.reverse(this.center.lat, this.center.lng, (error, location) => {
						this.isLocationFound = location ? true : false;
						this.isSearchingLocation = false;

						if (error) {
							alert(locationNotFoundMsg);
						} else {
							this.locationQuery = location.administrativeLevels.level3long; // FIXME: Use street and city
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

		onWhatInput(value) {
			// Reset if empty
			if (!value) {
				this.venueQuery = null;
				this.venueSuggestions = [];
				return;
			}

			// Always reset the category (it will be set back when selecting
			// a suggestion)
			this.category = null;

			this.loadVenueSuggestions(value);
		},

		loadVenueSuggestions: _.debounce(function(value) {
			// Load suggestions and use them
			axios.post('/suggestions', {
				what: value,
				c_lat: this.center.lat,
				c_lng: this.center.lng,
				sw_lat: this.sw.lat,
				sw_lng: this.sw.lng,
				ne_lat: this.ne.lat,
				ne_lng: this.ne.lng,
				near: this.locationQuery
			}).then(response => {
				this.venueSuggestions = response.data;
			});
		}, 300),

		selectVenueSuggestion(item) {
			// If it's a venue, go to detail page,
			// otherwise store the category name and value
			if (item.type == 'venue') {
				location.href = item.url;
			} else if (item.type == 'category') {
				this.category = item.id;
				this.venueQuery = item.name;
			}
		},

		selectLocationSuggestion(suggestion) {
			console.log(suggestion);
			const viewport = suggestion.geometry.viewport;

			this.center = this._extractCoords(viewport.getCenter());
			this.ne = this._extractCoords(viewport.getNorthEast());
			this.sw = this._extractCoords(viewport.getSouthWest());

			// Get the input value as a shortcut for the formatted address
			this.locationQuery = this.$refs.locationAutocomplete.value;
		},

		onSubmit(e) {
			if (!this.locationQuery || !this.center) {
				e.preventDefault();
			}
		},

		_extractCoords(input) {
			return {
				lat: input.lat(),
				lng: input.lng()
			};
		}
	},

	mounted() {
		const $autocompleteInput = $(this.$refs.locationAutocomplete.$refs.input);

		// FIXME: Handle events with vue and remove jquery
		// Prevent submitting the form when the locations dropdown is open
		$autocompleteInput.on('keydown', (e) => {
			if (e.which == 13 && $('.pac-container:visible').length) {
				e.preventDefault();
			}
		});

		// Reset location data when the input is empty
		$autocompleteInput.on('keyup', (e) => {
			if (e.target.value) return;

			this.isLocationFound = false;
			this.center = { lat: null, lng: null };
			this.ne = { lat: null, lng: null };
			this.sw = { lat: null, lng: null };
		});

		// If no location is set, find a generic one using IP info
		geocoder.geocodeByIp((error, location) => {
			if (!location || !location.latitude || !location.longitude || !location.city) return;

			this.center = {
				lat: location.latitude,
				lng: location.longitude
			};
			this.locationQuery = location.city;
		});
	}
});
