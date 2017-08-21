// Globals
import $ from 'jquery';
import Popper from 'popper.js/dist/umd/popper.js'; // Autoload wasn't working
window.Popper = Popper; // Autoload wasn't working
import 'bootstrap/js/src/util';
import 'bootstrap/js/src/alert';
import 'bootstrap/js/src/button';
// import 'bootstrap/js/src/carousel';
// import 'bootstrap/js/src/collapse';
import 'bootstrap/js/src/dropdown';
// import 'bootstrap/js/src/modal';
// import 'bootstrap/js/src/popover';
// import 'bootstrap/js/src/scrollspy';
// import 'bootstrap/js/src/tab';
import 'bootstrap/js/src/tooltip';
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/src';
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

// Register Vue plugins
Vue.use(VueMatchMedia);

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

	mq: {
		/*
		xs: '(max-width: 575px)',
		sm: '(min-width: 576px) and (max-width: 767px)',
		md: '(min-width: 768px) and (max-width: 991px)',
		lg: '(min-width: 992px) and (max-width: 1199px)',
		xl: '(min-width: 1200px)',
		*/
		constrained: '(max-width: 767px)',
		comfortable: '(min-width: 768px)'
	},

	mounted() {
		// Support for showing geolocation controls
		$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

		// Start tooltips
		$('[data-toggle="tooltip"]').tooltip();
	}
});
