<script>
import _cloneDeep from 'lodash/cloneDeep';
import _extend from 'lodash/extend';
import _isEqual from 'lodash/isEqual';
import { validationMixin } from 'vuelidate';

import anime from 'animejs';

import BNav from 'bootstrap-vue/es/components/nav/nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';

import BListGroup from 'bootstrap-vue/es/components/list-group/list-group';
import BListGroupItem from 'bootstrap-vue/es/components/list-group/list-group-item';

import PgButton from 'prontogioco/app/components/button';
import PgVenueFormGeneralPane from './general-pane';
import PgVenueFormServicesPane from './services-pane';
import PgVenueFormContactsPane from './contacts-pane';
import PgVenueFormHoursPane from './hours-pane';
import PgVenueFormPhotosPane from './photos-pane';
import PgVenueFormJackpotsPane from './jackpots-pane';

import validations from './validations';

import setTitle from 'prontogioco/utilities/set-title';
import constants from 'prontogioco/constants';

export default {
	name: 'PgVenueForm',

	components: {
		BNav,
		BNavItem,
		BListGroup,
		BListGroupItem,
		PgButton,
		PgVenueFormGeneralPane,
		PgVenueFormServicesPane,
		PgVenueFormContactsPane,
		PgVenueFormHoursPane,
		PgVenueFormPhotosPane,
		PgVenueFormJackpotsPane
	},

	mixins: [validationMixin],

	props: {
		venueId: [String, Number]
	},

	data() {
		return {
			loading: false,
			saving: false,
			panes: [
				{ value: 'general', title: 'Generale' },
				{ value: 'services', title: 'Servizi' },
				{ value: 'contacts', title: 'Contatti' },
				{ value: 'hours', title: 'Orari' },
				{ value: 'photos', title: 'Foto' },
				{ value: 'jackpots', title: 'Jackpot' }
			],
			selectedPane: 'general',
			categories: [],
			concessionaires: [],
			vltPlatforms: [],
			payPerViewPlatforms: [],
			venue: null,
			venueBackup: null,
			transition: ''
		}
	},

	computed: {
		isSaved() {
			return _isEqual(this.venue, this.venueBackup);
		},
	},

	validations,

	watch: {
		venue() {
			setTitle(this.venue && this.venue.id ? 'Modifica attività' : 'Aggiungi attività');
		}
	},

	methods: {
		loadData() {
			// Prepare url
			const url = [
				'/venues',
				this.venueId ? `/${this.venueId}/edit` : '/add'
			].join('');

			this.loading = true;

			this.$axios.get(url)
				.then(({ data }) => {
					this.concessionaires = data.concessionaires;
					this.categories = data.categories;
					this.payPerViewPlatforms = data.payPerViewPlatforms;
					this.vltPlatforms = data.vltPlatforms;

					const venue = _extend(data.venue, {
						category_ids: data.venue.categories.map(category => category.id),
						pay_per_view_platform_ids: data.venue.pay_per_view_platforms.map(platform => platform.id),
						vlt_platform_ids: data.venue.vlt_platforms.map(platform => platform.id)
					})

					this.venue = _cloneDeep(venue);
					this.venueBackup = _cloneDeep(venue);

					setTimeout(() => {
						this.loading = false;
					}, 500)
				})
		},

		selectPane(newPane) {
			const newIndex = this.panes.indexOf(newPane);
			const oldIndex = this.panes.indexOf(this.panes.find(pane => pane.value === this.selectedPane));
			const isComfortable = this.$mq.comfortable;
			const direction = newIndex > oldIndex ? (isComfortable ? 'up' : 'left') : (isComfortable ? 'down' : 'right');

			this.transition = `slide-${direction}`;

			this.$nextTick(() => {
				this.selectedPane = newPane.value;
			});
		},

		onPaneSwitch(el) {
			anime({
				targets: this.$refs.paneContainer,
				minHeight: el.getBoundingClientRect().height,
				easing: 'easeInOutQuad',
				duration: 200
			});
		},

		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) return;

			this.saving = true;

			// Prepare url
			let url = '/venues';
			if (this.venueId) url += `/${this.venueId}`

			this.$axios.post(url, this.venue)
				.then(() => {
					// Set model backup as saved
					this.venueBackup = _cloneDeep(this.venue);
				}).catch(() => {}).then(() => {
					this.saving = false;
				})
		}
	},

	beforeCreate() {
		_extend(this, constants);
	},

	mounted() {
		this.loadData()
	}
}
</script>

<template>
	<div>
		<pg-navbar variant="dark" />

		<div class="container d-flex text-muted text-center" style="height: 50vh" v-if="loading">
			<div class="m-auto">
				<pg-icon icon="circle-outline-notch" spinning />
				<h5 class="m-0">{{ venueId ? 'Carica la tua attività' : 'Preparo una nuova attività' }}&hellip;</h5>
			</div>
		</div>

		<div v-if="!loading && venue">
			<!-- Small screen menu -->
			<div class="sticky-top" v-if="$mq.constrained">
				<div class="navbar navbar-light navbar-expand">
					<div class="container">
						<h2 class="h4 mb-0">{{ venueId ? 'Modifica' : 'Aggiungi' }} attività</h2>
						<pg-button
							variant="primary"
							:disabled="isSaved"
							:loading="saving"
							@click="submit">
							{{ venueId ? 'Salva' : 'Aggiungi' }}
						</pg-button>
					</div>
				</div>
				<div class="navbar navbar-light navbar-expand-sm" style="overflow: auto;">
					<div class="container">
						<b-nav pills class="flex-nowrap">
							<b-nav-item
								v-for="pane in panes"
								:key="pane.value"
								:active="selectedPane == pane.value"
								@click="selectPane(pane)">
								{{ pane.title }}
							</b-nav-item>
						</b-nav>
					</div>
				</div>
			</div>

			<div class="container py-3 py-md-5" style="overflow: hidden">
				<div class="row">
					<div class="col-md-3">
						<!-- Big screen menu -->
						<template v-if="$mq.comfortable">
							<div class="card">
								<div class="card-body">
									<h2 class="h4 mb-0">{{ venueId ? 'Modifica' : 'Aggiungi' }} attività</h2>
									<p class="mb-0 text-muted" v-if="venueId">
										<router-link :to="`/venues/${venueId}`" title="Apri pagina">{{ venue.name || '(nessun nome)' }}</router-link>
									</p>
								</div>
								<b-list-group flush>
									<b-list-group-item
										href="#"
										v-for="pane in panes"
										:key="pane.value"
										:active="selectedPane == pane.value"
										@click="selectPane(pane)">
										<strong>{{ pane.title }}</strong>
									</b-list-group-item>
								</b-list-group>
							</div>
							<pg-button
								block
								class="mt-3"
								variant="primary"
								:disabled="isSaved"
								:loading="saving"
								@click="submit">
								{{ venueId ? 'Salva' : 'Aggiungi' }}
							</pg-button>
						</template>
					</div>
					<div class="col-md-9" ref="paneContainer">
						<div class="position-relative">
							<h4>{{ panes.find(pane => selectedPane == pane.value).title }}</h4>
							<transition :name="transition" @enter="onPaneSwitch">
								<keep-alive>
									<pg-venue-form-general-pane
										v-if ="selectedPane == 'general'"
										:venue="venue"
										:concessionaires="concessionaires"
										:categories="categories"
										:address.sync="venue.address"
										:coords.sync="venue.coords"
										:default-coords="DEFAULT_COORDS"
									/>
								</keep-alive>
							</transition>
							<transition :name="transition" @enter="onPaneSwitch">
								<pg-venue-form-services-pane
									v-if ="selectedPane == 'services'"
									:venue="venue"
									:vltPlatforms="vltPlatforms"
									:payPerViewPlatforms="payPerViewPlatforms"
								/>
							</transition>
							<transition :name="transition" @enter="onPaneSwitch">
								<pg-venue-form-contacts-pane
									v-if ="selectedPane == 'contacts'"
									:venue="venue"
								/>
							</transition>
							<transition :name="transition" @enter="onPaneSwitch">
								<pg-venue-form-hours-pane
									v-if ="selectedPane == 'hours'"
									:venue="venue"
									:hours.sync="venue.business_hours"
								/>
							</transition>
							<transition :name="transition" @enter="onPaneSwitch">
								<keep-alive>
									<pg-venue-form-photos-pane
										v-if ="selectedPane == 'photos'"
										:photos.sync="venue.photos"
									/>
								</keep-alive>
							</transition>
							<transition :name="transition" @enter="onPaneSwitch">
								<pg-venue-form-jackpots-pane
									v-if ="selectedPane == 'jackpots'"
									:venue="venue"
								/>
							</transition>
						</div>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>