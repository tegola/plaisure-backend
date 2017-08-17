// Globals
$ = window.jQuery = window.$ = require('jquery'); // Make it work with bootstrap
window.Popper = require('popper.js'); // Make it work with bootstrap

require('bootstrap/js/dist/util');
// require('bootstrap/js/dist/alert');
require('bootstrap/js/dist/button');
// require('bootstrap/js/dist/carousel');
require('bootstrap/js/dist/collapse');
require('bootstrap/js/dist/dropdown');
// require('bootstrap/js/dist/modal');
// require('bootstrap/js/dist/popover');
// require('bootstrap/js/dist/scrollspy');
require('bootstrap/js/dist/tab');
require('bootstrap/js/dist/tooltip');

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