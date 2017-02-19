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
			@focus="setFocus(true)"
			@blur="setFocus(false)"
			@keydown.down="down"
			@keydown.up="up"
			@keydown.enter="select"
			@input="input">
		<div v-if="isOpen" class="dropdown-menu w-100">
			<component v-for="(item, index) in items"
				:is="itemComponent"
				:item="item"
				:class="itemClass(index)"
				@mousedown="select"
				@mouseover="setActive(index)">
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
				focused: false,
				query: this.value,
				current: -1
			}
		},

		watch: {
			items() {
				this.current = -1
			}
		},

		computed: {
			items() {
				return this.suggestions
			},
			dropdownClass() {
				return {
					'dropdown': true,
					'show': this.isOpen
				}
			},
			isOpen() {
				return this.focused && this.items.length > 0
			}
		},

		methods: {
			itemClass(index) {
				return {
					'dropdown-item': true,
					'active': this.current === index
				}
			},

			input() {
				this.$emit('input', this.query)
			},

			up(e) {
				if (this.items.length) {
					e.preventDefault()
				}
				if (this.current > 0) {
					this.current--
				} else if (this.current === -1) {
					this.current = this.items.length - 1
				} else {
					this.current = -1
				}
			},

			down(e) {
				if (this.items.length) {
					e.preventDefault()
				}
				if (this.current < this.items.length - 1) {
					this.current++
				} else {
					this.current = -1
				}
			},

			select(e) {
				e.preventDefault()

				if (this.current === -1) return

				this.$emit('select', this.items[this.current])
				this.$refs.input.blur()
			},

			setActive(index) {
				this.current = index
			},

			setFocus(focused) {
				this.focused = focused
			}
		}
	}
</script>
