import axios from 'axios';
import { stringify } from 'qs';
import _extend from 'lodash/extend';
import _debounce from 'lodash/debounce';
import _last from 'lodash/last';
import singularOrPlural from '../../../utilities/singular-or-plural';
import { Map, Marker, InfoWindow } from 'vue2-google-maps';
import PgNavbar from '../../components/navbar';
import PgFilterButton from './filter-button';
import PgFilterButtonItem from './filter-button-item';
import PgPane from '../../components/pane';
import PgVenueListItem from './list-item';

export default {
	name: 'pg-explore-page',

	components: {
		'pg-navbar': PgNavbar,
		'pg-map': Map,
		'pg-map-marker': Marker,
		'pg-map-info-window': InfoWindow,
		'pg-filter-button': PgFilterButton,
		'pg-filter-button-item': PgFilterButtonItem,
		'pg-pane': PgPane,
		'pg-venue-list-item': PgVenueListItem
	},

	filters: {
		singularOrPlural: singularOrPlural
	},

	data() {
		return {
			radiuses: pg.radiuses,
			categories: pg.categories,
			// amenities: pg.amenities,

			loading: false,
			locating: false,
			userLocation: null,

			searchMode: 'center', // bounds, center
			searchParams: pg.searchParams,
			placeholder: undefined,
			query: pg.searchParams.query,
			pager: null,
			currentView: 'list',
			highlightedVenueId: null,
			selectedVenueId: null,

			mapNeedsRefresh: false,
			mapBoundsEventEnabled: true,
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
				gestureHandling: 'greedy',
				fullscreenControl: false,
				mapTypeControl: false,
				streetViewControl: false,
				zoomControl: this.$mq.comfortable,
				zoomControlOptions: {
					position: 1 //google.maps.ControlPosition.TOP_LEFT
				},
				styles: [
					{ // Hide points of interest
						'featureType': 'poi',
						'stylers': [{ 'visibility': 'off' }]
					}
				]
			}
		};
	},

	computed: {
		locationButtonIcon() {
			return this.locating ? 'circle-outline-notch' : this.userLocation ? 'location' : 'location-outline';
		},
		venues() {
			return this.pager && this.pager.data ? this.pager.data : [];
		},
		hasMorePages() {
			return this.pager && this.pager.next_page_url ? true : false;
		},
		showRadiusFilter() {
			return this.searchMode == 'center';
		},
		showList() {
			return this.$mq.comfortable || this.currentView == 'list';
		},
		showMap() {
			return this.$mq.comfortable || this.currentView == 'map';
		}
	},

	methods: {
		// Location search ----------------------------------------------------
		onPlaceChanged(place) {
			if (!place) return;

			const bounds = place.geometry && place.geometry.viewport ? place.geometry.viewport : null;
			const center = bounds && bounds.getCenter() ? bounds.getCenter() : null;

			// Change search mode
			this.searchMode = 'center';

			// Update view
			this.userLocation = null;
			this.placeholder = undefined;

			if (place.vicinity && place.name != place.vicinity) {
				this.query = `${place.name}, ${place.vicinity}`;
			} else {
				this.query = place.name;
			}

			// Move map, but disable refresh on bounds change
			if (bounds) {
				this.mapBoundsEventEnabled = false;
				if (this.$refs.map) this.$refs.map.fitBounds(bounds);
			}

			// Update search params
			_extend(this.searchParams, {
				query: this.query,
				c_lat: center ? center.lat() : null,
				c_lng: center ? center.lng() : null,
				ne_lat: null,
				ne_lng: null,
				sw_lat: null,
				sw_lng: null
			});

			// Load venues
			this.load();
		},

		// User location ------------------------------------------------------
		findUserLocation() {
			const options = {
				timeout: 10 * 1000, // 10 secs
				maximumAge: 5 * 60 * 1000 // last 5 minutes
			};

			this.locating = true;

			navigator.geolocation.getCurrentPosition(this.onUserLocationFound, this.onUserLocationNotFound, options);
		},

		onUserLocationFound(position) {
			const { latitude, longitude } = position.coords;

			// Change search mode
			this.searchMode = 'center';

			// Update view
			this.locating = false;
			this.userLocation = {
				lat: latitude,
				lng: longitude
			};
			this.query = '';
			this.placeholder = '(Vicino a te)';

			// Move map center, but disable refresh on bounds change
			this.mapBoundsEventEnabled = false;

			this.mapBounds = null;
			this.mapCenter = {
				lat: latitude,
				lng: longitude
			};

			// Update search params
			_extend(this.searchParams, {
				query: '',
				c_lat: latitude,
				c_lng: longitude,
				ne_lat: null,
				ne_lng: null,
				sw_lat: null,
				sw_lng: null
			});

			// Load venues
			this.load();
		},

		onUserLocationNotFound() {
			this.locating = false;
			this.userLocation = null;
			alert('Non è stato possibile trovare la tua posizione.');
		},

		// Filters ------------------------------------------------------------
		radiusFilterText() {
			return `${this.searchParams.radius} km`;
		},

		categoryFilterText() {
			const ids = this.searchParams.categories;

			if (ids.length == 1) {
				return this.categories.find(item => {
					return item.id == ids[0];
				}).name;
			} else if (ids.length > 1) {
				return `${ids.length} selezionati`;
			}
		},

		/*
		amenitiesFilterText() {
			const machineNames = this.searchParams.amenities;

			if (!machineNames.length) return null;

			if (machineNames.length == 1) {
				return this.amenities.find(item => {
					return item.machine_name == machineNames[0];
				}).name;
			} else {
				return [
					machineNames.length,
					this.$options.filters.singularOrPlural(machineNames.length, 'selezionato', 'selezionati')
				].join(' ');
			}
		},
		*/

		isFilterItemSelected(type, key) {
			switch (type) {
				case 'radius': return this.searchParams.radius && this.searchParams.radius == key;
				case 'category': return this.searchParams.categories.indexOf(key) !== -1;
				// case 'amenity': return this.searchParams.amenities.indexOf(key) !== -1;
			}
		},

		onRadiusSelect(radius) {
			this.searchParams.radius = radius;
			this.$forceUpdate();
		},

		onCategorySelect(category) {
			const ids = this.searchParams.categories;
			const index = ids.indexOf(category.id);
			const allIds = this.categories.map(category => category.id);

			if (index !== -1) {
				ids.splice(index, 1);
				if (!ids.length) ids.push(...allIds); // Never empty
			} else {
				ids.push(category.id);
			}
		},

		/*
		onAmenitySelect(amenity) {
			const names = this.searchParams.amenities;
			const index = names.indexOf(amenity.machine_name);

			if (index !== -1) {
				names.splice(index, 1);
			} else {
				names.push(amenity.machine_name);
			}
		},
		*/

		onFilterClose() {
			// TODO: Determine whether a reload is needed
			this.load();
		},

		// Map ----------------------------------------------------------------
		onMapBoundsChange: _debounce(function(bounds) { // Fat arrow functions do not work with debounce
			// Store bounds
			this.mapBounds = bounds;

			// Stop if map bounds event is not enabled
			if (!this.mapBoundsEventEnabled) {
				this.mapBoundsEventEnabled = true;
				return;
			}

			// Mark for map refresh
			this.mapNeedsRefresh = true;
		}, 200),

		mapMarkerIcon(venue, index) {
			let variant;
			let glyph;

			// Determine variant
			if (venue.id == this.selectedVenueId || venue.id == this.highlightedVenueId) {
				variant = 'inverse';
			} else {
				variant = 'normal';
			}

			// Determine glyph
			if (index < 25 && venue.first_category_machine_name) {
				glyph = venue.first_category_machine_name;
			} else {
				glyph = 'collapsed';
			}

			return `/img/map/pin-${variant}-${glyph}.svg`;
		},

		onSearchBoundsClick() {
			// Change search mode
			this.searchMode = 'bounds';

			// Update view
			this.mapNeedsRefresh = false;
			this.userLocation = null;
			this.query = null;
			this.placeholder = "(All'interno della mappa)";

			// Update search params
			const c = this.mapBounds.getCenter();
			const ne = this.mapBounds.getNorthEast();
			const sw = this.mapBounds.getSouthWest();

			_extend(this.searchParams, {
				query: '',
				c_lat: c.lat(),
				c_lng: c.lng(),
				ne_lat: ne.lat(),
				ne_lng: ne.lng(),
				sw_lat: sw.lat(),
				sw_lng: sw.lng()
			});

			this.load();
		},

		// Data loading -------------------------------------------------------
		load() {
			const paramsWithToken = _extend({}, this.searchParams, { _token: pg.config.csrfToken });

			// Load venues
			this.loading = true;

			axios.post('/venues/search', paramsWithToken).then(response => {
				this.pager = response.data;
				this.loading = false;
			});

			// Update url
			const baseName = _last(location.pathname.split('/'));
			const params = stringify(this.searchParams);

			window.history.replaceState({}, '', `${baseName}?${params}`);

			// Update title
			document.title = this.query ? `${this.query} - ${pg.app.name}` : pg.app.name;
		},

		// List support -------------------------------------------------------
		highlight(venue) {
			// Disabled when map is not visible
			if (!this.showMap) return;

			this.highlightedVenueId = venue ? venue.id : null;
		},

		select(venue) {
			// Disabled when map is not visible
			if (!this.showMap) return;

			// Always hide if no venue is passed
			if (!venue) {
				this.selectedVenueId = null;
				return;
			}

			// Select/deselect
			this.selectedVenueId = this.selectedVenueId != venue.id ? venue.id : null;
		}
	},

	mounted() {
		// Load
		this.load();
	}
};
