import $ from 'jquery';
import Vue from 'vue';
import Icon from './components/icon.vue';

// Register Vue components
Vue.component('pg-icon', Icon);

// Support for showing geolocation controls
$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');

// Start tooltips
$('[data-toggle="tooltip"]').tooltip();
