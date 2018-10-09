<script>
import _extend from 'lodash/extend';

import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import BCheckboxGroup from 'bootstrap-vue/es/components/form-checkbox/form-checkbox-group';

import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormServicesPane',

	components: {
		BFormGroup,
		BInput,
		BCheckbox,
		BCheckboxGroup
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

		vltPlatforms() {
			return this.$store.state[this.storeName].vltPlatforms;
		},

		amenities() {
			return Object.keys(this.venue.amenities);
		},

		payPerViewPlatforms() {
			return this.$store.state[this.storeName].payPerViewPlatforms;
		},

		venueSportsBetting: {
			get() {
				return this.venue.sports_betting;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'sports_betting',
					value
				});
			}
		},

		venueVirtualBetting: {
			get() {
				return this.venue.virtual_betting;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'virtual_betting',
					value
				});
			}
		},

		venueHorseBetting: {
			get() {
				return this.venue.horse_betting;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'horse_betting',
					value
				});
			}
		},

		venueArcadeRoulette: {
			get() {
				return this.venue.arcade_roulette;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'arcade_roulette',
					value
				});
			}
		},

		venueVltMachineCount: {
			get() {
				return this.venue.vlt_machine_count;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'vlt_machine_count',
					value
				});
			}
		},

		venueAwpMachineCount: {
			get() {
				return this.venue.awp_machine_count;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'awp_machine_count',
					value
				});
			}
		},

		venueSeatingCapacity: {
			get() {
				return this.venue.seating_capacity;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'seating_capacity',
					value
				});
			}
		},

		venueParkingCapacity: {
			get() {
				return this.venue.parking_capacity;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'parking_capacity',
					value
				});
			}
		},

		venueVltPlatformIds: {
			get() {
				return this.venue.vlt_platform_ids;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'vlt_platform_ids',
					value
				});
			}
		},

		venueAmenities: {
			get() {
				// Object to array of values
				const obj = this.venue.amenities;
				const arr = Object.keys(obj) // Get keys
					.map(key => obj[key] === true ? key : null) // Convert object in array of field names
					.filter(item => item !== null); // Remove null values

				return arr;
			},
			set(value) {
				// Array to object
				const amenities = _extend({}, this.venue.amenities);

				// Turn all to false
				for (const key of Object.keys(amenities)) {
					amenities[key] = false;
				}

				// Turn specified ones to true
				value.forEach(name => {
					amenities[name] = true;
				});

				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'amenities',
					value: amenities
				});
			}
		},

		venuePayPerViewPlatformIds: {
			get() {
				return this.venue.pay_per_view_platform_ids;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'pay_per_view_platform_ids',
					value
				});
			}
		},

		$v() {
			return this.$parent.$v.venue;
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h4>{{ $t('pages.venue_form.services.title') }}</h4>
		<hr>
		<b-form-group v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<div><b-checkbox v-model="venueSportsBetting">{{ $t('pages.venue_form.services.sports_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venueVirtualBetting">{{ $t('pages.venue_form.services.virtual_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venueHorseBetting">{{ $t('pages.venue_form.services.horse_betting') }}</b-checkbox></div>
					<div><b-checkbox v-model="venueArcadeRoulette">{{ $t('pages.venue_form.services.arcade_roulette') }}</b-checkbox></div>
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
					<b-input v-model.number="venueVltMachineCount" type="number" min="0" />
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
					<b-input v-model.number="venueAwpMachineCount" type="number" min="0" />
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
					<b-input v-model.number="venueSeatingCapacity" type="number" min="0" />
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
					<b-input v-model.number="venueParkingCapacity" type="number" min="0" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.services.vlt_platforms')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venueVltPlatformIds" stacked>
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
					<b-checkbox-group v-model="venueAmenities" stacked>
						<b-checkbox v-for="item in amenities" :value="item" :key="item">{{ $t(`pages.venue_form.services.amenities.${item}`) }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.services.pay_per_view_platforms')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venuePayPerViewPlatformIds" stacked>
						<b-checkbox v-for="item in payPerViewPlatforms" :value="item.id" :key="item.id">{{ item.name }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>
	</div>
</template>