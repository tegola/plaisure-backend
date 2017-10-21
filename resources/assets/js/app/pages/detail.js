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
				scrollwheel: false,
				styles: [
					{ // No labels on POI
						'featureType': 'poi',
						'elementType': 'labels.text',
						'stylers': [{ 'visibility': 'off' }]
					}
				]
			},
			hoursExpanded: false
		};
	},

	computed: {
		hoursIcon() {
			return this.hoursExpanded ? 'chevron-up' : 'chevron-down';
		}
	},

	methods: {
		toggleHours() {
			this.hoursExpanded = !this.hoursExpanded;
		}
	}
};
