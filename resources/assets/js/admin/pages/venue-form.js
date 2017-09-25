import $ from 'jquery';
import _clone from 'lodash/clone';
import _assign from 'lodash/assign';
import { Map, Marker } from 'vue2-google-maps';
import { geocode } from '../../utilities/geocoder';

export default {
	name: 'pga-venue-form-page',

	components: {
		'pg-map': Map,
		'pg-map-marker': Marker
	},

	data() {
		return {
			venue: pg.venue,
			venueCategories: pg.venueCategories,
			venueVltPlatforms: pg.venueVltPlatforms,
			venuePayPerViewPlatforms: pg.venuePayPerViewPlatforms,
			importedVenue: pg.importedVenue || null,
			machineTypes: pg.machineTypes,
			categories: pg.categories,
			concessionaires: pg.concessionaires,
			vltPlatforms: pg.vltPlatforms,
			payPerViewPlatforms: pg.payPerViewPlatforms,
			mapCenter: {
				lat: pg.venue.geo_latitude || pg.config.defaultMapCenter.lat,
				lng: pg.venue.geo_longitude || pg.config.defaultMapCenter.lng
			},
			mapZoom: pg.venue.geo_latitude && pg.venue.geo_longitude ? 15 : 5,
			plans: pg.config.plans,
			selectedPlan: pg.venue.plan ? pg.venue.plan.machine_name : null
		};
	},

	watch: {
		selectedPlan: 'onPlanSelection'
	},

	computed: {
		importedVenueAddress() {
			const iv = this.importedVenue;

			return iv ? [iv.address_1, iv.address_2].join(' ') : null;
		},

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


				$.extend(this.venue, {
					address_street: result.streetName || '',
					address_number: result.streetNumber || '',
					address_city: result.city || '',
					address_postcode: result.zipcode || '',
					address_province: result.administrativeLevels.level2long ? result.administrativeLevels.level2long.replace('Provincia di ', '') : '',
					address_region: result.administrativeLevels.level1long || '',
					address_country: result.country || '',
					geo_latitude: result.latitude || '',
					geo_longitude: result.longitude || ''
				});

				this.mapCenter = {
					lat: result.latitude,
					lng: result.longitude
				};

				this.mapZoom = 15;
			});
		},

		onMarkerDrag(location) {
			const latLng = location.latLng;

			this.venue.geo_latitude = latLng.lat().toFixed(6);
			this.venue.geo_longitude = latLng.lng().toFixed(6);
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
	}
};