<template>
	<svg :class="svgClass">
		<use v-bind="useProps"></use>
	</svg>
</template>

<script>
export default {
	name: 'PgIcon',

	props: {
		icon: {
			type: String,
			required: true
		},
		size: {
			type: String,
			validator: value => ['2x', '3x'].indexOf(value) !== -1
		},
		spinning: Boolean
	},

	computed: {
		svgClass() {
			return [
				'pg-icon',
				`pg-icon--${this.icon}`,
				this.size ? `pg-icon--${this.size}` : null,
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
