<script>
import PgVenueEditorHourRow from './hour-row';

export default {
	name: 'PgVenueEditorHoursPane',

	components: {
		PgVenueEditorHourRow
	},

	props: {
		hours: {
			type: Array,
			required: true
		}
	},

	data() {
		return {
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
		onRowInput(index, value) {
			this.mutableHours = this.mutableHours.slice(0);
			this.mutableHours[index] = value;
			this.$emit('update:hours', this.mutableHours)
		}
	}
};
</script>

<template>
	<div>
		<h4>Orari di apertura</h4>
		<hr>

		<div v-for="(day, index) in days" :key="day.index">
			<hr v-if="index > 0">
			<pg-venue-editor-hour-row
				:label="day.name"
				:value="hours[day.index]"
				@input="onRowInput(day.index, $event)">
			</pg-venue-editor-hour-row>
		</div>
	</div>
</template>