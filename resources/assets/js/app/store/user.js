import axios from 'prontogioco/app/axios';

const storage = window.localStorage;

export default {
	namespaced: true,

	state: {
		accessToken: storage.accessToken,
		refreshToken: storage.refreshToken,
		user: null,
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

		setUser: (state, user = null) => {
			state.user = user;
		},

		setVenues: (state, venues = []) => {
			state.venues = venues;
		},
	},

	getters: {
		isAuthenticated(state) {
			return (state.accessToken && state.refreshToken) ? true : false;
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
				dispatch('getData');
			});
		},

		login: ({ dispatch, commit }, { email, password }) => {
			return axios.post('/auth/login', { email, password }).then(response => {
				if (response.error) throw new Error(response.error);

				commit('setTokens', {
					accessToken: response.data.access_token,
					refreshToken: response.data.refresh_token
				});
			}).then(() => {
				dispatch('getData');
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

		getData: ({ commit }) => {
			return axios.get('/user').then(response => {
				commit('setUser', response.data.user);
				commit('setVenues', response.data.venues);
			});
		},

		update: ({ commit }, formData) => {
			return axios.post('/user', formData).then(response => {
				commit('setUser', response.data.user);
			});
		}
	}
};
