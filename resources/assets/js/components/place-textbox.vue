<script>
import { Autocomplete } from 'vue2-google-maps';

export default {
	name: 'PgPlaceTextbox',

	components: {
		Autocomplete
	},

	props: {
		value: {
			type: String,
			default: null
		},
		selectFirstOnEnter: {
			type: Boolean,
			default: true
		}
	},

	data() {
		return {
			place: null
		};
	},

	methods: {
		onFocus(e) {
			// Auto select text
			if (this.value) e.target.select();

			this.$emit('focus', e);
		},

		onBlur() {
			// Remove place if there's no text
			if (!this.value) this.$emit('place-changed', null);
		},

		onInput(e) {
			this.$emit('input', e.target.value);

			// Remove place if present
			if (this.place) {
				this.place = null;
				this.$emit('place-changed', null);
			}
		},

		onEscKey() {
			// Delete all text if not place is selected
			this.$emit('input', null);
			this.$emit('place-changed', null);
		},

		onEnterKey(e) {
			const menus = document.querySelectorAll('.pac-container');

			menus.forEach(menu => {
				if (menu.offsetWidth || menu.offsetHeight || menu.getClientRects().length) {
					e.preventDefault();
				}
			});

			// Pass event to parent if not stopped
			if (!e.defaultPrevented) {
				this.$emit(e.type, e);
			}
		},

		onPlaceChanged(place) {
			// Emit clean place name as input
			let value = place.name;
			if (place.vicinity && place.name != place.vicinity) {
				value = `${place.name}, ${place.vicinity}`;
			}

			this.$emit('input', value);

			// Store place locally
			this.place = place;

			// Emit place
			this.$emit('place-changed', place);
		}
	}
};
</script>

<template>
	<autocomplete
		ref="input"
		v-bind="$attrs"
		:value="value"
		:select-first-on-enter="selectFirstOnEnter"
		@place_changed="onPlaceChanged"
		@focus.native="onFocus"
		@blur.native="onBlur"
		@input.native="onInput"
		@keydown.native.esc="onEscKey"
		@keydown.native.enter="onEnterKey"
	/>
</template>