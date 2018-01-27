import _extend from 'lodash/extend';
import _debounce from 'lodash/debounce';
import axios from 'axios';
import * as geocoder from '../../utilities/geocoder';
import PgPlaceTextbox from '../components/place-textbox';
import InputTypeahead from '../components/input-typeahead';
import VenueSuggestionItem from '../components/venue-suggestion-item';
import { Map } from 'vue2-google-maps';

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

export default {
	name: 'pg-home-page',

	components: {
		PgPlaceTextbox,
		'pg-map': Map,
		'pg-input-typeahead': _extend(InputTypeahead, {
			components: {
				'pg-venue-suggestion-item': VenueSuggestionItem
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
				center: this.searchCenter.lat && this.searchCenter.lng ? this.searchCenter : pg.config.defaultMapCenter,
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
			axios.post('/suggestions', {
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
