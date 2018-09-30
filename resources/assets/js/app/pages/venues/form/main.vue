<script>
import _cloneDeep from 'lodash/cloneDeep';
import _extend from 'lodash/extend';
import _isEqual from 'lodash/isEqual';
import { validationMixin } from 'vuelidate';

import BNav from 'bootstrap-vue/es/components/nav/nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';

import BListGroup from 'bootstrap-vue/es/components/list-group/list-group';
import BListGroupItem from 'bootstrap-vue/es/components/list-group/list-group-item';

import PgButton from 'prontogioco/app/components/button';
import PgPlanBoardModal from 'prontogioco/app/components/plan-board-modal';
import PgVenueFormGeneralPane from './general-pane';
import PgVenueFormServicesPane from './services-pane';
import PgVenueFormContactsPane from './contacts-pane';
import PgVenueFormHoursPane from './hours-pane';
import PgVenueFormPhotosPane from './photos-pane';
import PgVenueFormJackpotsPane from './jackpots-pane';

import validations from './validations';

import constants from 'prontogioco/constants';

export default {
	name: 'PgVenueForm',

	components: {
		BNav,
		BNavItem,
		BListGroup,
		BListGroupItem,
		PgButton,
		PgPlanBoardModal,
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
			],
			categories: [],
			concessionaires: [],
			vltPlatforms: [],
			payPerViewPlatforms: [],
			venue: null,
			venueBackup: null
		};
	},

	computed: {
		isSaved() {
			return _isEqual(this.venue, this.venueBackup);
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

	mounted() {
		this.loadData();
	},

	methods: {
		loadData() {
			// Prepare url
			const url = [
				'/venues',
				this.venueId ? `/${this.venueId}/edit` : '/add'
			].join('');

			// this.error = false;
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
					});

					this.venue = _cloneDeep(venue);
					this.venueBackup = _cloneDeep(venue);

					setTimeout(() => {
						this.loading = false;
					}, 500);
				})
				.catch(() => {
					this.error = true;
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

			// Prepare url
			let url = '/venues';
			if (this.venueId) url += `/${this.venueId}`;

			this.$axios.post(url, this.venue)
				.then(() => {
					// Set model backup as saved
					this.venueBackup = _cloneDeep(this.venue);
				}).catch(() => {}).then(() => {
					this.saving = false;
				});
		}
	}
};
</script>

<template>
	<div class="pg-venue-form-page">
		<pg-navbar variant="dark" />

		<pg-plan-board-modal />

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
						<h2 class="h4 mb-0">{{ venueId ? $t('pages.venue_form.title.edit') : $t('pages.venue_form.title.add') }}</h2>
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
						<b-nav v-b-scrollspy="113" class="section-nav">
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
					<div class="col-lg-9 mx-lg-auto">
						<pg-venue-form-general-pane
							id="general"
							:venue="venue"
							:concessionaires="concessionaires"
							:categories="categories"
							:address.sync="venue.address"
							:coords.sync="venue.coords"
						/>
						<pg-venue-form-services-pane
							id="services"
							:venue="venue"
							:vlt-platforms="vltPlatforms"
							:pay-per-view-platforms="payPerViewPlatforms"
						/>
						<pg-venue-form-contacts-pane
							id="contacts"
							:venue="venue"
						/>
						<pg-venue-form-hours-pane
							id="hours"
							:venue="venue"
							:hours.sync="venue.business_hours"
						/>
						<pg-venue-form-photos-pane
							id="photos"
							:photos.sync="venue.photos"
							:plan="venue.plan"
						/>
						<pg-venue-form-jackpots-pane
							id="jackpots"
							:venue="venue"
						/>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>
