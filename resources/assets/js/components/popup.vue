<template>
	<span :style="{ display: displayStyle }">
		<slot></slot>

		<div v-show="visible" ref="popup" :class="popupClasses" tabindex="0" @keyup.esc.stop="onEsc">
			<slot name="popup">{{ content }}</slot>
		</div>
	</span>
</template>

<script>
// FIXME: Aggiustare la gestione del focus come con dialog.vue (ciclo del focus, ripristino alla chiusura, ecc.)
import Popper from 'popper.js';

export default {
	name: 'ap-popup',

	props: {
		displayStyle: {
			type: String,
			default: 'inline-block' // inline-block, block
		},
		visible: {
			type: Boolean,
			default: false
		},
		placement: {
			type: [String, Array],
			default: 'bottom'
		},
		boundaries: {
			type: String,
			default: 'scrollParent'
		},
		appendToBody: {
			type: Boolean,
			default: false
		},
		content: String,
	},

	data() {
		return {
			currentPlacement: this.placement
		}
	},

	computed: {
		popupClasses() {
			return [
				'ap-popup',
				this.currentPlacement ? 'ap-popup--' + this.currentPlacement : ''
			];
		}
	},

	watch: {
		visible(newValue) {
			if (newValue) {
				this.$nextTick(() => {
					this.initPopper();
				});
			}
		}
	},

	methods: {
		initPopper() {
			if (this.popper) {
				this.popper.update();
				return;
			}

			this.popper = new Popper(this.$el, this.$refs.popup, {
				placement: Array.isArray(this.placement) ? this.placement[0] : this.placement,
				removeOnDestroy: true,
				modifiers: {
					flip: {
						behavior: Array.isArray(this.placement) ? this.placement : 'flip'
					},
					preventOverflow: {
						priority: ['left', 'right'], // Don't move if top and bottom boundaries aren't enough
						boundariesElement: this.boundaries
					},
					arrow: {
						element: '.ap-popup__connector' // FIXME: In nested popovers, querySelector gets the last one
					}
				},
				onCreate: data => {
					this.currentPlacement = data.placement;

					this.$nextTick(() => {
						if (this.appendToBody) document.body.appendChild(this.$refs.popup);
					});
				},
				onUpdate: data => {
					this.currentPlacement = data.placement;
				}
			});
		},

		onEsc() {
			this.close();
		},

		close() {
			this.$emit('update:visible', false)	
		},

		onClickOut() {
			if (this.visible && !this.$el.contains(event.target)) this.close();
		},
	},

	mounted() {
		if (typeof document !== 'undefined') {
			document.documentElement.addEventListener('click', this.onClickOut);
		}

		this.$nextTick(this.initPopper);
	},

	destroyed() {
		if (typeof document !== 'undefined') {
			document.removeEventListener('click', this.onClickOut);
		}

		if (this.popper) {
			this.popper.destroy();
			this.popper = null;
		}
	}
}
</script>