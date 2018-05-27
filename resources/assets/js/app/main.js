// Load polyfills
import 'classlist-polyfill';
import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media

// Load local libs
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';
import { load as loadGMaps } from 'vue2-google-maps';

// Setup Axios
import axios from 'axios';
Vue.prototype.$axios = axios.create({
	headers: {
		'X-Requested-With': 'XMLHttpRequest'
	}
});

// Load Google Maps API
import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';
loadGMaps({
	key: GOOGLE_MAPS_API_KEY,
	language: 'it', // FIXME: Use user locale
	region: 'it', // FIXME: Use user locale
	libraries: 'places'
});

// Register Vue plugins, directives and components
Vue.use(VueMatchMedia);

// Register common components and directives
import PgLogo from 'prontogioco/app/components/logo';
import PgNavbar from 'prontogioco/app/components/navbar';
import PgIcon from 'prontogioco/app/components/icon';
import PgPageFooter from 'prontogioco/app/components/page-footer';

Vue.component('pg-logo', PgLogo);
Vue.component('pg-navbar', PgNavbar);
Vue.component('pg-icon', PgIcon);
Vue.component('pg-page-footer', PgPageFooter);

// Register pages
import PgHomePage from 'prontogioco/app/pages/home';
import PgExplorePage from 'prontogioco/app/pages/explore';
import PgVenueDetailPage from 'prontogioco/app/pages/venues/detail';
import PgVenueEditor from 'prontogioco/app/pages/venues/editor';

Vue.component('pg-home-page', PgHomePage);
Vue.component('pg-explore-page', PgExplorePage);
Vue.component('pg-venue-detail-page', PgVenueDetailPage);
Vue.component('pg-venue-editor', PgVenueEditor);

// Startup VM
new Vue({
	el: '#app',

	data: {
		hasGeolocation: navigator.geolocation ? true : false
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
	}
});
