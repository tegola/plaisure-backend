<script>
export default {
	name: 'PgNoItems',

	props: {
		title: {
			type: String,
			default: function() {
				return this.$t('components.no_items.title');
			}
		},
		subtitle: {
			type: String,
			default: null
		}
	}

};
</script>

<template>
	<div class="text-muted text-center">
		<h4>{{ title }}</h4>
		<p v-if="$slots.default || subtitle" class="mb-0">
			<slot>{{ subtitle }}</slot>
		</p>
	</div>
</template>