import { required, url, email } from 'vuelidate/lib/validators'

export default {
	venue: {
		name: {
			required
		},

		category_ids: {
			required
		},

		address: {
			street: {
				required
			},
			number: {
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
			},
			tripadvisor: {
				url
			}
		}
	}
}