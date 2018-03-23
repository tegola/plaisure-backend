<template>
	<popup display-style="block" :visible="popupVisible" placement="bottom-start" append-to-body>
		<input
			type="text"
			class="form-control"
			:name="name"
			:disabled="disabled"
			:required="required"
			:value="value"
			@change="onChange"
			@focus="onFocus"
			@blur="onBlur"
			@click="showPopup"
			@keyup.down="showPopup">
		<div class="dropdown-menu show" slot="popup">
			<a v-for="option in popupOptions"
				href="#"
				:class="['dropdown-item', option == value ? 'disabled' : null]"
				@mousedown="select(option)">
				{{ option }}
			</a>
		</div>
	</popup>
</template>

<style scoped>
	.dropdown-menu {
		max-height: 15em;
		overflow: auto;
	}
</style>

<script>
import Popup from 'prontogioco/components/popup';

export default {
	name: 'pga-time-picker',

	components: {
		'popup': Popup
	},

	props: {
		name: String,
		value: String,
		disabled: Boolean,
		required: Boolean
	},

	data() {
		return {
			focused: false,
			popupVisible: false
		};
	},

	computed: {
		popupOptions() {
			let options = [];

			for (let h = 0; h <= 23; h++) {
				const hours = h < 10 ? '0' + h : String(h);

				['00', '30'].forEach(minutes => {
					options.push([hours, ':', minutes].join(''));
				});
			}
			options.push('24:00');

			return options;
		},
	},

	methods: {
		onChange(event) {
			const node = event.target;
			const newValue = node.value;

			// Check that it's a correct time string (hh:mm) up to 24:00
			const re1 = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
			const re2 = /^24:00$/;

			if (!re1.test(newValue) && !re2.test(newValue)) {
				node.value = this.value;
				return;
			}

			this.$emit('input', newValue);
		},

		onFocus() {
			this.focused = true;
		},

		onBlur() {
			this.focused = false;
			this.hidePopup();
		},

		showPopup() {
			if (this.disabled || !this.focused) return;
			this.popupVisible = true;
		},

		hidePopup() {
			this.popupVisible = false;
		},

		select(option) {
			this.$emit('input', option);
		}
	}
};
</script>