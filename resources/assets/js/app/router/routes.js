export default [
	// Auth -------------------------------------------------------------------
	{
		path: '/login',
		name: 'login',
		component: () => import('prontogioco/app/pages/login'),
		props: route => ({ redirect: route.query.redirect })
	},

	// Home -------------------------------------------------------------------
	{
		path: '/',
		name: 'home',
		component: () => import('prontogioco/app/pages/home')
	},

	// Explore ----------------------------------------------------------------
	{
		path: '/venues/explore',
		name: 'venues.explore',
		component: () => import('prontogioco/app/pages/venues/explore'),
		meta: {
			title: 'Ricerca'
		}
	},

	// Add venue --------------------------------------------------------------
	{
		path: '/venues/add',
		name: 'venues.add',
		component: () => import('prontogioco/app/pages/venues/form'),
		props: true,
		meta: {
			requiresAuth: true
		}
	},

	// Venue detail -----------------------------------------------------------
	{
		path: '/venues/:venueId',
		name: 'venues.detail',
		component: () => import('prontogioco/app/pages/venues/detail/main.vue'),
		props: true
	},

	// Edit venue -------------------------------------------------------------
	{
		path: '/venues/:venueId/edit',
		name: 'venues.edit',
		component: () => import('prontogioco/app/pages/venues/form'),
		props: true,
		meta: {
			requiresAuth: true
		}
	},

	// Promote ----------------------------------------------------------------
	{
		path: '/promote',
		name: 'promote',
		component: () => import('prontogioco/app/pages/promote'),
		meta: {
			title: 'Promuovi la tua attività, è gratis!'
		}
	},

	// About ------------------------------------------------------------------
	{
		path: '/about',
		name: 'about',
		component: () => import('prontogioco/app/pages/about'),
		meta: {
			title: 'Chi siamo'
		}
	},

	// Play responsibly -------------------------------------------------------
	{
		path: '/play-responsibly',
		name: 'playResponsibly',
		component: () => import('prontogioco/app/pages/play-responsibly'),
		meta: {
			title: 'Gioca responsabilmente'
		}
	},

	// Catch all --------------------------------------------------------------
	{
	    path: '*',
	    component: () => import('prontogioco/app/pages/error.vue')
	}
]