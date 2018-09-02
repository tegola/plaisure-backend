<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';

import PgVenueFormHourFieldset from './hour-fieldset';

import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormGeneralPane',

	components: {
		BFormGroup,
		PgVenueFormHourFieldset
	},

	props: {
		venue: {
			type: Object,
			required: true
		},
		hours: {
			type: Array,
			required: true
		}
	},

	data() {
		return {
			formGroupProps,
			mutableHours: this.hours,
			days: [
				{ index: 1, name: this.$t('common.weekdays.monday') },
				{ index: 2, name: this.$t('common.weekdays.tuesday') },
				{ index: 3, name: this.$t('common.weekdays.wednesday') },
				{ index: 4, name: this.$t('common.weekdays.thursday') },
				{ index: 5, name: this.$t('common.weekdays.friday') },
				{ index: 6, name: this.$t('common.weekdays.saturday') },
				{ index: 0, name: this.$t('common.weekdays.sunday') }
			]
		};
	},

	watch: {
		hours() {
			this.mutableHours = this.hours;
		}
	},

	methods: {
		onHourRowInput(index, value) {
			this.mutableHours = this.mutableHours.slice(0);
			this.mutableHours[index] = value;
			this.$emit('update:hours', this.mutableHours);
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h4>{{ $t('pages.venue_form.hours.title') }}</h4>
		<hr>
		<b-form-group
			v-for="day in days"
			:key="day.index"
			:label="day.name"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<pg-venue-form-hour-fieldset
						:label="day.name"
						:value="hours[day.index]"
						@input="onHourRowInput(day.index, $event)"
					/>
				</div>
			</div>
		</b-form-group>
	</div>
</template>