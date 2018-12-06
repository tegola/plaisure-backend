<script>
import _extend from 'lodash/extend';
import { validationMixin } from 'vuelidate';

import BNav from 'bootstrap-vue/es/components/nav/nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';
import BListGroup from 'bootstrap-vue/es/components/list-group/list-group';
import BListGroupItem from 'bootstrap-vue/es/components/list-group/list-group-item';

import PgButton from 'prontogioco/app/components/button';
import PgSubscriptionCard from 'prontogioco/app/components/subscription-card';

import PgVenueFormGeneralPane from './general-pane';
import PgVenueFormServicesPane from './services-pane';
import PgVenueFormContactsPane from './contacts-pane';
import PgVenueFormHoursPane from './hours-pane';
import PgVenueFormPhotosPane from './photos-pane';
import PgVenueFormJackpotsPane from './jackpots-pane';

import validations from './validations';

import constants from 'prontogioco/constants';
import venueFormStore from 'prontogioco/app/store/venue-form';

export default {
	name: 'PgVenueForm',

	components: {
		BNav,
		BNavItem,
		BListGroup,
		BListGroupItem,
		PgButton,
		PgSubscriptionCard,
		PgVenueFormGeneralPane,
		PgVenueFormServicesPane,
		PgVenueFormContactsPane,
		PgVenueFormHoursPane,
		PgVenueFormPhotosPane,
		PgVenueFormJackpotsPane
	},

	mixins: [validationMixin],

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			loading: false,
			error: false,
			saving: false,
			panes: [
				'general',
				'services',
				'contacts',
				'hours',
				'photos',
				'jackpots'
			]
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			const state = this.$store.state[this.storeName];
			return state ? state.venue : null;
		},

		isSaved() {
			return this.$store.getters[`${this.storeName}/isSaved`];
		}
	},

	meta() {
		return {
			title: this.venue && this.venue.id ? this.$t('pages.venue_form.title.edit') : this.$t('pages.venue_form.title.add')
		};
	},

	validations,

	beforeCreate() {
		_extend(this, constants);
	},

	created() {
		// Register store
		if (!this.$store.state[this.storeName]) {
			this.$store.registerModule(this.storeName, venueFormStore);
			this.$store.commit(`${this.storeName}/setVenueId`, this.venueId);
		}
	},

	mounted() {
		// Load venue if it isn't already loaded
		if (!this.venue) this.loadData();
	},

	methods: {
		loadData() {
			this.error = false;
			this.loading = true;

			this.$store.dispatch(`${this.storeName}/load`)
				.catch(() => {
					this.error = true;
				})
				.then(() => {
					this.loading = false;
				});
		},

		scrollIntoView(e) {
			const href = e.target.getAttribute('href');
			const el = href ? document.querySelector(href) : null;
			const offset = this.$refs.sectionNavWrapper.offsetHeight;

			e.preventDefault();

			if (el) window.scroll(0, el.offsetTop + offset);
		},

		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) return;

			this.saving = true;

			this.$store.dispatch(`${this.storeName}/save`)
				.then(() => {
					console.log('then in program');
				})
				.catch(() => {})
				.then(() => {
					this.saving = false;
				});
		}
	}
};
</script>

<template>
	<div class="pg-venue-form-page">
		<pg-navbar variant="dark" />

		<div v-if="loading || error" class="container d-flex text-muted text-center" style="height: 50vh">
			<div class="m-auto">
				<template v-if="loading">
					<pg-icon icon="circle-outline-notch" spinning />
					<h5 class="m-0">{{ $t('common.status.loading') }}&hellip;</h5>
				</template>
				<h4 v-if="error" class="text-danger mb-0">C'è stato un errore nel caricamento dei dati</h4>
			</div>
		</div>

		<div v-if="!loading && venue">
			<div class="secondary-nav">
				<div class="title-wrapper">
					<div class="container d-flex align-items-center justify-content-between">
						<h2 class="h5 mb-0">{{ venueId ? $t('pages.venue_form.title.edit') : $t('pages.venue_form.title.add') }}</h2>
						<pg-button
							:disabled="isSaved"
							:loading="saving"
							variant="primary"
							@click="submit">
							{{ $t('common.actions.save') }}
						</pg-button>
					</div>
				</div>
				<div ref="sectionNavWrapper" class="section-nav-wrapper">
					<div class="container">
						<b-nav v-b-scrollspy="123" class="section-nav">
							<b-nav-item
								v-for="pane in panes"
								:key="pane"
								:href="`#${pane}`"
								@click="scrollIntoView">
								{{ $t(`pages.venue_form.${pane}.title`) }}
							</b-nav-item>
						</b-nav>
					</div>
				</div>
			</div>

			<div class="container">
				<div class="row">
					<div class="col-lg-8 mx-lg-auto">
						<component
							v-for="pane in panes"
							:key="pane"
							:is="`pg-venue-form-${pane}-pane`"
							:id="pane"
							:venue-id="venueId"
						/>
					</div>
					<div class="col-lg-4">
						<pg-subscription-card
							v-if="venueId"
							:subscription="venue.subscription"
							:current-subscription="venue.subscription"
							:selected-subscription="venue.subscription"
							highlight="Abbonamento corrente"
							class="my-5"
							@select="$router.push({ name: 'venues.selectPlan', params: { venueId: venueId }})"
						/>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>
