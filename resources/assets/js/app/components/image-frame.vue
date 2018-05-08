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

<style lang="scss">
.pg-image-frame {
	position: relative;
	display: block;
	width: 100%;
	padding: 0;
	overflow: hidden;

	background-position: center center;
	background-repeat: no-repeat;
	background-size: cover;

	&__content {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: 0;
	}
}
</style>