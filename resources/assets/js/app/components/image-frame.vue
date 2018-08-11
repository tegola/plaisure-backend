<script>
export default {
	name: 'PgImageFrame',

	props: {
		src: {
			type: String,
			default: ''
		},
		ratio: {
			type: String,
			default: '3:2',
			validator: value => value.match(/^(\d+):(\d+)$/)
		}
	},

	computed: {
		styles() {
			return {
				backgroundImage: this.src ? `url(${this.src})` : null
			};
		},

		sizerStyles() {
			const ratio = this.ratio.split(':');
			const padding = (ratio[1] / ratio[0]) * 100;

			return {
				paddingTop: `${padding}%`
			};
		}
	}
};
</script>

<template>
	<div :style="styles" class="pg-image-frame">
		<div :style="sizerStyles" />
		<div v-if="$slots.default" class="pg-image-frame__content">
			<slot />
		</div>
	</div>
</template>