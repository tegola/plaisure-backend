import _ from 'lodash'
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
		venues: pg.venues,
		what: pg.searchParams.what,
		near: pg.searchParams.near,
		mapCenter: {
			lat: pg.searchParams.center_lat,
			lng: pg.searchParams.center_lng
		},
		mapBounds: {
			north: pg.searchParams.ne_lat,
			east: pg.searchParams.ne_lng,
			south: pg.searchParams.sw_lat,
			west: pg.searchParams.sw_lng
		},
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
		mapZoom() {
			return (this.latitude && this.longitude) ? 15 : 5
		},

		hasMorePages() {
			return this.venues.next_page_url ? true : false
		},
	},

	watch: {
		// Update page title when position name changes
		near(newValue) {
			document.title = newValue ? `${newValue} - ${pg.app.name}` : pg.app.name
		}
	},

	methods: {
		onSuggestionSelect(item) {
			if (item.geometry && item.geometry.viewport) {
				this.boundsToPositionData(item.geometry.viewport)
			}

			// Get the input value as a shortcut for the formatted address
			this.near = this.$refs.locationAutocomplete.$refs.input.value

			// Reload
			this.load()
		},

		onMapBoundsChange(bounds) {
			this.boundsToPositionData(bounds)
			this.load()
		},

		boundsToPositionData(bounds) {
			//const center = bounds.getCenter()
			const ne = bounds.getNorthEast()
			const sw = bounds.getSouthWest()

			/*
			this.mapCenter = {
				lat: center.lat(),
				lng: center.lng()
			}*/
			this.mapBounds = {
				north: ne.lat(),
				east: ne.lng(),
				south: sw.lat(),
				west: sw.lng()
			}
		},

		load(page = 1) {
			console.log('load')

			const searchParams = {
				what: this.what,
				near: this.near,
				page: page,
				//center_lat: this.mapCenter.lat,
				//center_lng: this.mapCenter.lng,
				ne_lat: this.mapBounds.north,
				ne_lng: this.mapBounds.east,
				sw_lat: this.mapBounds.south,
				sw_lng: this.mapBounds.west,
			}

			// Load venues
			$.get('/venues/search', searchParams, (venues) => {
				this.venues = venues
			})

			// Update url
			const baseName = _.last(location.pathname.split('/'))
			const params = $.param(searchParams, _.omit(['page']))

			window.history.replaceState({}, '', `${baseName}?${params}`)
		},

		loadMore() {
			this.load(this.venues.current_page + 1)
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

