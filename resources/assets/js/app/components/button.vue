<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import PgIcon from 'prontogioco/app/components/icon'

export default {
	name: 'PgButton',

	inheritAttrs: false,

	components: {
		BButton,
		PgIcon
	},

	props: {
		disabled: Boolean,
		loading: Boolean,
		icon: String
	},

	computed: {
		isDisabled() {
			return this.disabled || this.loading;
		}
	}
}
</script>

<template>
	<b-button v-bind="$attrs" v-on="$listeners" :disabled="isDisabled">
		<pg-icon
			v-if="icon || loading"
			:icon="loading ? 'circle-outline-notch' : icon"
			:spinning="loading"
		/>
		<slot></slot>
	</b-button>
</template>