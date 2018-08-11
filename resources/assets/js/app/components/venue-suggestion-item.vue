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

<template>
	<a class="dropdown-item" href="#">
		<div :title="item.name" class="text-truncate">{{ item.name }}</div>
		<div v-if="metadata" :title="metadata" class="text-truncate text-muted">{{ metadata }}</div>
	</a>
</template>