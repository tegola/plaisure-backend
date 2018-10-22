<script>
import _extend from 'lodash/extend';
import _shuffle from 'lodash/shuffle';
import { formatResult } from 'prontogioco/utilities/geocoder';
import PgButton from 'prontogioco/app/components/button';
import PgPlaceTextbox from 'prontogioco/app/components/place-textbox';
import PgVenueGridItem from 'prontogioco/app/components/venue-grid-item';
import PgToken from './token';
import { APP_NAME } from 'prontogioco/constants';
import { mapState } from 'vuex';
import cities from './cities';
import { MAP_DEFAULT_BOUNDS, MAP_DEFAULT_ZOOM } from 'prontogioco/constants';

export default {
	name: 'PgHomePage',

	components: {
		PgButton,
		PgPlaceTextbox,
		PgVenueGridItem,
		PgToken
	},

	data() {
		return {
			APP_NAME,
			query: null,
			placeholder: this.$t('pages.home.search.city_placeholder'),
			placeTextboxOptions: {
				types: ['geocode'] // Limit search to cities, addresses, etc.
			},
			locating: false,
			useUserLocation: false,
			searchParams: {
				query: null,
				c_lat: null,
				c_lng: null
			},
			categories: [],
			highlightedVenues: [],
			newVenues: []
		};
	},

	computed: {
		hasGeolocation() {
			return this.$root.hasGeolocation;
		},

		...mapState('user', {
			user: 'user',
			userVenues: 'venues'
		}),

		userLocation() {
			return this.user.coords;
		},

		canSubmit() {
			return (this.searchParams.c_lat && this.searchParams.c_lng);
		},

		tokenPresets() {
			const presets = [];

			// Categories
			this.categories.forEach(category => {
				presets.push({
					type: 'category',
					value: category.machine_name,
					icon: category.machine_name.replace('_', '-'),
					label: this.$t(`db.categories.${category.machine_name}`),
					route: {
						name: 'venues.explore',
						query: {
							categories: [category.id],
							ne_lat: MAP_DEFAULT_BOUNDS.ne.lat,
							ne_lng: MAP_DEFAULT_BOUNDS.ne.lng,
							sw_lat: MAP_DEFAULT_BOUNDS.sw.lat,
							sw_lng: MAP_DEFAULT_BOUNDS.sw.lng,
							zoom: MAP_DEFAULT_ZOOM
						}
					}
				});
			});

			// Cities
			cities.forEach(city => {
				presets.push({
					type: 'city',
					value: city.query,
					icon: 'location',
					label: city.query,
					route: {
						name: 'venues.explore',
						query: city
					}
				});
			});

			return _shuffle(presets);
		},

		promoteButton() {
			// Unregistered user
			if (!this.user) {
				return {
					route: { name: 'register'},
					label: this.$t('pages.home.promote.register')
				};
			}

			// Logged in user with no venues
			if (!this.userVenues.length) {
				return {
					route: { name: 'venues.add' },
					label: this.$t('pages.home.promote.add')
				};
			}

			// Logged in user with at least a venue
			return {
				route: { name: 'user' },
				label: this.$t('pages.home.promote.manage')
			};
		}
	},

	mounted() {
		this.loadData();
	},

	methods: {
		loadData() {
			this.$axios.get('/').then(({ data }) => {
				this.categories = data.categories;
				this.highlightedVenues = data.highlightedVenues;
				this.newVenues = data.newVenues;
			});
		},

		svg(path) {
			return require(`!svg-inline-loader!assets/svg/${path}`);
		},

		findUserLocation() {
			this.locating = true;

			this.$store.dispatch('user/findCoords').then(coords => {
				const { lat, lng } = coords;

				// Update search params
				_extend(this.searchParams, {
					query: null,
					c_lat: lat,
					c_lng: lng
				});

				// Update view
				this.query = null;
				this.placeholder = ['(', this.$t('pages.home.search.location_placeholder'), ')'].join('');
				this.useUserLocation = true;

				// Find city name
				if (!this.geocoder) this.geocoder = new google.maps.Geocoder();

				this.geocoder.geocode({ location: coords }, (results, status) => {
					this.searchingMarkerCoords = false;

					if (status === 'OK') {
						const result = formatResult(results[0]);

						let address = [];

						if (result.streetName) address.push(result.streetName);
						address.push(result.administrativeLevels.level3long);
						address = address.join(', ');

						this.query = address;
						this.searchParams.query = address;
					}
				});
			}).catch(() => {
				alert(this.$t('pages.home.search.location_error'));
			}).then(() => {
				this.locating = false;
			});
		},

		onPlaceChanged(place) {
			// Reset user location indicator
			this.useUserLocation = false;
			this.placeholder = this.$t('pages.home.search.city_placeholder');

			// Reset search
			if (!place) {
				this.query = null;
				_extend(this.searchParams, {
					query: null,
					c_lat: null,
					c_lng: null
				});
				return;
			}

			// Update search params
			let query = place.name;
			if (place.vicinity && place.name != place.vicinity) {
				query = `${place.name}, ${place.vicinity}`;
			}

			const center = place.geometry.viewport.getCenter();

			this.query = query;

			_extend(this.searchParams, {
				query: query,
				c_lat: center.lat(),
				c_lng: center.lng()
			});
		},

		submit() {
			this.$router.push({
				name: 'venues.explore',
				query: this.searchParams
			});
		}
	}
};
</script>

<template>
	<div class="pg-home-page">
		<div class="pg-home-page__hero">
			<pg-navbar :search="false" />

			<div class="pg-home-page__hero-content">
				<div class="container">
					<div class="row">
						<div class="col-md-10 col-lg-8">
							<div class="row">
								<div class="col-md-10">
									<h1 class="display-3 text-dark-green mb-4">{{ $t('pages.home.search.title') }}</h1>
									<p class="lead text-dark-green-muted font-weight-semibold mb-4">{{ $t('pages.home.search.subtitle', { name: APP_NAME, count: 5000 }) }}</p>
								</div>
							</div>

							<div class="row form-row">
								<div class="col">
									<div class="position-relative">
										<label class="sr-only">{{ $t('pages.home.search.label') }}</label>
										<pg-place-textbox
											:placeholder="placeholder"
											:place="query"
											:value="query"
											:options="placeTextboxOptions"
											class="form-control form-control-lg pg-home-page__search-form-control pg-home-page__search-query-control"
											@place-changed="onPlaceChanged"
										/>
										<div
											v-b-tooltip
											v-if="hasGeolocation"
											:title="$t('pages.home.search.location')"
											class="pg-home-page__search-locate-btn-wrapper">
											<pg-button
												:icon="useUserLocation ? 'location' : 'location-outline'"
												:loading="locating"
												:disabled="useUserLocation ? true : false"
												variant="naked"
												size="lg"
												class="pg-home-page__search-locate-btn"
												tabindex="-1"
												@click="findUserLocation"
											/>
										</div>
									</div>
								</div>
								<div class="col-auto">
									<pg-button
										:disabled="!canSubmit"
										variant="accent"
										size="lg"
										class="pg-home-page__search-submit-btn"
										block
										@click="submit">
										{{ $t('pages.home.search.submit') }}
									</pg-button>
								</div>
							</div>
						</div>
						<!--
						<div class="col-md-5 position-relative">
							<div class="pg-home-page__main-venue-container">
								<div class="mb-md-2 text-right small">
									<a href="#">{{ $t('pages.home.venue.hint') }}</a>
								</div>
								<div class="pg-home-page__main-venue" />
							</div>
						</div>
						-->
					</div>
				</div>
			</div>
		</div>

		<div class="my-5 pg-home-page__token-section">
			<div class="container">
				<h5 class="font-weight-bold">{{ $t('pages.home.explore.title') }}</h5>
			</div>
			<div class="pg-home-page__scrollable-pane">
				<div class="container">
					<div class="pg-home-page__scrollable-pane-row">
						<pg-token
							v-for="preset in tokenPresets"
							:key="preset.value"
							:icon="preset.icon"
							:type="preset.type"
							:to="preset.route">
							{{ preset.label }}
						</pg-token>
					</div>
				</div>
			</div>
		</div>

		<div v-if="highlightedVenues.length" class="my-5">
			<div class="container">
				<h5 class="font-weight-bold">{{ $t('pages.home.highlights.title') }}</h5>
			</div>
			<div class="pg-home-page__scrollable-pane">
				<div class="container">
					<div class="row pg-home-page__scrollable-pane-row">
						<div v-for="venue in highlightedVenues" :key="venue.id" class="col-11 col-md-6 mb-4">
							<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }" class="text-inherit">
								<pg-venue-grid-item :venue="venue" />
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div v-if="newVenues.length" class="my-5">
			<div class="container">
				<h5 class="font-weight-bold">{{ $t('pages.home.new.title') }}</h5>
			</div>
			<div class="pg-home-page__scrollable-pane">
				<div class="container">
					<div class="row pg-home-page__scrollable-pane-row">
						<div
							v-for="(venue, index) in newVenues"
							:key="venue.id"
							:class="index == newVenues.length - 1 ? 'd-xl-none' : null"
							class="col-7 col-md-4 col-xl-3 mb-4">
							<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }" class="text-inherit">
								<pg-venue-grid-item :venue="venue" />
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="my-5 pg-home-page__promote-section">
			<div class="container">
				<div class="row align-items-md-center">
					<div class="col-md-3 mx-auto">
						<div
							class="pg-home-page__promote-img"
							v-html="svg('illustrations/venue.svg')"
						/>
					</div>
					<div class="col-md-8 col-xl-7">
						<p class="text-dark-green-muted mb-1">{{ $t('pages.home.promote.intro') }}</p>
						<h3 class="display-4 text-dark-green mb-3">{{ $t('pages.home.promote.title') }}</h3>
						<p class="lead text-dark-green mb-4">{{ $t('pages.home.promote.paragraph') }}</p>
						<p>
							<pg-button
								:to="promoteButton.route"
								:block="$mq.xs"
								variant="primary"
								icon="arrow-right"
								icon-position="right">
								{{ promoteButton.label }}
							</pg-button>
							<pg-button
								:to="{ name: 'promote' }"
								:block="$mq.xs"
								variant="link"
								class="text-dark-green">
								{{ $t('pages.home.promote.more') }}
							</pg-button>
						</p>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>