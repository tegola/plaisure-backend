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

	meta() {
		if (!this.venue) return;

		return {
			title: this.$t('pages.venue_claim.meta_title', {
				name: this.venue.name
			})
		};
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
					alert(this.$t('pages.venue_claim.submit_error'));
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
					<h3>{{ $t('pages.venue_claim.title') }}</h3>
					<p>{{ $t('pages.venue_claim.intro') }}</p>

					<pg-claim-venue-page-item v-if="venue" :venue="venue" />

					<form v-if="codeRequired" @submit.prevent="submit">
						<p>{{ $t('pages.venue_claim.continue_code') }}</p>

						<div class="row">
							<div class="col-sm">
								<b-form-group
									:state="!$v.model.code.$error"
									:label="$t('pages.venue_claim.code')"
									:invalid-feedback="$t('pages.venue_claim.code_error')"
									class="mb-0"
									label-sr-only>
									<b-input
										ref="input"
										:placeholder="$t('pages.venue_claim.code_placeholder')"
										v-model.trim="model.code"
										autocomplete="off"
										autofocus
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
									{{ $t('pages.venue_claim.submit') }}
								</pg-button>
							</div>
						</div>
					</form>

					<div v-else class="row align-items-center">
						<div class="col-sm">
							<p>{{ $t('pages.venue_claim.continue_nocode') }}</p>
						</div>
						<div class="col-sm-auto">
							<pg-button
								:loading="saving"
								variant="primary"
								icon="arrow-right"
								icon-position="right"
								@click="submit">
								{{ $t('pages.venue_claim.submit') }}
							</pg-button>
						</div>
					</div>

					<hr class="mt-5">

					<router-link :to="{ name: 'venues.detail', params: { venueId: venueId } }">{{ $t('pages.venue_claim.back') }}</router-link>
				</div>

				<div class="col-md-4 mt-4 mt-md-0">
					<div class="card border-accent">
						<div class="card-body">
							<h5 class="card-title text-accent">{{ $t('pages.venue_claim.infobox.title') }}</h5>
							<p class="card-text">{{ $t('pages.venue_claim.infobox.body') }}</p>
							<p class="card-text font-weight-semibold">
								<router-link :to="{ name: 'promote' }" class="text-accent">
									{{ $t('pages.venue_claim.infobox.action') }}
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