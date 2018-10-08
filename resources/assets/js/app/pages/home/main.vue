<script>
import _extend from 'lodash/extend';
import { formatResult } from 'prontogioco/utilities/geocoder';
import PgButton from 'prontogioco/app/components/button';
import PgPlaceTextbox from 'prontogioco/app/components/place-textbox';
import PgToken from './token';
import PgVenueItem from './venue-item';
import { APP_NAME } from 'prontogioco/constants';
import { cityPresets } from 'prontogioco/app/static';

export default {
	name: 'PgHomePage',

	components: {
		PgButton,
		PgPlaceTextbox,
		PgToken,
		PgVenueItem
	},

	data() {
		return {
			APP_NAME,
			query: null,
			placeholder: this.$t('pages.home.city_placeholder'),
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
			venues: []
		};
	},

	computed: {
		hasGeolocation() {
			return this.$root.hasGeolocation;
		},

		userLocation() {
			return this.$store.state.user.coords;
		},

		canSubmit() {
			return (this.searchParams.c_lat && this.searchParams.c_lng);
		},

		tokenPresets() {
			const presets = [];

			// Categories
			this.categories.forEach(category => {
				presets.push({
					value: category.machine_name,
					label: this.$t(`db.categories.${category.machine_name}`),
					route: {
						name: 'venues.explore',
						query: {
							radius: 10,
							categories: category.id
						}
					}
				});
			});

			// Cities
			cityPresets.forEach(preset => {
				presets.push({
					value: preset.query,
					label: preset.query,
					route: {
						name: 'venues.explore',
						query: preset
					}
				});
			});

			return presets;
		}
	},

	mounted() {
		this.loadData();
	},

	methods: {
		loadData() {
			this.$axios.get('/').then(({ data }) => {
				this.categories = data.categories;
				this.venues = data.venues;
			});
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
				this.placeholder = ['(', this.$t('pages.home.location_placeholder'), ')'].join('');
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
				alert(this.$t('pages.home.location_error'));
			}).then(() => {
				this.locating = false;
			});
		},

		onPlaceChanged(place) {
			// Reset user location indicator
			this.useUserLocation = false;
			this.placeholder = this.$t('pages.home.city_placeholder');

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
		<pg-navbar :search="false" />

		<div class="pg-home-page__hero">
			<div class="container">
				<div class="row">
					<div class="col-md-7">
						<div class="row">
							<div class="col-md-10">
								<h1 class="pg-home-page__title">Trova le sale da gioco più vicine a te.</h1>
								<p class="pg-home-page__intro">Su {{ APP_NAME }} è veloce, e con più di 5000 sale c'è solo l'imbarazzo della scelta!</p>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-8">
								<div class="position-relative">
									<label class="sr-only">{{ $t('pages.home.search') }}</label>
									<pg-place-textbox
										:placeholder="placeholder"
										:place="query"
										:value="query"
										:options="placeTextboxOptions"
										class="form-control pg-home-page__search-form-control pg-home-page__search-query-control"
										autofocus
										@place-changed="onPlaceChanged"
									/>
									<div
										v-b-tooltip
										v-if="hasGeolocation"
										:title="$t('pages.home.location')"
										class="pg-home-page__search-locate-btn-wrapper">
										<pg-button
											:icon="useUserLocation ? 'location' : 'location-outline'"
											:loading="locating"
											:disabled="useUserLocation ? true : false"
											variant="naked"
											class="pg-home-page__search-locate-btn"
											tabindex="-1"
											@click="findUserLocation"
										/>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<pg-button
									:disabled="!canSubmit"
									variant="accent"
									class="pg-home-page__search-submit-btn"
									@click="submit">
									{{ $t('pages.home.submit') }}
								</pg-button>
							</div>
						</div>
					</div>
					<div class="col-md-5 position-relative">
						<div class="pg-home-page__main-venue-container">
							<div class="mb-md-2 text-right small">
								<a href="#">Mostra qui la tua attività</a>
							</div>
							<div class="pg-home-page__main-venue" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="my-5 pg-home-page__token-section">
				<h5 class="font-weight-bold">Esplora</h5>

				<pg-token
					v-for="preset in tokenPresets"
					:key="preset.value"
					:to="preset.route">
					{{ preset.label }}
				</pg-token>
			</div>

			<div class="my-5">
				<h5 class="font-weight-bold">In rilievo</h5>
				<div class="row">
					<div v-for="venue in venues" :key="venue.id" class="col-md-6 mb-4">
						<pg-venue-item :venue="venue" />
					</div>
				</div>
				<div class="row">
					<div v-for="venue in venues" :key="venue.id" class="col-md-4 col-xl-3 mb-4">
						<pg-venue-item :venue="venue" />
					</div>
				</div>
			</div>
		</div>

		<div class="my-5 pg-home-page__promote-section">
			<div class="container">
				<div class="row">
					<div class="col-md-4 text-center">
						Immagine qui
					</div>
					<div class="col-md-8">
						<p class="text-muted">Scusa il gioco di parole</p>
						<h3 class="display-4">Mettiti in gioco</h3>
						<p clas="h4">Registra la tua attività o reclama la gestione di un’attività già presente. È veloce, e soprattutto è gratis!</p>
						<p>
							<pg-button variant="primary">Registrati come gestore</pg-button>
						</p>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>