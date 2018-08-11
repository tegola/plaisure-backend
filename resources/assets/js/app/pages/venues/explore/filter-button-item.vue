<template>
	<div class="filter-button-item" tabindex="0" @click="onClick" @mouseenter="onHover">
		<div class="filter-button-item__icon-cell">
			<pg-icon v-if="checked" :icon="icon" class="filter-button-item__icon" />
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
		checked: {
			type: Boolean,
			default: false
		},
		label: {
			type: String,
			default: ''
		}
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