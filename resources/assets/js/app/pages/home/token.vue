<script>
export default {
	name: 'PgHomePageToken',

	props: {
		icon: {
			type: String,
			default: null
		},
		to: {
			type: [String, Object],
			required: true
		}
	}
};
</script>

<template>
	<router-link :to="to" class="pg-home-page__token">
		<span v-if="icon" class="pg-home-page__token-icon" />
		<slot class="pg-home-page__token-label" />
	</router-link>
</template>