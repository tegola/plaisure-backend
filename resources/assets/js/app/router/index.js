import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import headful from 'prontogioco/app/plugins/headful';

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

// Set page title from meta data
router.afterEach(to => {
	const metaTitle = to.meta.title || null;
	const title = typeof metaTitle === 'function' ? metaTitle(to) : metaTitle;

	headful({ title });
});

export default router;