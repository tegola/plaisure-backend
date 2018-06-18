// Load polyfills
import 'classlist-polyfill';
import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media

// Setup Vue and plugins
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';

Vue.use(VueMatchMedia);

// Setup Google Analytics
import './analytics';

// Setup Axios
import './axios';

// Load Google Maps API
import { load as loadGMaps } from 'vue2-google-maps';
import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';
loadGMaps({
	key: GOOGLE_MAPS_API_KEY,
	language: 'it', // FIXME: Use user locale
	region: 'it', // FIXME: Use user locale
	libraries: 'places'
});

// Register directives
import BTooltipDirective from 'bootstrap-vue/es/directives/tooltip/tooltip';

Vue.directive('b-tooltip', BTooltipDirective);

// Register common components
import PgLogo from 'prontogioco/app/components/logo';
import PgNavbar from 'prontogioco/app/components/navbar';
import PgIcon from 'prontogioco/app/components/icon';
import PgPageFooter from 'prontogioco/app/components/page-footer';

Vue.component('pg-logo', PgLogo);
Vue.component('pg-navbar', PgNavbar);
Vue.component('pg-icon', PgIcon);
Vue.component('pg-page-footer', PgPageFooter);

// Init router and store
import router from './router';
import store from './store';

// Startup VM
new Vue({
	el: '#app',

	router,

	store,

	data: {
		hasGeolocation: navigator.geolocation ? true : false
	},

	mq: {
		constrained: '(max-width: 767px)',
		comfortable: '(min-width: 768px)'
	}
});
