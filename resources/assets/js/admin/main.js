import $ from 'jquery';
import Vue from 'vue';

window.Vue = Vue;
window.$ = $;

// Pages
import maintainPage from './maintain.js';

new Vue({
	el: '#app',

	components: {
		'pga-maintain-page': maintainPage
	}
});