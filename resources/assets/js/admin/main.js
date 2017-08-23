// Globals
import $ from 'jquery';
import Popper from 'popper.js/dist/umd/popper.js'; // Autoload wasn't working
window.Popper = Popper; // Autoload wasn't working

import 'bootstrap/js/src/util';
// import 'bootstrap/js/src/alert';
// import 'bootstrap/js/src/button';
// import 'bootstrap/js/src/carousel';
import 'bootstrap/js/src/collapse';
import 'bootstrap/js/src/dropdown';
// import 'bootstrap/js/src/modal';
// import 'bootstrap/js/src/popover';
// import 'bootstrap/js/src/scrollspy';
// import 'bootstrap/js/src/tab';
import 'bootstrap/js/src/tooltip';

import Vue from 'vue';
import { load as loadGMaps } from 'vue2-google-maps';
import PGAVenueFormPage from './pages/venue-form.js';

loadGMaps({
	key: pg.config.googleMapsApiKey,
	language: pg.config.locale,
	region: pg.config.locale,
	libraries: 'places'
});

new Vue({
	el: '#app',

	components: {
		'pga-venue-form-page': PGAVenueFormPage
	},

	mounted() {
		$('[data-toggle="tooltip"]').tooltip();
	}
});