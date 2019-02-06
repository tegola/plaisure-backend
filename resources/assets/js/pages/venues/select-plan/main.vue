<script>
import { mapState, mapGetters } from 'vuex';
import { validationMixin } from 'vuelidate';
import { requiredIf } from 'vuelidate/lib/validators';
import _extend from 'lodash/extend';
import constants from '@/constants';
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import PgButton from '@/components/button';
import PgSubscriptionCard from '@/components/subscription-card';
import { Card as StripeCard, createToken } from 'vue-stripe-elements-plus';
import countryOptions from '@/common/country-select-options';
import subscriptions from './subscriptions';
import venueFormStore from '@/store/venue-form';

export default {
	name: 'PgSelectVenuePlanPage',

	components: {
		BFormGroup,
		BInput,
		BSelect,
		PgButton,
		PgSubscriptionCard,
		StripeCard
	},

	mixins: [validationMixin],

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		const stripeOptions = {
			elements: {
				locale: constants.APP_LOCALE_LANGUAGE,
				// Avoid loading fonts locally, since we have no support for
				// https. Instead, fonts are loaded from the system
				fonts: process.env.NODE_ENV == 'development' ? [] : [{
					family: 'Manrope',
					src: `url(${constants.APP_URL}/fonts/regular.otf)`
				}]
			},
			hidePostalCode: true,
			style: {
				base: {
					fontFamily: 'Manrope, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
					fontSize: '16px'
				}
			}
		};

		return {
			loading: false,
			error: false,
			saving: false,
			newBilling: false,
			countryOptions,
			stripeOptions,
			cardError: null,
			subscriptions,
			model: {
				subscription_name: '',
				legal_name: '',
				address_line1: '',
				address_line2: '',
				address_city: '',
				address_postcode: '',
				address_region: '',
				country: '',
				vat_number: '',
				token_id: null,
				card_holder_name: ''
			}
		};
	},

	computed: {
		...mapState('user', {
			user: 'user',
			userVenues: 'venues'
		}),
		...mapGetters('user', [
			'hasBillingInfo',
			'hasCreditCard'
		]),

		storeName() {
			return `venueForm/${this.venueId}`;
		},

		venue() {
			const state = this.$store.state[this.storeName];
			return state ? state.venue : null;
		},

		selectedSubscription() {
			if (!this.model.subscription_name) return;

			return this.subscriptions.find(subscription => subscription.name == this.model.subscription_name);
		},

		showBillingForm() {
			return !this.hasBillingInfo || !this.hasCreditCard || this.newBilling;
		},

		address() {
			if (!this.venue) return;

			const a = this.venue.address;

			return [a.line1, a.city].join(', ');
		},

		stripeTokenData() {
			if (!this.user) return;

			return {
				name: this.model.card_holder_name,
				address_line1: this.user.address_line1,
				address_line2: this.user.address_line2,
				address_city: this.user.address_city,
				address_postal_code: this.user.address_postcode,
				country: this.user.country
			};
		}
	},

	meta() {
		return {
			title: this.venue && this.venue.id ? this.$t('pages.venue_form.title.edit') : this.$t('pages.venue_form.title.add')
		};
	},

	watch: {
		user: {
			immediate: true,
			handler() {
				if (!this.user) return;

				_extend(this.model, {
					legal_name: this.user.legal_name,
					address_line1: this.user.address_line1,
					address_line2: this.user.address_line2,
					address_city: this.user.address_city,
					address_postcode: this.user.address_postcode,
					address_region: this.user.address_region,
					country: this.user.country,
					vat_number: this.user.vat_number
				});
			}
		}
	},

	validations: {
		model: {
			legal_name: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			address_line1: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			address_city: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			address_postcode: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			address_region: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			country: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			vat_number: {
				required: requiredIf(function() { return !this.hasBillingInfo || this.newBilling; })
			},
			token_id: {
				required: requiredIf(function() { return !this.hasCreditCard || this.newBilling; })
			},
			card_holder_name: {
				required: requiredIf(function() { return !this.hasCreditCard || this.newBilling; })
			}
		}
	},

	created() {
		_extend(this, constants);

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

			return this.$store.dispatch(`${this.storeName}/load`)
				.catch(() => {
					this.error = true;
				})
				.then(() => {
					this.loading = false;
				});
		},

		cancelSelection() {
			this.model.subscription_name = null;
		},

		onCardChange({ error }) {
			this.cardError = error ? error.message : null;
		},

		prepareToken() {
			return new Promise((resolve, reject) => {
				// No billing form, just return
				if (!this.showBillingForm) resolve();

				// Billing form visible, create the token for storing the new
				// cretid card
				if (this.showBillingForm) {
					createToken(this.stripeTokenData).then(({ token, error }) => {
						// Error
						if (error) return reject(error);

						// Success, store token id
						this.model.token_id = token.id;
						resolve();
					});
				}
			});
		},

		submit() {
			const msg = "C'è stato un errore nel tentativo di effettuare il pagamento. Ti preghiamo di riprovare più tardi.";

			this.saving = true;

			// Before checking for errors, we need to be sure that we get the
			// token for registering the new credit card
			this.prepareToken().then(() => {
				// Validate
				this.$v.$touch();

				// Stop on validation errors
				if (this.$v.$error) {
					this.saving = false;
					return;
				}

				// Prepare model to save - just the subscription name or the
				// whole model
				const model = this.showBillingForm
					? { subscription_name: this.model.subscription_name }
					: this.model;

				// Save subscription
				// FIXME: Move to store
				this.$axios.post(`/venues/${this.venueId}/subscribe`, model)
					.catch(() => {
						alert(msg);
					})
					.then(() => {
						this.saving = false;
						this.loadData();

						// Reload user data, including venues
						this.$store.dispatch('user/fetch');
					});

				// FIXME: Ricaricare le attività
			}).catch(() => {
				this.saving = false;
				alert(msg);
			});
		}
	}
};
</script>

<template>
	<div class="pg-select-venue-plan-page">
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

		<template v-if="!loading && venue">
			<div class="container my-5">
				<h2 class="h4">{{ venue.subscription ? 'Modifica abbonamento' : 'Abbonati' }}</h2>
				<p>
					<router-link :to="{ name: 'venues.edit', params: { venueId }}">
						<pg-icon icon="arrow-left" />
						<strong class="font-weight-bold">{{ venue.name }}</strong>
					</router-link>
					<span class="text-muted">({{ address }})</span>
				</p>

				<div v-if="!model.subscription_name" class="row my-5">
					<div
						v-for="subscription in subscriptions"
						:key="subscription.name"
						class="col-md-4 d-flex">
						<pg-subscription-card
							:subscription="subscription"
							:current-subscription="venue.subscription"
							:selected-subscription-name="model.subscription_name"
							class="flex-fill mb-3 mb-md-0"
							@select="model.subscription_name = subscription.name"
						/>
					</div>
				</div>


				<div v-if="selectedSubscription" class="row">
					<div class="col-lg-4 order-lg-2">
						<pg-subscription-card
							:subscription="selectedSubscription"
							:current-subscription="venue.subscription"
							:selected-subscription="selectedSubscription"
							highlight="Nuovo abbonamento"
							@select="cancelSelection"
						/>
					</div>
					<div class="col-lg-7 mr-lg-auto order-lg-0">
						<!-- Back to default subscription -->
						<template v-if="model.subscription_name == 'default'">
							<p>Hai scelto l'abbonamento gratuito. Questo annullerà il rinnovo mensile e non ti verrà addebitato nessun altro costo. L'attuale abbonamento rimarrà comunque attivo fino allo scadere del periodo già pagato.</p>
						</template>

						<!-- All other subscriptions -->
						<template v-else>
							<p>
								Hai scelto un abbonamento a pagamento.
								<template v-if="!hasBillingInfo">Per continuare, abbiamo bisogno dei dati di fatturazione. Inseriscili qui di seguito.</template>
								<template v-if="hasBillingInfo">Assicurati che i dati di fatturazione e pagamento siano corretti. Se non lo sono, puoi modificarli prima di continuare.</template>
							</p>

							<p v-if="hasBillingInfo && userVenues.length > 1" class="font-weight-bold">
								<strong class="text-danger">Attenzione:</strong> se hai abbonamenti attivi sulle altre attività, queste verranno aggiornate con i nuovi dati.
							</p>

							<div v-if="!showBillingForm" class="my-5">
								<div class="row">
									<div class="col-md">
										<p class="initialism text-muted font-size-xs mb-1">Fatturazione</p>
										<p>
											<strong>{{ user.legal_name }}</strong><br>
											{{ user.address_line1 }},
											<template v-if="user.address_line2">{{ user.address_line2 }},</template>
											{{ user.address_city }}
											{{ user.address_postcode }}
											{{ user.address_region }}
											({{ user.country }})<br>
											P. IVA: {{ user.vat_number }}
										</p>
									</div>
									<div class="col-md">
										<p class="initialism text-muted font-size-xs mb-1">Pagamento (carta di credito)</p>
										<p>
											{{ user.card_brand }} <code>**** {{ user.card_last_four }}</code><br>
											{{ user.card_holder_name }}<br>
											Scadenza: {{ user.card_expiry_month }}/{{ user.card_expiry_year }}
										</p>
									</div>
								</div>
								<p class="text-center">
									<pg-button @click="newBilling = true">Modifica dati di fatturazione</pg-button>
								</p>
							</div>

							<template v-if="showBillingForm">
								<h5 class="mt-5">Fatturazione</h5>
								<hr>
								<b-form-group
									:state="!$v.model.legal_name.$error"
									:label="$t('pages.user_form.billing.legal_name')"
									:invalid-feedback="$t('pages.user_form.billing.legal_name_error')">
									<b-input v-model="model.legal_name" type="text" autocomplete="organization" autofocus />
								</b-form-group>
								<b-form-group
									:state="!$v.model.address_line1.$error"
									:label="$t('pages.user_form.billing.address')"
									:invalid-feedback="$t('pages.user_form.billing.address_error')">
									<b-input v-model="model.address_line1" type="text" autocomplete="address-line1" class="mb-2" />
									<b-input v-model="model.address_line2" type="text" autocomplete="address-line2" />
								</b-form-group>
								<div class="form-row">
									<div class="col-sm-4">
										<b-form-group
											:state="!$v.model.address_postcode.$error"
											:label="$t('pages.user_form.billing.postcode')"
											:invalid-feedback="$t('pages.user_form.billing.postcode_error')">
											<b-input v-model="model.address_postcode" type="text" autocomplete="postal-code" />
										</b-form-group>
									</div>
									<div class="col-sm-8">
										<b-form-group
											:state="!$v.model.address_city.$error"
											:label="$t('pages.user_form.billing.city')"
											:invalid-feedback="$t('pages.user_form.billing.city_error')">
											<b-input v-model="model.address_city" type="text" autocomplete="address-level2" />
										</b-form-group>
									</div>
								</div>
								<div class="form-row">
									<div class="col-sm">
										<b-form-group
											:state="!$v.model.address_region.$error"
											:label="$t('pages.user_form.billing.region')"
											:invalid-feedback="$t('pages.user_form.billing.region_error')">
											<b-input v-model="model.address_region" type="text" autocomplete="address-level1" />
										</b-form-group>
									</div>
									<div class="col-sm">
										<b-form-group
											:state="!$v.model.country.$error"
											:label="$t('pages.user_form.billing.country')"
											:invalid-feedback="$t('pages.user_form.billing.country_error')">
											<b-select v-model="model.country" :options="countryOptions" />
										</b-form-group>
									</div>
								</div>
								<b-form-group
									:state="!$v.model.vat_number.$error"
									:label="$t('pages.user_form.billing.vat_number')"
									:invalid-feedback="$t('pages.user_form.billing.vat_number_error')">
									<b-input v-model="model.vat_number" type="text" />
								</b-form-group>

								<h5 class="mt-5">Pagamento</h5>
								<hr>
								<b-form-group
									:state="!cardError"
									:label="$t('Carta di credito')"
									:invalid-feedback="cardError">
									<stripe-card
										:stripe="STRIPE_KEY"
										:options="stripeOptions"
										@change="onCardChange"
									/>
								</b-form-group>
								<b-form-group
									:state="!$v.model.card_holder_name.$error"
									:label="$t('Nome e cognome intestatario')"
									:invalid-feedback="$t('Inserisci il nome dell\'intestatario come mostrato sulla carta')">
									<b-input v-model="model.card_holder_name" type="text" autocomplete="cc-name" />
								</b-form-group>
							</template>

							<p class="small">Stai per attivare un abbonamento costante. Se fai clic su "Abbonati", autorizzi {{ APP_NAME }} ad addebitarti mensilmente il costo dell'abbonamento (attualmente pari a 1,99 €/mese). Puoi annullare l'abbonamento in qualsiasi momento. Ulteriori informazioni.</p>
							<p class="small">Se fai clic su "Abbonati" accetti i Termini di servizio di {{ APP_NAME }} l'Informativa sulla privacy. Accetti inoltre che il tuo acquisto sarà subito disponibile e di rinunciare al diritto di recesso previsto dalla legge (ad eccezione dei servizi).</p>
						</template>

						<!-- Confirm change -->
						<div class="text-center">
							<pg-button
								:loading="saving"
								:block="$mq.constrained"
								variant="primary"
								size="lg"
								@click="submit">
								{{ venue.subscription ? 'Modifica abbonamento' : 'Abbonati' }}
							</pg-button>
						</div>
					</div>
				</div>
			</div>
		</template>

		<pg-page-footer />
	</div>
</template>