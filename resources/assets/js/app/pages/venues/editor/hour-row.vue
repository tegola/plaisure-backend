<script>
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';

// Generate options
let options = [];

for (let h = 0; h <= 24; h++) {
	const hours = h < 10 ? `0${h}` : `${h}`;

	['00', '30'].forEach(minutes => {
		if (h == 24 && minutes == '30') return; // No 24:30
		options.push(`${hours}:${minutes}`);
	});
}

const defaultValue = ['10:00', '20:00'];

export default {
	name: 'PgVenuEditorHourRow',

	components: {
		BInputGroup,
		BSelect,
		BCheckbox
	},

	props: {
		label: {
			type: String,
			required: true
		},
		value: {
			type: Array,
			default: () => defaultValue
		}
	},

	data() {
		return {
			mutableValue: this.value,
			enabled: this.value.length > 0,
			showSecondary: this.value.length > 2,
			options: options
		};
	},

	watch: {
		value() {
			this.mutableValue = this.value;
			this.enabled = this.value.length > 0;
			this.showSecondary = this.value.length > 2;
		}
	},

	methods: {
		onEnabledChange(checked) {
			this.$emit('input', checked ? defaultValue : []);
		},

		onTimeChange(index, value) {
			this.mutableValue[index] = value;
			this.$emit('input', this.mutableValue);
		},

		onShowSecondaryInput(checked) {
			this.$emit('input', checked ? this.mutableValue.concat(defaultValue) : this.mutableValue.slice(0, 2));
		}
	}
};
</script>

<template>
	<div>
		<div class="form-group form-row align-items-center">
			<div class="col-md-3">
				<b-checkbox :checked="enabled" @change="onEnabledChange">{{ label }}</b-checkbox>
			</div>
			<div class="col-md-6">
				<b-input-group>
					<b-select
						:disabled="!enabled"
						:value="mutableValue[0]"
						@change="onTimeChange(0, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
					<b-select
						:disabled="!enabled"
						:value="mutableValue[1]"
						@change="onTimeChange(1, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
				</b-input-group>
			</div>
			<div class="col-md-3" v-if="enabled">
				<b-checkbox :checked="showSecondary" @input="onShowSecondaryInput">Due orari</b-checkbox>
			</div>
		</div>
		<div v-if="enabled && showSecondary" class="form-group form-row">
			<div class="ml-md-auto col-md-6 mr-md-auto">
				<b-input-group>
					<b-select
						:disabled="!enabled"
						:value="mutableValue[2]"
						@change="onTimeChange(2, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
					<b-select
						:disabled="!enabled"
						:value="mutableValue[3]"
						@change="onTimeChange(3, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
				</b-input-group>
			</div>
		</div>
	</div>
</template>
