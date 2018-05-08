<template>
	<autocomplete
		ref="input"
		v-bind="$attrs"
		v-model="place"
		:select-first-on-enter="selectFirstOnEnter"
		@place_changed="onPlaceChanged"
		@focus.native="onFocus"
		@blur.native="onBlur"
		@input.native="onInput"
		@keydown.native.esc="onEscKey"
		@keydown.native.enter="onEnterKey">
	</autocomplete>
</template>

<script>
import { Autocomplete } from 'vue2-google-maps';

export default {
	name: 'PgPlaceTextbox',

	components: {
		'autocomplete': Autocomplete
	},

	props: {
		place: String,
		selectFirstOnEnter: {
			type: Boolean,
			default: true
		}
	},

	methods: {
		onPlaceChanged(place) {
			this.$emit('place-changed', place);
		},

		onFocus(e) {
			if (this.place) e.target.select();
		},

		onBlur(e) {
			if (!this.place) e.target.value = '';
		},

		onInput(e) {
			const input = e.target;

			this.$emit('input', input.value);

			if (this.place) this.$emit('place-changed', null);
		},

		onEscKey(e) {
			if (!this.place) e.target.value = '';
		},

		onEnterKey(e) {
			const menus = document.querySelectorAll('.pac-container');

			menus.forEach(menu => {
				if (menu.offsetWidth || menu.offsetHeight || menu.getClientRects().length) e.preventDefault();
			});
		}
	}
};
</script>