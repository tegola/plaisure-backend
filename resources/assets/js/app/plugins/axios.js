import Vue from 'vue';
import axios from 'axios';
import store from 'prontogioco/app/store';

const instance = axios.create({
	baseURL: '/api',
	headers: {
		'X-Requested-With': 'XMLHttpRequest'
	}
});

// Get access token from store
const accessToken = store.state.user.accessToken;
const refreshToken = store.state.user.refreshToken;

if (accessToken) instance.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;

// Refresh token interceptor
// https://gist.github.com/mkjiau/650013a99c341c9f23ca00ccb213db1c
let isRefreshing = false;
let refreshSubscribers = [];

const subscribeToTokenRefresh = cb => {
	refreshSubscribers.push(cb);
};
const onAccessTokenRefresh = refreshedAccessToken => {
	refreshSubscribers.map(cb => cb(refreshedAccessToken));
};

instance.interceptors.response.use(
	response => response,
	error => {
		const originalRequest = error.config;

		// Not an 'unauthorized' response, just run the rejection
		if (error.response.status !== 401) return Promise.reject(error);

		// No refresh token found, mark user as logged out
		if (!refreshToken) {
			store.commit('user/setTokens', {});
			return Promise.reject(error);
		}

		// Refresh token or logout
		if (!isRefreshing) {
			isRefreshing = true;

			instance.post('/auth/refresh', {
				refresh_token: refreshToken
			}).then(response => {
				// Stop if there's an error
				if (response.data.error) throw new Error(response.data.error);

				// Store tokens (login)
				const accessToken = response.data.access_token;
				const refreshToken = response.data.refresh_token;

				store.commit('user/setTokens', { accessToken, refreshToken });

				// Retry pending requests
				onAccessTokenRefresh(accessToken);

				isRefreshing = false;
			}).catch(error => {
				store.dispatch('user/logout', true);
				isRefreshing = false;
				Promise.reject(error);
			});
		}

		// Return new Promise with original request with updated headers and
		// cleaned up url (baseUrl is added again when retrying)
		return new Promise(resolve => {
			subscribeToTokenRefresh(refreshedAccessToken => {
				originalRequest.url = originalRequest.url.replace(/^\/api/, '');
				originalRequest.headers['Authorization'] = `Bearer ${refreshedAccessToken}`;
				resolve(axios(originalRequest));
			});
		});
	}
);

// Register on all Vue instances
Vue.prototype.$axios = instance;

// Export as named export for use in .js files
export default instance;