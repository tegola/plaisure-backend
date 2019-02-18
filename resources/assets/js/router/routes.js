import { requireAuth, requireSignup, redirectIfAuthenticated } from './guards';

export default [
	// Home -------------------------------------------------------------------
	{
		path: '/',
		name: 'home',
		component: () => import('@/pages/home')
	},

	// Login/register ---------------------------------------------------------
	{
		path: '/login',
		name: 'login',
		component: () => import('@/pages/auth/login'),
		props: route => ({ redirect: route.query.redirect }),
		beforeEnter: redirectIfAuthenticated
	},
	{
		path: '/register',
		name: 'register',
		component: () => import('@/pages/auth/register'),
		props: route => ({ redirect: route.query.redirect }),
		beforeEnter: redirectIfAuthenticated
	},

	// Password reset
	{
		path: '/password/forgot',
		name: 'password.forgot',
		component: () => import('@/pages/auth/password/forgot'),
		beforeEnter: redirectIfAuthenticated
	},
	{
		path: '/password/reset/:email/:token',
		name: 'password.reset',
		component: () => import('@/pages/auth/password/reset'),
		props: true,
		beforeEnter: redirectIfAuthenticated
	},

	// User -------------------------------------------------------------------
	{
		path: '/user',
		name: 'user',
		component: () => import('@/pages/user/detail'),
		beforeEnter: requireAuth
	},
	{
		path: '/user/edit',
		name: 'user.edit',
		component: () => import('@/pages/user/form'),
		beforeEnter: requireAuth,
		meta: {
			title: 'Modifica i tuoi dati'
		}
	},

	// Explore ----------------------------------------------------------------
	{
		path: '/venues/explore',
		name: 'venues.explore',
		component: () => import('@/pages/venues/explore')
	},

	// Add venue --------------------------------------------------------------
	{
		path: '/venues/add',
		name: 'venues.add',
		component: () => import('@/pages/venues/form'),
		props: true,
		beforeEnter: requireAuth
	},

	// Venue detail -----------------------------------------------------------
	{
		path: '/venues/:venueId',
		name: 'venues.detail',
		component: () => import('@/pages/venues/detail'),
		props: true
	},

	// Claim venue ------------------------------------------------------------
	{
		path: '/venues/:venueId/claim',
		name: 'venues.claim',
		component: () => import('@/pages/venues/claim'),
		props: true,
		beforeEnter: requireSignup
	},

	// Edit venue -------------------------------------------------------------
	{
		path: '/venues/:venueId/edit',
		name: 'venues.edit',
		component: () => import('@/pages/venues/form'),
		props: true,
		beforeEnter: requireAuth
	},
	{
		path: '/venues/:venueId/plan',
		name: 'venues.selectPlan',
		component: () => import('@/pages/venues/select-plan'),
		props: true,
		beforeEnter: requireAuth
	},

	// Promote ----------------------------------------------------------------
	{
		path: '/promote',
		name: 'promote',
		component: () => import('@/pages/promote')
	},

	// About ------------------------------------------------------------------
	{
		path: '/about',
		name: 'about',
		component: () => import('@/pages/about')
	},

	// Play responsibly -------------------------------------------------------
	{
		path: '/play-responsibly',
		name: 'play-responsibly',
		component: () => import('@/pages/play-responsibly')
	},

	// Catch all --------------------------------------------------------------
	{
		path: '*',
		name: 'error',
		component: () => import('@/pages/error')
	}
];