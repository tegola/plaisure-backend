import $ from 'jquery'
import * as geocoder from '../../utilities/geocoder'
import InputTypeahead from '../components/input-typeahead.vue'
import Icon from '../components/icon.vue'
import VenueSuggestionItem from '../components/venue-suggestion-item.vue'

export default {
	components: {
		'pg-input-typeahead': $.extend(InputTypeahead, {
			components: {
				'pg-venue-suggestion-item': VenueSuggestionItem
			}
		}),
		'pg-icon': Icon,
	},

	props: {
		action: {
			type: String,
			required: true
		},
		autoLocate: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			venueQuery: this.what,
			venueSuggestions: [],
			locationQuery: this.near,
			locationAutocompleteOptions: {
				types: ['geocode'] // Limit search to cities, addresses, etc.
			},
			isSearchingLocation: false,
			isLocationFound: false,
			latitude: this.lat,
			longitude: this.lng,
		}
	},

	computed: {
		locateButtonIcon() {
			return this.isSearchingLocation ? 'circle-outline-notch' : this.isLocationFound ? 'location' : 'location-outline'
		},
		isLocateButtonDisabled() {
			return this.isSearchingLocation || this.isLocationFound
		},
		isSubmitButtonDisabled() {
			return !this.latitude || !this.longitude
		}
	},

	methods: {
		locate() {
			const self = this
			this.isSearchingLocation = true

			navigator.geolocation.getCurrentPosition(
				(position) => {
					self.latitude = position.coords.latitude
					self.longitude = position.coords.longitude

					// Find city name and use it to fill the City field
					geocoder.reverse(self.latitude, self.longitude, (error, location) => {
						if (error) return

						self.locationQuery = location.administrativeLevels.level3long // FIXME: Use street and city
						self.isSearchingLocation = false
						self.isLocationFound = true

						self.$emit('locate', self.latitude, self.longitude, self.locationQuery)
					})
				},
				() => {
					self.isSearchingLocation = false
					self.isLocationFound = false
					alert('Non è stato possibile trovare la tua posizione.')
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
				lat: this.latitude,
				lng: this.longitude,
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

		selectLocationSuggestion(item) {
			if (!item.geometry) {
				return
			}

			// Store lat/lng/name
			this.latitude = item.geometry.location.lat()
			this.longitude = item.geometry.location.lng()
			this.locationQuery = item.name

			// Emit location change
			this.$emit('locate', this.latitude, this.longitude, this.locationQuery)
		},

		onSubmit(e) {
			if (!this.locationQuery || !this.latitude || !this.longitude) {
				e.preventDefault()
			}
		}
	},

	mounted() {
		const self = this

		// Prevent submitting the form when the locations dropdown is open
		// (key events are not handled by gmap-autocomplete)
		$(this.$refs.locationAutocomplete.$refs.input).on('keydown', function(e){
			if (e.which == 13 && $('.pac-container:visible').length) {
				e.preventDefault()
			}
		})

		// If no location is set, find a generic one using IP info
		// FIXME: move outside
		if (this.autoLocate && (!this.latitude || !this.longitude || !this.locationQuery)) {
			geocoder.geocodeByIp((error, location) => {
				if (!location) return

				self.latitude = location.latitude
				self.longitude = location.longitude
				self.locationQuery = location.city

				self.$emit('locate', self.latitude, self.longitude, self.locationQuery)
			})
		}
	}
}