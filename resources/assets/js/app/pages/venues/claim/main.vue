<script>
import { validationMixin } from 'vuelidate';
import { requiredIf } from 'vuelidate/lib/validators';
import axios from 'prontogioco/app/plugins/axios';
import PgButton from 'prontogioco/app/components/button';
import PgImageFrame from 'prontogioco/app/components/image-frame';
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';

import PgClaimVenuePageItem from './item';

const handleRoute = function(to, from, next) {
	const venueId = to.params.venueId;

	axios.get(`/venues/${venueId}/claim`)
		.then(response => {
			next(vm => {
				vm.venue = response.data.venue;
				vm.codeRequired = response.data.codeRequired;
			});
		})
		.catch(() => {
			// Can't claim this venue, go to detail page
			next({
				name: 'venues.detail',
				params: { venueId },
				replace: true
			});
		});
};

export default {
	name: 'ApClaimVenuePage',

	components: {
		PgButton,
		PgImageFrame,
		BFormGroup,
		BInput,
		PgClaimVenuePageItem
	},

	mixins: [validationMixin],

	props: {
		venueId: {
			type: String,
			required: true
		}
	},

	data() {
		return {
			saving: false,
			error: false,
			venue: null,
			codeRequired: false,
			model: {
				code: ''
			}
		};
	},

	computed: {
		address() {
			const a = this.venue.address;

			return [
				[a.street, a.number].join(' '),
				a.city
			].join(', ');
		}
	},

	validations: {
		model: {
			code: {
				required: requiredIf(function() {
					return this.codeRequired;
				})
			}
		}
	},

	beforeRouteEnter: handleRoute,

	methods: {
		submit() {
			// Validate
			this.$v.$touch();

			// Stop on validation errors
			if (this.$v.$error) {
				this.$refs.input.focus();
				return;
			}

			this.saving = true;

			this.$axios.post(`/venues/${this.venueId}/claim`, this.model)
				.then(() => {
					// Success, go to edit
					this.$router.push({
						name: 'user',
						replace: true
					});
				})
				.catch(() => {
					this.saving = false;
					this.$refs.input.focus();
					alert('Il codice non è corretto.');
				});
		}
	}
};
</script>

<template>
	<div class="ap-claim-venue-page">
		<pg-navbar variant="dark" />

		<div class="container my-5">
			<div class="row">
				<div class="col-md-8">
					<h3>Rivendica attività</h3>
					<p>Stai per rivendicare la seguente attività, che non ha un proprietario o gestore. Così facendo essa sarà assegnata a te e potrai gestirne i dati.</p>

					<pg-claim-venue-page-item v-if="venue" :venue="venue" />

					<template v-if="codeRequired">
						<p>Per continuare, inserisci il codice di censimento dell'attività come registrata con l'AAMS:</p>

						<form @submit.prevent="submit">
							<div class="row">
								<div class="col-sm">
									<b-form-group
										:state="!$v.model.code.$error"
										class="mb-0"
										invalid-feedback="Inserisci il codice di censimento AAMS"
										label="Codice AAMS"
										label-sr-only>
										<b-input
											ref="input"
											v-model.trim="model.code"
											autocomplete="off"
											autofocus
											placeholder="Codice censimento AAMS"
										/>
									</b-form-group>
								</div>
								<div class="col-sm-auto">
									<pg-button
										:loading="saving"
										type="submit"
										variant="primary"
										icon="arrow-right"
										icon-position="right"
										block>
										Continua
									</pg-button>
								</div>
							</div>
						</form>
					</template>

					<div v-else class="row align-items-center">
						<div class="col-sm">
							Per continuare, fai click su “Prosegui”.
						</div>
						<div class="col-sm-auto">
							<pg-button
								:loading="saving"
								variant="primary"
								icon="arrow-right"
								icon-position="right"
								@click="submit">
								Prosegui
							</pg-button>
						</div>
					</div>

					<hr class="mt-5">

					<router-link :to="{ name: 'venues.detail', params: { venueId: venueId } }">Torna all'attività</router-link>
				</div>

				<div class="col-md-4">
					<div class="card border-accent">
						<div class="card-body">
							<h5 class="card-title text-accent">È gratis!</h5>
							<p class="card-text">Gestire un'attività è completamente gratuito. Se lo vorrai, potrai sottoscrivere un'abbonamento mensile per promuoverla.</p>
							<p class="card-text font-weight-semibold">
								<router-link :to="{ name: 'promote' }" class="text-accent">
									Maggiori informazioni
									<pg-icon icon="arrow-right" />
								</router-link>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>