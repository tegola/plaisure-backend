<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import PgIcon from '@/components/icon';

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
		},
		iconPosition: {
			type: String,
			default: 'left',
			validator: value => ['left', 'right'].indexOf(value) !== -1
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
		<!-- Left icon (used also as loader) -->
		<pg-icon
			v-if="(icon && iconPosition === 'left') || loading"
			:icon="loading ? 'circle-outline-notch' : icon"
			:spinning="loading"
			class="pg-button__icon"
		/>

		<!-- Content -->
		<slot v-if="!loading" />

		<!-- Right icon -->
		<pg-icon
			v-if="(icon && iconPosition === 'right') && !loading"
			:icon="icon"
			class="pg-button__icon"
		/>
	</b-button>
</template>