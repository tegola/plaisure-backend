import Vue from 'vue';
import Vuex from 'vuex';

import venueDetail from './venue-detail';

Vue.use(Vuex);

const store = new Vuex.Store({
	modules: {
		venueDetail
	}
});

export default store;