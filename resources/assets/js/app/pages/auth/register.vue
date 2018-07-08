<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import PgButton from 'prontogioco/app/components/button';
import { validationMixin } from 'vuelidate';
import { required, email, minLength } from 'vuelidate/lib/validators';
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
				password: ''
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
				required,
				minLength: minLength(8)
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
					// Go to the next page
					this.$router.push(this.redirect)
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
				<h2>Iscriviti a {{ appName }}</h2>
				<p class="lead text-muted">Potrai così registrare o modificare la tua attività.</p>
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
							invalid-feedback="Scegli una password.">
							<b-input type="password" v-model="model.password" />
						</b-form-group>

						<p class="small text-muted">Cliccando su su Iscriviti, accetti le nostre <a href="#">Condizioni</a>. Scopri in che modo usiamo i tuoi dati nella nostra <a href="#">Normativa sui dati</a>.</p>

						<b-form-group>
							<pg-button type="submit" variant="primary" block>Iscriviti</pg-button>
						</b-form-group>

						<p class="text-center">
							Sei già registrato? <router-link :to="{ name: 'login', query: { redirect: redirect } }">Accedi</router-link>
						</p>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>