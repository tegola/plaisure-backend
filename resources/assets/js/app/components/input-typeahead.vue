<template>
	<div class="dropdown">
		<input
			ref="input"
			type="text"
			:class="inputClass"
			:value="value"
			autocomplete="off"
			v-bind="$attrs"
			@keydown.down="onDownPress"
			@keydown.up="onUpPress"
			@keydown.esc="onEscPress"
			@keydown.enter="onEnterPress"
			@input="onInput"
			@focus="onFocus"
			@blur="onBlur">
		<div v-if="open && items.length" class="dropdown-menu w-100 show">
			<component v-for="(item, index) in items" :key="item.id"
				:is="itemComponent"
				:item="item"
				:class="itemClass(index)"
				@mouseover.native="onMouseOver"
				@mousedown.native="onMouseDown(index)">
			</component>
		</div>
	</div>
</template>

<script>
export default {
	name: 'pg-input-typeahead',

	inheritAttrs: false,

	props: {
		inputClass: String,
		value: [String, Number],
		itemComponent: {
			type: String,
			required: true
		},
		suggestions: {
			type: Array,
			default: []
		}
	},

	data() {
		return {
			open: false,
			focused: false,
			current: -1
		};
	},

	watch: {
		items() {
			this.current = -1;
		}
	},

	computed: {
		items() {
			return this.suggestions;
		}
	},

	methods: {
		itemClass(index) {
			return {
				'active': this.current === index
			};
		},

		onInput(event) {
			const value = event.target.value;

			this.open = value ? true : false;
			this.$emit('input', value);
		},

		onEscPress() {
			this.open = false;
		},

		onUpPress(event) {
			if (this.items.length) {
				this.open = true;
				event.preventDefault();
			}

			if (this.current > 0) {
				this.current--;
			} else if (this.current === -1) {
				this.current = this.items.length - 1;
			} else {
				this.current = -1;
			}
		},

		onDownPress(event) {
			if (this.items.length) {
				this.open = true;
				event.preventDefault();
			}

			if (this.current < this.items.length - 1) {
				this.current++;
			} else {
				this.current = -1;
			}
		},

		onEnterPress(event) {
			if (this.open) event.preventDefault();
			this.select();
		},

		onFocus() {
			if (this.items.length) this.open = true;
		},

		onBlur() {
			this.open = false;
		},

		onMouseOver() {
			this.current = -1;
		},

		onMouseDown(index) {
			this.current = index;
			this.select();
		},

		select(event) {
			if (this.current === -1) return;

			this.open = false;
			this.$emit('select', this.items[this.current]);
		},
	}
};
</script>
