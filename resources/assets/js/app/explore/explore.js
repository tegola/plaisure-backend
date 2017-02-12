// FIXME: Port to ES6

// import $ from 'jquery';
import Vue from 'vue';
import * as VueGoogleMaps from 'vue2-google-maps';
import SearchForm from './search-form.vue';
import formatDistance from '../../utilities/format-distance';

Vue.use(VueGoogleMaps, {
	load: {
		key: pg.constants.googleMapsApiKey
	}
});

new Vue({
	el: '.page-content',

	components: {
		'pg-search-form': SearchForm
	},

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
		mapCenter(){
			if (this.lat && this.lng) {
				return {
					lat: this.lat,
					lng: this.lng
				};
			} else {
				// Default to italy
				return {
					lat: 41.2053112,
					lng: 8.0860841
				};
			}
		},
		mapZoom() {
			return (this.lat && this.lng) ? 15 : 5;
		}
	},

	filters: {
		formatDistance: formatDistance
	},

	watchers: {
		near() {
			this.lat = null;
			this.lng = null;
		}
	},

	methods: {
		loadMore() {
			console.log('loadMore');
		},

		select(venue) {
			this.currentVenue = venue;
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue);
		}
	}
});