//import $ from 'jquery';
import * as geocoder from '../../utilities/geocoder';
import InputTypeahead from '../components/input-typeahead.vue';
import Icon from '../components/icon.vue';

export default {
	components: {
		'pg-input-typeahead': InputTypeahead,
		'pg-icon': Icon,
	},

	props: {
		action: {
			type: String,
			required: true
		},
		lat: {
			type: Number,
			default: this.lat
		},
		lng: {
			type: Number,
			default: this.lng
		},
		what: {
			type: String,
			default: this.what
		},
		near: {
			type: String,
			default: this.near
		}
	},

	data() {
		return {
			isLocating: false,
			locationFound: false
		};
	},

	computed: {
		locateButtonIcon() {
			return this.isLocating ? 'circle-outline-notch' : this.locationFound ? 'location' : 'location-outline';
		},
		isLocateButtonDisabled() {
			return this.isLocating || this.locationFound;
		}
	},

	methods: {
		locate() {
			const self = this;
			this.isLocating = true;

			navigator.geolocation.getCurrentPosition(
				(position) => {
					console.log('get current position');
					self.lat = position.coords.latitude;
					self.lng = position.coords.longitude;

					// Find city name and use it to fill the City field
					geocoder.reverse(self.lat, self.lng, (error, location) => {
						if (error) return;

						self.near = location.administrativeLevels.level3long; // FIXME: Use street and city
						self.isLocating = false;
						self.locationFound = true;

						self.$emit('locate', self.lat, self.lng, self.near);
					});
				},
				() => {
					self.isLocating = false;
					self.locationFound = false;
					alert('Non è stato possibile trovare la tua posizione.');
				},
				{
					timeout: 10 * 1000, // 10 secs
					maximumAge: 5 * 60 * 1000 // last 5 minutes
				}
			);
		}
	},

	mounted() {
		const self = this;

		// If no location is set, find a generic one using IP info
		if (!this.lat || !this.lng || !this.near) {
			geocoder.geocode((error, location) => {
				if (!location) return;

				self.lat = location.latitude;
				self.lng = location.longitude;
				self.near = location.city;

				self.$emit('locate', self.lat, self.lng, self.near);
			});
		}
	}
};