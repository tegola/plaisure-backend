import Vue from 'vue';
import $ from 'jquery';

// Pages
import venueFormPage from './pages/venueForm.js';

new Vue({
	el: '#app',

	components: {
		'pga-venue-form-page': venueFormPage
	},

	mounted() {
		$('[data-toggle="tooltip"]').tooltip();
	}
});