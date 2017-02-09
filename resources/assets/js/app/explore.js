// FIXME: Port to ES6

import * as VueGoogleMaps from 'vue2-google-maps';
import Vue from 'vue';

import formatDistance from '../utilities/format-distance';

Vue.use(VueGoogleMaps, {
	load: {
		key: pg.constants.googleMapsApiKey
	}
});

new Vue({
	el: '.page-content',

	data: {
		lat: pg.lat,
		lng: pg.lng,
		what: pg.what,
		near: pg.near,
		venues: pg.venues,
		mapOptions: {
			mapTypeControl: false,
			streetViewControl: false
		},
		currentVenue: null
	},

	computed: {
		center: function(){
			if (this.lat && this.lng) {
				return {
					lat: this.lat,
					lng: this.lng
				};
			} else {
				return null;
			}
		}
	},

	filters: {
		formatDistance: formatDistance
	},

	watchers: {
		near: function() {
			this.lat = null;
			this.lng = null;
		}
	},

	methods: {
		loadMore: function() {
			console.log('loadMore');
		},

		select: function(venue) {
			this.currentVenue = venue;
		},

		toggleFavorite: function(venue) {
			console.log('aggiungo ai preferiti', venue);
		}
	}
});