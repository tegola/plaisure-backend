import Vue from 'vue';
import { Map, Marker } from 'vue2-google-maps';

Vue.component('pg-venue-detail-page', {
	components: {
		'gmap-map': Map,
		'gmap-marker': Marker
	},

	data() {
		return {
			lat: pg.lat,
			lng: pg.lng,
			what: pg.what,
			near: pg.near,
			mapOptions: {
				disableDefaultUI: true,
				draggable: false,
				scrollwheel: false
			}
		};
	}
});
