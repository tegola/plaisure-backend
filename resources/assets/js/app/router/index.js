import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';

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

export default router;