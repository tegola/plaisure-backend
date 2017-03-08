import $ from 'jquery'
import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import * as geocoder from '../utilities/geocoder'
import InputTypeahead from './components/input-typeahead.vue'
import Icon from './components/icon.vue'
import VenueSuggestionItem from './components/venue-suggestion-item.vue'

const locationNotFoundMsg = 'Non è stato possibile trovare la tua posizione.'

// Register Vue Google Maps
// FIXME: Pass region per site and language per user locale
Vue.use(VueGoogleMaps, {
	load: {
		key: pg.config.googleMapsApiKey,
		language: pg.config.locale,
		region: pg.config.locale,
		libraries: 'places'
	}
})

new Vue({
	el: '.page-content',

	components: {
		'pg-input-typeahead': $.extend(InputTypeahead, {
			components: {
				'pg-venue-suggestion-item': VenueSuggestionItem
			}
		}),
		'pg-icon': Icon,
	},

	data: {
		venueQuery: '',
		venueSuggestions: [],
		locationQuery: '',
		locationAutocompleteOptions: {
			types: ['geocode'] // Limit search to cities, addresses, etc.
		},
		isSearchingLocation: false,
		isLocationFound: false,
		center: { lat: pg.config.defaultMapCenter.lat, lng: pg.config.defaultMapCenter.lng },
		ne: { lat: null, lng: null },
		sw: { lat: null, lng: null },
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
	},

	computed: {
		zoom() {
			return this.center.lat && this.center.lng ? 15 : 5
		},
		locateButtonIcon() {
			return this.isSearchingLocation ? 'circle-outline-notch' : this.isLocationFound ? 'location' : 'location-outline'
		},
		isLocateButtonDisabled() {
			return this.isSearchingLocation || this.isLocationFound
		},
		isSubmitButtonDisabled() {
			return !this.center.lat || !this.center.lng
		}
	},

	methods: {
		locate() {
			this.isSearchingLocation = true

			navigator.geolocation.getCurrentPosition(
				(position) => {
					this.center = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					}

					// Find city name and use it to fill the City field
					geocoder.reverse(this.center.lat, this.center.lng, (error, location) => {
						this.isLocationFound = location ? true : false
						this.isSearchingLocation = false

						if (error) {
							alert(locationNotFoundMsg)
						} else {
							this.locationQuery = location.administrativeLevels.level3long // FIXME: Use street and city
						}
					})
				},
				() => {
					this.isSearchingLocation = false
					this.isLocationFound = false
					alert(locationNotFoundMsg)
				},
				{
					timeout: 10 * 1000, // 10 secs
					maximumAge: 5 * 60 * 1000 // last 5 minutes
				}
			)
		},

		loadVenueSuggestions(value) {
			// Reset if no value is set
			if (!value) {
				this.venueQuery = null
				this.venueSuggestions = []
				return
			}

			// Load suggestions and use them
			$.get('/venues/suggestions', {
				what: value,
				center_lat: this.center.lat,
				center_lng: this.center.lng,
				sw_lat: this.sw.lat,
				sw_lng: this.sw.lng,
				ne_lat: this.ne.lat,
				ne_lng: this.ne.lng,
				near: this.locationQuery
			}).done((data) => {
				this.venueSuggestions = data
			})
		},

		selectVenueSuggestion(item) {
			// If it's a venue, go to detail page
			if (item.type == 'venue' && item.id) {
				location.href = `/venues/${item.id}`
				return
			}

			// Else just set the category name
			this.venueQuery = item.name
		},

		selectLocationSuggestion(suggestion) {
			const viewport = suggestion.geometry.viewport

			this.center = this._extractCoords(viewport.getCenter())
			this.ne = this._extractCoords(viewport.getNorthEast())
			this.sw = this._extractCoords(viewport.getSouthWest())

			// Get the input value as a shortcut for the formatted address
			this.locationQuery = this.$refs.locationAutocomplete.$refs.input.value
		},

		onSubmit(e) {
			if (!this.locationQuery || !this.center) {
				e.preventDefault()
			}
		},

		_extractCoords(input) {
			return {
				lat: input.lat(),
				lng: input.lng()
			}
		}
	},

	mounted() {
		// Prevent submitting the form when the locations dropdown is open
		// (key events are not handled by gmap-autocomplete)
		$(this.$refs.locationAutocomplete.$refs.input).on('keydown', (e) => {
			if (e.which == 13 && $('.pac-container:visible').length) {
				e.preventDefault()
			}
		})

		// If no location is set, find a generic one using IP info
		if ((!this.center.lat && !this.center.lng) || !this.locationQuery) {
			geocoder.geocodeByIp((error, location) => {
				if (!location) return

				this.center = {
					lat: location.latitude,
					lng: location.longitude
				}
				this.locationQuery = location.city

				this.$emit('locate', this.center.lat, this.center.lng, this.locationQuery)
			})
		}

		$('[data-toggle="tooltip"]').tooltip()
	}
})