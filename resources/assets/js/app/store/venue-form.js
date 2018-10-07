import _extend from 'lodash/extend';
import _cloneDeep from 'lodash/cloneDeep';
import _isEqual from 'lodash/isEqual';
import axios from 'prontogioco/app/plugins/axios';

export default {
	namespaced: true,

	state() {
		return {
			venueId: null,
			venue: null,
			originalVenue: null,
			categories: [],
			concessionaires: [],
			vltPlatforms: [],
			payPerViewPlatforms: []
		};
	},

	getters: {
		isSaved: state => {
			return _isEqual(state.venue, state.originalVenue);
		}
	},

	mutations: {
		setVenueId: (state, venueId) => {
			state.venueId = venueId;
		},

		setVenue: (state, venue) => {
			state.venue = venue;
		},

		setOriginalVenue: (state, originalVenue) => {
			state.originalVenue = originalVenue;
		},

		setSupportData: (state, data) => {
			state.categories = data.categories;
			state.concessionaires = data.concessionaires;
			state.vltPlatforms = data.vltPlatforms;
			state.payPerViewPlatforms = data.payPerViewPlatforms;
		},

		setVenueField: (state, { field, value }) => {
			if (field === 'amenities') {
				// Avoid changing the entire object or it will trigger a loop
				// FIXME: ...
				_extend(state.venue.amenities, value);
			} else {
				state.venue[field] = value;
			}
		},

		setVenueAmenityField: (state, { amenity, value }) => {
			state.venue.amenities[amenity] = value;
		}
	},

	actions: {
		load: ({ state, commit }) => {
			const url = [
				'/venues',
				state.venueId ? `/${state.venueId}/edit` : '/add'
			].join('');

			return axios.get(url).then(({ data }) => {
				commit('setVenue', data.venue);
				commit('setOriginalVenue', _cloneDeep(data.venue));
				commit('setSupportData', {
					concessionaires: data.concessionaires,
					categories: data.categories,
					payPerViewPlatforms: data.payPerViewPlatforms,
					vltPlatforms: data.vltPlatforms
				});
			});
		},

		save: ({ state, commit }) => {
			// Prepare url
			let url = '/venues';
			if (state.venueId) url += `/${state.venueId}`;

			return axios.post(url, state.venue)
				.then(() => {
					// Keep the original copy so it will appear as saved
					console.log('then in store');
					commit('setOriginalVenue', state.venue);
				})
				.catch(() => {})
				.then(() => {
					this.saving = false;
				});
		}
	}
};