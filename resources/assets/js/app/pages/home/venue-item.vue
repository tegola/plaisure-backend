<script>
import isVenueOpen from 'prontogioco/utilities/is-venue-open';
import PgImageFrame from 'prontogioco/app/components/image-frame';

export default {
	name: 'PgHomePageVenueItem',

	components: {
		PgImageFrame
	},

	props: {
		venue: {
			type: Object,
			required: true
		}
	},

	computed: {
		route() {
			return {
				name: 'venues.detail',
				params: {
					venueId: this.venue.id
				}
			};
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
	<router-link :to="route" class="pg-home-page__venue-item">
		<pg-image-frame
			:src="photo ? photo.resized_url : null"
			class="pg-home-page__venue-item-image"
		/>

		<div class="pg-home-page__venue-item-category">{{ categories }}</div>
		<div class="pg-home-page__venue-item-name">{{ venue.name }}</div>
		<div class="pg-home-page__venue-item-address">{{ address }}</div>
		<div v-if="highlight" :class="['pg-home-page__venue-item-highlight', highlight.class]">{{ highlight.label }}</div>
	</router-link>
</template>