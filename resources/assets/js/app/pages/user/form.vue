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
		}
	},

	computed: {
		...mapState('user', ['user'])
	},

	watch: {
		user(newUser) {
			this.model = {
				name: newUser.name,
				email: newUser.email,
				aams_subject_enrollment_code: newUser.aams_subject_enrollment_code,
				new_password: '',
				new_password_confirmation: '',
				send_newsletter: newUser.send_newsletter
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
					alert('dati salvati');
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
		<pg-navbar variant="dark" />

		<div class="container my-5" v-if="user">
			<div class="row">
				<div class="col-md-8 col-lg-6 mx-auto">
					<h2>Modifica i tuoi dati</h2>
					<form method="post" @submit.prevent="submit">
						<b-form-group
							label="Nome e cognome"
							:state="!$v.model.name.$error"
							invalid-feedback="Inserisci nome e cognome.">
							<b-input type="text" v-model="model.name" />
						</b-form-group>
						<b-form-group
							label="E-mail">
							<b-input type="email" v-model="model.email" disabled />
						</b-form-group>
						<b-form-group
							label="Codice iscrizione AAMS"
							:state="!$v.model.aams_subject_enrollment_code.$error"
							invalid-feedback="Inserisci il tuo codice di iscrizione AAMS."
							description="Necessario per inserire la tua attività.">
							<b-input type="text" v-model="model.aams_subject_enrollment_code" />
						</b-form-group>
						<b-form-group
							label="Nuova password"
							:state="!$v.model.new_password.$error"
							invalid-feedback="Inserisci almeno 8 caratteri tra lettere e numeri"
							description="Almeno 8 caratteri tra lettere e numeri">
							<b-input type="password" v-model="model.new_password" />
						</b-form-group>
						<b-form-group
							label="Nuova password"
							:state="!$v.model.new_password_confirmation.$error"
							invalid-feedback="Le password non combaciano.">
							<b-input type="password" v-model="model.new_password_confirmation" />
						</b-form-group>
						<b-form-group>
							<b-checkbox v-model="model.send_newsletter">Voglio ricevere la newsletter di {{ appName }}</b-checkbox>
						</b-form-group>
						<b-form-group class="mt-3">
							<div class="d-flex justify-content-md-end">
								<pg-button type="submit" variant="primary" :block="$mq.constrained" :loading="loading">Salva</pg-button>
							</div>
						</b-form-group>
					</form>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>