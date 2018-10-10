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
	},

	computed: {
		iconSvg() {
			return require(`!svg-inline-loader!assets/svg/category-icons/${this.icon}.svg`);
		}
	}
};
</script>

<template>
	<router-link :to="to" class="pg-home-page__token">
		<span v-if="icon" class="pg-home-page__token-icon" v-html="iconSvg" />
		<slot class="pg-home-page__token-label" />
	</router-link>
</template>