import $ from 'jquery';
import { load, Map, Marker } from 'vue2-google-maps';
import { geocode } from '../../utilities/geocoder';

// Load Google Maps API
load(pg.config.googleMapsApiKey);

// Prepare page
export default {
	components: {
		'g-map': Map,
		'g-map-marker': Marker
	},

	data() {
		return {
			venue: pg.venue,
			venueCategories: pg.venueCategories,
			venueOriginalAddress: pg.venueOriginalAddress,
			machineTypes: pg.machineTypes,
			categories: pg.categories,
			mapCenter: {
				lat: pg.venue.geo_latitude || pg.config.defaultMapCenter.lat,
				lng: pg.venue.geo_longitude || pg.config.defaultMapCenter.lng
			},
			mapZoom: pg.venue.geo_latitude && pg.venue.geo_longitude ? 15 : 5
		};
	},

	methods: {
		geocode() {
			geocode(this.venueOriginalAddress, (error, results) => {
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
					address_province: result.administrativeLevels.level2short,
					address_region: result.administrativeLevels.level1Long,
					address_country: result.country,
					geo_latitude: result.latitude,
					geo_longitude: result.longitude
				});
			});
		},

		onMarkerDrag(location) {
			const latLng = location.latLng;

			this.venue.geo_latitude = latLng.lat().toFixed(6);
			this.venue.geo_longitude = latLng.lng().toFixed(6);
		}
	}
};