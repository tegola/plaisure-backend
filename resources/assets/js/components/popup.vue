<template>
	<div :is="element">
		<slot></slot>

		<div v-show="visible" ref="popup" :class="popupClasses" tabindex="0" @keyup.esc.stop="close">
			<div tabindex="0" @focus.stop="onFirstElFocus" ref="firstEl"></div>
			<slot name="popup">{{ content }}</slot>
			<div tabindex="0" @focus.stop="onLastElFocus" ref="lastEl"></div>
		</div>
	</div>
</template>

<script>
// FIXME: Aggiustare la gestione del focus come con dialog.vue (ciclo del focus, ripristino alla chiusura, ecc.)
// FIXME: Fare in modo che funzioni con v-if (quindi inizializzare solo quando è effettivamente visibile)
import Popper from 'popper.js';

export default {
	name: 'pg-popup',

	props: {
		element: {
			type: String,
			default: 'span'
		},
		popupClass: String,
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
		content: String
	},

	data() {
		return {
			outsideFocusedEl: null,
			currentPlacement: this.placement
		};
	},

	computed: {
		popupClasses() {
			return [
				this.popupClass,
				'pg-popup',
				this.currentPlacement ? 'pg-popup--' + this.currentPlacement : ''
			];
		}
	},

	watch: {
		visible() {
			this.$nextTick(() => {
				this.visible ? this.onOpen() : this.onClose();
			});
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
						boundariesElement: this.boundaries,
						padding: 0
					},
					arrow: {
						element: '.pg-popup__connector' // FIXME: In nested popovers, querySelector gets the last one
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

		onOpen() {
			this.outsideFocusedEl = document.activeElement;
			this.initPopper();
			this.focus();
			this.$emit('open');
		},

		getFocusables() {
			const selector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
			const focusables = this.$refs.popup.querySelectorAll(selector);

			return [...focusables].filter(node => {
				return node !== this.$refs.firstEl && node !== this.$refs.lastEl;
			});
		},

		focus() {
			this.$refs.lastEl.focus(); // Automatically focuses the first item;
		},

		onFirstElFocus() {
			const focusables = this.getFocusables();
			if (focusables.length) focusables[focusables.length-1].focus();
		},

		onLastElFocus() {
			const focusables = this.getFocusables();
			if (focusables.length) focusables[0].focus();
		},

		close() {
			this.$emit('update:visible', false);
		},

		onClose() {
			if (this.outsideFocusedEl) this.outsideFocusedEl.focus();
			this.$emit('close');
		},

		onClickOut(event) {
			const target = event.target;
			const popup = this.$refs.popup;

			// Stop if popup is not shown or if click is inside the component
			if (!this.visible ||
				target === popup ||
				popup.contains(target) ||
				this.$el.contains(target))
				return;

			// Close the popup
			this.close();
		},
	},

	mounted() {
		if (typeof document !== 'undefined') {
			document.addEventListener('click', this.onClickOut);
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
};
</script>