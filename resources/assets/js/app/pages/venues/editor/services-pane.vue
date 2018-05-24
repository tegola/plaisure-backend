<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import BCheckboxGroup from 'bootstrap-vue/es/components/form-checkbox/form-checkbox-group';

import formGroupProps from './form-group-props'

export default {
	name: 'PgVenueEditorCategoriesPane',

	components: {
		BFormGroup,
		BInput,
		BCheckbox,
		BCheckboxGroup
	},

	props: {
		venue: {
			type: Object,
			required: true
		},
		vltPlatforms: {
			type: Array,
			default: () => []
		},
		payPerViewPlatforms: {
			type: Array,
			default: () => []
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
		},
	}
}
</script>

<template>
	<div>
		<b-form-group v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<div><b-checkbox v-model="venue.sports_betting">Scommesse sportive</b-checkbox></div>
					<div><b-checkbox v-model="venue.virtual_betting">Scommesse virtuali</b-checkbox></div>
					<div><b-checkbox v-model="venue.horse_betting">Scommesse ippiche</b-checkbox></div>
					<div><b-checkbox v-model="venue.arcade_roulette">Roulette arcade</b-checkbox></div>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="N. macchine VLT"
			v-bind="formGroupProps"
			:state="!$v.vlt_machine_count.$error"
			invalid-feedback="Valore non valido.">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input type="number" v-model.number="venue.vlt_machine_count" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="N. macchine AWP"
			v-bind="formGroupProps"
			:state="!$v.awp_machine_count.$error"
			invalid-feedback="Valore non valido.">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input type="number" v-model.number="venue.awp_machine_count" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Posti a sedere"
			v-bind="formGroupProps"
			:state="!$v.seating_capacity.$error"
			invalid-feedback="Valore non valido.">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input type="number" v-model.number="venue.seating_capacity" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Posti auto"
			v-bind="formGroupProps"
			:state="!$v.parking_capacity.$error"
			invalid-feedback="Valore non valido.">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input type="number" v-model.number="venue.parking_capacity" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Piattaforme VLT"
			label-class="pt-0"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.vlt_platform_ids" stacked>
						<b-checkbox v-for="item in vltPlatforms" :value="item.id" :key="item.id">{{ item.name}}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Comodità"
			label-class="pt-0"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<div><b-checkbox v-model="venue.amenities.atm">Totem Bancomat</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.bar">Bar</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.pay_per_view">Pay per view</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.pos">POS</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.private_parking">Parcheggio privato</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.restaurant">Ristorante</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.security">Security</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.smoking_area">Area fumatori</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.wifi">Wi-Fi</b-checkbox></div>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Piattaforme Pay Per View"
			label-class="pt-0"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.pay_per_view_platform_ids" stacked>
						<b-checkbox v-for="item in payPerViewPlatforms" :value="item.id" :key="item.id">{{ item.name}}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>