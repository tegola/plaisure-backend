// Load local libs
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import { load as loadGMaps } from 'vue2-google-maps';
import PGAVenueFormPage from 'prontogioco/admin/pages/venue-form.js';

Vue.use(BootstrapVue);

loadGMaps({
	key: pg.app.googleMapsApiKey,
	language: pg.app.locale,
	region: pg.app.locale,
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