<template>
	<div class="pg-lightbox"
		:class="{ 'pg-lightbox--visible': visible }"
		@click.self="close"
		@keydown.esc="close"
		@keydown.left="prev"
		@keydown.right="next">
		<div class="pg-lightbox__header">
			<div style="flex: 1">
				<h4 class="pg-lightbox__title">{{ title }}</h4>
				<p class="pg-lightbox__subtitle" v-html="subtitle"></p>
			</div>
			<button type="button" class="pg-lightbox__close" @click="close" title="Chiudi" aria-label="Chiudi">
				<pg-icon icon="close" class="pg-lightbox__close-icon"></pg-icon>
			</button>
		</div>

		<div class="pg-lightbox__display" ref="display" tabindex="0">
			<div v-for="image in images" class="pg-lightbox__slide">
				<img :src="image.url" class="pg-lightbox__image">
			</div>
			<button v-if="arrows" type="button" class="pg-lightbox__arrow pg-lightbox__prev-arrow" @click="prev" title="Precedente" aria-label="Precedente">
				<pg-icon icon="chevron-left" class="pg-lightbox__arrow-icon"></pg-icon>
			</button>
			<button v-if="arrows" type="button" class="pg-lightbox__arrow pg-lightbox__next-arrow" @click="next" title="Seguente" aria-label="Seguente">
				<pg-icon icon="chevron-right" class="pg-lightbox__arrow-icon"></pg-icon>
			</button>
		</div>

		<div class="pg-lightbox__thumbnail-list" v-if="thumbnails" ref="thumbnails">
			<div v-for="(image, index) in images"
				class="pg-lightbox__thumbnail"
				:style="thumbnailStyle(image)"
				:class="thumbnailClass(image)"
				@click="select(index)">
			</div>
		</div>
	</div>
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
		visible: Boolean,
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
		}
	},

	watch: {
		visible: 'onVisibilityChange',
		arrows: 'refreshSlider',
		thumbnails: 'refreshSlider',
		index() {
			this.select(this.index, true);
		},
		currentIndex() {
			if (!this.thumbnails) return;

			const thumb = this.$refs.thumbnails.childNodes[this.currentIndex];
			thumb.scrollIntoView();
		}

	},

	methods: {
		initSlider() {
			this.flickity = new Flickity(this.$refs.display, {
				cellSelector: '.pg-lightbox__slide',
				wrapAround: true,
				prevNextButtons: false,
				pageDots: false,
				setGallerySize: false,
				accessibility: false // We handle the keyboard keys by ourselves
			});

			this.flickity.on('select', () => {
				this.currentIndex = this.flickity.selectedIndex;
			});

			this.$refs.display.focus();
		},

		refreshSlider() {
			this.flickity.destroy();
			this.initSlider();
		},

		onVisibilityChange() {
			if (this.visible) this.$refs.display.focus();
			document.body.classList.toggle('pg--pg-lightbox-visible', this.visible ? true : false);
		},

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
			this.$emit('update:visible', false);
		}
	},

	mounted() {
		this.initSlider();
		this.onVisibilityChange();
	},

	beforeDestroy () {
		this.flickity.destroy();
		this.flickity = null;
	}
};
</script>