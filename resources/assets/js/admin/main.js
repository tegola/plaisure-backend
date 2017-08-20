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