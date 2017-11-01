import { Map, Marker } from 'vue2-google-maps';
import PgLightbox from '../components/lightbox';

export default {
	name: 'pg-venue-detail-page',

	components: {
		'pg-map': Map,
		'pg-map-marker': Marker,
		'pg-lightbox': PgLightbox
	},

	data() {
		return {
			venue: pg.venue,
			lightboxIndex: 0,
			lightboxVisible: false,
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
		},

		lightboxImages() {
			const photos = this.venue.photos;

			if (!photos || !photos.length) return null;

			return photos.map(file => {
				return {
					caption: file.caption,
					url: file.resized_url,
					thumbnail_url: file.thumbnail_url
				};
			});
		}
	},

	methods: {
		showLightbox(index) {
			this.lightboxIndex = index;
			this.lightboxVisible = true;
		},

		closeLightbox() {
			this.lightboxVisible = false;
		},

		toggleHours() {
			this.hoursExpanded = !this.hoursExpanded;
		}
	}
};
