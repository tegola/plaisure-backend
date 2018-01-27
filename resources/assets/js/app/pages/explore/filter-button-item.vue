<template>
	<div class="filter-button-item" @click="onClick" tabindex="0">
		<div class="filter-button-item__icon-cell">
			<pg-icon class="filter-button-item__icon" :icon="icon" v-if="checked"></pg-icon>
		</div>
		<div class="filter-button-item__text-cell">
			<slot>{{ label }}</slot>
		</div>
	</div>
</template>

<script>
import PgIcon from '../../components/icon';

export default {
	components: {
		'pg-icon': PgIcon
	},

	props: {
		icon: {
			type: String,
			default: 'checkmark'
		},
		checked: Boolean,
		label: String
	},

	methods: {
		onClick() {
			this.$emit('click');
		}
	}
};
</script>