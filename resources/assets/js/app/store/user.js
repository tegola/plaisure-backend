import axios from 'prontogioco/app/plugins/axios';

const storage = window.localStorage;

export default {
	namespaced: true,

	state: {
		accessToken: storage.accessToken,
		refreshToken: storage.refreshToken,
		user: null,
		coords: null,
		venues: []
	},

	mutations: {
		setTokens: (state, { accessToken, refreshToken }) => {
			state.accessToken = accessToken;
			state.refreshToken = refreshToken;

			if (accessToken && refreshToken) {
				storage.accessToken = accessToken;
				storage.refreshToken = refreshToken;
				axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
			} else {
				storage.removeItem('accessToken');
				storage.removeItem('refreshToken');
				axios.defaults.headers.common['Authorization'] = null;
			}
		},

		setUser: (state, user) => {
			state.user = user;
		},

		setCoords: (state, coords) => {
			state.coords = coords;
		},

		setVenues: (state, venues = []) => {
			state.venues = venues;
		}
	},

	getters: {
		isAuthenticated(state) {
			return (state.accessToken && state.refreshToken) ? true : false;
		},

		hasBillingInfo(state) {
			const u = state.user;

			if (!u) return false;

			return Boolean(u.legal_name
				&& (u.address_line1 || u.address_line2)
				&& u.address_city
				&& u.address_postcode
				&& u.address_region
				&& u.address_country
				&& u.vat_number);
		},

		hasCreditCard(state) {
			return Boolean(state.user && state.user.card_brand);
		}
	},

	actions: {
		register: ({ dispatch, commit }, formData) => {
			return axios.post('/auth/register', formData).then(response => {
				if (response.error) throw new Error(response.error);

				commit('setTokens', {
					accessToken: response.data.access_token,
					refreshToken: response.data.refresh_token
				});
			}).then(() => {
				dispatch('fetch');
			});
		},

		login: ({ dispatch, commit }, { email, password }) => {
			return axios.post('/auth/login', { email, password }).then(response => {
				if (response.data.error) throw new Error(response.data.error);

				commit('setTokens', {
					accessToken: response.data.access_token,
					refreshToken: response.data.refresh_token
				});
			}).then(() => {
				dispatch('fetch');
			});
		},

		logout: ({ commit }, local = false) => {
			const tokens = {
				accessToken: null,
				refreshToken: null
			};

			if (local) {
				commit('setTokens', tokens);
				commit('setUser', null);
			} else {
				return axios.post('/auth/logout').then(() => {
					commit('setTokens', tokens);
					commit('setUser', null);
				});
			}
		},

		fetch: ({ commit }) => {
			return axios.get('/user').then(response => {
				commit('setUser', response.data.user);
				commit('setVenues', response.data.venues);
			});
		},

		update: ({ commit }, formData) => {
			return axios.post('/user', formData).then(response => {
				commit('setUser', response.data.user);
			});
		},

		findCoords: ({ commit }) => {
			return new Promise((resolve, reject) => {
				navigator.geolocation.getCurrentPosition(position => {
					const coords = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					commit('setCoords', coords);
					resolve(coords);
				},
				reject,
				{
					timeout: 10 * 1000, // 10 secs
					maximumAge: 5 * 60 * 1000 // last 5 minutes
				});
			});
		}
	}
};
