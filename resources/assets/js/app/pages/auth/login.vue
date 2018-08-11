<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from 'prontogioco/app/components/button';
import { validationMixin } from 'vuelidate';
import { required, email } from 'vuelidate/lib/validators';
import { APP_NAME } from 'prontogioco/constants';

export default {
	name: 'PgLoginPage',

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
				email: '',
				password: ''
			}
		};
	},

	meta() {
		return {
			title: this.$t('pages.login.meta_title')
		};
	},

	validations: {
		model: {
			email: {
				email,
				required
			},
			password: {
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

			this.$store.dispatch('user/login', this.model)
				.then(() => {
					// Go to the next page
					this.$router.push(this.redirect);
				}).catch(error => {
					console.log('error', error);
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
				<h2>{{ $t('pages.login.title') }}</h2>
				<p class="lead text-muted">{{ $t('pages.login.intro', { name: APP_NAME }) }}</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<b-form-group
							:label="$t('pages.login.email')"
							:state="!$v.model.email.$error"
							:invalid-feedback="$t('pages.login.email_error')">
							<b-input v-model="model.email" type="email" autofocus />
						</b-form-group>

						<b-form-group
							:label="$t('pages.login.password')"
							:state="!$v.model.password.$error"
							:invalid-feedback="$t('pages.login.password_error')">
							<b-input v-model="model.password" type="password" />
						</b-form-group>

						<b-form-group>
							<pg-button type="submit" variant="primary" block>{{ $t('pages.login.submit') }}</pg-button>
						</b-form-group>

						<p class="text-center">
							<router-link to="/password/reset">{{ $t('pages.login.forgot') }}</router-link><br>
							<i18n path="pages.login.register1">
								<span place="link">
									<router-link :to="{ name: 'register', query: { redirect: redirect } }">{{ $t('pages.login.register2') }}</router-link>
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