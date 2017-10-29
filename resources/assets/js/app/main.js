// Load polyfills
import 'classlist-polyfill';
import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media

// Set globals
import $ from 'jquery';
import Popper from 'popper.js';
window.Popper = Popper;
import 'bootstrap';

// Load local libs
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';
import { load as loadGMaps } from 'vue2-google-maps';
import PgNavbarSearchForm from './components/navbar-search-form';
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
Vue.component('pg-navbar-search-form', PgNavbarSearchForm);
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
		document.documentElement.classList.add(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

		// Start tooltips
		$('[data-toggle="tooltip"]').tooltip();
	}
});
