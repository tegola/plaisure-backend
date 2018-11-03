<script>
import { mapState } from 'vuex';
import { validationMixin } from 'vuelidate';
import { required, requiredIf, email, minLength, sameAs } from 'vuelidate/lib/validators';
import countryOptions from 'prontogioco/app/common/country-select-options';

import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgUserFormPage',

	components: {
		BFormGroup,
		BInput,
		BSelect,
		BCheckbox,
		PgButton
	},

	mixins: [validationMixin],

	data() {
		return {
			loading: false,
			countryOptions,
			model: {}
		};
	},

	computed: {
		...mapState('user', ['user']),

		hasAnyLegalField() {
			const m = this.model;

			return Boolean(
				m.legal_name ||
				m.address_street ||
				m.address_city ||
				m.address_postcode ||
				m.address_region ||
				m.address_country ||
				m.vat_number
			);
		}
	},

	meta() {
		return {
			title: this.$t('pages.user_form.meta_title')
		};
	},

	watch: {
		user: {
			immediate: true,
			handler() {
				if (!this.user) return;

				this.model = {
					name: this.user.name,
					email: this.user.email,
					send_newsletter: this.user.send_newsletter,
					legal_name: this.user.legal_name,
					address_street: this.user.address_street,
					address_city: this.user.address_city,
					address_postcode: this.user.address_postcode,
					address_region: this.user.address_region,
					address_country: this.user.address_country,
					vat_number: this.user.vat_number,
					new_password: '',
					new_password_confirmation: ''
				};
			}
		}
	},

	validations: {
		model: {
			name: {
				required
			},
			email: {
				required,
				email
			},
			legal_name: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			address_street: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			address_city: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			address_postcode: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			address_region: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			address_country: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			vat_number: {
				required: requiredIf(function() { return this.hasAnyLegalField; })
			},
			new_password: {
				minLength: minLength(8)
			},
			new_password_confirmation: {
				sameAsPassword: sameAs('new_password')
			}
		}
	},

	methods: {
		submit() {
			// Validate
			this.$v.$touch();

			// Scroll to error on validation
			if (this.$v.$error) {
				alert(this.$t('common.status.invalid_form'));
				return;
			}

			this.loading = true;

			this.$store.dispatch('user/update', this.model)
				.then(() => {
					this.$router.push({ name: 'user' });
				}).catch(() => {
					alert(this.$t('common.status.save_error'));
				}).then(() => {
					this.loading = false;
				});
		}
	}
};
</script>

<template>
	<div>
		<pg-navbar variant="dark" />

		<div v-if="user" class="container my-5">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<h3>{{ $t('pages.user_form.title') }}</h3>
					<hr>

					<form method="post" class="mt-4" @submit.prevent="submit">
						<b-form-group
							:state="!$v.model.name.$error"
							:label="$t('pages.user_form.general.name')"
							:invalid-feedback="$t('pages.user_form.general.name_error')">
							<b-input v-model="model.name" type="text" autocomplete="name" />
						</b-form-group>
						<b-form-group
							:label="$t('pages.user_form.general.email')">
							<b-input v-model="model.email" type="email" autocomplete="email" disabled />
						</b-form-group>
						<b-form-group>
							<b-checkbox v-model="model.send_newsletter">{{ $t('pages.user_form.general.newsletter') }}</b-checkbox>
						</b-form-group>

						<h5 class="mt-5 font-weight-bold">{{ $t('pages.user_form.billing.title') }}</h5>
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

						<h5 class="mt-5 font-weight-bold">{{ $t('pages.user_form.password.title') }}</h5>
						<p>{{ $t('pages.user_form.password.intro') }}</p>
						<b-form-group
							:state="!$v.model.new_password.$error"
							:label="$t('pages.user_form.password.password')"
							:invalid-feedback="$t('pages.user_form.password.password_error')"
							:description="$t('pages.user_form.password.password_hint')">
							<b-input v-model="model.new_password" type="password" autocomplete="new-password" />
						</b-form-group>
						<b-form-group
							:state="!$v.model.new_password_confirmation.$error"
							:label="$t('pages.user_form.password.password_confirmation')"
							:invalid-feedback="$t('pages.user_form.password.password_confirmation_error')">
							<b-input v-model="model.new_password_confirmation" type="password" autocomplete="new-password" />
						</b-form-group>
						<b-form-group class="mt-3 text-right">
							<pg-button :block="$mq.constrained" :to="{ name: 'user' }">{{ $t('common.actions.cancel') }}</pg-button>
							<pg-button :block="$mq.constrained" :loading="loading" type="submit" variant="primary">{{ $t('common.actions.save') }}</pg-button>
						</b-form-group>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>