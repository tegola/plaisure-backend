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
		},
		contentClass: {
			type: String,
			default: null
		},
		sizing: {
			type: String,
			default: 'cover',
			validator: value => ['cover', 'contain'].indexOf(value) !== -1
		}
	},

	computed: {
		classes() {
			return [
				'pg-image-frame',
				this.sizing ? `pg-image-frame--${this.sizing}` : null
			];
		},

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
	<div :class="classes" :style="styles">
		<div :style="sizerStyles" />
		<div v-if="$slots.default" :class="['pg-image-frame__content', contentClass]">
			<slot />
		</div>
	</div>
</template>