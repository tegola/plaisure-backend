<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';

import PgVenueEditorHourFieldset from './hour-fieldset';

import formGroupProps from './form-group-props'

export default {
	name: 'PgVenueEditorGeneralPane',

	components: {
		BFormGroup,
		PgVenueEditorHourFieldset
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
				{ index: 1, name: 'Lunedì' },
				{ index: 2, name: 'Martedì' },
				{ index: 3, name: 'Mercoledì' },
				{ index: 4, name: 'Giovedì' },
				{ index: 5, name: 'Venerdì' },
				{ index: 6, name: 'Sabato' },
				{ index: 0, name: 'Domenica' }
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
			this.$emit('update:hours', this.mutableHours)
		}
	}
}
</script>

<template>
	<div>
		<b-form-group
			v-for="day in days"
			:key="day.index"
			:label="day.name"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<pg-venue-editor-hour-fieldset
						:label="day.name"
						:value="hours[day.index]"
						@input="onHourRowInput(day.index, $event)">
					</pg-venue-editor-hour-fieldset>
				</div>
			</div>
		</b-form-group>
	</div>
</template>