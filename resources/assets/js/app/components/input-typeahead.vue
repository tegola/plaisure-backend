<template>
	<div :class="dropdownClass">
		<input
			ref="input"
			type="text"
			:class="classes"
			:name="name"
			:value="value"
			:placeholder="placeholder"
			:autofocus="autofocus"
			autocomplete="off"
			v-model="query"
			@keydown.down="down"
			@keydown.up="up"
			@keydown.esc="esc"
			@keydown.enter="select"
			@input="input"
			@focus="focus"
			@blur="blur">
		<div v-if="open && items.length" class="dropdown-menu w-100">
			<component v-for="(item, index) in items"
				:is="itemComponent"
				:item="item"
				:class="itemClass(index)"
				@mouseover.native="mouseover"
				@mousedown.native="mousedown(index)">
			</component>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			classes: String,
			name: String,
			value: [String, Number],
			placeholder: String,
			autofocus: Boolean,
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
				query: this.value,
				current: -1
			};
		},

		watch: {
			value(newValue) {
				this.query = newValue;
			},
			items() {
				this.current = -1;
			}
		},

		computed: {
			items() {
				return this.suggestions;
			},
			dropdownClass() {
				return {
					'dropdown': true,
					'show': this.open && this.items.length
				};
			}
		},

		methods: {
			itemClass(index) {
				return {
					'active': this.current === index
				};
			},

			input() {
				this.open = true;
				this.$emit('input', this.query);
			},

			esc() {
				this.open = false;
			},

			up(e) {
				if (this.items.length) {
					this.open = true;
					e.preventDefault();
				}

				if (this.current > 0) {
					this.current--;
				} else if (this.current === -1) {
					this.current = this.items.length - 1;
				} else {
					this.current = -1;
				}
			},

			down(e) {
				if (this.items.length) {
					this.open = true;
					e.preventDefault();
				}

				if (this.current < this.items.length - 1) {
					this.current++;
				} else {
					this.current = -1;
				}
			},

			focus() {
				if (this.items.length) {
					this.open = true;
				}
			},

			blur() {
				this.open = false;
			},

			mouseover() {
				this.current = -1;
			},

			mousedown(index) {
				this.current = index;
				this.select();
			},

			select(e) {
				if (this.current === -1) return;

				// Stop enter key if still open
				if (this.open) e.preventDefault();

				this.open = false;
				this.$emit('select', this.items[this.current]);
			},
		}
	};
</script>
