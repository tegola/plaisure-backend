// Load polyfills
import 'classlist-polyfill';
import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media

// Load local libs
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';
import { load as loadGMaps } from 'vue2-google-maps';
import BsTooltip from 'bootstrap-vue/es/components/tooltip/tooltip';
import BsTooltipDirective from 'bootstrap-vue/es/directives/tooltip/tooltip';
import PgLogo from './components/logo';
import PgNavbar from './components/navbar';
import PgIcon from './components/icon';
import PgHomePage from './pages/home';
import PgExplorePage from './pages/explore';
import PgVenueDetailPage from './pages/detail';

// Load Google Maps API
loadGMaps({
	key: pg.config.googleMapsApiKey,
	language: pg.config.locale,
	region: pg.config.locale,
	libraries: 'places'
});

// Register Vue plugins, directives and components
Vue.use(VueMatchMedia);

Vue.directive('bs-tooltip', BsTooltipDirective);

Vue.component('bs-tooltip', BsTooltip);
Vue.component('pg-logo', PgLogo);
Vue.component('pg-navbar', PgNavbar);
Vue.component('pg-icon', PgIcon);

// Startup VM
new Vue({
	el: '#app',

	components: {
		PgHomePage,
		PgExplorePage,
		PgVenueDetailPage
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
	},

	mounted() {
		// Support for showing geolocation controls
		document.documentElement.classList.add(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');
	}
});
