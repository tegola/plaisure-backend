<template>
	<pg-popup :visible.sync="popupVisible" placement="bottom-start" append-to-body popup-class="filter-button-popup" @close="onPopupClose">
		<div :class="{ 'filter-button--open': popupVisible }" class="filter-button" @click="popupVisible = !popupVisible">
			<div>
				<span class="filter-button__label">{{ label }}</span>
				<pg-icon icon="chevron-down" class="filter-button__arrow" />
			</div>
			<div v-if="text" class="filter-button__text">{{ text }}</div>
			<div v-if="!text" class="filter-button__text filter-button__placeholder">{{ placeholder }}</div>
		</div>
		<template slot="popup">
			<slot />
		</template>
	</pg-popup>
</template>

<script>
import PgPopup from 'prontogioco/components/popup';
import PgIcon from 'prontogioco/app/components/icon';

export default {
	components: {
		PgPopup,
		PgIcon
	},

	props: {
		label: {
			type: String,
			required: true
		},
		placeholder: {
			type: String,
			default: ''
		},
		text: {
			type: String,
			default: ''
		}
	},

	data() {
		return {
			popupVisible: false
		};
	},

	methods: {
		onPopupClose() {
			this.$emit('close');
		}
	}
};
</script>