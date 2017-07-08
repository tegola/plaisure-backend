import _ from 'lodash';
import $ from 'jquery';
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
			importedVenue: pg.importedVenue || null,
			venueCategories: pg.venueCategories,
			machineTypes: pg.machineTypes,
			categories: pg.categories,
			mapCenter: {
				lat: pg.venue.geo_latitude || pg.config.defaultMapCenter.lat,
				lng: pg.venue.geo_longitude || pg.config.defaultMapCenter.lng
			},
			mapZoom: pg.venue.geo_latitude && pg.venue.geo_longitude ? 15 : 5,
			plans: pg.config.plans,
			selectedPlan: pg.venue.plan ? pg.venue.plan.short_name : null
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

				$.extend(this.venue, {
					address_street: result.streetName,
					address_number: result.streetNumber,
					address_city: result.city,
					address_postcode: result.zipcode,
					address_province: result.administrativeLevels.level2long.replace('Provincia di ', ''),
					address_region: result.administrativeLevels.level1long,
					address_country: result.country,
					geo_latitude: result.latitude,
					geo_longitude: result.longitude
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

			// If it's a custom plan, keep the current values but replace the
			// short name. 
			if (planName == 'custom') {
				this.venue.plan.name = 'Personalizzato';
				this.venue.plan.short_name = planName;
				return;
			}

			// Otherwise, copy over the plan settings
			const selectedPlan = this.plans.find(plan => {
				return plan.short_name == planName;
			});

			if (!this.venue.plan) this.venue.plan = {};
			_.assign(this.venue.plan, selectedPlan);
		},

		removePlan() {
			this.venue.plan = null;
			this.selectedPlan = null;
		}
	}
};