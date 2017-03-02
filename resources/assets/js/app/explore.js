import $ from 'jquery'
import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import formatDistance from '../utilities/format-distance'
import singularOrPlural from '../utilities/singular-or-plural'

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

	filters: {
		formatDistance: formatDistance,
		singularOrPlural: singularOrPlural
	},

	data: {
		latitude: pg.lat,
		longitude: pg.lng,
		what: pg.what,
		near: pg.near,
		venues: pg.venues,
		mapOptions: {
			mapTypeControl: false,
			streetViewControl: false,
			styles: [
				{ // Hide points of interest
					'featureType': 'poi',
					'stylers': [{ 'visibility': 'off' }]
				}
			]
		},
		followMap: true,
		highlightedVenue: null,
		selectedVenue: null
	},

	computed: {
		mapCenter(){
			if (this.latitude && this.longitude) {
				return {
					lat: this.latitude,
					lng: this.longitude
				}
			} else {
				return pg.config.defaultMapCenter
			}
		},
		mapZoom() {
			return (this.latitude && this.longitude) ? 15 : 5
		}
	},

	watch: {
		// Update page title when position name changes
		near(newValue) {
			document.title = newValue ? `${newValue} - ${pg.app.name}` : pg.app.name
		}
	},

	methods: {
		onSuggestionSelect(item) {
			// Get location
			this.latitude = item.geometry ? item.geometry.location.lat() : null
			this.longitude = item.geometry ? item.geometry.location.lng() : null

			// Get the input value as a shortcut for the formatted address
			this.near = this.$refs.locationAutocomplete.$refs.input.value

			// Reload
			this.load()
		},

		onMapBoundsChange(viewport) {
			//console.log(viewport)
		},

		onMapCenterChange(location) {
			console.log('map center change')

			// Save location
			this.latitude = location.lat()
			this.longitude = location.lng()

			// TODO: Reload if "followMap" is active
		},

		load(page = 1) {
			$.get('/venues/search', {
				page: page,
				what: this.what,
				lat: this.latitude,
				lng: this.longitude,
				near: this.near
			}, (venues) => {
				this.venues = venues
			})

			this.updateUrl()
		},

		loadMore() {
			this.load(this.venues.current_page + 1)
		},

		updateUrl() {
			const params = $.param({
				what: this.what,
				lat: this.latitude,
				lng: this.longitude,
				near: this.near
			})
			window.history.replaceState({}, '', `explore?${params}`)
		},

		highlight(venue) {
			this.highlightedVenue = venue || null
		},

		select(venue) {
			this.selectedVenue = venue
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue)
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
	}
})

