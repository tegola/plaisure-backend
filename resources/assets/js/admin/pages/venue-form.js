import _clone from 'lodash/clone';
import _assign from 'lodash/assign';
import _extend from 'lodash/extend';
import { Map, Marker } from 'vue2-google-maps';
import { geocode } from 'prontogioco/utilities/geocoder';
import Uploader from 'vue-upload-component';

import PGABusinessHoursManager from 'prontogioco/admin/components/business-hours';

import constants from 'prontogioco/constants';

export default {
	name: 'pga-venue-form-page',

	components: {
		'pga-business-hours-manager': PGABusinessHoursManager,
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

			importedVenue: pg.importedVenue || null,
			importedVenueAddress: null,

			machineTypes: pg.machineTypes,
			categories: pg.categories,
			concessionaires: pg.concessionaires,
			vltPlatforms: pg.vltPlatforms,
			payPerViewPlatforms: pg.payPerViewPlatforms,

			mapCenter: {
				lat: pg.venue.geo_latitude || this.MAP_DEFAULT_CENTER.lat,
				lng: pg.venue.geo_longitude || this.MAP_DEFAULT_CENTER.lng
			},
			mapZoom: pg.venue.geo_latitude && pg.venue.geo_longitude ? 15 : 5,

			uploaderHeaders: {
				'X-CSRF-TOKEN': pg.csrfToken
			},
			uploaderFiles: [],

			plans: pg.plans,
			selectedPlan: pg.venue.plan ? pg.venue.plan.machine_name : null
		};
	},

	watch: {
		selectedPlan: 'onPlanSelection'
	},

	computed: {
		planFieldDisabled() {
			return this.selectedPlan != 'custom';
		}
	},

	methods: {
		geocode() {
			geocode(this.importedVenueAddress, (error, results) => {
				if (error) {
					alert("Non è stato possibile utilizzare Google Maps per trovare la posizione dell'attività.");
					return;
				}

				const result = results[0];
				const provinceRegex = /(Provincia di )|(Città Metropolitana di )/gi;

				_assign(this.venue, {
					address_line1: [result.streetName || '', + results.streetNumber || ''].join(' '),
					address_city: result.city || '',
					address_postcode: result.zipcode || '',
					address_province: result.administrativeLevels.level2long ? result.administrativeLevels.level2long.replace(provinceRegex, '') : '',
					address_region: result.administrativeLevels.level1long || '',
					country: result.country || '',
					geo_latitude: result.latitude || '',
					geo_longitude: result.longitude || ''
				});

				this.mapZoom = 15;
				this.mapCenter = {
					lat: result.latitude,
					lng: result.longitude
				};

				if (!result.streetName ||
					!result.streetNumber ||
					!result.city ||
					!result.zipcode ||
					!result.administrativeLevels.level2long ||
					!result.administrativeLevels.level1long ||
					!result.country ||
					!result.latitude ||
					!result.longitude) {
					alert('Google Maps non ha restituito tutti i dati. Cercali manualmente e inseriscili prima di salvare.');
				}
			});
		},

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
		},

		onPlanSelection(planName) {
			if (!planName) return;

			let newPlan;

			// If it's a custom plan, keep the current values but replace the
			// name. Otherwise, copy all plan settings.
			if (planName == 'custom') {
				newPlan = _clone(this.venue.plan);
				newPlan.name = 'Personalizzato',
				newPlan.machine_name = 'custom';
			} else {
				const selectedPlan = this.plans.find(plan => plan.machine_name == planName);
				newPlan = _clone(selectedPlan);
			}

			// Update venue with plan
			this.venue = _assign({}, this.venue, { plan: newPlan });
		},

		removePlan() {
			this.venue.plan = null;
			this.selectedPlan = null;
		}
	},

	beforeCreate() {
		_extend(this, constants);
	},

	mounted() {
		// Copy imported venue address for searching purposes
		const iv = this.importedVenue;
		this.importedVenueAddress = iv ? [iv.address_1, iv.address_2].join(' ') : null;
	}
};