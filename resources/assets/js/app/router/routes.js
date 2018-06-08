export default [
	{
		path: '/',
		name: 'home',
		component: () => import('prontogioco/app/pages/home')
	},
	{
		path: '/venues/explore',
		name: 'venues.explore',
		component: () => import('prontogioco/app/pages/venues/explore'),
		meta: {
			title: 'Ricerca'
		}
	},
	{
		path: '/venues/:venueId',
		name: 'venues.detail',
		component: () => import('prontogioco/app/pages/venues/detail/main.vue'),
		props: true
	},
	{
		path: '/venues/:venueId/edit',
		name: 'venues.edit',
		component: () => import('prontogioco/app/pages/venues/form'),
		props: true
	},
	{
		path: '/promote',
		name: 'promote',
		component: () => import('prontogioco/app/pages/promote'),
		meta: {
			title: 'Promuovi la tua attività, è gratis!'
		}
	},
	{
		path: '/about',
		name: 'about',
		component: () => import('prontogioco/app/pages/about'),
		meta: {
			title: 'Chi siamo'
		}
	},
	{
		path: '/play-responsibly',
		name: 'playResponsibly',
		component: () => import('prontogioco/app/pages/play-responsibly'),
		meta: {
			title: 'Gioca responsabilmente'
		}
	},

	{
	    path: '*',
	    component: () => import('prontogioco/app/pages/error.vue')
	}
]