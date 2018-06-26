import axios from 'prontogioco/app/axios';

const storage = window.localStorage;

export default {
	namespaced: true,

	state: {
		accessToken: storage.accessToken,
		refreshToken: storage.refreshToken
	},

	mutations: {
		setTokens: (state, { accessToken, refreshToken }) => {
			state.accessToken = accessToken;
			state.refreshToken = refreshToken;
		}
	},

	getters: {
		isAuthenticated(state) {
			return (state.accessToken && state.refreshToken) ? true : false;
		}
	},

	actions: {
		login: ({ commit }, { accessToken, refreshToken }) => {
			commit('setTokens', { accessToken, refreshToken });

			storage.accessToken = accessToken;
			storage.refreshToken = refreshToken;
			axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
		},

		logout: ({ commit }) => {
			commit('setTokens', {
				accessToken: null,
				refreshToken: null
			});

			storage.removeItem('accessToken');
			storage.removeItem('refreshToken');
			axios.defaults.headers.common['Authorization'] = null;
		}
	}
};
