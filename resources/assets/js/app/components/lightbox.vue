<script>
import 'classlist-polyfill';
import Flickity from 'flickity';
import PgIcon from './icon';

export default {
	name: 'PgLightbox',

	components: {
		PgIcon
	},

	props: {
		title: {
			type: String,
			required: true
		},
		images: {
			type: Array,
			required: true,
			default: () => []
		},
		arrows: {
			type: Boolean,
			default: true
		},
		thumbnails: {
			type: Boolean,
			default: true
		},
		index: {
			type: Number,
			default: 0
		}
	},

	data() {
		return {
			mutableIndex: this.index
		};
	},

	computed: {
		counter() {
			return this.$t('components.lightbox.counter', {
				current: this.mutableIndex + 1,
				total: this.images.length
			});
		},
		caption() {
			return this.images[this.mutableIndex].caption;
		},
		showArrows() {
			return this.arrows && this.images.length > 1;
		},
		showThumbnails() {
			return this.thumbnails && this.images.length > 1;
		}
	},

	watch: {
		index() {
			this.select(this.index, true);
		}
	},

	mounted() {
		this.flickity = new Flickity(this.$refs.display, {
			cellSelector: '.pg-lightbox__slide',
			wrapAround: true,
			prevNextButtons: false,
			pageDots: false,
			setGallerySize: false,
			accessibility: false, // We handle the keyboard keys by ourselves
			initialIndex: this.index
		});

		// Enable cells to get focus:
		// https://github.com/metafizzy/flickity/issues/565#issuecomment-304754578
		this.flickity.canPreventDefaultOnPointerDown = () => false;

		// Update current index on cell change
		this.flickity.on('select', () => {
			this.mutableIndex = this.flickity.selectedIndex;

			if (this.showThumbnails) {
				const thumb = this.$refs.thumbnails.childNodes[this.mutableIndex];
				thumb.scrollIntoView();
			}
		});

		// Focus
		this.$el.focus();

		// Add body class to prevent mouse scrolling
		document.body.classList.add('pg--pg-lightbox-open');
	},

	methods: {
		thumbnailStyle(image) {
			return {
				'background-image': `url(${image.thumbnail_url})`
			};
		},

		thumbnailClass(image) {
			return {
				'pg-lightbox__thumbnail--selected': this.images.indexOf(image) == this.mutableIndex
			};
		},

		prev() {
			this.flickity.previous(true);
		},

		next() {
			this.flickity.next(true);
		},

		select(index, instant = false) {
			this.flickity.select(index, true, instant);
		},

		close() {
			this.$emit('close');
		},

		beforeLeave() {
			// Remove body class
			document.body.classList.remove('pg--pg-lightbox-open');
		},

		afterLeave() {
			// Destroy slider
			this.flickity.destroy();
			this.flickity = null;
		}
	}
};
</script>

<template>
	<transition name="pg-lightbox--visible" @before-leave="beforeLeave" @after-leave="afterLeave">
		<div class="pg-lightbox"
			tabindex="0"
			@click="close"
			@keydown.esc="close"
			@keydown.left="prev"
			@keydown.right="next">
			<div class="pg-lightbox__header">
				<div class="pg-lightbox__title-container">
					<h4 class="pg-lightbox__title">{{ title }}</h4>
					<p class="pg-lightbox__subtitle">
						{{ counter }}
						<template v-if="caption">&ndash; {{ caption }}</template>
					</p>
				</div>
				<button
					:title="$t('components.lightbox.close')"
					:aria-label="$t('components.lightbox.close')"
					type="button"
					class="pg-lightbox__close"
					@click="close">
					<pg-icon icon="close" class="pg-lightbox__close-icon" />
				</button>
			</div>

			<div ref="display" class="pg-lightbox__display" @click.stop>
				<div v-for="(image, index) in images" :key="index" class="pg-lightbox__slide">
					<img :src="image.url" class="pg-lightbox__image">
				</div>
				<button
					v-if="showArrows"
					:title="$t('components.lightbox.previous')"
					:aria-label="$t('components.lightbox.previous')"
					type="button"
					class="pg-lightbox__arrow pg-lightbox__prev-arrow"
					@click="prev">
					<pg-icon icon="chevron-left" class="pg-lightbox__arrow-icon" />
				</button>
				<button
					v-if="showArrows"
					:title="$t('components.lightbox.next')"
					:aria-label="$t('components.lightbox.next')"
					type="button"
					class="pg-lightbox__arrow pg-lightbox__next-arrow"
					@click="next">
					<pg-icon icon="chevron-right" class="pg-lightbox__arrow-icon" />
				</button>
			</div>

			<div v-if="showThumbnails" ref="thumbnails" class="pg-lightbox__thumbnail-list">
				<div v-for="(image, index) in images"
					:key="index"
					:style="thumbnailStyle(image)"
					:class="thumbnailClass(image)"
					:title="image.caption"
					class="pg-lightbox__thumbnail"
					@click.stop="select(index)"
				/>
			</div>
		</div>
	</transition>
</template>