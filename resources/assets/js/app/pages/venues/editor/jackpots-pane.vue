<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

import formGroupProps from './form-group-props'

export default {
	name: 'PgVenueEditorJackpotsPane',

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
}
</script>

<template>
	<div>
		<b-form-group v-for="n in 3" :key="n"
			:label="`Jackpot ${n}`"
			v-bind="formGroupProps"
			:state="!$v.contacts.email.$error"
			invalid-feedback="Inserisci nu numero valido.">
			<div class="form-row">
				<div class="col-md col-lg-5">
					<b-input placeholder="Nome" v-model="venue.jackpots[n].label" />
				</div>
				<div class="col-md col-lg-4">
					<b-input-group prepend="€">
						<b-input
							type="number"
							class="text-right"
							placeholder="Valore"
							v-model.number="venue.jackpots[n].value"
							min="0"
							step="0.01"
						/>
					</b-input-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>