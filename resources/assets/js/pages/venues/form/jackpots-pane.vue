<script>
import _extend from 'lodash/extend';

import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormJackpotsPane',

	components: {
		BFormGroup,
		BInput,
		BInputGroup
	},

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			formGroupProps
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		},

		venueJackpots: {
			get() {
				return this.venue.jackpots;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'jackpots',
					value
				});
			}
		},

		$v() {
			return this.$parent.$v.venue;
		}
	},

	methods: {
		onInput(type, number, value) {
			const jackpots = _extend({}, this.venueJackpots);
			jackpots[number][type] = type == 'value' ? parseInt(value) : value;
			this.venueJackpots = jackpots;
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h5>{{ $t('pages.venue_form.jackpots.title') }}</h5>
		<hr>
		<!-- FIXME: Controllare la validazione e i messaggi di errore -->
		<b-form-group v-for="n in 3" :key="n"
			:label="$t('pages.venue_form.jackpots.name', { number: n })"
			v-bind="formGroupProps"
			:state="!$v.contacts.email.$error"
			:invalid-feedback="$t('pages.venue_form.jackpots.amount_error')">
			<div class="form-row">
				<div class="col-md col-lg-5">
					<b-input
						:placeholder="$t('pages.venue_form.jackpots.name_placeholder')"
						:value="venueJackpots[n].label"
						class="mb-2 mb-md-0"
						@input="onInput('label', n, $event)"
					/>
				</div>
				<div class="col-md col-lg-5">
					<b-input-group prepend="€">
						<b-input
							:placeholder="$t('pages.venue_form.jackpots.amount_placeholder')"
							:value="venueJackpots[n].value"
							type="number"
							class="text-right"
							min="0"
							step="0.01"
							@input="onInput('value', n, $event)"
						/>
					</b-input-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>