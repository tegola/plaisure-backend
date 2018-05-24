<template>
	<div class="pg-image-frame" :style="styles">
		<div :style="sizerStyles"></div>
		<div class="pg-image-frame__content" v-if="$slots.default">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'PgImageFrame',

	props: {
		src: String,
		ratio: {
			type: String,
			validator: value => value.match(/^(\d+):(\d+)$/)
		}
	},

	computed: {
		styles() {
			return {
				backgroundImage: this.src ? `url(${this.src})` : null,
			}
		},

		sizerStyles() {
			const ratio = this.ratio.split(':');
			const padding = (ratio[1] / ratio[0]) * 100;

			return {
				paddingTop: `${padding}%`
			}
		}
	}
};
</script>