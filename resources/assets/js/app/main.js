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
import PgLogo from 'prontogioco/app/components/logo';
import PgNavbar from 'prontogioco/app/components/navbar';
import PgIcon from 'prontogioco/app/components/icon';
import PgHomePage from 'prontogioco/app/pages/home';
import PgExplorePage from 'prontogioco/app/pages/explore';
import PgVenueDetailPage from 'prontogioco/app/pages/detail';

// Load Google Maps API
loadGMaps({
	key: pg.app.googleMapsApiKey,
	language: pg.app.locale,
	region: pg.app.locale,
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
