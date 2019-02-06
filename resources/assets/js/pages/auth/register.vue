<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from '@/components/button';
import { validationMixin } from 'vuelidate';
import { required, email, minLength } from 'vuelidate/lib/validators';
import { APP_NAME } from '@/constants';

export default {
	name: 'PgRegisterPage',

	components: {
		BFormGroup,
		BInput,
		PgButton
	},

	mixins: [validationMixin],

	props: {
		redirect: {
			type: String,
			default: '/'
		}
	},

	data() {
		return {
			loading: false,
			model: {
				name: '',
				email: '',
				password: ''
			}
		};
	},

	meta() {
		return {
			title: this.$t('pages.register.meta_title')
		};
	},

	validations: {
		model: {
			name: {
				required
			},
			email: {
				email,
				required
			},
			password: {
				required,
				minLength: minLength(8)
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

			this.$store.dispatch('user/register', this.model)
				.then(() => {
					// Go to the next page
					this.$router.push(this.redirect);
				}).catch(error => {
					const data = error.response.data;

					if (data.errors.email) {
						this.$refs.emailInput.focus();
						alert(this.$t('pages.register.submit_error'));
					}
				}).then(() => {
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
				<h2>{{ $t('pages.register.title', { name: APP_NAME }) }}</h2>
				<p class="lead text-muted">{{ $t('pages.register.intro') }}</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<b-form-group
							:label="$t('pages.register.name')"
							:state="!$v.model.email.$error"
							:invalid-feedback="$t('pages.register.name_error')">
							<b-input v-model="model.name" type="text" autofocus />
						</b-form-group>

						<b-form-group
							:label="$t('pages.register.email')"
							:state="!$v.model.email.$error"
							:invalid-feedback="$t('pages.register.email_error')">
							<b-input ref="emailInput" v-model="model.email" type="email" />
						</b-form-group>

						<b-form-group
							:label="$t('pages.register.password')"
							:state="!$v.model.password.$error"
							:invalid-feedback="$t('pages.register.password_error')">
							<b-input v-model="model.password" type="password" />
						</b-form-group>

						<i18n tag="p" class="small text-muted" path="pages.register.agree1">
							<a href="#" place="terms_link">{{ $t('pages.register.agree2') }}</a>
							<a href="#" place="privacy_link">{{ $t('pages.register.agree3') }}</a>
						</i18n>

						<b-form-group>
							<pg-button
								:loading="loading"
								type="submit"
								variant="primary"
								block>
								{{ $t('pages.register.submit') }}
							</pg-button>
						</b-form-group>

						<p class="text-center">
							<i18n path="pages.register.login1">
								<span place="link">
									<router-link :to="{ name: 'login', query: { redirect: redirect } }">{{ $t('pages.register.login2') }}</router-link>
								</span>
							</i18n>
						</p>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>