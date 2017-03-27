import $ from 'jquery';
import Vue from 'vue';

window.Vue = Vue;
window.$ = $;

// Pages
import venueFormPage from './pages/venueForm.js';

new Vue({
	el: '#app',

	components: {
		'pga-venue-form-page': venueFormPage
	}
});