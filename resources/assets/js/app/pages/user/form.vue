<script>
import { mapState } from 'vuex';
import { validationMixin } from 'vuelidate';
import { required, email, minLength, sameAs } from 'vuelidate/lib/validators';

import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import PgButton from 'prontogioco/app/components/button';

import { APP_NAME } from 'prontogioco/constants';

export default {
	name: 'PgUserFormPage',

	components: {
		BFormGroup,
		BInput,
		BCheckbox,
		PgButton
	},

	mixins: [validationMixin],

	data() {
		return {
			loading: false,
			appName: APP_NAME,
			model: {}
		};
	},

	computed: {
		...mapState('user', ['user'])
	},

	watch: {
		user: {
			immediate: true,
			handler() {
				if (!this.user) return;

				this.model = {
					name: this.user.name,
					email: this.user.email,
					aams_subject_enrollment_code: this.user.aams_subject_enrollment_code,
					new_password: '',
					new_password_confirmation: '',
					send_newsletter: this.user.send_newsletter
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
			aams_subject_enrollment_code: {
				required
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

			// Stop on validation errors
			if (this.$v.$error) return;

			this.loading = true;

			this.$store.dispatch('user/update', this.model)
				.then(() => {
					this.$router.push({ name: 'user' });
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
		<pg-navbar variant="dark" />

		<div v-if="user" class="container my-5">
			<div class="row">
				<div class="col-md-8 col-lg-6 mx-auto">
					<h2>Modifica i tuoi dati</h2>
					<form method="post" @submit.prevent="submit">
						<b-form-group
							:state="!$v.model.name.$error"
							label="Nome e cognome"
							invalid-feedback="Inserisci nome e cognome.">
							<b-input v-model="model.name" type="text" />
						</b-form-group>
						<b-form-group
							label="E-mail">
							<b-input v-model="model.email" type="email" disabled />
						</b-form-group>
						<b-form-group
							:state="!$v.model.aams_subject_enrollment_code.$error"
							label="Codice iscrizione AAMS"
							invalid-feedback="Inserisci il tuo codice di iscrizione AAMS."
							description="Necessario per inserire la tua attività.">
							<b-input v-model="model.aams_subject_enrollment_code" type="text" />
						</b-form-group>
						<b-form-group
							:state="!$v.model.new_password.$error"
							label="Nuova password"
							invalid-feedback="Inserisci almeno 8 caratteri tra lettere e numeri"
							description="Almeno 8 caratteri tra lettere e numeri">
							<b-input v-model="model.new_password" type="password" />
						</b-form-group>
						<b-form-group
							:state="!$v.model.new_password_confirmation.$error"
							label="Nuova password"
							invalid-feedback="Le password non combaciano.">
							<b-input v-model="model.new_password_confirmation" type="password" />
						</b-form-group>
						<b-form-group>
							<b-checkbox v-model="model.send_newsletter">Voglio ricevere la newsletter di {{ appName }}</b-checkbox>
						</b-form-group>
						<b-form-group class="mt-3">
							<div class="d-flex justify-content-md-end">
								<pg-button :block="$mq.constrained" :loading="loading" type="submit" variant="primary">Salva</pg-button>
							</div>
						</b-form-group>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>