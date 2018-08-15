import 'core-js/es6/set'; // For vue-match-media
import 'core-js/fn/array/from'; // For vue-match-media
import Vue from 'vue';
import VueMatchMedia from 'vue-match-media/dist';

Vue.use(VueMatchMedia);

export default {
	constrained: '(max-width: 767px)',
	comfortable: '(min-width: 768px)'
};