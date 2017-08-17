// Globals
import $ from 'jquery';
import Popper from 'popper.js/dist/umd/popper.js'; // Autoload wasn't working
window.Popper = Popper; // Autoload wasn't working
import 'bootstrap/js/dist/util';
import 'bootstrap/js/dist/alert';
import 'bootstrap/js/dist/button';
// import 'bootstrap/js/dist/carousel';
// import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/dropdown';
// import 'bootstrap/js/dist/modal';
// import 'bootstrap/js/dist/popover';
// import 'bootstrap/js/dist/scrollspy';
// import 'bootstrap/js/dist/tab';
import 'bootstrap/js/dist/tooltip';
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
		$('[data-toggle="tooltip"]').tooltip();
	}
});
