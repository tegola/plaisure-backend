import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import SearchForm from './search-form.vue'

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
		// Italy
		mapCenter: pg.config.defaultMapCenter,
		mapZoom: 15,
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

	methods: {
		onLocationUpdate(lat, lng) {
			this.mapCenter = {
				lat: lat,
				lng: lng
			}
			this.mapZoom = 15
		}
	}
})