<script>
export default {
	name: 'PgHomePageToken',

	props: {
		icon: {
			type: String,
			default: null
		},
		type: {
			type: String,
			default: 'category'
		},
		to: {
			type: [String, Object],
			required: true
		}
	},

	computed: {
		classes() {
			return [
				'pg-home-page__token',
				`pg-home-page__token--${this.type}`
			];
		},
		iconSvg() {
			return require(`!svg-inline-loader!assets/svg/category-icons/${this.icon}.svg`);
		}
	}
};
</script>

<template>
	<router-link :to="to" :class="classes">
		<span v-if="icon" class="pg-home-page__token-icon" v-html="iconSvg" />
		<slot class="pg-home-page__token-label" />
	</router-link>
</template>