import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'

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

	data: {
		lat: pg.lat,
		lng: pg.lng,
		what: pg.what,
		near: pg.near,
		mapOptions: {
			disableDefaultUI: true,
			draggable: false,
			scrollwheel: false
		}
	}
})