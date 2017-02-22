import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'
import SearchForm from './explore/search-form.vue' // FIXME: make it a common file

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
		mapCenter: {
			lat: pg.lat,
			lng: pg.lng,
		},
		mapOptions: {
			scrollwheel: false,
			mapTypeControl: false,
			streetViewControl: false
		}
	}
})