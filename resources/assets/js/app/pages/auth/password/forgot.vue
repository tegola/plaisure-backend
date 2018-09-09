<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from 'prontogioco/app/components/button';
import { validationMixin } from 'vuelidate';
import { required, email } from 'vuelidate/lib/validators';
import { APP_NAME } from 'prontogioco/constants';

export default {
	name: 'PgResetPasswordPage',

	components: {
		BFormGroup,
		BInput,
		PgButton
	},

	mixins: [validationMixin],

	data() {
		return {
			loading: false,
			done: false,
			model: {
				email: ''
			}
		};
	},

	meta() {
		return {
			title: this.$t('pages.forgot_password.title')
		};
	},

	validations: {
		model: {
			email: {
				email,
				required
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

			this.$axios.post('/auth/password/forgot', this.model)
				.then(() => {
					this.model.email = '';
					this.$v.$reset();
					this.done = true;
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
				<h2>{{ $t('pages.forgot_password.title') }}</h2>
				<p class="lead text-muted">{{ $t('pages.forgot_password.intro') }}</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<p v-if="done" class="lead text-success text-center">{{ $t('pages.forgot_password.submit_success') }}</p>
						<b-form-group
							:label="$t('pages.forgot_password.email')"
							:state="!$v.model.email.$error"
							:invalid-feedback="$t('pages.forgot_password.email_error')">
							<b-input v-model="model.email" :disabled="loading" type="email" autofocus />
						</b-form-group>

						<b-form-group>
							<pg-button
								:loading="loading"
								type="submit"
								variant="primary"
								block>
								{{ $t('pages.forgot_password.submit') }}
							</pg-button>
						</b-form-group>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>