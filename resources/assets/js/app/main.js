// Globals
$ = window.jQuery = window.$ = require('jquery'); // Make it work with bootstrap
window.Popper = require('popper.js'); // Make it work with bootstrap

require('bootstrap/js/src/util');
// require('bootstrap/js/src/alert');
require('bootstrap/js/src/button');
// require('bootstrap/js/src/carousel');
require('bootstrap/js/src/collapse');
require('bootstrap/js/src/dropdown');
// require('bootstrap/js/src/modal');
// require('bootstrap/js/src/popover');
// require('bootstrap/js/src/scrollspy');
require('bootstrap/js/src/tab');
require('bootstrap/js/src/tooltip');

import Vue from 'vue';
import { load as loadGMaps, Autocomplete } from 'vue2-google-maps';
import PgIcon from './components/icon';
import PgHomePage from './pages/home';
import PgExplorePage from './pages/explore';
import PgVenueDetailPage from './pages/detail';

// Load Google Maps API
loadGMaps({
	key: pg.config.googleMapsApiKey,
	language: pg.config.locale,
	region: pg.config.locale,
	libraries: 'places'
});

// Register global components
Vue.component('pg-map-autocomplete', Autocomplete);
Vue.component('pg-icon', PgIcon);

// Startup VM
new Vue({
	el: '#app',

	components: {
		PgHomePage,
		PgExplorePage,
		PgVenueDetailPage
	},

	mounted() {
		// Support for showing geolocation controls
		$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

		// Start tooltips
		// $('[data-toggle="tooltip"]').tooltip();
	}
});
