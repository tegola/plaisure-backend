// Load local libs
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import { load as loadGMaps } from 'vue2-google-maps';

import PGAVenueFormPage from 'prontogioco/admin/pages/venue-form.js';

import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';

Vue.use(BootstrapVue);

loadGMaps({
	key: GOOGLE_MAPS_API_KEY,
	language: 'it',
	region: 'it',
	libraries: 'places'
});

new Vue({
	el: '#app',

	components: {
		'pga-venue-form-page': PGAVenueFormPage
	},

	methods: {
		onLogoutClick() {
			this.$refs.logoutForm.submit();
		}
	}
});