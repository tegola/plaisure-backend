<template>
	<div class="pg-pane">
		<template v-if="!loading">
			<div class="pg-pane__inner" @scroll="calcShadows" ref="inner" @DOMSubtreeModified="calcShadows">
				<slot></slot>
			</div>
			<div class="pg-pane__top-shadow" :style="topShadowStyles"></div>
			<div class="pg-pane__bottom-shadow" :style="bottomShadowStyles"></div>
		</template>
		<div v-if="loading" class="pg-pane__loader">
			<pg-icon icon="circle-outline-notch" spinning></pg-icon>
			Caricamento&hellip;
		</div>
	</div>
</template>

<script>
import PgIcon from './icon';
import _throttle from 'lodash/throttle';

export default {
	name: 'PgPane',

	components: {
		PgIcon
	},

	props: {
		loading: Boolean,
		shadow: String // top, bottom, both
	},

	data() {
		return {
			showTopShadow: false,
			showBottomShadow: false
		};
	},

	computed: {
		topShadowStyles() {
			return {
				opacity: this.showTopShadow ? 1 : 0
			};
		},
		bottomShadowStyles() {
			return {
				opacity: this.showBottomShadow ? 1 : 0
			};
		},
	},

	methods: {
		calcShadows: _throttle(function() { // Do not use fat arrow functions
			// Stop if not shadows are set
			if (!this.shadow) return;

			const target = this.$refs.inner;
			const isBelowTop = target.scrollTop > 0;
			const isAboveBottom = ((target.scrollHeight - target.scrollTop) - target.clientHeight) > 0;

			this.showTopShadow = this.shadow == 'top' || this.shadow == 'both' ? isBelowTop : false;
			this.showBottomShadow = this.shadow == 'bottom' || this.shadow == 'both' ? isAboveBottom : false;
		}, 200)
	}
};
</script>