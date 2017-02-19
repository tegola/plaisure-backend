<template>
	<a class="dropdown-item">
		<div class="text-truncate">{{ name }}</div>
	</a>
</template>

<script>
	export default {
		props: {
			item: {
				type: Object,
				required: true
			}
		},

		computed: {
			name() {
				if (this.item.streetName && this.item.city) {
					return [this.item.streetName, this.item.city].join(', ')
				} else if (this.item.streetName || this.item.city) {
					return this.item.streetName || this.item.city
				} else {
					return null
				}
			}
		}
	}
</script>