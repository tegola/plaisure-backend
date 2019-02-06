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
			default: null,
			validator: value => ['2x', '3x'].indexOf(value) !== -1
		},
		spinning: {
			type: Boolean,
			default: false
		}
	},

	computed: {
		classes() {
			return [
				'pg-icon',
				`pg-icon--${this.icon}`,
				this.size ? `pg-icon--${this.size}` : null,
				this.spinning ? 'pg-icon--spinning' : null
			];
		},

		svg() {
			return require(`!svg-inline-loader!assets/svg/icons/${this.icon}.svg`);
		}
	}
};
</script>

<template>
	<div :class="classes" v-html="svg" />
</template>