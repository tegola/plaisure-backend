<template>
	<a class="dropdown-item" href="#">
		<div class="text-truncate" :title="item.name">{{ item.name }}</div>
		<div v-if="metadata" class="text-truncate text-muted" :title="metadata">{{ metadata }}</div>
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
		metadata() {
			if (this.item.category && this.item.city) {
				return [this.item.category, this.item.city].join(', ');
			} else if (this.item.category || this.item.city) {
				return this.item.category || this.item.city;
			} else {
				return null;
			}
		}
	}
};
</script>