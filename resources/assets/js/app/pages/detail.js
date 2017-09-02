import { Map, Marker } from 'vue2-google-maps';

export default {
	name: 'pg-venue-detail-page',

	components: {
		'pg-map': Map,
		'pg-map-marker': Marker
	},

	data() {
		return {
			mapOptions: {
				disableDefaultUI: true,
				draggable: false,
				scrollwheel: false
			}
		};
	}
};
