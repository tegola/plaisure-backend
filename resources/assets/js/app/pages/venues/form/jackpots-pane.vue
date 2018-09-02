<script>
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
		venue: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			formGroupProps
		};
	},

	computed: {
		$v() {
			return this.$parent.$v.venue;
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h4>{{ $t('pages.venue_form.jackpots.title') }}</h4>
		<hr>
		<b-form-group v-for="n in 3" :key="n"
			:label="$t('pages.venue_form.jackpots.name', { number: n })"
			v-bind="formGroupProps"
			:state="!$v.contacts.email.$error"
			:invalid-feedback="$t('pages.venue_form.jackpots.amount_error')">
			<div class="form-row">
				<div class="col-md col-lg-5">
					<b-input v-model="venue.jackpots[n].label" :placeholder="$t('pages.venue_form.jackpots.name_placeholder')" />
				</div>
				<div class="col-md col-lg-4">
					<b-input-group prepend="€">
						<b-input
							v-model.number="venue.jackpots[n].value"
							:placeholder="$t('pages.venue_form.jackpots.amount_placeholder')"
							type="number"
							class="text-right"
							min="0"
							step="0.01"
						/>
					</b-input-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>