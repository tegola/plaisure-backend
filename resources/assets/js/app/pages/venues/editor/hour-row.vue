<script>
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import BRadio from 'bootstrap-vue/es/components/form-radio/form-radio';
import BRadioGroup from 'bootstrap-vue/es/components/form-radio/form-radio-group';
import BButton from 'bootstrap-vue/es/components/button/button';

// Generate options
let options = [];

for (let h = 0; h <= 24; h++) {
	const hours = h < 10 ? `0${h}` : `${h}`;

	['00', '30'].forEach(minutes => {
		if (h == 24 && minutes == '30') return; // No 24:30
		options.push(`${hours}:${minutes}`);
	});
}

export default {
	name: 'PgVenuEditorHourRow',

	components: {
		BInputGroup,
		BSelect,
		BRadio,
		BRadioGroup,
		BButton
	},

	props: {
		label: {
			type: String,
			required: true
		},
		value: {
			type: Array,
			default: () => ['10:00', '20:00']
		}
	},

	data() {
		return {
			mutableValue: this.value,
			options: options
		};
	},

	computed: {
		mode: {
			get() {
				const value = this.value;

				if (!value.length) return 'closed';
				if (value.length == 2) return 'full';
				if (value.length > 2) return 'split';
			},
			set(value) {
				const old = this.mutableValue;

				switch (value) {
					case 'closed': 
						this.mutableValue = [];
						break;

					case 'full':
						this.mutableValue = [
							old[0] || '10:00',
							old[1] || '20:00'
						]
						break;

					case 'split':
						this.mutableValue = [
							old[0] || '10:00',
							old[1] || '13:00',
							old[3] || '14:00',
							old[4] || '20:00'
						];
						break;
				}

				this.$emit('input', this.mutableValue)
			}
		},

		primaryDisabled() {
			return this.value.length == 0;
		},

		secondaryDisabled() {
			return this.value.length <= 2;
		},

		show24h() {
			const value = this.value
			return value.length == 2 && (value[0] != '00:00' || value[1] != '24:00');
		}
	},

	watch: {
		value() {
			this.mutableValue = this.value;
		}
	},

	methods: {
		onTimeChange(index, value) {
			this.mutableValue = this.mutableValue.slice(0);
			this.mutableValue[index] = value;
			this.$emit('input', this.mutableValue);
		},

		on24hClick() {
			this.mutableValue = ['00:00', '24:00'];
			this.$emit('input', this.mutableValue);
		}
	}
};
</script>

<template>
	<div>
		<div class="form-group row">
			<label class="col-md-2 col-form-label">{{ label }}</label>
			<div class="col-md">
				<b-radio-group v-model="mode">
					<b-radio value="closed">Chiuso</b-radio>
					<b-radio value="full">Orario continuato</b-radio>
					<b-radio value="split">Orario spezzato</b-radio>
				</b-radio-group>
			</div>
		</div>
		<div class="form-row" v-if="mode != 'closed'">
			<div class="form-group ml-md-auto col-md-5">
				<template v-if="!secondaryDisabled">Mattina</template>
				<b-input-group>
					<b-select
						:disabled="primaryDisabled"
						:value="value[0]"
						@change="onTimeChange(0, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
					<b-select
						:disabled="primaryDisabled"
						:value="value[1]"
						@change="onTimeChange(1, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
				</b-input-group>
			</div>
			<div class="form-group col-md-5 align-items-center" :class="secondaryDisabled ? 'd-flex align-items-center' : ''">
				<template v-if="!secondaryDisabled">Pomeriggio</template>
				<b-input-group v-if="!secondaryDisabled">
					<b-select
						:value="value[2]"
						@change="onTimeChange(2, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
					<b-select
						:value="value[3]"
						@change="onTimeChange(3, $event)">
						<option v-for="option in options">{{ option }}</option>
					</b-select>
				</b-input-group>
				<a href="#" v-if="show24h" @click="on24hClick">Sempre aperto (24h)</a>
			</div>
		</div>
	</div>
</template>
