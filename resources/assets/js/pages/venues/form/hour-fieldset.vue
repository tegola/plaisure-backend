<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';

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
	name: 'PgVenuEditorHourFieldset',

	components: {
		BFormGroup,
		BInputGroup,
		BSelect
	},

	props: {
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
				const v = this.value;

				if (!v.length) return 'closed';
				if (v.length > 2) return 'split';
				if (v.length == 2 && v[0] == '00:00' && v[1] == '24:00') return 'always';
				if (v.length == 2) return 'full';
			},
			set(value) {
				switch (value) {
					case 'closed':
						this.mutableValue = [];
						break;

					case 'split':
						this.mutableValue = ['09:00', '13:00', '14:00', '20:00'];
						break;

					case 'full':
						this.mutableValue = ['09:00', '20:00'];
						break;

					case 'always':
						this.mutableValue = ['00:00', '24:00'];
				}

				this.$emit('input', this.mutableValue);
			}
		},

		showControls() {
			return ['full', 'split'].indexOf(this.mode) !== -1;
		},

		showSecondaryControls() {
			return this.mode == 'split';
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
		}
	}
};
</script>

<template>
	<div>
		<b-form-group :class="showControls ? 'mb-2' : 'mb-0'">
			<b-select v-model="mode">
				<option value="always">{{ $t('pages.venue_form.hours.always') }}</option>
				<option value="full">{{ $t('pages.venue_form.hours.full') }}</option>
				<option value="split">{{ $t('pages.venue_form.hours.split') }}</option>
				<option value="closed">{{ $t('pages.venue_form.hours.closed') }}</option>
			</b-select>
		</b-form-group>

		<div v-if="showControls" class="form-row">
			<b-form-group
				:label="showSecondaryControls ? $t('pages.venue_form.hours.morning') : $t('pages.venue_form.hours.from_to')"
				class="col-sm-6">
				<b-input-group>
					<b-select
						:value="value[0]"
						@change="onTimeChange(0, $event)">
						<option v-for="(option, index) in options" :key="index">{{ option }}</option>
					</b-select>
					<b-select
						:value="value[1]"
						@change="onTimeChange(1, $event)">
						<option v-for="(option, index) in options" :key="index">{{ option }}</option>
					</b-select>
				</b-input-group>
			</b-form-group>
			<b-form-group
				v-if="showSecondaryControls"
				:label="$t('pages.venue_form.hours.afternoon')"
				class="col-sm-6">
				<b-input-group>
					<b-select
						:value="value[2]"
						@change="onTimeChange(2, $event)">
						<option v-for="(option, index) in options" :key="index">{{ option }}</option>
					</b-select>
					<b-select
						:value="value[3]"
						@change="onTimeChange(3, $event)">
						<option v-for="(option, index) in options" :key="index">{{ option }}</option>
					</b-select>
				</b-input-group>
			</b-form-group>
		</div>
	</div>
</template>
