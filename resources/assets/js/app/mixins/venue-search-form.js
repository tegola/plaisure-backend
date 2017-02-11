import $ from 'jquery';
import geocode from '../../utilities/geocoder';

export default {
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
		locationInputIcon() {
			return this.isLocating ? 'circle-outline-notch' : this.locationFound ? 'location' : 'location-outline';
		},
		isLocationInputDisabled() {
			return this.isLocating || this.locationFound;
		}
	},

	methods: {
		locate() {
			const self = this;
			this.isLocating = true;

			navigator.geolocation.getCurrentPosition(
				(position) => {
					self.lat = position.coords.latitude;
					self.lng = position.coords.longitude;

					// Find city name and use it to fill the City field
					geocode(self.lat, self.lng, (error, location) => {
						if (error) return;

						self.near = location.administrativeLevels.level3long;
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
			$.get('https://freegeoip.net/json/').then((location) => {
				if (!location) return;

				self.lat = location.latitude;
				self.lng = location.longitude;
				self.near = location.city;

				self.$emit('locate', self.lat, self.lng, self.near);
			});
		}
	}
};