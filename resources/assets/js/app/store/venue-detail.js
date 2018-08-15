import axios from 'prontogioco/app/plugins/axios';
import capitalize from 'capitalize';

const indexToDayName = index => {
	// Create any date but se the right day of the week
	const date = new Date();
	date.setDate(date.getDate() - date.getDay() + index);

	const name = date.toLocaleDateString(undefined, { weekday: 'long' });

	return capitalize(name);
};

export default {
	namespaced: true,

	state: {
		venue: null,
		nearbyVenues: [],
		structuredData: null
	},

	getters: {
		isOpen: (state) => {
			const hours = state.venue && state.venue.business_hours ? state.venue.business_hours : [];

			if (!hours.length) return false;

			const now = new Date();
			const time = now.toTimeString().split(' ')[0];
			const today = hours[now.getDay()];
			const yesterday = hours[new Date(now.getDate() - 1).getDay()];

			// Find a match in today's normal hours
			if (today.length == 2) {
				if (today[0] <= time && today[1] >= time) return true;
			} else if (today.length == 4) {
				if (today[0] <= time && today[1] >= time ||
					today[2] <= time && today[3] >= time) return true;
			}

			// Find a match in today's inverted hours, meaning the closing
			// time is in late night, and so is smaller than the opening
			// time
			if (today.length == 2) {
				if (today[1] < today[0] && today[0] <= time) return true;
			} else if (today.length == 4) {
				if (today[3] < today[0] && today[0] <= time) return true;
			}

			// Find a match in yesterday's hours by getting the previous
			// week day
			if (today.length == 2) {
				if (yesterday[1] < yesterday[0] && yesterday[1] >= time) return true;
			} else if (today.length == 4) {
				if (yesterday[3] < yesterday[0] && yesterday[3] >= time) return true;
			}

			return false;
		},

		businessHoursRows: (state) => {
			const rows = [];

			if (!state.venue) return rows;

			return state.venue.business_hours.map((hours, index) => {
				return {
					day: indexToDayName(index),
					hours: hours
				};
			});
		},

		vltPlatformNames: (state) => {
			if (state.venue) return state.venue.vlt_platforms.map(platform => platform.name).join(', ');
		},

		payPerViewPlatformNames: (state) => {
			if (state.venue) return state.venue.pay_per_view_platforms.map(platform => platform.name).join(', ');
		},

		hasJackpots: (state) => {
			if (!state.venue) return;

			const j = state.venue.jackpots;
			return j[1].value || j[2].value || j[3].value;
		},

		hasContacts: (state) => {
			if (!state.venue) return false;

			const c = state.venue.contacts;
			return c.phone || c.email || c.facebook || c.twitter;
		},

		hasUrls: (state) => {
			if (!state.venue) return false;

			const u = state.venue.urls;
			return u.site || u.facebook;
		},

		googleMapsUrl: (state) => {
			if (!state.venue) return null;

			const baseUrl = 'https://www.google.com/maps/dir/?api=1&map_action=map&destination=';
			const address = encodeURIComponent(state.venue.address.long);
			return `${baseUrl}${address}`;
		},

		facebookMessengerUrl: (state) => {
			if (!state.venue || !state.venue.contacts.facebook) return null;

			return `https://www.messenger.com/t/${state.venue.contacts.facebook}`;
		},

		twitterUrl: (state) => {
			if (!state.venue || !state.venue.contacts.twitter) return null;

			return `https://www.twitter.com/${state.venue.contacts.twitter}`;
		},

		readableSiteUrl: (state) => {
			if (!state.venue || !state.venue.urls.site) return null;

			let parser = document.createElement('a');
			parser.href = state.venue.urls.site;

			return parser.hostname.replace('www.', '');
		}
	},

	mutations: {
		setVenue: (state, venue) => {
			state.venue = venue;
		},

		setNearbyVenues: (state, nearbyVenues = []) => {
			state.nearbyVenues = nearbyVenues;
		},

		setStructuredData: (state, structuredData) => {
			state.structuredData = structuredData;
		}
	},

	actions: {
		load: ({ commit }, venueId) => {
			// this.loading = true;
			const request = axios.get(`/venues/${venueId}`);

			request.then(({ data }) => {
				commit('setVenue', data.venue);
				commit('setNearbyVenues', data.nearbyVenues);
				commit('setStructuredData', data.structuredData);
			}).catch(() => {}).then(() => {
				// this.loading = false;
			});

			return request;
		}
	}
};
