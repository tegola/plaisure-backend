import _ from 'lodash';
import $ from 'jquery';
import formatDistance from '../../utilities/format-distance';
import singularOrPlural from '../../utilities/singular-or-plural';
import { Map, Marker, InfoWindow } from 'vue2-google-maps';

export default {
	name: 'pg-explore-page',

	components: {
		'pg-map': Map,
		'pg-map-marker': Marker,
		'pg-map-info-window': InfoWindow
	},

	filters: {
		formatDistance: formatDistance,
		singularOrPlural: singularOrPlural
	},

	data() {
		return {
			searchParams: pg.searchParams,
			pager: null,
			mapNeedsRefresh: false,
			followMap: false,
			highlightedVenueId: null,
			selectedVenueId: null,
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
			}
		}
	},

	computed: {
		venues() {
			return this.pager && this.pager.data ? this.pager.data : [];
		},
		hasMorePages() {
			return this.pager && this.pager.next_page_url ? true : false;
		},
	},

	watch: {
		// Automatically load when following the map
		followMap(newValue) {
			if (newValue) this.load();
		}
	},

	methods: {
		onSuggestionSelect(item) {
			if (item.geometry && item.geometry.viewport) {
				this.storeBounds(item.geometry.viewport);
				this.fitBounds();
			}

			// Get the input value as a shortcut for the formatted address
			this.searchParams.near = this.$refs.locationAutocomplete.value;

			// Reload
			this.load();
		},

		onCategoryChange(e) {
			this.searchParams.category = e.target.value;
			this.load();
		},

		onMapBoundsChange: _.debounce(function(bounds) { // Fat arrow functions do not work with debounce
			// Store bounds
			this.storeBounds(bounds);

			// Load or mark as needed
			if (this.followMap) {
				this.load();
			} else {
				this.mapNeedsRefresh = true;
			}
		}, 200),

		storeBounds(bounds) {
			const c = bounds.getCenter();
			const ne = bounds.getNorthEast();
			const sw = bounds.getSouthWest();

			// Store position in search params in a single swoop, to avoid
			// stressing the watcher
			_.extend(this.searchParams, {
				c_lat: c.lat(),
				c_lng: c.lng(),
				ne_lat: ne.lat(),
				ne_lng: ne.lng(),
				sw_lat: sw.lat(),
				sw_lng: sw.lng()
			});			
		},

		fitBounds() {
			this.mapBounds = {
				north: this.searchParams.ne_lat,
				east: this.searchParams.ne_lng,
				south: this.searchParams.sw_lat,
				west: this.searchParams.sw_lng
			};
			this.$refs.map.fitBounds(this.mapBounds);
		},

		load() {
			// Load venues
			$.get('/venues/search', this.searchParams, pager => {
				this.pager = pager;
			});

			// Reset need to refresh the map
			this.mapNeedsRefresh = false;

			// Update url
			const baseName = _.last(location.pathname.split('/'));
			const params = $.param(this.searchParams, _.omit(['page']));

			window.history.replaceState({}, '', `${baseName}?${params}`);

			// Update title
			document.title = this.searchParams.near ? `${this.searchParams.near} - ${pg.app.name}` : pg.app.name;
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

			// Disable map follow to avoid reloading data
			this.followMap = false;

			// Select/deselect
			this.selectedVenueId = this.selectedVenueId != venue.id ? venue.id : null;
		},

		toggleFavorite(venue) {
			console.log('aggiungo ai preferiti', venue);
		}
	},

	mounted() {
		// Prevent submitting the form when the locations dropdown is open
		// (key events are not handled by pg-map-autocomplete)
		$(this.$refs.locationAutocomplete.$refs.input).on('keydown', (e) => {
			if (e.which == 13 && $('.pac-container:visible').length) {
				e.preventDefault();
			}
		});

		// Load
		this.load();
	}
};
