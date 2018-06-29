const storage = window.localStorage;
const isAuthenticated = () => {
	return (storage.accessToken && storage.refreshToken) ? true : false;
};

/**
 * Redirect to login if needed.
 * 
 * @param  {Route}    to
 * @param  {Route}    from
 * @param  {Function} next
 * @return void
 */
export const requireAuth = (to, from, next) => {
	if (!isAuthenticated()) {
		next({
			name: 'login',
			query: {
				redirect: to.fullPath
			}
		});
	} else {
		next();
	}
};

/**
 * Go to home page if already authenticated.
 * 
 * @param  {Route}    to
 * @param  {Route}    from
 * @param  {Function} next
 * @return void
 */
export const redirectIfAuthenticated = (to, from, next) => {
	if (isAuthenticated()) {
		next({
			name: 'home'
		});
	} else {
		next();
	}
};

export default {
	requireAuth,
	redirectIfAuthenticated
};