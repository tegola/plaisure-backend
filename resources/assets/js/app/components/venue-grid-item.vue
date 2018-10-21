<script>
import PgVenueItemMixin from 'prontogioco/app/mixins/venue-collection-item';
import PgImageFrame from 'prontogioco/app/components/image-frame';

export default {
	name: 'PgVenueGridItem',

	components: {
		PgImageFrame
	},

	mixins: [PgVenueItemMixin],

	props: {
		showHighlight: {
			type: Boolean,
			default: true
		}
	},

	computed: {
		highlight() {
			if (this.isNew) return {
				class: 'text-info',
				label: this.$t('components.venue_item.highlight.new')
			};

			if (this.isOpen) return {
				class: 'text-success',
				label: this.$t('components.venue_item.highlight.open')
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
				v-html="iconMarkup"
			/>
		</pg-image-frame>

		<div class="pg-venue-grid-item__category">{{ categories }}</div>
		<div class="pg-venue-grid-item__name">{{ venue.name }}</div>
		<div class="pg-venue-grid-item__address">{{ address }}</div>
		<div v-if="showHighlight && highlight" :class="['pg-venue-grid-item__highlight', highlight.class]">{{ highlight.label }}</div>
	</div>
</template>