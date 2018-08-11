<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import PgIcon from 'prontogioco/app/components/icon';

export default {
	name: 'PgButton',

	components: {
		BButton,
		PgIcon
	},

	inheritAttrs: false,

	props: {
		disabled: {
			type: Boolean,
			default: false
		},
		loading: {
			type: Boolean,
			default: false
		},
		icon: {
			type: String,
			default: ''
		}
	},

	computed: {
		isDisabled() {
			return this.disabled || this.loading;
		}
	}
};
</script>

<template>
	<b-button v-bind="$attrs" :disabled="isDisabled" v-on="$listeners">
		<pg-icon
			v-if="icon || loading"
			:icon="loading ? 'circle-outline-notch' : icon"
			:spinning="loading"
		/>
		<slot />
	</b-button>
</template>