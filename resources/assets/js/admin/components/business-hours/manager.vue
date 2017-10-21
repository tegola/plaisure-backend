<template>
	<div class="row">
		<div class="col-md-5 mb-3 mb-md-0">
			<div class="form-check">
				<label class="form-check-label">
					<input type="radio" class="form-check-input" value="always" v-model="mode">
					Sempre aperto
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label">
					<input type="radio" class="form-check-input" value="open" v-model="mode">
					Aperto negli orari specificati
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label">
					<input type="radio" class="form-check-input" value="closed" v-model="mode">
					Chiuso
				</label>
			</div>
		</div>

		<div class="col-md-7">
			<!-- Always open -->
			<template v-if="mode == 'always'" v-for="day in days">
				<input type="hidden" :name="`${name}[${day.index}][day]`" :value="day.index">
				<input type="hidden" :name="`${name}[${day.index}][opens]`" value="00:00">
				<input type="hidden" :name="`${name}[${day.index}][closes]`" value="24:00">
			</template>

			<!-- Open on selected hours -->
			<pga-business-hours-row
				v-if="mode == 'open'"
				v-for="day in days"
				:day-label="day.name"
				:day-index="day.index"
				:name="`${name}`"
				:value="valueForDay(day.index)"
				:disabled="!hasValueForDay(day.index)"
				:key="day.index"
				@input="onDayInput">
			</pga-business-hours-row>
		</div>
	</div>
</template>

<script>
import _every from 'lodash/every';
import _filter from 'lodash/filter';
import PGABusinessHoursRow from './row';

export default {
	name: 'pga-business-hours-manager',

	components: {
		'pga-business-hours-row': PGABusinessHoursRow
	},

	props: {
		name: {
			type: String,
			default: 'business_hours'
		},
		value: {
			type: Array,
			default: () => []
		}
	},

	data() {
		return {
			mode: 'always',
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

	computed: {

	},

	methods: {
		detectMode() {
			// No hours set, it's always closed
			if (!this.value.length) return 'closed';

			// All days 00:00 -> 24:00, it's always open
			var always24h = _every(this.value, day => day.opens == '00:00' && day.closes == '24:00');
			if (this.value.length == 7 && always24h) return 'always';

			// Otherwise, has mixed hours
			return 'open';
		},

		hoursByDay(index) {
			return _filter(this.value, item => {
				return item.day == index;
			});
		},

		valueForDay(index) {
			let values = [];

			this.hoursByDay(index).forEach(item => {
				values.push(item.opens, item.closes);
			});

			return values.length ? values : undefined;
		},

		hasValueForDay(index) {
			return this.hoursByDay(index).length ? true : false;
		},

		onDayInput(dayIndex, values) {
			// Get all existing hours except those for the given day
			let newHours = _filter(this.value, item => {
				return item.day != dayIndex;
			});

			// Add new hours in multiple records for the given day
			if (values && values.length) {
				newHours.push({
					day: dayIndex,
					opens: values[0],
					closes: values[1]
				});
				if (values.length > 2) { // Don't use == 4
					newHours.push({
						day: dayIndex,
						opens: values[2],
						closes: values[3]
					});
				}
			}

			this.$emit('input', newHours);
		}
	},

	mounted() {
		this.mode = this.detectMode();
	}
};
</script>
