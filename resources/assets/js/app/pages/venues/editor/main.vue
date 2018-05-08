<script>
import _cloneDeep from 'lodash/cloneDeep'
import _isEqual from 'lodash/isEqual'
import { validationMixin } from 'vuelidate'

import BNav from 'bootstrap-vue/es/components/nav/nav'
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item'
import BBtn from 'bootstrap-vue/es/components/button/button';

import PgVenueEditorGeneralPane from './general-pane'
import PgVenueEditorCategoriesPane from './categories-pane'
import PgVenueEditorAddressPane from './address-pane'
import PgVenueEditorContactsPane from './contacts-pane'
import PgVenueEditorPhotosPane from './photos-pane'
import PgVenueEditorJackpotsPane from './jackpots-pane'
import PgVenueEditorHoursPane from './hours-pane'

import validations from './validations'

export default {
	name: 'PgVenueEditor',

	components: {
		BNav,
		BNavItem,
		BBtn,
		PgVenueEditorGeneralPane,
		PgVenueEditorCategoriesPane,
		PgVenueEditorAddressPane,
		PgVenueEditorContactsPane,
		PgVenueEditorPhotosPane,
		PgVenueEditorJackpotsPane,
		PgVenueEditorHoursPane
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
		defaultMapCenter: pg.app.defaultMapCenter,
		panes: {
			general: 'Generale',
			categories: 'Categorie',
			address: 'Indirizzo',
			contacts: 'Contatti',
			photos: 'Foto',
			jackpots: 'Jackpot',
			hours: 'Orari'
		},
		selectedPane: 'general',
		categories: [],
		concessionaires: [],
		vltPlatforms: [],
		payPerViewPlatforms: [],
		venue: null,
		venueBackup: null
	}),

	computed: {
		isUnsaved() {
			return !_isEqual(this.venue, this.venueBackup);
		}
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

		submit() {

			// Validate
			this.$v.$touch();

			// Stop on validation errors
			// if (this.$v.$error) return;

			// this.saving = true;

			// Prepare url
			const url = [
				'/venues',
				this.venueId ? `/${this.venueId}` : null
			].join('');

			this.$axios.post(url, this.venue)
				.then(({ data }) => {
					console.log('data', data);
				})
		}
	},

	mounted() {
		this.loadData()
	}
}
</script>

<template>
	<!-- FIXME: Mostrare un loader mentre si caricano i dati -->
	<div v-if="venue">
		<div class="navbar navbar-light sticky-top">
			<div class="container d-flex flex-row">
				<div>
					<h2 class="h4 mb-0">{{ venue.id ? 'Modifica' : 'Aggiungi' }} attività</h2>
					<p class="mb-0">
						<template v-if="venue.name">{{ venue.name }}</template>
						<span class="text-muted" v-else>(Senza nome)</span>
					</p>
				</div>
				<b-nav pills>
					<b-nav-item
						v-for="(label, value) in panes"
						:key="value"
						:active="selectedPane == value"
						@click="selectedPane = value">
						{{ label }}
					</b-nav-item>
				</b-nav>
				<b-btn :variant="isUnsaved ? 'danger' : 'primary'" @click="submit">{{ venue.id ? 'Salva' : 'Aggiungi' }}</b-btn>
			</div>
		</div>
		<div class="container my-5">
			<div class="row">
				<div class="col-md-3 col-lg-2">
					<b-nav vertical pills>
						<b-nav-item
							v-for="(label, value) in panes"
							:key="value"
							:active="selectedPane == value"
							@click="selectedPane = value">
							{{ label }}
						</b-nav-item>
					</b-nav>
				</div>
				<div class="col-md-9 col-lg-8 mx-lg-auto">
					<pg-venue-editor-general-pane
						v-if ="selectedPane == 'general'"
						:venue="venue"
						:concessionaires="concessionaires"
						:vltPlatforms="vltPlatforms"
						:payPerViewPlatforms="payPerViewPlatforms"
					/>
					<pg-venue-editor-categories-pane
						v-if ="selectedPane == 'categories'"
						:venue="venue"
						:categories="categories"
					/>
					<keep-alive>
						<pg-venue-editor-address-pane
							v-if ="selectedPane == 'address'"
							:address.sync="venue.address"
							:coords.sync="venue.coords"
							:default-map-center="defaultMapCenter"
						/>
					</keep-alive>
					<pg-venue-editor-contacts-pane
						v-if ="selectedPane == 'contacts'"
						:venue="venue"
					/>
					<keep-alive>
						<pg-venue-editor-photos-pane
							v-if ="selectedPane == 'photos'"
							:venue="venue"
						/>
					</keep-alive>
					<pg-venue-editor-jackpots-pane
						v-if ="selectedPane == 'jackpots'"
						:venue="venue"
					/>
					<pg-venue-editor-hours-pane
						v-if ="selectedPane == 'hours'"
						:hours.sync="venue.business_hours"
					/>
				</div>
			</div>
		</div>
	</div>
</template>