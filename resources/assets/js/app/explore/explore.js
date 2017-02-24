import $ from 'jquery'
import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import SearchForm from './search-form.vue'
import formatDistance from '../../utilities/format-distance'
import singularOrPlural from '../../utilities/singular-or-plural'

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
		'pg-search-form': SearchForm
	},

	data: {
		lat: pg.lat,
		lng: pg.lng,
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
		currentVenue: null
	},

	computed: {
		mapCenter(){
			if (this.lat && this.lng) {
				return {
					lat: this.lat,
					lng: this.lng
				}
			} else {
				// Default to italy
				return {
					lat: 41.2053112,
					lng: 8.0860841
				}
			}
		},
		mapZoom() {
			return (this.lat && this.lng) ? 15 : 5
		}
	},

	filters: {
		formatDistance: formatDistance,
		singularOrPlural: singularOrPlural
	},

	watchers: {
		near() {
			this.lat = null
			this.lng = null
		}
	},

	methods: {
		loadMore() {
			const self = this;
			const nextPage = this.venues.next_page_url ? this.venues.current_page + 1 : null

			if (!nextPage) {
				return
			}

			$.get('/venues/search', {
				page: nextPage,
				what: this.what,
				lat: this.lat,
				lng: this.lng,
				near: this.near
			}, (venues) => {
				console.log(venues)
				self.venues = venues
			})
		},

		select(venue) {
			this.currentVenue = venue
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue)
		}
	}
})

