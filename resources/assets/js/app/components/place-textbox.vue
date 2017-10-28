<template>
	<gmap-autocomplete
		ref="input"
		v-bind="$props"
		:select-first-on-enter="selectFirstOnEnter"
		@place_changed="onPlaceChanged"
		@focus.native="onFocus"
		@input.native="onInput"
		@keydown.native.esc="onEscKey"
		@keydown.native.enter="onEnterKey">
	</gmap-autocomplete>
</template>

<script>
import { Autocomplete } from 'vue2-google-maps';

export default {
	name: 'pg-place-textbox',

	components: {
		'gmap-autocomplete': Autocomplete
	},

	props: {
		place: String,
		selectFirstOnEnter: {
			type: Boolean,
			default: true
		}
	},

	data() {
		return {
			mutablePlace: this.place
		};
	},

	watch: {
		place(newPlace) {
			this.mutablePlace = newPlace;
		}
	},

	methods: {
		onPlaceChanged(place) {
			this.mutablePlace = place;
			this.$emit('place-changed', place);
		},

		onFocus(e) {
			if (this.mutablePlace) e.target.select();
		},

		onBlur(e) {
			if (!this.mutablePlace) e.target.value = '';
		},

		onInput(e) {
			const input = e.target;

			if (this.mutablePlace) {
				input.value = e.data;
				this.mutablePlace = null;
				this.$emit('place-changed', null);
			}

			this.$emit('input', input.value);
		},

		onEscKey(e) {
			if (!this.mutablePlace) e.target.value = '';
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