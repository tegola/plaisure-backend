<script>
import BModal from 'bootstrap-vue/es/components/modal/modal';

export default {
	name: 'PgConfirmModal',

	inheritAttrs: false,

	components: {
		BModal
	},

	props: {
		value: Boolean,
		variant: String,
		cancelTitle: {
			type: String,
			default: 'Annulla'
		}
	},

	computed: {
		open: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit('input', val)
			}
		}
	}
}
</script>

<template>
	<b-modal
		v-model="open"
		lazy
		centered
		hide-header-close
		:header-text-variant="variant"
		:ok-variant="variant"
		cancel-variant="light"
		:cancel-title="cancelTitle"
		v-bind="$attrs"
		v-on="$listeners">
		<p class="lead mb-0" v-if="$slots.message" ><slot name="message"></slot></p>
		<slot></slot>
	</b-modal>
</template>