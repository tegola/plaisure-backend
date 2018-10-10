import { requireAuth, redirectIfAuthenticated } from './guards';

export default [
	// Home -------------------------------------------------------------------
	{
		path: '/',
		name: 'home',
		component: () => import('prontogioco/app/pages/home')
	},

	// Login/register ---------------------------------------------------------
	{
		path: '/login',
		name: 'login',
		component: () => import('prontogioco/app/pages/auth/login'),
		props: route => ({ redirect: route.query.redirect }),
		beforeEnter: redirectIfAuthenticated
	},
	{
		path: '/register',
		name: 'register',
		component: () => import('prontogioco/app/pages/auth/register'),
		props: route => ({ redirect: route.query.redirect }),
		beforeEnter: redirectIfAuthenticated
	},

	// Password reset
	{
		path: '/password/forgot',
		name: 'password.forgot',
		component: () => import('prontogioco/app/pages/auth/password/forgot'),
		beforeEnter: redirectIfAuthenticated
	},
	{
		path: '/password/reset/:email/:token',
		name: 'password.reset',
		component: () => import('prontogioco/app/pages/auth/password/reset'),
		props: true,
		beforeEnter: redirectIfAuthenticated
	},

	// User -------------------------------------------------------------------
	{
		path: '/user',
		name: 'user',
		component: () => import('prontogioco/app/pages/user/detail'),
		beforeEnter: requireAuth
	},
	{
		path: '/user/edit',
		name: 'user.edit',
		component: () => import('prontogioco/app/pages/user/form'),
		beforeEnter: requireAuth,
		meta: {
			title: 'Modifica i tuoi dati'
		}
	},

	// Explore ----------------------------------------------------------------
	{
		path: '/venues/explore',
		name: 'venues.explore',
		component: () => import('prontogioco/app/pages/venues/explore')
	},

	// Add venue --------------------------------------------------------------
	{
		path: '/venues/add',
		name: 'venues.add',
		component: () => import('prontogioco/app/pages/venues/form'),
		props: true,
		beforeEnter: requireAuth
	},

	// Venue detail -----------------------------------------------------------
	{
		path: '/venues/:venueId',
		name: 'venues.detail',
		component: () => import('prontogioco/app/pages/venues/detail'),
		props: true
	},

	// Edit venue -------------------------------------------------------------
	{
		path: '/venues/:venueId/edit',
		name: 'venues.edit',
		component: () => import('prontogioco/app/pages/venues/form'),
		props: true,
		beforeEnter: requireAuth
	},
	{
		path: '/venues/:venueId/plan',
		name: 'venues.selectPlan',
		component: () => import('prontogioco/app/pages/venues/select-plan'),
		props: true,
		beforeEnter: requireAuth
	},

	// Promote ----------------------------------------------------------------
	{
		path: '/promote',
		name: 'promote',
		component: () => import('prontogioco/app/pages/promote')
	},

	// About ------------------------------------------------------------------
	{
		path: '/about',
		name: 'about',
		component: () => import('prontogioco/app/pages/about')
	},

	// Play responsibly -------------------------------------------------------
	{
		path: '/play-responsibly',
		name: 'playResponsibly',
		component: () => import('prontogioco/app/pages/play-responsibly')
	},

	// Catch all --------------------------------------------------------------
	{
		path: '*',
		name: 'error',
		component: () => import('prontogioco/app/pages/error')
	}
];