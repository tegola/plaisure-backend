<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from '@/components/button';
import { validationMixin } from 'vuelidate';
import { required, email, minLength, sameAs } from 'vuelidate/lib/validators';
import { APP_NAME } from '@/constants';

export default {
	name: 'PgResetPasswordPage',

	components: {
		BFormGroup,
		BInput,
		PgButton
	},

	mixins: [validationMixin],

	props: {
		email: {
			type: String,
			required: true
		},
		token: {
			type: String,
			required: true
		}
	},

	data() {
		return {
			loading: false,
			error: false,
			model: {
				token: this.token,
				email: this.email,
				password: '',
				password_confirmation: ''
			}
		};
	},

	meta() {
		return {
			title: this.$t('pages.reset_password.title')
		};
	},

	validations: {
		model: {
			email: {
				email,
				required
			},
			password: {
				required,
				minLength: minLength(8)
			},
			password_confirmation: {
				sameAsPassword: sameAs('password')
			}
		}
	},

	created() {
		this.APP_NAME = APP_NAME;
	},

	methods: {
		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) return;

			this.loading = true;

			this.$axios.post('/auth/password/reset', this.model)
				.then(() => {
					// Login and go back to gome
					const loginModel = {
						email: this.model.email,
						password: this.model.password
					};
					this.$store.dispatch('user/login', loginModel)
						.then(() => {
							this.$router.push({ name: 'home' });
						});
				}).catch(() => {
					this.error = true;
					this.loading = false;
				});
		}
	}
};
</script>

<template>
	<div>
		<pg-navbar />

		<div class="container my-5">
			<div class="text-center mb-5">
				<h2>{{ $t('pages.reset_password.title') }}</h2>
				<p class="lead text-muted">{{ $t('pages.reset_password.intro') }}</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<p v-if="error" class="text-danger text-center">{{ $t('pages.reset_password.submit_error') }}</p>

						<b-form-group
							:label="$t('pages.reset_password.email')"
							:state="!$v.model.email.$error"
							:invalid-feedback="$t('pages.reset_password.email_error')">
							<b-input :value="model.email" type="email" plaintext readonly />
						</b-form-group>

						<b-form-group
							:label="$t('pages.reset_password.password')"
							:state="!$v.model.password.$error"
							:invalid-feedback="$t('pages.reset_password.password_error')">
							<b-input v-model="model.password" :disabled="loading" type="password" autofocus />
						</b-form-group>

						<b-form-group
							:label="$t('pages.reset_password.password_confirmation')"
							:state="!$v.model.password_confirmation.$error"
							:invalid-feedback="$t('pages.reset_password.password_confirmation_error')">
							<b-input v-model="model.password_confirmation" :disabled="loading" type="password" />
						</b-form-group>

						<b-form-group>
							<pg-button
								:loading="loading"
								type="submit"
								variant="primary"
								block>
								{{ $t('pages.reset_password.submit') }}
							</pg-button>
						</b-form-group>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>