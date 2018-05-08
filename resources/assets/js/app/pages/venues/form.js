import { Map, Marker } from 'vue2-google-maps';
import Uploader from 'vue-upload-component';

export default {
	name: 'PgVenueFormPage',

	components: {
		'pg-map': Map,
		'pg-map-marker': Marker,
		'pg-uploader': Uploader
	},

	data() {
		return {
			venue: pg.venue,
			venueCategories: pg.venueCategories,
			venueVltPlatforms: pg.venueVltPlatforms,
			venuePayPerViewPlatforms: pg.venuePayPerViewPlatforms,

			categories: pg.categories,
			concessionaires: pg.concessionaires,
			vltPlatforms: pg.vltPlatforms,
			payPerViewPlatforms: pg.payPerViewPlatforms,

			mapCenter: {
				lat: pg.venue.geo_latitude || pg.app.defaultMapCenter.lat,
				lng: pg.venue.geo_longitude || pg.app.defaultMapCenter.lng
			},
			mapZoom: pg.venue.geo_latitude && pg.venue.geo_longitude ? 15 : 5,

			uploaderHeaders: {
				'X-CSRF-TOKEN': pg.app.csrfToken
			},
			uploaderFiles: [],

			selectedTab: 'general'
		};
	},

	methods: {
		onMarkerDrag(location) {
			const latLng = location.latLng;

			this.venue.geo_latitude = latLng.lat().toFixed(6);
			this.venue.geo_longitude = latLng.lng().toFixed(6);
		},

		onBusinessHoursInput(businessHours) {
			this.venue.business_hours = businessHours;
		},

		onUploaderFileInput(newFile, oldFile) {
			// Update
			if (newFile && oldFile) {
				// FIXME: Qui dovremmo controllare l'errore del server e
				// scriverlo nell'istanza del file, quindi mostrarlo
				if (newFile.response && newFile.response.file) {
					newFile.error = newFile.response.file[0];
					// newFile = this.$refs.uploader.update(newFile, { error: newFile.response.file });
				}

				// Upload successful
				if (newFile.success !== oldFile.success) {
					this.venue.photos.push(newFile.response);
					this.$refs.uploader.remove(newFile);
				}
			}

			// Automatic upload
			if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
				if (!this.$refs.uploader.active) {
					this.$refs.uploader.active = true;
				}
			}
		},

		deletePhoto(file) {
			if (!confirm('Sei sicuro di voler eliminare questa foto? Verrà eliminata quando salvi i dati della sala.')) return;
			this.venue.photos.splice(this.venue.photos.indexOf(file), 1);
		}
	}
}