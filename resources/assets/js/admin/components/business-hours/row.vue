<template>
	<div>
		<input type="hidden" :name="`${name}[${dayIndex}-1][day]`" :value="dayIndex" :disabled="!enabled">
		<input type="hidden" :name="`${name}[${dayIndex}-2][day]`" :value="dayIndex" :disabled="!enabled" v-if="showSecondary">
		<div class="form-group form-row align-items-center">
			<div class="col-md-3">
				<div class="form-check mb-0">
					<label class="form-check-label">
						<input type="checkbox" class="form-check-input" v-model="enabled">
						{{ dayLabel }}
					</label>
				</div>
			</div>
			<div class="col-md-6">
				<div class="d-flex align-items-center">
					<pga-time-picker
						:name="`${name}[${dayIndex}-1][opens]`"
						:disabled="!enabled"
						required
						:value="valueFor(0)"
						@input="onTimePickerInput(0, $event)">
					</pga-time-picker>
					<span class="mx-2">&ndash;</span>
					<pga-time-picker
						:name="`${name}[${dayIndex}-1][closes]`"
						:disabled="!enabled"
						required
						:value="valueFor(1)"
						@input="onTimePickerInput(1, $event)">
					</pga-time-picker>
				</div>
			</div>
			<div class="col-md-3" v-if="enabled">
				<div class="form-check mb-0">
					<label class="form-check-label">
						<input type="checkbox" class="form-check-input" v-model="showSecondary">
						Due orari
					</label>
				</div>
			</div>
		</div>
		<div v-if="enabled && showSecondary" class="form-group form-row">
			<div class="ml-md-auto col-md-6 mr-md-auto">
				<div class="d-flex align-items-center">
					<pga-time-picker
						:name="`${name}[${dayIndex}-2][opens]`"
						:disabled="!enabled"
						required
						:value="valueFor(2)"
						@input="onTimePickerInput(2, $event)">
					</pga-time-picker>
					<span class="mx-2">&ndash;</span>
					<pga-time-picker
						:name="`${name}[${dayIndex}-2][closes]`"
						:disabled="!enabled"
						required
						:value="valueFor(3)"
						@input="onTimePickerInput(3, $event)">
					</pga-time-picker>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import PGATimePicker from '../time-picker';

export default {
	name: 'pga-business-hours-manager-row',

	components: {
		'pga-time-picker': PGATimePicker
	},

	props: {
		name: {
			type: String,
			required: true
		},
		value: {
			type: Array,
			default: () => ['10:00', '20:00']
		},
		dayLabel: {
			type: String,
			required: true
		},
		dayIndex: {
			type: [String, Number],
			required: true
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			enabled: !this.disabled,
			showSecondary: this.value && this.value.length > 2
		};
	},

	watch: {
		disabled(newDisabled) {
			this.enabled = !newDisabled;
		},

		value(newValue) {
			if (newValue && newValue.length > 2) this.showSecondary = true;
		},

		enabled(newEnabled) {
			const value = newEnabled && this.value && this.value.length ? this.value : [];

			this.$emit('input', this.dayIndex, value);
		}
	},

	methods: {
		valueFor(index) {
			return this.value && this.value[index] ? this.value[index] : null;
		},

		onTimePickerInput(index, value) {
			let tempValue = this.value;
			tempValue[index] = value;

			this.$emit('input', this.dayIndex, tempValue);
		}
	}
};
</script>