<template>
	<pg-popup :visible.sync="popupVisible" placement="bottom-start" append-to-body @close="onPopupClose" popup-class="filter-button-popup">
		<div class="filter-button" :class="{ 'filter-button--open': popupVisible }" @click="popupVisible = !popupVisible">
			<div>
				<span class="filter-button__label">{{ label }}</span>
				<pg-icon icon="chevron-down" class="filter-button__arrow"></pg-icon>
			</div>
			<div class="filter-button__text" v-if="text">{{ text }}</div>
			<div class="filter-button__text filter-button__placeholder" v-if="!text">{{ placeholder }}</div>
		</div>
		<template slot="popup">
			<slot></slot>
		</template>
	</pg-popup>
</template>

<script>
import PgPopup from '../../../components/popup';
import PgIcon from '../../components/icon';

export default {
	components: {
		'pg-popup': PgPopup,
		'pg-icon': PgIcon
	},

	props: {
		label: {
			type: String,
			required: true
		},
		placeholder: String,
		text: {
			type: String,
			default: ''
		}
	},

	data() {
		return {
			popupVisible: false,
		};
	},

	methods: {
		onPopupClose() {
			this.$emit('close');
		}
	}
};
</script>