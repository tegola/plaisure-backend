<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import BCheckboxGroup from 'bootstrap-vue/es/components/form-checkbox/form-checkbox-group';

import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormCategoriesPane',

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
		}
	}
};
</script>

<template>
	<div>
		<b-form-group v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<div><b-checkbox v-model="venue.sports_betting">{{ $t('pages.venue_form.services.sports_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.virtual_betting">{{ $t('pages.venue_form.services.virtual_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.horse_betting">{{ $t('pages.venue_form.services.horse_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.arcade_roulette">{{ $t('pages.venue_form.services.arcade_roulette') }}</b-checkbox></div>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.vlt_machine_count.$error"
			:label="$t('pages.venue_form.services.vlt_machine_count')"
			:invalid-feedback="$t('pages.venue_form.services.invalid_value')">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input v-model.number="venue.vlt_machine_count" type="number" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.awp_machine_count.$error"
			:label="$t('pages.venue_form.services.awp_machine_count')"
			:invalid-feedback="$t('pages.venue_form.services.invalid_value')">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input v-model.number="venue.awp_machine_count" type="number" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.seating_capacity.$error"
			:label="$t('pages.venue_form.services.seating_capacity')"
			:invalid-feedback="$t('pages.venue_form.services.invalid_value')">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input v-model.number="venue.seating_capacity" type="number" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.parking_capacity.$error"
			:label="$t('pages.venue_form.services.parking_capacity')"
			:invalid-feedback="$t('pages.venue_form.services.invalid_value')">
			<div class="form-row">
				<div class="col-md-3 col-lg-2">
					<b-input v-model.number="venue.parking_capacity" type="number" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.services.vlt_platforms')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.vlt_platform_ids" stacked>
						<b-checkbox v-for="item in vltPlatforms" :value="item.id" :key="item.id">{{ item.name }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.services.amenities.title')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-9">
					<div><b-checkbox v-model="venue.amenities.atm">{{ $t('pages.venue_form.services.amenities.atm') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.bar">{{ $t('pages.venue_form.services.amenities.bar') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.pay_per_view">{{ $t('pages.venue_form.services.amenities.pay_per_view') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.pos">{{ $t('pages.venue_form.services.amenities.pos') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.private_parking">{{ $t('pages.venue_form.services.amenities.private_parking') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.restaurant">{{ $t('pages.venue_form.services.amenities.restaurant') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.security">{{ $t('pages.venue_form.services.amenities.security') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.smoking_area">{{ $t('pages.venue_form.services.amenities.smoking_area') }}</b-checkbox></div>
					<div><b-checkbox v-model="venue.amenities.wifi">{{ $t('pages.venue_form.services.amenities.wifi') }}</b-checkbox></div>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.services.pay_per_view_platforms')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.pay_per_view_platform_ids" stacked>
						<b-checkbox v-for="item in payPerViewPlatforms" :value="item.id" :key="item.id">{{ item.name }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>