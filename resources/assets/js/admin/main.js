// Load local libs
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import { load as loadGMaps } from 'vue2-google-maps';
import PGAVenueFormPage from 'prontogioco/admin/pages/venue-form.js';

Vue.use(BootstrapVue);

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

	methods: {
		onLogoutClick() {
			this.$refs.logoutForm.submit();
		}
	}
});