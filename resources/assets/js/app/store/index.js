import Vue from 'vue';
import Vuex from 'vuex';

import user from './user';
import venueDetail from './venue-detail';

Vue.use(Vuex);

const store = new Vuex.Store({
	modules: {
		user,
		venueDetail
	}
});

export default store;