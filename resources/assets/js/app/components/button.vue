<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import PgIcon from 'prontogioco/app/components/icon'

export default {
	name: 'PgButton',

	components: {
		BButton,
		PgIcon
	},

	props: {
		loading: Boolean,
		icon: String
	}
}
</script>

<template>
	<b-button v-bind="$attrs" v-on="$listeners" :disabled="loading">
		<pg-icon
			v-if="icon || loading"
			:icon="loading ? 'circle-outline-notch' : icon"
			:spinning="loading"
		/>
		<slot></slot>
	</b-button>
</template>