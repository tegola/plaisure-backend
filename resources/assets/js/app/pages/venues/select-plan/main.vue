<script>
import { mapState, mapGetters } from 'vuex';
import { validationMixin } from 'vuelidate';
import { requiredIf } from 'vuelidate/lib/validators';
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import PgButton from 'prontogioco/app/components/button';
import { Card as StripeCard, createToken as createStripeToken } from 'vue-stripe-elements-plus';
import countryOptions from 'prontogioco/app/common/country-select-options';
import subscriptions from './subscriptions';
import PgSelectPlanPageSubscription from './card';
import venueFormStore from 'prontogioco/app/store/venue-form';

const locale = constants.APP_LOCALE.split('_')[0]; // it_IT -> it

export default {
	name: 'PgSelectVenuePlanPage',

	components: {
		BFormGroup,
		BInput,
		BSelect,
		PgButton,
		StripeCard,
		PgSelectPlanPageSubscription
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
				locale,
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
			countryOptions,
			stripeOptions,
			cardError: null,
			subscriptions,
			model: {
				subscription_name: '',
				legal_name: '',
				address_street: '',
				address_city: '',
				address_postcode: '',
				address_region: '',
				address_country: '',
				vat_number: '',
				stripe_token: null
			}
		};
	},

	computed: {
		...mapState('user', ['user']),
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

		address() {
			if (!this.venue) return;

			const a = this.venue.address;

			return [
				[a.street, a.number].join(' '),
				a.city
			].join(', ');
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
					address_street: this.user.address_street,
					address_city: this.user.address_city,
					address_postcode: this.user.address_postcode,
					address_region: this.user.address_region,
					address_country: this.user.address_country,
					vat_number: this.user.vat_number
				});
			}
		}
	},

	validations: {
		model: {
			legal_name: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			address_street: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			address_city: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			address_postcode: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			address_region: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			address_country: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
			},
			vat_number: {
				required: requiredIf(function() { return !this.hasBillingInfo; })
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

		onCardChange({ error }) {
			this.cardError = error ? error.message : null;
		},

		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) return;

			this.saving = true;

			// FIXME: Add user data
			createStripeToken().then(({ token, error }) => {
				this.saving = false;

				if (error) return; // FIXME: Gestire l'errore
				if (token) this.model.stripe_token = token.id;
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
				<h2 class="h4">{{ venue.subscription.name === 'default' ? 'Abbonati' : 'Modifica abbonamento' }}</h2>
				<p>
					<router-link :to="{ name: 'venues.edit', params: { venueId }}">
						<pg-icon icon="arrow-left" />
						<strong class="font-weight-bold">{{ venue.name }}</strong>
					</router-link>
					<span class="text-muted">({{ address }})</span>
				</p>

				<div class="card-group my-5">
					<pg-select-plan-page-subscription
						v-for="subscription in subscriptions"
						:key="subscription.machine_name"
						:subscription="subscription"
						:selected="model.subscription_name == subscription.machine_name"
						:current="venue.subscription.name == subscription.machine_name"
						:end-date="venue.subscription.ends_at.date"
						@select="model.subscription_name = subscription.machine_name"
					/>
				</div>

				<template v-if="model.subscription_name">
					<div class="row">
						<div class="col-lg-8 mx-auto">
							<div v-if="!hasBillingInfo" class="mb-5">
								<p>Per continuare, abbiamo bisogno dei dati di fatturazione. Inseriscili qui di seguito.</p>
								<b-form-group
									:state="!$v.model.legal_name.$error"
									:label="$t('pages.user_form.billing.legal_name')"
									:invalid-feedback="$t('pages.user_form.billing.legal_name_error')">
									<b-input v-model="model.legal_name" type="text" autocomplete="organization" />
								</b-form-group>
								<b-form-group
									:state="!$v.model.address_street.$error"
									:label="$t('pages.user_form.billing.address')"
									:invalid-feedback="$t('pages.user_form.billing.address_error')">
									<b-input v-model="model.address_street" type="text" autocomplete="street-address" />
								</b-form-group>
								<div class="row">
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
								<div class="row">
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
											:state="!$v.model.address_country.$error"
											:label="$t('pages.user_form.billing.country')"
											:invalid-feedback="$t('pages.user_form.billing.country_error')">
											<b-select v-model="model.address_country" :options="countryOptions" />
										</b-form-group>
									</div>
								</div>
								<b-form-group
									:state="!$v.model.vat_number.$error"
									:label="$t('pages.user_form.billing.vat_number')"
									:invalid-feedback="$t('pages.user_form.billing.vat_number_error')">
									<b-input v-model="model.vat_number" type="text" />
								</b-form-group>
							</div>

							<div class="mb4">
								<h6>Dati di fatturazione</h6>
								<p>
									<strong>{{ user.legal_name }}</strong><br>
									<small>
										{{ user.address_street }}
										-
										{{ user.address_city }}
										{{ user.address_postcode }}
										{{ user.address_region }}
										({{ user.address_country }})
									</small><br>
									Partita IVA: {{ user.vat_number }}
								</p>
								<p><router-link :to="{ name: 'user.edit' }">Modifica</router-link></p>
							</div>

							<h6>Pagamento</h6>
							<template v-if="hasCreditCard">
								<b-form-group :label="$t('Carta di credito')">
									<div class="row">
										<div class="col-auto">
											<div class="input-group">
												<div class="input-group-prepend">
													<code class="input-group-text font-weight-bold">{{ user.card_brand }}</code>
												</div>
												<div class="input-group-append flex-grow-1">
													<code class="input-group-text w-100 bg-transparent">**** **** **** {{ user.card_last_four }}</code>
												</div>
											</div>
										</div>
										<div class="col-auto">
											Card holder
										</div>
									</div>
									<p><router-link :to="{ name: 'user.edit' }">Modifica</router-link></p>
								</b-form-group>
							</template>

							<b-form-group
								v-else
								:state="!cardError"
								:label="$t('Carta di credito')"
								:invalid-feedback="cardError">
								<stripe-card
									:stripe="STRIPE_KEY"
									:options="stripeOptions"
									@change="onCardChange"
								/>
							</b-form-group>

							<p class="small">Stai per attivare un abbonamento costante. Se fai clic su "Abbonati", autorizzi {{ APP_NAME }} ad addebitarti mensilmente il costo dell'abbonamento (attualmente pari a 1,99 €/mese). Puoi annullare l'abbonamento in qualsiasi momento. Ulteriori informazioni.</p>
							<p class="small">Se fai clic su "Abbonati" accetti i Termini di servizio di {{ APP_NAME }} l'Informativa sulla privacy. Accetti inoltre che il tuo acquisto sarà subito disponibile e di rinunciare al diritto di recesso previsto dalla legge (ad eccezione dei servizi).</p>

							<div class="form-row justify-content-between">
								<div class="col-auto">
									<pg-button
										:disabled="saving"
										block
										variant="naked"
										@click="model.subscription_name = ''">
										Annulla
									</pg-button>
								</div>
								<div class="col col-md-auto">
									<pg-button
										:loading="saving"
										:block="$mq.constrained"
										variant="primary"
										@click="submit">
										Abbonati
									</pg-button>
								</div>
							</div>
						</div>
					</div>
				</template>
			</div>
		</template>

		<pg-page-footer />
	</div>
</template>