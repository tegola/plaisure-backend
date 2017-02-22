import $ from 'jquery'
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

// HOME -----------------------------------------------------------------------

var $homePage = $('.page-home .page-content')[0]

if ($homePage) {
	new Vue({
		el: $homePage,

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
}

// DETAIL ---------------------------------------------------------------------

/*
var $detailPage = $('.page-detail .page-content');

if ($detailPage) {
	var $map = $('.map');
	var coords = new google.maps.LatLng($map.data('lat'), $map.data('lng'));

	new google.maps.Map($map[0], {
		center: coords,
		zoom: 15,
		scrollwheel: false,
		mapTypeControl: false,
		streetViewControl: false
	});
}
*/