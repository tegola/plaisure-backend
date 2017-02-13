<template>
	<div :class="dropdownClass">
		<input
			type="text"
			:class="classes"
			:name="name"
			:value="value"
			:placeholder="placeholder"
			:autofocus="autofocus"
			autocomplete="off"
			v-model="query"
			@focus="onFocus"
			@blur="onBlur"
			@keydown.down="down"
			@keydown.up="up"
			@keydown.enter="hit"
			@keydown.esc="reset"
			@input="update">
		<div v-if="isOpen" class="dropdown-menu w-100">
			<!-- Replace with custom slot content -->
			<a v-for="(item, index) in items"
				:class="itemClass(index)"
				:href="'prova'"
				@mousedown="hit"
				@mousemove="setActive(index)">
				{{ item.name }} - {{ item.type }}
			</a>
		</div>
	</div>
</template>

<script>
	import $ from 'jquery' // FIXME: Replace with something else (axios? fetch?)

	export default {
		props: {
			// Input
			classes: String,
			name: String,
			value: [String, Number],
			placeholder: String,
			autofocus: Boolean,

			// XHR
			url: {
				type: String,
				required: true
			},
			limit: {
				type: Number,
				default: 5
			}
		},

		data() {
			return {
				isFocused: false,
				isLoading: false,
				query: this.value,
				items: [],
				current: -1
			}
		},

		computed: {
			dropdownClass() {
				return {
					'dropdown': true,
					'show': this.isOpen
				}
			},
			isOpen() {
				return this.isFocused && this.items.length > 0
			},
			hasItems() {
				return this.items.length > 0
			},
			isEmpty() {
				return !this.query
			},
			isDirty() {
				return !!this.query
			}
		},

		methods: {
			itemClass(index) {
				return {
					'dropdown-item': true,
					'active': this.current === index
				}
			},
			update() {
				if (!this.query) {
					return this.reset()
				}

				if (this.minChars && this.query.length < this.minChars) {
					return
				}

				this.isLoading = true

				// FIXME: Replace with axios or fetch?
				$.get(this.url, {
					[this.name]: this.query
				}).done((data) => {
					console.log(data)
					this.items = this.limit ? data.slice(0, this.limit) : data
					this.current = -1
				}).always(() => {
					this.isLoading = false
				})
			},

			reset() {
				this.items = []
				this.query = ''
				this.isLoading = false
			},

			setActive (index) {
				this.current = index
			},

			activeClass (index) {
				return {
					active: this.current === index
				}
			},

			hit() {
				if (this.current !== -1) {
					this.onHit(this.items[this.current])
				}
			},

			up() {
				if (this.current > 0) {
					this.current--
				} else if (this.current === -1) {
					this.current = this.items.length - 1
				} else {
					this.current = -1
				}
			},

			down() {
				if (this.current < this.items.length - 1) {
					this.current++
				} else {
					this.current = -1
				}
			},

			onFocus() {
				this.isFocused = true
			},

			onBlur() {
				this.isFocused = false
			},

			onHit() {
				
			}
		}
	}
</script>
