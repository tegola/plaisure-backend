// Set globals
import $ from 'jquery';
import Popper from 'popper.js';
window.Popper = Popper;
import 'bootstrap';

// Load local libs
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