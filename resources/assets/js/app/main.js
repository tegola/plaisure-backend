import $ from 'jquery';
import Vue from 'vue';
import Icon from './components/icon.vue';
import { load as loadGMaps, Autocomplete } from 'vue2-google-maps';

// Load Google Maps API
loadGMaps({
	key: pg.config.googleMapsApiKey,
	language: pg.config.locale,
	region: pg.config.locale,
	libraries: 'places'
});

// Register components used sitewide
Vue.component('pg-icon', Icon);
Vue.component('gmap-autocomplete', Autocomplete);

// Startup VM
new Vue({
	el: '#app',

	mounted() {
		// Support for showing geolocation controls
		$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

		// Start tooltips
		$('[data-toggle="tooltip"]').tooltip();
	}
});
