import Vue from 'vue';

import './plugins/axios';
import './plugins/meta';
import './plugins/maps';
import './plugins/analytics';

import router from './router';
import store from './store';
import i18n from './lang';
import mq from './plugins/match-media';

import BTooltipDirective from 'bootstrap-vue/es/directives/tooltip/tooltip';
import BScrollspyDirective from 'bootstrap-vue/es/directives/scrollspy/scrollspy';

import PgLogo from 'prontogioco/app/components/logo';
import PgNavbar from 'prontogioco/app/components/navbar';
import PgIcon from 'prontogioco/app/components/icon';
import PgPageFooter from 'prontogioco/app/components/page-footer';
import PgApp from './main';

import { APP_NAME } from 'prontogioco/constants';

// Register common directives
Vue.directive('b-tooltip', BTooltipDirective);
Vue.directive('b-scrollspy', BScrollspyDirective);

// Register common components
Vue.component('pg-logo', PgLogo);
Vue.component('pg-navbar', PgNavbar);
Vue.component('pg-icon', PgIcon);
Vue.component('pg-page-footer', PgPageFooter);

// Startup root VM
new Vue({
	el: '#app',

	components: {
		PgApp
	},

	router,

	store,

	i18n,

	mq,

	data: {
		hasGeolocation: Boolean(navigator.geolocation)
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
			this.$store.dispatch('user/fetch');
		}
	}
});
