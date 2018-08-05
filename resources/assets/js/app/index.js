// Load polyfills
import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media

// Constants
import { APP_NAME } from 'prontogioco/constants';

// Setup Vue and plugins
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';

Vue.use(VueMatchMedia);

// Setup Axios, Google Maps and Analytics
import './plugins/axios';
import './plugins/meta';
import './plugins/maps';
import './plugins/analytics';

// Init router, store and i18n
import router from './router';
import store from './store';
import i18n from './lang';

import BTooltipDirective from 'bootstrap-vue/es/directives/tooltip/tooltip';

import PgLogo from 'prontogioco/app/components/logo';
import PgNavbar from 'prontogioco/app/components/navbar';
import PgIcon from 'prontogioco/app/components/icon';
import PgPageFooter from 'prontogioco/app/components/page-footer';

// Register directives
Vue.directive('b-tooltip', BTooltipDirective);

// Register common components
Vue.component('pg-logo', PgLogo);
Vue.component('pg-navbar', PgNavbar);
Vue.component('pg-icon', PgIcon);
Vue.component('pg-page-footer', PgPageFooter);

import PgApp from './main';

// Startup VM
new Vue({
	el: '#app',

	components: {
		PgApp
	},

	router,

	store,

	i18n,

	mq: {
		constrained: '(max-width: 767px)',
		comfortable: '(min-width: 768px)'
	},

	data: {
		hasGeolocation: navigator.geolocation ? true : false
	},

	meta() {
		return {
			titleTemplate: (titleChunk) => titleChunk ? `${titleChunk} - ${APP_NAME}` : APP_NAME,
			htmlAttrs: {
				lang: i18n.locale
			}
		};
	},

	computed: {
		userIsAuthenticated() {
			return this.$store.getters['user/isAuthenticated'];
		}
	},

	watch: {
		userIsAuthenticated(newValue) {
			if (!newValue) this.$router.push({ name: 'login' });
		}
	},

	created() {
		// Automatically get user data
		if (this.userIsAuthenticated) {
			this.$store.dispatch('user/getData');
		}
	}
});
