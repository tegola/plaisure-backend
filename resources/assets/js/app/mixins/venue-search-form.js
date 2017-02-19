import $ from 'jquery'
import * as geocoder from '../../utilities/geocoder'
import InputTypeahead from '../components/input-typeahead.vue'
import Icon from '../components/icon.vue'
import VenueSuggestionItem from '../components/venue-suggestion-item.vue'
import LocationSuggestionItem from '../components/location-suggestion-item.vue'

export default {
	components: {
		'pg-input-typeahead': $.extend(InputTypeahead, {
			components: {
				'pg-venue-suggestion-item': VenueSuggestionItem,
				'pg-location-suggestion-item': LocationSuggestionItem // FIXME: replace
			}
		}),
		'pg-icon': Icon,
	},

	props: {
		action: {
			type: String,
			required: true
		},
		lat: Number,
		lng: Number,
		what: String,
		near: String
	},

	data() {
		return {
			query: this.what,
			venueSuggestions: [],
			locationSuggestions: [],
			location: {
				isSearching: false,
				isFound: false,
				lat: this.lat,
				lng: this.lng,
				near: this.near
			}
		}
	},

	computed: {
		locateButtonIcon() {
			return this.location.isSearching ? 'circle-outline-notch' : this.location.isFound ? 'location' : 'location-outline'
		},
		isLocateButtonDisabled() {
			return this.location.isSearching || this.location.isFound
		}
	},

	methods: {
		locate() {
			const self = this
			this.location.isSearching = true

			navigator.geolocation.getCurrentPosition(
				(position) => {
					self.location.lat = position.coords.latitude
					self.location.lng = position.coords.longitude

					// Find city name and use it to fill the City field
					geocoder.reverse(self.location.lat, self.location.lng, (error, location) => {
						if (error) return

						self.location.near = location.administrativeLevels.level3long // FIXME: Use street and city
						self.location.isSearching = false
						self.location.isFound = true

						self.$emit('locate', self.location.lat, self.location.lng, self.location.near)
					})
				},
				() => {
					self.location.isSearching = false
					self.location.isFound = false
					alert('Non è stato possibile trovare la tua posizione.')
				},
				{
					timeout: 10 * 1000, // 10 secs
					maximumAge: 5 * 60 * 1000 // last 5 minutes
				}
			)
		},

		loadVenueSuggestions(value) {
			// Save value
			this.query = value;

			// Load suggestions and use them
			$.get('/venues/suggestions', {
				what: value
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
			this.query = item.name
		},

		loadLocationSuggestions(value) {
			// TODO: Save value
			// ...

			// Load suggestions and use them
			geocoder.geocode(value, (error, locations) => {
				console.log(JSON.stringify(locations))
				if (error) {
					this.locationSuggestions = []
					return
				}
				
				this.locationSuggestions = locations
			})
		},

		selectLocationSuggestion(item) {
			console.log('select location', item)
		}
	},

	mounted() {
		const self = this

		// If no location is set, find a generic one using IP info
		if (!this.location.lat || !this.location.lng || !this.location.near) {
			geocoder.geocodeByIp((error, location) => {
				if (!location) return

				self.location.lat = location.latitude
				self.location.lng = location.longitude
				self.location.near = location.city

				self.$emit('locate', self.location.lat, self.location.lng, self.location.near)
			})
		}
	}
}