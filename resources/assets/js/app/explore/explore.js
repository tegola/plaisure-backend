import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import SearchForm from './search-form.vue'
import formatDistance from '../../utilities/format-distance'
import singularOrPlural from '../../utilities/singular-or-plural'

// Register Vue Google Maps
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
			streetViewControl: false
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
			console.log('loadMore')
		},

		select(venue) {
			this.currentVenue = venue
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue)
		}
	}
	/*
	// Make the "load more" button actually load inline
	$('[data-action="load-more"]').on('click', function(){
		var button = $(this)
		var resultsContainer = $('#results')
		var url = button.attr('href')

		$.get(url).then(function(data){
			// Update url
			history.pushState({}, '', url)

			// Update list
			var resultsHtml = $('#results', data)[0].innerHTML
			resultsContainer.append(resultsHtml)

			// Update load more button
			var newButton = $('[data-action="load-more"]', data)
			if (!newButton || newButton.attr('href') == url) {
				button.remove()
			} else {
				button.attr('href', newButton.attr('href'))
			}

			// FIXME: Update map
		})

		return false
	})
	*/
})

