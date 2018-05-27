<script>
import _cloneDeep from 'lodash/cloneDeep';
import _extend from 'lodash/extend';
import _isEqual from 'lodash/isEqual';
import { validationMixin } from 'vuelidate';

import anime from 'animejs';

import BNav from 'bootstrap-vue/es/components/nav/nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';

import PgButton from 'prontogioco/app/components/button';
import PgVenueEditorGeneralPane from './general-pane';
import PgVenueEditorServicesPane from './services-pane';
import PgVenueEditorContactsPane from './contacts-pane';
import PgVenueEditorHoursPane from './hours-pane';
import PgVenueEditorPhotosPane from './photos-pane';
import PgVenueEditorJackpotsPane from './jackpots-pane';

import validations from './validations';

import constants from 'prontogioco/constants'

export default {
	name: 'PgVenueEditor',

	components: {
		BNav,
		BNavItem,
		PgButton,
		PgVenueEditorGeneralPane,
		PgVenueEditorServicesPane,
		PgVenueEditorContactsPane,
		PgVenueEditorHoursPane,
		PgVenueEditorPhotosPane,
		PgVenueEditorJackpotsPane
	},

	mixins: [validationMixin],

	props: {
		venueId: {
			type: [String, Number],
			required: true
		}
	},

	data: () => ({
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
	}),

	computed: {
		isUnsaved() {
			return !_isEqual(this.venue, this.venueBackup);
		},
	},

	validations,

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

					this.venue = _cloneDeep(data.venue);
					this.venueBackup = _cloneDeep(data.venue);
				})
		},

		selectPane(newPane) {
			const newIndex = this.panes.indexOf(newPane);
			const oldIndex = this.panes.indexOf(this.panes.find(pane => pane.value === this.selectedPane));

			this.transition = newIndex > oldIndex ? 'slide-left' : 'slide-right';
			this.$nextTick(() => {
				this.selectedPane = newPane.value;
			})
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
				.then(({ data }) => {
					console.log('data', data);
				}).catch().then(() => {
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
	<!-- FIXME: Mostrare un loader mentre si caricano i dati -->
	<div>
		<div class="sticky-top">
			<div class="navbar navbar-light navbar-expand-sm">
				<div class="container">
					<div>
						<h2 class="h4 mb-0">{{ venue && venue.id ? 'Modifica' : 'Aggiungi' }} attività</h2>
					</div>
					<b-nav pills class="ml-4 mr-auto" v-if="$mq.comfortable">
						<b-nav-item
							v-for="pane in panes"
							:key="pane.value"
							:active="selectedPane == pane.value"
							@click="selectPane(pane)">
							{{ pane.title }}
						</b-nav-item>
					</b-nav>
					<pg-button :variant="isUnsaved ? 'danger' : 'primary'" :loading="saving" @click="submit">{{ venue && venue.id ? 'Salva' : 'Aggiungi' }}</pg-button>
				</div>
			</div>
			<div class="navbar navbar-light navbar-expand-sm" style="overflow: auto;" v-if="$mq.constrained">
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
		<div class="container my-5" ref="paneContainer">
			<div class="position-relative" v-if="venue">
				<transition :name="transition" @enter="onPaneSwitch">
					<keep-alive>
						<pg-venue-editor-general-pane
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
					<pg-venue-editor-services-pane
						v-if ="selectedPane == 'services'"
						:venue="venue"
						:vltPlatforms="vltPlatforms"
						:payPerViewPlatforms="payPerViewPlatforms"
					/>
				</transition>
				<transition :name="transition" @enter="onPaneSwitch">
					<pg-venue-editor-contacts-pane
						v-if ="selectedPane == 'contacts'"
						:venue="venue"
					/>
				</transition>
				<transition :name="transition" @enter="onPaneSwitch">
					<pg-venue-editor-hours-pane
						v-if ="selectedPane == 'hours'"
						:venue="venue"
						:hours.sync="venue.business_hours"
					/>
				</transition>
				<transition :name="transition" @enter="onPaneSwitch">
					<keep-alive>
						<pg-venue-editor-photos-pane
							v-if ="selectedPane == 'photos'"
							:venue="venue"
						/>
					</keep-alive>
				</transition>
				<transition :name="transition" @enter="onPaneSwitch">
					<pg-venue-editor-jackpots-pane
						v-if ="selectedPane == 'jackpots'"
						:venue="venue"
					/>
				</transition>
			</div>
		</div>
	</div>
</template>