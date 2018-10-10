<script>
import isVenueOpen from 'prontogioco/utilities/is-venue-open';
import PgImageFrame from 'prontogioco/app/components/image-frame';

export default {
	name: 'PgVenueGridItem',

	components: {
		PgImageFrame
	},

	props: {
		venue: {
			type: Object,
			required: true
		},

		showHighlight: {
			type: Boolean,
			default: true
		}
	},

	computed: {
		icon() {
			const name = this.firstCategoryMachineName.replace('-', '_');

			return require(`!svg-inline-loader!assets/svg/category-icons/${name}.svg`);
		},

		photo() {
			if (!this.venue.photos || !this.venue.photos.length) return null;

			return this.venue.photos[0];
		},

		categories() {
			if (!this.venue.categories || !this.venue.categories.length) return null;

			return this.venue.categories
				.slice(0, 2)
				.map(category => this.$t(`db.categories.${category.machine_name}`))
				.join(', ');
		},

		firstCategoryMachineName() {
			if (!this.venue.categories || !this.venue.categories.length) return null;

			return this.venue.categories[0].machine_name;
		},

		address() {
			const a = this.venue.address;

			return [
				[a.street, a.number].join(' '),
				a.city
			].join(', ');
		},

		isOpen() {
			return isVenueOpen(this.venue.business_hours);
		},

		isNew() {
			const created = new Date(this.venue.created_at);
			const now = new Date();
			const days = (now - created) / (1000*60*60*24);

			return days <= 1;
		},

		highlight() {
			if (this.isNew) return {
				class: 'text-info',
				label: 'Nuovo!'
			};

			if (this.isOpen) return {
				class: 'text-success',
				label: 'Aperto ora!'
			};

			return null;
		}
	}
};
</script>

<template>
	<div class="pg-venue-grid-item">
		<pg-image-frame
			:src="photo ? photo.resized_url : null"
			:content-class="photo ? null : 'pg-venue-grid-item__image-content'"
			class="pg-venue-grid-item__image">
			<div
				v-if="!photo"
				class="pg-venue-grid-item__image-icon"
				v-html="icon"
			/>
		</pg-image-frame>

		<div class="pg-venue-grid-item__category">{{ categories }}</div>
		<div class="pg-venue-grid-item__name">{{ venue.name }}</div>
		<div class="pg-venue-grid-item__address">{{ address }}</div>
		<div v-if="showHighlight && highlight" :class="['pg-venue-grid-item__highlight', highlight.class]">{{ highlight.label }}</div>
	</div>
</template>