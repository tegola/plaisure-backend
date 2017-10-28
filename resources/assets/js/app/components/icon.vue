<template>
	<svg :class="['icon', 'icon-' + icon, spinning ? 'icon-spinning': '']"><use v-bind="useProps"></use></svg>
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
		useProps() {
			return {
				'xlink:href': '#icon-' + this.icon
			};
		}
	}
};
</script>
