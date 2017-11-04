<template>
	<svg :class="svgClass">
		<use v-bind="useProps"></use>
	</svg>
</template>

<script>
export default {
	name: 'pg-icon',

	props: {
		icon: {
			type: String,
			required: true
		},
		spinning: Boolean
	},

	computed: {
		svgClass() {
			return [
				'pg-icon',
				'pg-icon--' + this.icon,
				this.spinning ? 'pg-icon--spinning' : null
			];
		},
		useProps() {
			return {
				'xlink:href': '#pg-icon-' + this.icon
			};
		}
	}
};
</script>
