<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';

import PgVenueFormHourFieldset from './hour-fieldset';
import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormHoursPane',

	components: {
		BFormGroup,
		PgVenueFormHourFieldset
	},

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			formGroupProps,
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

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		},

		venueHours: {
			get() {
				return this.venue.business_hours;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'business_hours',
					value
				});
			}
		}
	},

	methods: {
		onHourRowInput(index, value) {
			const hours = this.venueHours.slice(0);
			hours[index] = value;
			this.venueHours = hours;
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h5>{{ $t('pages.venue_form.hours.title') }}</h5>
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
						:value="venueHours[day.index]"
						@input="onHourRowInput(day.index, $event)"
					/>
				</div>
			</div>
		</b-form-group>
	</div>
</template>