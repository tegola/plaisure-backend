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

	data() {
		return {
			appName: APP_NAME,
			model: {
				email: '',
				password: ''
			}
		}
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

	methods: {
		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) return;

			this.saving = true;

			// Prepare url
			let url = '/venues';
			if (this.venueId) url += `/${this.venueId}`

			this.$axios.post('/auth/login', this.model)
				.then(() => {
					// Set model backup as saved
					this.venueBackup = _cloneDeep(this.venue);
				}).catch(() => {}).then(() => {
					this.saving = false;
				})
		}
	}
}
</script>

<template>
	<div>
		<pg-navbar />

		<div class="container my-5">
			<div class="text-center mb-5">
				<h2>Accedi</h2>
				<p class="lead text-muted">Inserisci email e password per accedere a {{ appName }}.</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<b-form-group
							label="Indirizzo e-mail"
							:state="!$v.model.email.$error"
							invalid-feedback="Inserisci il tuo indirizzo e-mail.">
							<b-input type="email" v-model="model.email" autofocus />
						</b-form-group>

						<b-form-group
							label="Password"
							:state="!$v.model.password.$error"
							invalid-feedback="Inserisci la password.">
							<b-input type="password" v-model="model.password" />
						</b-form-group>

						<b-form-group>
							<pg-button type="submit" variant="primary" block>Accedi</pg-button>
						</b-form-group>

						<p class="text-center">
							<router-link to="/password/reset">Password dimenticata?</router-link>
						</p>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>