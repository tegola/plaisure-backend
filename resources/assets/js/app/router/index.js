import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import setTitle from 'prontogioco/utilities/set-title';

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes,
	scrollBehaviour: () => ({ y: 0 })
});

router.afterEach((to) => {
	const metaTitle = to.meta.title || null;
	const title = typeof metaTitle === 'function' ? metaTitle(to) : metaTitle;

	setTitle(title);
});

export default router;