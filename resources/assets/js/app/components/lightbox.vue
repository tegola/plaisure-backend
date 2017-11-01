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
					<p class="pg-lightbox__subtitle" v-html="subtitle"></p>
				</div>
				<button type="button" class="pg-lightbox__close" @click="close" title="Chiudi" aria-label="Chiudi">
					<pg-icon icon="close" class="pg-lightbox__close-icon"></pg-icon>
				</button>
			</div>

			<div class="pg-lightbox__display" ref="display" @click.stop>
				<div v-for="image in images" class="pg-lightbox__slide">
					<img :src="image.url" class="pg-lightbox__image">
				</div>
				<button v-if="showArrows" type="button" class="pg-lightbox__arrow pg-lightbox__prev-arrow" @click="prev" title="Precedente" aria-label="Precedente">
					<pg-icon icon="chevron-left" class="pg-lightbox__arrow-icon"></pg-icon>
				</button>
				<button v-if="showArrows" type="button" class="pg-lightbox__arrow pg-lightbox__next-arrow" @click="next" title="Seguente" aria-label="Seguente">
					<pg-icon icon="chevron-right" class="pg-lightbox__arrow-icon"></pg-icon>
				</button>
			</div>

			<div class="pg-lightbox__thumbnail-list" v-if="showThumbnails" ref="thumbnails">
				<div v-for="(image, index) in images"
					class="pg-lightbox__thumbnail"
					:style="thumbnailStyle(image)"
					:class="thumbnailClass(image)"
					@click.stop="select(index)">
				</div>
			</div>
		</div>
	</transition>
</template>

<script>
import Flickity from 'flickity';
import Icon from './icon';

export default {
	name: 'pg-lightbox',

	components: {
		'pg-icon': Icon
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
		index: 0
	},

	data() {
		return {
			currentIndex: this.index
		};
	},

	computed: {
		subtitle() {
			const currentImage = this.images[this.currentIndex];

			return [
				this.currentIndex + 1,
				'di',
				this.images.length,
				currentImage.caption ? `&ndash; ${currentImage.caption}` : ''
			].join(' ');
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
		},
		currentIndex() {
			if (!this.showThumbnails) return;

			const thumb = this.$refs.thumbnails.childNodes[this.currentIndex];
			thumb.scrollIntoView();
		}

	},

	methods: {
		imageClass(image) {
			return {
				'pg-lightbox__image--selected': this.images.indexOf(image) == this.currentIndex
			};
		},

		thumbnailStyle(image) {
			return {
				'background-image': `url(${image.thumbnail_url})`
			};
		},

		thumbnailClass(image) {
			return {
				'pg-lightbox__thumbnail--selected': this.images.indexOf(image) == this.currentIndex
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
		},
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
			this.currentIndex = this.flickity.selectedIndex;
		});

		// Focus
		this.$el.focus();

		// Add body class to prevent mouse scrolling
		document.body.classList.add('pg--pg-lightbox-open');
	}
};
</script>