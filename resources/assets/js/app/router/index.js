import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import setTitle from 'prontogioco/utilities/set-title';

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes,
	scrollBehavior: (to) => {
		if (to.hash) {
			return {
				selector: to.hash,
				offset: {
					y: 50 // Scroll a bit more to the top
				}
			};
		} else {
			return {
				y: 0
			};
		}
	}
});

// Go to login if needed
router.beforeEach((to, from, next) => {
	const storage = window.localStorage;
	const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
	const isAuthenticated = (storage.accessToken && storage.refreshToken) ? true : false;

	if (requiresAuth && !isAuthenticated) {
		next({
			name: 'login',
			query: {
				redirect: to.fullPath
			}
		});
	} else {
		next();
	}
});

// Set page title from meta data
router.afterEach(to => {
	const metaTitle = to.meta.title || null;
	const title = typeof metaTitle === 'function' ? metaTitle(to) : metaTitle;

	setTitle(title);
});

export default router;