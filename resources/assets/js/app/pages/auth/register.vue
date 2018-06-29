<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from 'prontogioco/app/components/button';
import { validationMixin } from 'vuelidate';
import { required, email, sameAs } from 'vuelidate/lib/validators';
import { APP_NAME } from 'prontogioco/constants';

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
			appName: APP_NAME,
			loading: false,
			model: {
				name: '',
				email: '',
				password: '',
				password_confirmation: ''
			}
		}
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
				required
			},
			password_confirmation: {
				sameAsPassword: sameAs('password')
			}
		}
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
					// Go to the user page
					this.$router.push({ name: 'home' })
				}).catch(error => {
					console.log('error', error);
				}).then(() => {
					this.loading = false;
				});
		}
	}
}
</script>

<template>
	<div>
		<pg-navbar />

		<div class="container my-5">
			<div class="text-center mb-5">
				<h2>Registrati a {{ appName }}</h2>
				<p class="lead text-muted">Registrandoti potrai modificare la tua attività.</p>
			</div>

			<div class="row">
				<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
					<form @submit.prevent="submit">
						<b-form-group
							label="Nome"
							:state="!$v.model.email.$error"
							invalid-feedback="Inserisci il tuo nome.">
							<b-input type="text" v-model="model.name" autofocus />
						</b-form-group>

						<b-form-group
							label="Indirizzo e-mail"
							:state="!$v.model.email.$error"
							invalid-feedback="Inserisci il tuo indirizzo e-mail.">
							<b-input type="email" v-model="model.email" />
						</b-form-group>

						<b-form-group
							label="Password"
							:state="!$v.model.password.$error"
							invalid-feedback="Inserisci la password.">
							<b-input type="password" v-model="model.password" />
						</b-form-group>

						<b-form-group
							label="Ripeti password"
							:state="!$v.model.password_confirmation.$error"
							invalid-feedback="Ripeti la password.">
							<b-input type="password" v-model="model.password_confirmation" />
						</b-form-group>

						<b-form-group>
							<pg-button type="submit" variant="primary" block>Registrati</pg-button>
						</b-form-group>

						<p class="text-center">
							oppure <router-link :to="{ name: 'home' }">torna all'home page</router-link>
						</p>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>