import Vue from 'vue';
import VueAnalytics from 'vue-analytics';
import { GOOGLE_ANALYTICS_CODE } from 'prontogioco/constants';
import router from 'prontogioco/app/router';

if (process.env.NODE_ENV == 'production') {
	Vue.use(VueAnalytics, {
		id: GOOGLE_ANALYTICS_CODE,
		router
	});
}