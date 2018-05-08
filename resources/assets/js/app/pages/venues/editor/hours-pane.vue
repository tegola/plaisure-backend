<script>
import _cloneDeep from 'lodash/cloneDeep';

import BRadio from 'bootstrap-vue/es/components/form-radio/form-radio';
import BRadioGroup from 'bootstrap-vue/es/components/form-radio/form-radio-group';

import PgVenueEditorHourRow from './hour-row';

export default {
	name: 'PgVenueEditorHoursPane',

	components: {
		BRadio,
		BRadioGroup,
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
		},

		mutableHours: {
			deep: true,
			handler() {
				this.$emit('update:hours', this.mutableHours);
			}
		}
	},

	computed: {
		mode: {
			get() {
				// No hours set, it's always closed
				const alwaysClosed = this.hours.every(hoursForDay => hoursForDay.length == 0);
				if (alwaysClosed) return 'closed';

				// All days 00:00 -> 24:00, it's always open
				const alwaysOpen = this.hours.every(hoursForDay => hoursForDay.length == 2 && hoursForDay[0] == '00:00' && hoursForDay[1] == '24:00');
				if (alwaysOpen) return '24h';

				// Otherwise, has mixed hours
				return 'mixed';
			},
			set(value) {
				const days = [1, 2, 3, 4, 5, 6, 0];

				switch (value) {
					case '24h': 
						// Set all hours to 24h
						this.mutableHours = days.map(index => ['00:00', '24:00']);
						break;

					case 'closed':
						// Remove all hours
						this.mutableHours = days.map(index => []);
						break;

					case 'mixed':
						// Set default hours if none is set
						if (this.hours.every(hoursForDay => hoursForDay.length == 0)) {
							this.mutableHours = days.map(index => ['10:00', '20:00']);
						}
						break;
				}
			}
		}
	}
};
</script>

<template>
	<div>
		<h4>Orari di apertura</h4>
		<hr>

		<b-radio-group stacked v-model="mode">
			<b-radio value="24h">Sempre aperto</b-radio>
			<b-radio value="mixed">Aperto negli orari specificati</b-radio>
			<b-radio value="closed">Chiuso</b-radio>
		</b-radio-group>

		<pg-venue-editor-hour-row
			v-for="day in days"
			:key="day.index"
			:label="day.name"
			v-model="mutableHours[day.index]">
		</pg-venue-editor-hour-row>
	</div>
</template>