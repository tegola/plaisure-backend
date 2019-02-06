import {
	required,
	minValue,
	minLength,
	numeric,
	url,
	email
} from 'vuelidate/lib/validators';

export default {
	venue: {
		name: {
			required
		},
		surface_size: {
			required,
			numeric,
			minValue: minValue(1)
		},
		vlt_machine_count: {
			numeric,
			minValue: minValue(0)
		},
		awp_machine_count: {
			numeric,
			minValue: minValue(0)
		},
		seating_capacity: {
			numeric,
			minValue: minValue(0)
		},
		parking_capacity: {
			numeric,
			minValue: minValue(0)
		},
		category_ids: {
			required,
			minLength: minLength(1)
		},
		address: {
			line1: {
				required
			},
			postcode: {
				required
			},
			city: {
				required
			},
			province: {
				required
			}
		},
		coords: {
			lat: {
				required
			},
			lng: {
				required
			}
		},
		contacts: {
			email: {
				email
			}
		},
		urls: {
			site: {
				url
			},
			online_casino: {
				url
			},
			facebook: {
				url
			}
		}
	}
};