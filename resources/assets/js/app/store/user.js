import axios from 'prontogioco/app/axios';

const storage = window.localStorage;

export default {
	namespaced: true,

	state: {
		accessToken: storage.accessToken,
		refreshToken: storage.refreshToken,
		data: null
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

		setData: (state, data = null) => {
			state.data = data;
		}
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
				dispatch('refreshData');
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
				dispatch('refreshData');
			});
		},

		logout: ({ commit }) => {
			return axios.post('/auth/logout').then(() => {
				commit('setTokens', {
					accessToken: null,
					refreshToken: null
				});
				commit('setData', null);
			});
		},

		refreshData: ({ commit }) => {
			return axios.get('/user').then(response => {
				commit('setData', response.data);
			});
		},
	}
};
