<template>
	<div class="filter-button-item" @click="onClick" @mouseenter="onHover" tabindex="0">
		<div class="filter-button-item__icon-cell">
			<pg-icon class="filter-button-item__icon" :icon="icon" v-if="checked" />
		</div>
		<div class="filter-button-item__text-cell">
			<slot>{{ label }}</slot>
		</div>
	</div>
</template>

<script>
import PgIcon from 'prontogioco/app/components/icon';

export default {
	components: {
		PgIcon
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
		},

		onHover(event) {
			event.target.focus();
		}
	}
};
</script>