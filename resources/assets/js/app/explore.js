import _ from 'lodash';
import $ from 'jquery';
import Vue from 'vue';
import * as VueGoogleMaps from 'vue2-google-maps';
import formatDistance from '../utilities/format-distance';
import singularOrPlural from '../utilities/singular-or-plural';

// Register Vue Google Maps
// FIXME: Pass region per site and language per user locale
Vue.use(VueGoogleMaps, {
	load: {
		key: pg.config.googleMapsApiKey,
		language: pg.config.locale,
		region: pg.config.locale,
		libraries: 'places'
	}
});


new Vue({
	el: '.page-content',

	filters: {
		formatDistance: formatDistance,
		singularOrPlural: singularOrPlural
	},

	data: {
		searchParams: pg.searchParams,
		venues: pg.venues,
		what: pg.searchParams.what, // FIXME: Remove
		near: pg.searchParams.near, // FIXME: Remove
		
		isFirstLoad: true,
		mapNeedsRefresh: false,
		followMap: true,
		highlightedVenueId: null,
		selectedVenueId: null,

		// View-related, not really used after here
		mapCenter: {
			lat: pg.searchParams.c_lat,
			lng: pg.searchParams.c_lng
		},
		mapBounds: {
			north: pg.searchParams.ne_lat,
			east: pg.searchParams.ne_lng,
			south: pg.searchParams.sw_lat,
			west: pg.searchParams.sw_lng
		},
		mapOptions: {
			mapTypeControl: false,
			streetViewControl: false,
			styles: [
				{ // Hide points of interest
					'featureType': 'poi',
					'stylers': [{ 'visibility': 'off' }]
				}
			]
		},
		infoWindowOptions: {
			disableAutoPan: true
		}
	},

	computed: {
		hasMorePages() {
			return this.venues.next_page_url ? true : false;
		},
	},

	watch: {
		followMap(newValue) {
			if (newValue) this.loadFromBegin();
		}
	},

	methods: {
		onSuggestionSelect(item) {
			if (item.geometry && item.geometry.viewport) {
				this.storeBounds(item.geometry.viewport);
			}

			// Get the input value as a shortcut for the formatted address
			this.searchParams.near = this.$refs.locationAutocomplete.$refs.input.value;

			// Reload
			this.searchParams.page = 1;
			this.loadFromBegin();
		},

		onMapBoundsChange(bounds) {
			if (this.isFirstLoad) {
				this.isFirstLoad = false;
				return;
			}

			// Store bounds
			this.storeBounds(bounds);

			// Load or mark as needed
			if (this.followMap) {
				this.loadFromBegin();
			} else {
				this.mapNeedsRefresh = true;
			}
		},

		storeBounds(bounds) {
			const c = bounds.getCenter();
			const ne = bounds.getNorthEast();
			const sw = bounds.getSouthWest();
			const p = this.searchParams;

			// Store position in search params
			p.c_lat = c.lat();
			p.c_lng = c.lng();
			p.ne_lat = ne.lat();
			p.ne_lng = ne.lng();
			p.sw_lat = sw.lat();
			p.sw_lng = sw.lng();
		},

		load() {
			console.log('load');

			// Load venues
			$.get('/venues/search', this.searchParams, (venues) => {
				this.venues = venues;
			});

			// Update url
			const baseName = _.last(location.pathname.split('/'));
			const params = $.param(this.searchParams, _.omit(['page']));

			window.history.replaceState({}, '', `${baseName}?${params}`);

			// Update title
			document.title = this.searchParams.near ? `${this.searchParams.near} - ${pg.app.name}` : pg.app.name;

			// Reset need to refresh the map
			this.mapNeedsRefresh = false;
		},

		loadMore() {
			this.searchParams.page = this.searchParams.page + 1;
			this.load();
		},

		loadFromBegin() {
			this.searchParams.page = 1;
			this.load();
		},

		highlight(venue) {
			this.highlightedVenueId = venue ? venue.id : null;
		},

		select(venue) {
			// Always hide if no venue is passed
			if (!venue) {
				this.selectedVenueId = null;
				return;
			}

			// Select/deselect
			this.selectedVenueId = this.selectedVenueId != venue.id ? venue.id : null;
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue);
		}
	},

	mounted() {
		// Prevent submitting the form when the locations dropdown is open
		// (key events are not handled by gmap-autocomplete)
		$(this.$refs.locationAutocomplete.$refs.input).on('keydown', (e) => {
			if (e.which == 13 && $('.pac-container:visible').length) {
				e.preventDefault();
			}
		});
	}
});

