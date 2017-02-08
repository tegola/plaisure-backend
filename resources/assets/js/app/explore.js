// FIXME: Port to ES6

import * as VueGoogleMaps from 'vue2-google-maps';
import Vue from 'vue';

Vue.use(VueGoogleMaps, {
	load: {
		key: 'AIzaSyC7HUu36wqXlH_E27AMOFFF9v7t1809Upk' // FIXME: Load from laravel config
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
		singularOrPlural(count, singularName, pluralName) {
			return parseInt(count) == 1 ? singularName : pluralName;
		},
		formatDistance: function(distance) {
			if (!distance) return null;

			if (distance > 10) return Math.round(distance) + ' km';
			if (distance > 1) return distance.toFixed(1) + ' km';
			if (distance < 1) return Math.round(distance * 100) + ' m';
		}
	},

	watchers: {
		near: function(newNear) {
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